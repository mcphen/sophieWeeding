<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vider les inscriptions et sessions existantes avant restructuration
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('training_registrations')->truncate();
        DB::table('training_sessions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::table('training_sessions', function (Blueprint $table) {
            // Supprimer les colonnes qui passent dans masterclasses
            $table->dropUnique(['slug']);
            $table->dropColumn(['title', 'description', 'content', 'image_path', 'document_path', 'slug']);
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            // Ajouter la clé étrangère masterclass
            $table->foreignId('masterclass_id')->after('id')->constrained()->onDelete('cascade');

            // Restructurer les dates : garder start_date comme date+heure début, end_date comme heure fin
            // Renommer location en adresse et ajouter les nouveaux champs
            $table->renameColumn('location', 'adresse');

            // Nouveaux champs de localisation
            $table->enum('location_type', ['presentiel', 'online', 'both'])->default('presentiel')->after('adresse');
            $table->string('google_maps_url')->nullable()->after('location_type');
            $table->string('online_link')->nullable()->after('google_maps_url');

            // Prix nullable (null = gratuit)
            $table->decimal('price', 10, 2)->nullable()->change();

            // Places limite nullable (null = illimité)
            $table->integer('max_participants')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropForeign(['masterclass_id']);
            $table->dropColumn(['masterclass_id', 'location_type', 'google_maps_url', 'online_link']);
            $table->renameColumn('adresse', 'location');
            $table->string('title')->after('id');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('image_path')->nullable();
            $table->string('document_path')->nullable();
            $table->string('slug')->unique();
        });
    }
};
