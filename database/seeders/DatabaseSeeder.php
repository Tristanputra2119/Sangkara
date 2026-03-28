<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Ensure permissions and super admin role are always ready after fresh seeding.
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'sangkara',
            '--no-interaction' => true,
        ]);

        Artisan::call('shield:super-admin', [
            '--user' => $testUser->id,
            '--panel' => 'sangkara',
            '--no-interaction' => true,
        ]);

        Artisan::call('permission:cache-reset');

        // Call data seeders
        $this->call([
            DummyDataSeeder::class,
            ProposalMaret2026Seeder::class,
        ]);
    }
}
