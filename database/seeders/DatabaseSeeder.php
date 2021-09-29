<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(3)->create();
        User::create([
            "name" => "testuser",
            "email" => "user@tes.com",
            "password" => Hash::make("password"),
            "phone_number" => "672374414"
        ]);
    }
}