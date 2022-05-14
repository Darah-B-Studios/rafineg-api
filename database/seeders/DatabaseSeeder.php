<?php

namespace Database\Seeders;

use App\Models\Package;
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
            "code" => "P02r4",
            "firstname" => "bless",
            "lastname" => "darah",
            "email" => "user@test.com",
            "isAdmin" => true,
            "password" => Hash::make("password"),
            "phone_number" => "672374414"
        ]);

        Package::create([
            "id" => 1,
            "name" => "first package",
            "fee" => 5000,
            "low_investment_limit" => 5000,
            "high_investment_limit" => 100000,
            "image" => "image",
            "description" => "package description",
            "theme" => "tomato"
        ]);
    }
}