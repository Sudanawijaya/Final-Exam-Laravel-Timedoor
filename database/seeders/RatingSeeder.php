<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\User;
use App\Models\Book;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = User::pluck('id')->toArray();
        $bookIds = Book::pluck('id')->toArray();

        if (empty($userIds) || empty($bookIds)) {
            $this->command->warn('User or Book data missing. Run UserSeeder and BookSeeder first.');
            return;
        }

        $ratings = [];
        for ($i = 0; $i < 500000; $i++) {
            $ratings[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'book_id' => $bookIds[array_rand($bookIds)],
                'rating' => $faker->numberBetween(1, 10),
                'review' => $faker->optional(0.3)->sentence,
                'created_at' => Carbon::now()->subDays(rand(0, 120))->subMinutes(rand(0, 1440)),
                'updated_at' => now(),
            ];

            if ($i % 5000 === 0) {
                Rating::insert($ratings);
                $ratings = [];
            }
        }

        if (!empty($ratings)) {
            Rating::insert($ratings);
        }
    }
}
