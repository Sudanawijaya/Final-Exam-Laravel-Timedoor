<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $authorIds = DB::table('authors')->pluck('id')->toArray();

        $mallList = [
            'Grand Indonesia, Jakarta',
            'Tunjungan Plaza, Surabaya',
            'Trans Studio Mall, Bandung',
            'Pakuwon Mall, Surabaya',
            'AEON Mall, BSD'
        ];

        $books = [];

        for ($i = 0; $i < 100000; $i++) {
            $books[] = [
                'title' => $faker->sentence(3),
                'isbn' => $faker->unique()->isbn13,
                'author_id' => $faker->randomElement($authorIds),
                'publisher' => $faker->company,
                'publication_year' => $faker->numberBetween(1980, 2025),
                'availability' => $faker->randomElement(['available', 'rented', 'reserved']),
                'store_location' => $faker->randomElement($mallList),
                'total_votes' => 0,
                'avg_rating' => 0,
                'weighted_score' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Sisipkan tiap 2000 baris agar tidak berat
            if (count($books) >= 2000) {
                DB::table('books')->insert($books);
                $books = [];
            }
        }

        // Sisa data yang belum dimasukkan
        if (!empty($books)) {
            DB::table('books')->insert($books);
        }
    }
}
