<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::create([
            'name' => 'Test User',
            'email' => 'ade@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        //User::factory()->create([
        //    'name' => 'Test User',
        //    'email' => 'test@example.com',
        // ]);
    }
}
