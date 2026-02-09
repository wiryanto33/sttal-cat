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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('STTAL CBT');
            $table->string('logo_path')->nullable(); // Logo Header
            $table->string('favicon_path')->nullable(); // Ikon Browser
            $table->string('login_image_path')->nullable(); // Gambar samping Login
            $table->string('register_image_path')->nullable(); // Gambar samping Register
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
