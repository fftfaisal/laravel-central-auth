<?php

use App\Models\User;
use Laravel\Passport\Passport;

test('guests cannot view oauth clients page', function () {
    $response = $this->get(route('clients.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view oauth clients page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('clients.index'));

    $response->assertOk();
});

test('authenticated users can create a new oauth client', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('clients.store'), [
        'name' => 'Test Tenant Application',
        'redirect' => 'http://test-tenant.local.test/auth/callback',
    ]);

    $response->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('oauth_clients', [
        'name' => 'Test Tenant Application',
        'revoked' => false,
    ]);
});

test('authenticated users can update an oauth client', function () {
    $user = User::factory()->create();

    $client = Passport::client()->forceCreate([
        'id' => '33333333-3333-3333-3333-333333333333',
        'name' => 'Original App Name',
        'secret' => 'original-secret',
        'provider' => 'users',
        'redirect_uris' => json_encode(['http://original.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'revoked' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($user)->put(route('clients.update', $client->id), [
        'name' => 'Updated App Name',
        'redirect' => 'http://updated.test/callback',
    ]);

    $response->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('oauth_clients', [
        'id' => $client->id,
        'name' => 'Updated App Name',
    ]);
});

test('authenticated users can revoke an oauth client', function () {
    $user = User::factory()->create();

    $client = Passport::client()->forceCreate([
        'id' => '44444444-4444-4444-4444-444444444444',
        'name' => 'App To Revoke',
        'secret' => 'revoke-secret',
        'provider' => 'users',
        'redirect_uris' => json_encode(['http://revoke.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'revoked' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($user)->delete(route('clients.destroy', $client->id));

    $response->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('oauth_clients', [
        'id' => $client->id,
        'revoked' => true,
    ]);
});
