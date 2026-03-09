<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 users
        // $users = User::factory(3)->create();

        /*
        User::create([
            'name' => 'Test User',
            'email' => 'ade@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        */
        

        // Create posts with PDFs
        Post::factory(50)->create();

        // Import posts to Scout search index
        $this->command->info('Importing posts to search index...');
        Artisan::call('scout:import', ['model' => 'App\\Models\\Post']);

        /*
        
        //open this
        $this->call([
            //add seeder
            UserRoleMaker::class
        ]);

        
        // Create profiles for the existing users (one-to-one)
        $users->each(function ($user) {
            Profile::factory()->create(['user_id' => $user->id]);
        });
        */

        
    }
}
