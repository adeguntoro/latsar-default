<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Profile;

class CreateMissingProfiles extends Command
{
    protected $signature = 'profiles:create-missing';
    protected $description = 'Create profiles for users that do not have one';

    public function handle()
    {
        $usersWithoutProfiles = User::doesntHave('profile')->get();
        $count = 0;
        
        foreach ($usersWithoutProfiles as $user) {
            Profile::factory()->create(['user_id' => $user->id]);
            $count++;
        }
        
        $this->info("Created profiles for {$count} users.");
        return 0;
    }
}