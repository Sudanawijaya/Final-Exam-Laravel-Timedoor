<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BookCategorySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $bookIds = DB::table('books')->pluck('id')->toArray();
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        if (empty($bookIds) || empty($categoryIds)) {
            $this->command->warn('Books or Categories data missing. Run BookSeeder and CategorySeeder first.');
            return;
        }

        $pivots = [];
        foreach ($bookIds as $bid) {
            $countCats = rand(1, 4);
            $pickedKeys = (array) array_rand($categoryIds, $countCats);

            foreach ($pickedKeys as $k) {
                $pivots[] = [
                    'book_id' => $bid,
                    'category_id' => $categoryIds[$k]
                ];

                if (count($pivots) >= 5000) {
                    DB::table('book_category')->insert($pivots);
                    $pivots = [];
                }
            }
        }

        if (count($pivots)) {
            DB::table('book_category')->insert($pivots);
        }

        $this->command->info('Book-Category pivot table seeded successfully!');
    }
}
