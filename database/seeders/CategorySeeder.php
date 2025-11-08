<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $categories = [];

        for ($i = 0; $i < 3000; $i++) {
            $categories[] = [
                'name' => ucfirst($faker->words(2, true)) . " $i",
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($categories) >= 500) {
                DB::table('categories')->insert($categories);
                $categories = [];
            }
        }

        if (count($categories)) {
            DB::table('categories')->insert($categories);
        }
    }
}
