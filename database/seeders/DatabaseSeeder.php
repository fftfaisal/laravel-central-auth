<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Test User
        User::firstOrCreate(
            ['email' => 'user@central.test'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Seed Tenant 1 OAuth Client
        DB::table('oauth_clients')->updateOrInsert(
            ['id' => '11111111-1111-1111-1111-111111111111'],
            [
                'name' => 'Tenant App One',
                'secret' => 'app-one-secret',
                'provider' => 'users',
                'redirect_uris' => json_encode(['http://app-one.local.test/auth/callback']),
                'grant_types' => json_encode(['authorization_code', 'refresh_token']),
                'revoked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Seed Tenant 2 OAuth Client
        DB::table('oauth_clients')->updateOrInsert(
            ['id' => '22222222-2222-2222-2222-222222222222'],
            [
                'name' => 'Tenant App Two',
                'secret' => 'app-two-secret',
                'provider' => 'users',
                'redirect_uris' => json_encode(['http://app-two.local.test/auth/callback']),
                'grant_types' => json_encode(['authorization_code', 'refresh_token']),
                'revoked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
