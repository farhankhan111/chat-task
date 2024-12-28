<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'user 1',
            'email' => 'user1@test.com',
            'password'=> bcrypt('12345'),
        ]);

        \App\Models\User::create([
            'name' => 'user 2',
            'email' => 'user2@test.com',
            'password'=> bcrypt('12345'),
        ]);
    }
}
