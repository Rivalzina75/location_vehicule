<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Crée un compte administrateur par défaut
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        if (User::where('email', 'admin@machina.fr')->exists()) {
            $this->command->info('Admin account already exists.');

            return;
        }

        // Créer le compte admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Machina',
            'email' => 'admin@machina.fr',
            'email_verified_at' => now(),
            'password' => Hash::make('Admin@Machina2025!'), // Mot de passe robuste par défaut
            'date_of_birth' => '01/01/1990',
            'phone_number' => '01 23 45 67 89',
            'address_line1' => '1 Rue de la Location',
            'postal_code' => '75001',
            'city' => 'Paris',
            'role' => 'admin',
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Email: admin@machina.fr');
        $this->command->info('Password: Admin@Machina2025!');
        $this->command->warn('⚠️  IMPORTANT: Changez ce mot de passe en production!');
    }
}
