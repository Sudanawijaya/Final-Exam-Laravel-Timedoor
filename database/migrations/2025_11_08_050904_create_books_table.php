<?php

// database/migrations/2025_01_01_000003_create_books_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration {
    public function up() {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('isbn')->nullable()->index();
            $table->unsignedBigInteger('author_id')->index();
            $table->string('publisher')->nullable()->index();
            $table->year('publication_year')->nullable()->index();
            $table->enum('availability', ['available','rented','reserved'])->default('available')->index();
            $table->string('store_location')->nullable()->index();

            $table->unsignedInteger('total_votes')->default(0)->index();
            $table->decimal('avg_rating', 4, 2)->default(0.00)->index();
            $table->decimal('weighted_score', 10, 6)->default(0.0)->index();
      
            $table->unsignedInteger('recent_votes_30d')->default(0);
            $table->decimal('avg_last_30d', 4, 2)->default(0.00);
            $table->decimal('avg_prev_30d', 4, 2)->default(0.00);
            $table->decimal('avg_last_7d', 4, 2)->default(0.00);

            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
        });
    }
    public function down() {
        Schema::dropIfExists('books');
    }
}

