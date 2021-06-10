<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin Admin',
                'nickname' => 'admin',
                'email' => 'admin@material.com',
                'email_verified_at' => now(),
                'password' => Hash::make('secret'),
                'isFullySet' => true,
                'premium' => true,
                'matches_created' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Cris Savino',
                'nickname' => 'crissavino',
                'email' => 'cris@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123123'),
                'isFullySet' => true,
                'premium' => true,
                'matches_created' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
