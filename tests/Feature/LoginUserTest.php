<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credential(): void
    {
        $user = User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'user@gmail.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/app');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credential(): void
    {
        $user = User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'user@gmail.com',
                'password' => 'salah-password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }
}