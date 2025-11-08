<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RatingController extends Controller
{
    public function create()
    {

        $authors = \App\Models\Author::select('id', 'name')->orderBy('name')->get();
        return view('ratings.create', compact('authors'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|exists:users,id',
            'author_id' => 'required|exists:authors,id',
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:2000'
        ]);

        // validate book-author combination
        $book = Book::find($data['book_id']);
        if (!$book || $book->author_id != $data['author_id']) {
            return back()->withErrors(['book_id' => 'Selected book does not belong to the chosen author.'])->withInput();
        }


        $last = DB::table('ratings')
            ->where('user_id', $data['user_id'])
            ->where('book_id', $data['book_id'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($last && Carbon::parse($last->created_at)->greaterThan(Carbon::now()->subHours(24))) {
            return back()->withErrors([
                'rate_limit' => 'You must wait 24 hours before re-rating this book. Last rating: ' . $last->created_at
            ])->withInput();
        }

        DB::transaction(function () use ($data, $book) {
            Rating::create([
                'user_id' => $data['user_id'],
                'book_id' => $data['book_id'],
                'rating' => $data['rating'],
                'review' => $data['review'] ?? null,
            ]);

            DB::update('UPDATE books SET 
                total_votes = total_votes + 1,
                avg_rating = ROUND((avg_rating * (total_votes) + ?) / (total_votes + 1), 2),
                weighted_score = ROUND((( (avg_rating * (total_votes)) + ? ) / (total_votes + 1)) * LOG(1 + (total_votes + 1)), 6),
                updated_at = NOW()
                WHERE id = ?', [$data['rating'], $data['rating'], $data['book_id']]);
        }, 5);

        return redirect()->route('books.index')->with('success', 'Rating submitted successfully.');
    }
}
