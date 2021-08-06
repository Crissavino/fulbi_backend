<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert(
            [
                [
                    'name' => 'Euro',
                    'code' => 'eur',
                    'symbol' => '€'
                ],
                [
                    'name' => 'Pound sterling',
                    'code' => 'gbp',
                    'symbol' => '£'
                ],
            ]
        );
    }
}
