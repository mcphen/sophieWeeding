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
        Schema::table('training_registrations', function (Blueprint $table) {
            // Add client_id foreign key
            $table->foreignId('client_id')->nullable()->after('training_session_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_registrations', function (Blueprint $table) {
            // Drop foreign key constraint and column
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};
