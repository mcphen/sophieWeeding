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
        Schema::table('training_sessions', function (Blueprint $table) {
            // Add document_path column for PDF flyers or documents
            $table->string('document_path')->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            // Drop document_path column
            $table->dropColumn('document_path');
        });
    }
};
