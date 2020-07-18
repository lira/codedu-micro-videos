<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CategoriesSeeder::class);
        $this->call(GenresSeeder::class);
        $this->call(CastMemberSeeder::class);
        $this->call(VideoSeeder::class);
    }
}
