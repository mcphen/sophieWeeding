<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('email_list_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_list_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->unique(['email_list_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_list_entries');
        Schema::dropIfExists('email_lists');
    }
};
