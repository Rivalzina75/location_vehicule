<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Supprimer l'ancienne colonne "name"
            $table->dropColumn('name');

            // 2. Ajouter les nouvelles colonnes pour Machina
            // (Nous les plaçons après la colonne 'id' pour la clarté)
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->date('date_of_birth')->after('email'); // Date de naissance
            $table->string('phone_number')->after('date_of_birth'); // Téléphone
            
            // Champs d'adresse
            $table->string('address_line1')->after('phone_number'); // ex: 10 rue de la Paix
            $table->string('postal_code', 10)->after('address_line1'); // ex: 75001
            $table->string('city')->after('postal_code'); // ex: Paris
        });
    }

    /**
     * Reverse the migrations.
     */
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Recréer la colonne "name" (nullable pour ne pas causer d'erreur)
            $table->string('name')->after('id')->nullable();

            // 2. Supprimer toutes les nouvelles colonnes
            $table->dropColumn([
                'first_name',
                'last_name',
                'date_of_birth',
                'phone_number',
                'address_line1',
                'postal_code',
                'city'
            ]);
        });
    }
};
