<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Créer le compte admin par défaut
        $this->call([
            AdminSeeder::class,
            VehicleSeeder::class,
        ]);

        // 2. Créer un utilisateur de test (client)
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => 'client',
        ]);

        // 3. Créer 10 utilisateurs clients aléatoires pour les tests
        User::factory(10)->create();

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📧 Admin: admin@machina.fr / Admin@Machina2025!');
        $this->command->info('📧 Client test: test@example.com / password');
    }
}
