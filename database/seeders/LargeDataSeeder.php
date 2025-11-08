<?php

// database/seeders/LargeDataSeeder.php
namespace Database\Seeders; // ← wajib


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class LargeDataSeeder extends Seeder
{
    public function run()
    {
        Model::unguard(); 
        $faker = Faker::create();

       
        $authors = [];
        for ($i = 0; $i < 1000; $i++) {
            $authors[] = [
                'name' => $faker->name,
                'bio' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('authors')->insert($authors);
        $authorIds = DB::table('authors')->pluck('id')->toArray();

       
        $categories = [];
        for ($i = 0; $i < 3000; $i++) {
            $categories[] = [
                'name' => ucfirst($faker->words(2, true)) . ' ' . $i,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        foreach (array_chunk($categories, 500) as $chunk)
            DB::table('categories')->insert($chunk);
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        // 3️⃣ Users 50k
        $users = [];
        for ($i = 0; $i < 50000; $i++) {
            $users[] = [
                'name' => $faker->name,
                'email' => 'user' . $i . '@example.com',
                'password' => bcrypt('secret'), // wajib diisi
                'created_at' => now(),
                'updated_at' => now()
            ];
            if (count($users) >= 2000) {
                DB::table('users')->insert($users);
                $users = [];
            }
        }
        if (count($users))
            DB::table('users')->insert($users);
        $userIds = DB::table('users')->pluck('id')->toArray();

        // 4️⃣ Books 100k
        $books = [];

        // daftar 5 mall Indonesia
        $mallList = [
            'Grand Indonesia, Jakarta',
            'Tunjungan Plaza, Surabaya',
            'Trans Studio Mall, Bandung',
            'Pakuwon Mall, Surabaya',
            'AEON Mall, BSD'
        ];

        for ($i = 0; $i < 100000; $i++) {
            $authorId = $authorIds[array_rand($authorIds)];
            $books[] = [
                'title' => $faker->sentence(3),
                'isbn' => $faker->unique()->isbn13,
                'author_id' => $authorId,
                'publisher' => $faker->company,
                'publication_year' => $faker->numberBetween(1980, 2025),
                'availability' => $faker->randomElement(['available', 'rented', 'reserved']),
                'store_location' => $faker->randomElement($mallList), // hanya 5 mall
                'total_votes' => 0,
                'avg_rating' => 0,
                'weighted_score' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($books) >= 2000) {
                DB::table('books')->insert($books);
                $books = [];
            }
        }
        if (count($books))
            DB::table('books')->insert($books);


        // 5️⃣ Book-Category pivot 1-4 per book
        $pivots = [];
        foreach ($bookIds as $bid) {
            $countCats = rand(1, 4);
            $pickedKeys = (array) array_rand($categoryIds, $countCats);
            foreach ($pickedKeys as $k) {
                $pivots[] = ['book_id' => $bid, 'category_id' => $categoryIds[$k]];
                if (count($pivots) >= 5000) {
                    DB::table('book_category')->insert($pivots);
                    $pivots = [];
                }
            }
        }
        if (count($pivots))
            DB::table('book_category')->insert($pivots);

        // 6️⃣ Ratings 500k
        $ratings = [];
        for ($i = 0; $i < 500000; $i++) {
            $ratings[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'book_id' => $bookIds[array_rand($bookIds)],
                'rating' => $faker->numberBetween(1, 10),
                'review' => $faker->optional(0.2)->sentence,
                'created_at' => Carbon::now()->subDays(rand(0, 119))->subMinutes(rand(0, 1440)),
                'updated_at' => now()
            ];
            if (count($ratings) >= 2000) {
                DB::table('ratings')->insert($ratings);
                $ratings = [];
            }
        }
        if (count($ratings))
            DB::table('ratings')->insert($ratings);

        // DONE: Hitung stats denormalisasi dengan batch
        $this->command->info('Computing book stats...');
        $chunkSize = 1000;
        DB::table('books')->select('id')->orderBy('id')->chunk($chunkSize, function ($bookChunk) {
            $ids = $bookChunk->pluck('id')->toArray();
            $stats = DB::table('ratings')
                ->selectRaw('book_id, COUNT(*) as cnt, AVG(rating) as avg')
                ->whereIn('book_id', $ids)
                ->groupBy('book_id')
                ->get()
                ->keyBy('book_id');

            // update batch per 1000 using CASE WHEN (lebih cepat)
            $caseVotes = $caseAvg = [];
            foreach ($stats as $bid => $s) {
                $caseVotes[] = "WHEN id=$bid THEN $s->cnt";
                $caseAvg[] = "WHEN id=$bid THEN " . round($s->avg, 2);
            }
            $idsStr = implode(',', $stats->keys()->toArray());
            if (!empty($idsStr)) {
                DB::update("UPDATE books SET 
                    total_votes = CASE " . implode(' ', $caseVotes) . " END,
                    avg_rating = CASE " . implode(' ', $caseAvg) . " END,
                    updated_at = NOW()
                    WHERE id IN ($idsStr)
                ");
            }
        });

        $this->command->info('Seeding complete.');
        Model::reguard();
    }
}

