<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter les migrations (Ajouter les colonnes).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Compteur d'erreurs de connexion. Initialisé à 0.
            $table->integer('login_attempts')->default(0)->after('password'); 
            // Date et heure jusqu'à laquelle le compte est bloqué.
            $table->timestamp('blocked_until')->nullable()->after('login_attempts'); 
        });
    }

    /**
     * Annuler les migrations (Supprimer les colonnes).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_attempts');
            $table->dropColumn('blocked_until');
        });
    }
};
