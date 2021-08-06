<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('types')->insert(
            [
                [
                    'name_key' => 'general.types.f5',
                    'sport_id' => 1,
                ],
                [
                    'name_key' => 'general.types.f7',
                    'sport_id' => 1,
                ],
                [
                    'name_key' => 'general.types.f9',
                    'sport_id' => 1,
                ],
                [
                    'name_key' => 'general.types.f11',
                    'sport_id' => 1,
                ],
            ]
        );
    }
}
