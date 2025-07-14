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
        Schema::create('article_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('actualites')->onDelete('cascade');
            $table->enum('type', ['text', 'timeline', 'gallery']);
            $table->json('content');
            $table->integer('position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_blocks');
    }
};
