<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Gate::before(function ($user, $ability) {
        return true;
    });
});

it('can render user creation page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(UserResource::getUrl('create'))->assertSuccessful();
});

it('can create a user and redirect to index page', function () {
    $admin = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(CreateUser::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect(UserResource::getUrl('index'));

    $this->assertDatabaseHas('users', [
        'email' => 'testuser@example.com',
    ]);
});
