<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\RatingController;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::get('/authors/top', [AuthorController::class, 'top'])->name('authors.top');

Route::get('/ratings/create', [RatingController::class, 'create'])->name('ratings.create');
Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');

// helper endpoint for rating form (load books by author)
Route::get('/api/author/{author}/books', function(\App\Models\Author $author){
    return $author->books()->select('id','title')->orderBy('title')->limit(200)->get();
});

