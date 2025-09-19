<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Test cases untuk fitur Last Login
 */
class LastLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test successful login updates last_login timestamp
     */
    public function test_successful_login_updates_last_login()
    {
        // Create user without last_login
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'last_login' => null
        ]);

        // Attempt login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Assert successful response
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'last_login'
                        ],
                        'token',
                        'token_type'
                    ]
                ]);

        // Refresh user and check last_login is updated
        $user->refresh();
        $this->assertNotNull($user->last_login);
        $this->assertTrue($user->last_login->isToday());
        $this->assertTrue($user->last_login->diffInMinutes(now()) < 1);
    }

    /**
     * Test failed login does not update last_login
     */
    public function test_failed_login_does_not_update_last_login()
    {
        // Create user without last_login
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'last_login' => null
        ]);

        // Attempt login with wrong password
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        // Assert failed response
        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);

        // Refresh user and check last_login is still null
        $user->refresh();
        $this->assertNull($user->last_login);
    }

    /**
     * Test multiple logins update last_login each time
     */
    public function test_multiple_logins_update_last_login()
    {
        // Create user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'last_login' => null
        ]);

        // First login
        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ])->assertStatus(200);

        $user->refresh();
        $firstLogin = $user->last_login;
        $this->assertNotNull($firstLogin);

        // Wait a moment
        sleep(1);

        // Second login
        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ])->assertStatus(200);

        $user->refresh();
        $secondLogin = $user->last_login;
        $this->assertNotNull($secondLogin);
        $this->assertTrue($secondLogin->gt($firstLogin));
    }

    /**
     * Test login with invalid email format
     */
    public function test_login_with_invalid_email_format()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test login with missing credentials
     */
    public function test_login_with_missing_credentials()
    {
        // Missing email
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);

        // Missing password
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test user profile includes last_login information
     */
    public function test_user_profile_includes_last_login()
    {
        // Create user and login
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Login to set last_login
        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Get user profile
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/app/auth/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'last_login'
                    ]
                ]);

        $user->refresh();
        $this->assertNotNull($user->last_login);
    }

    /**
     * Test last_login timestamp format
     */
    public function test_last_login_timestamp_format()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        
        $responseData = $response->json();
        $lastLogin = $responseData['data']['user']['last_login'];
        
        // Check if last_login is in ISO 8601 format
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/',
            $lastLogin
        );
    }

    /**
     * Test concurrent logins from different sessions
     */
    public function test_concurrent_logins_update_last_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Simulate concurrent logins
        $responses = [];
        for ($i = 0; $i < 3; $i++) {
            $responses[] = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);
        }

        // All should be successful
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        // Check that last_login is updated
        $user->refresh();
        $this->assertNotNull($user->last_login);
        $this->assertTrue($user->last_login->isToday());
    }

    /**
     * Test login rate limiting (if implemented)
     * Note: Currently using throttle:60,1 which allows 60 requests per minute
     * This test is skipped until specific failed login rate limiting is implemented
     */
    public function test_login_rate_limiting()
    {
        $this->markTestSkipped('Rate limiting for failed login attempts not yet implemented');
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Make multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword'
            ]);
            
            if ($i < 5) {
                $response->assertStatus(401);
            } else {
                // After 5 failed attempts, should be rate limited
                $response->assertStatus(429);
            }
        }

        // Verify last_login is still null after failed attempts
        $user->refresh();
        $this->assertNull($user->last_login);
    }

    /**
     * Test database migration includes last_login field
     */
    public function test_users_table_has_last_login_field()
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasColumn('users', 'last_login')
        );
    }

    /**
     * Test User model casts last_login as datetime
     */
    public function test_user_model_casts_last_login_as_datetime()
    {
        $user = User::factory()->create();
        
        // Set last_login manually
        $user->update(['last_login' => now()]);
        $user->refresh();
        
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->last_login);
    }

    /**
     * Test logout does not affect last_login
     */
    public function test_logout_does_not_affect_last_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Login
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');

        $user->refresh();
        $lastLoginBeforeLogout = $user->last_login;
        $this->assertNotNull($lastLoginBeforeLogout);

        // Logout
        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/app/auth/logout');

        $logoutResponse->assertStatus(200);

        // Check that last_login is unchanged
        $user->refresh();
        $this->assertEquals(
            $lastLoginBeforeLogout->timestamp,
            $user->last_login->timestamp
        );
    }
}