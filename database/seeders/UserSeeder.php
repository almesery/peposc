<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            "name" => "Admin",
            "email" => 'admin@admin.com',
            "password" => \Hash::make("admin"),
        ]);
        User::factory()
            ->count(10)
            ->hasLastlogins(2)
            ->create();
    }
}
