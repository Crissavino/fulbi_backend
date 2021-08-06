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
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('secret'),
                'is_fully_set' => true,
                'premium' => true,
                'matches_created' => 0,
                'genre_id' => 1,
                'profile_image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Cris Savino',
                'nickname' => 'crissavino',
                'email' => 'cris@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123123'),
                'is_fully_set' => true,
                'premium' => true,
                'matches_created' => 0,
                'genre_id' => 1,
                'profile_image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        DB::table('locations')->insert([
            [
                'lat' => 53.35,
                'lng' => -6.26,
                'country' => 'Irlanda',
                'country_code' => 'IE',
                'province' => 'Dublín',
                'province_code' => 'D',
                'city' => 'Dublín',
                'place_id' => 'ChIJL6wn6oAOZ0gRoHExl6nHAAo',
                'formatted_address' => 'Dublín, Irlanda',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lat' => 53.35,
                'lng' => -6.26,
                'country' => 'Irlanda',
                'country_code' => 'IE',
                'province' => 'Dublín',
                'province_code' => 'D',
                'city' => 'Dublín',
                'place_id' => 'ChIJL6wn6oAOZ0gRoHExl6nHAAo',
                'formatted_address' => 'Dublín, Irlanda',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        DB::table('players')->insert([
            [
                'user_id' => 1,
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 2,
                'location_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        DB::table('player_position')->insert([
            [
                'player_id' => 1,
                'position_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'player_id' => 1,
                'position_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'player_id' => 1,
                'position_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'player_id' => 1,
                'position_id' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'player_id' => 2,
                'position_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'player_id' => 2,
                'position_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'player_id' => 2,
                'position_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'player_id' => 2,
                'position_id' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
