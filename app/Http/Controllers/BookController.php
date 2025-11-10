<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $C = DB::table('ratings')->avg('rating') ?? 5.0; 
        $m = 50;

        $query = Book::with(['author', 'categories']);
            
        $weightedScoreFormula = "
            ( (SELECT COUNT(*) FROM ratings WHERE ratings.book_id = books.id) /
            ((SELECT COUNT(*) FROM ratings WHERE ratings.book_id = books.id) + {$m}) )
            * (SELECT AVG(rating) FROM ratings WHERE ratings.book_id = books.id)
            +
            ({$m} / ((SELECT COUNT(*) FROM ratings WHERE ratings.book_id = books.id) + {$m})) * {$C}
        ";

        $query->select('books.*')
            ->selectRaw("({$weightedScoreFormula}) as weighted_score")
            ->selectRaw('(SELECT COUNT(*) FROM ratings WHERE ratings.book_id = books.id) as ratings_count')
            ->selectRaw('(SELECT AVG(rating) FROM ratings WHERE ratings.book_id = books.id) as avg_rating');
            
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhereHas('author', fn($a) => $a->where('name', 'like', "%{$search}%"));
            });
        }

        $categoryIds = $request->input('categories'); 
        $catMode = strtoupper($request->input('cat_mode', 'OR')); 

        if (!empty($categoryIds) && is_array($categoryIds)) {
            
            switch ($catMode) {
                case 'AND':
                    foreach ($categoryIds as $catId) {
                        $query->whereHas('categories', fn($q) => $q->where('categories.id', $catId));
                    }
                    break;

                case 'OR':
                default:
                    $query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds));
                    break;
            }
        }
        
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        if ($request->filled('year_from')) {
            $query->where('publication_year', '>=', (int) $request->year_from);
        }
        if ($request->filled('year_to')) {
            $query->where('publication_year', '<=', (int) $request->year_to);
        }

        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }

        if ($request->filled('store_location')) {
            $query->where('store_location', $request->store_location);
        }

        if ($request->filled('rating_min')) {
            $query->where('avg_rating', '>=', (float) $request->rating_min);
        }
        if ($request->filled('rating_max')) {
            $query->where('avg_rating', '<=', (float) $request->rating_max);
        }

        $sort = $request->input('sort', 'weighted');

        switch ($sort) {
            case 'votes':
                $query->orderByDesc('ratings_count')->orderBy('id');
                break;

            case 'recent_pop':
                $query->orderByDesc('recent_votes_30d')
                    ->orderByDesc('avg_last_30d')
                    ->orderBy('id');
                break;
            case 'alpha':
                $query->orderBy('title')->orderBy('id');
                break;
            
            default: 
                $query->orderByDesc('weighted_score')->orderBy('id');
                break;
        }

        $perPage = min(50, (int) $request->input('per_page', 20));
        $books = $query->paginate($perPage)
                ->onEachSide(1) 
                ->withQueryString();
        
        $avgGlobal = $C; 

        $books->through(function ($book) use ($avgGlobal) {
            $rating = (float) $book->avg_rating; 

            if (is_null($rating) || $rating === 0) {
                $book->trend = null;
            } elseif ($rating > $avgGlobal) {
                $book->trend = 'up';
            } elseif ($rating < $avgGlobal) {
                $book->trend = 'down';
            } else {
                $book->trend = null;
            }

            return $book;
        });

        $authors = Author::select('id', 'name')->orderBy('name')->limit(200)->get();
        $categories = Category::select('id', 'name')->orderBy('name')->limit(300)->get();
        $locations = Book::select('store_location')->distinct()->pluck('store_location');

        return view('books.index', compact('books', 'authors', 'categories', 'locations', 'sort'));
    }
}