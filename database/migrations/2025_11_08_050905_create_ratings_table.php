<?php

// database/migrations/2025_01_01_000005_create_ratings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration {
    public function up() {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('book_id')->index();
            $table->tinyInteger('rating'); // 1..10
            $table->text('review')->nullable();
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

  
            $table->index(['created_at','book_id']);
        });
    }
    public function down() {
        Schema::dropIfExists('ratings');
    }
}

