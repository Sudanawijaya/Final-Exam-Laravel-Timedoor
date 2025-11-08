<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use Faker\Factory as Faker;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $authors = [];
        for ($i = 0; $i < 1000; $i++) {
            $authors[] = [
                'name' => $faker->name,
                'bio' => $faker->optional()->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $chunks = array_chunk($authors, 500);
        foreach ($chunks as $chunk) {
            Author::insert($chunk);
        }
    }
}
