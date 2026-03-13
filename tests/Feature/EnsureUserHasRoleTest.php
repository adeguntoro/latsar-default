<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnsureUserHasRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create required roles for tests
        Role::create(['name' => 'staff']);
    }

    /**
     * Test that a user without any role gets redirected to /no-role.
     */
    public function test_user_without_role_gets_403_error(): void
    {
        // Create a user without any role
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/my-profile');
        
        $response->assertRedirect('/no-role');
    }

    /**
     * Test that a user with a role can access the page.
     */
    public function test_user_with_role_can_access_page(): void
    {
        // Create a user with a role
        $user = User::factory()->create();
        $user->assignRole('staff');
        
        $response = $this->actingAs($user)
            ->get('/my-profile');
        
        $response->assertStatus(200);
    }

    /**
     * Test that the middleware works with role-prefixed routes.
     */
    public function test_user_without_role_cannot_access_role_routes(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/staff/dashboard');
        
        $response->assertRedirect('/no-role');
    }

    /**
     * Test that a user with correct role can access role-prefixed routes.
     */
    public function test_user_with_correct_role_can_access_role_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');
        
        $response = $this->actingAs($user)
            ->get('/staff/dashboard');
        
        $response->assertStatus(200);
    }
}