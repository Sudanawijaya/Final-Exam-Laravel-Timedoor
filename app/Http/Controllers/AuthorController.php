<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Author;
use Carbon\Carbon;

class AuthorController extends Controller
{
    public function top(Request $request)
    {
        $tab = $request->input('tab','popularity'); 

        if ($tab === 'popularity') {
            $authors = DB::table('authors')
                ->leftJoin('books','books.author_id','authors.id')
                ->leftJoin('ratings','ratings.book_id','books.id')
                ->select('authors.id','authors.name', DB::raw('SUM(CASE WHEN ratings.rating > 5 THEN 1 ELSE 0 END) as voters_gt5'))
                ->groupBy('authors.id','authors.name')
                ->orderByDesc('voters_gt5')
                ->limit(20)
                ->get();
        } elseif ($tab === 'avg_rating') {
            $authors = DB::table('authors')
                ->leftJoin('books','books.author_id','authors.id')
                ->leftJoin('ratings','ratings.book_id','books.id')
                ->select('authors.id','authors.name', DB::raw('AVG(ratings.rating) as avg_rating'), DB::raw('COUNT(ratings.id) as total_ratings'))
                ->groupBy('authors.id','authors.name')
                ->orderByDesc('avg_rating')
                ->limit(20)
                ->get();
        } else {
            $now = Carbon::now();
            $last30 = $now->copy()->subDays(30)->toDateTimeString();
            $prev60 = $now->copy()->subDays(60)->toDateTimeString();
            $prev31 = $now->copy()->subDays(31)->toDateTimeString();

            $raw = DB::table('authors')
                ->leftJoin('books','books.author_id','authors.id')
                ->leftJoin('ratings','ratings.book_id','books.id')
                ->select('authors.id','authors.name',
                    DB::raw("AVG(CASE WHEN ratings.created_at >= '{$last30}' THEN ratings.rating END) as avg_last_30d"),
                    DB::raw("AVG(CASE WHEN ratings.created_at BETWEEN '{$prev60}' AND '{$prev31}' THEN ratings.rating END) as avg_prev_30d"),
                    DB::raw("COUNT(CASE WHEN ratings.created_at >= '{$last30}' THEN 1 END) as voters_last30")
                )
                ->groupBy('authors.id','authors.name')
                ->get();

    
            $authors = $raw->map(function($a){
                $a->avg_last_30d = $a->avg_last_30d ?? 0;
                $a->avg_prev_30d = $a->avg_prev_30d ?? 0;
                $diff = $a->avg_last_30d - $a->avg_prev_30d;
                $a->trend_score = $diff * log(1 + ($a->voters_last30 ?? 0));
                return $a;
            })->sortByDesc('trend_score')->take(20)->values();
        }

        $authorIds = $authors->pluck('id')->toArray();
        $stats = [];
        foreach ($authorIds as $id) {
            $total = DB::table('books')->where('author_id',$id)->join('ratings','ratings.book_id','books.id')->count();
            $best = DB::table('books')->where('author_id',$id)
                    ->leftJoin('ratings','ratings.book_id','books.id')
                    ->select('books.id','books.title',DB::raw('AVG(ratings.rating) as avg'))->groupBy('books.id')->orderByDesc('avg')->first();
            $worst = DB::table('books')->where('author_id',$id)
                    ->leftJoin('ratings','ratings.book_id','books.id')
                    ->select('books.id','books.title',DB::raw('AVG(ratings.rating) as avg'))->groupBy('books.id')->orderBy('avg')->first();
            $stats[$id] = ['total'=>$total,'best'=>$best,'worst'=>$worst];
        }

        return view('authors.top', compact('authors','stats','tab'));
    }
}
