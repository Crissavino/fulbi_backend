<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('positions')->insert(
            [
                [
                    'name_key' => 'general.positions.gk',
                    'sport_id' => 1,
                ],
                [
                    'name_key' => 'general.positions.def',
                    'sport_id' => 1,
                ],
                [
                    'name_key' => 'general.positions.mid',
                    'sport_id' => 1,
                ],
                [
                    'name_key' => 'general.positions.for',
                    'sport_id' => 1,
                ]
            ]
        );
    }
}
