<?php

// database/migrations/2025_01_01_000001_create_authors_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration {
    public function up() {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('bio')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('authors');
    }
}

