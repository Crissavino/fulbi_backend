<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('genres')->insert(
            [
                [
                    'name_key' => 'general.genres.male',
                ],
                [
                    'name_key' => 'general.genres.female',
                ],
                [
                    'name_key' => 'general.genres.mix',
                ]
            ]
        );
    }
}
