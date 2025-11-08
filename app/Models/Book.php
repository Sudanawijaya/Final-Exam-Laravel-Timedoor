<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model {
    use HasFactory;
    protected $fillable = [
        'title','isbn','author_id','publisher','publication_year',
        'availability','store_location',
        'total_votes','avg_rating','weighted_score',
        'recent_votes_30d','avg_last_30d','avg_prev_30d','avg_last_7d'
    ];

    public function author() {
        return $this->belongsTo(Author::class);
    }
    public function categories() {
        return $this->belongsToMany(Category::class, 'book_category');
    }
    public function ratings() {
        return $this->hasMany(Rating::class);
    }
}

