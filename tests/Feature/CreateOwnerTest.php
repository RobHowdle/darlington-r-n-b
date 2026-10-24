<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateOwnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_verified_account_with_hashed_password(): void
    {
        $this->artisan('howdle:create-owner')
            ->expectsQuestion('Name', 'Site Owner')
            ->expectsQuestion('Email', 'Owner@example.com')
            ->expectsQuestion('Password', 'a-long-test-password')
            ->expectsOutput('Owner account created. Sign in at /admin.')
            ->doesntExpectOutput('a-long-test-password')->assertSuccessful();
        $user = User::sole();
        $this->assertSame('Site Owner', $user->name);
        $this->assertSame('owner@example.com', $user->email);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertTrue(Hash::check('a-long-test-password', $user->password));
        $this->assertNotSame('a-long-test-password', $user->password);
    }

    public function test_existing_email_is_rejected_before_password_prompt(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com']);
        $this->artisan('howdle:create-owner')
            ->expectsQuestion('Name', 'Another Owner')
            ->expectsQuestion('Email', 'OWNER@example.com')
            ->expectsOutput('An account with this email already exists.')->assertFailed();
        $this->assertDatabaseCount('users', 1);
        $this->assertSame($user->password, $user->fresh()->password);
    }

    public function test_short_password_is_rejected(): void
    {
        $this->artisan('howdle:create-owner')
            ->expectsQuestion('Name', 'Owner')
            ->expectsQuestion('Email', 'owner@example.com')
            ->expectsQuestion('Password', 'short')
            ->expectsOutput('Password must contain at least 12 characters.')->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->artisan('howdle:create-owner')
            ->expectsQuestion('Name', 'Owner')->expectsQuestion('Email', 'invalid')->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }
}
