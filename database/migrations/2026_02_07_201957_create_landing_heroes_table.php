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
        Schema::create('landing_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul Besar
            $table->text('caption')->nullable(); // Sub Judul (Opsional)
            $table->string('image_path'); // Gambar Background
            $table->boolean('is_active')->default(true); // Untuk memilih banner mana yang aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_heroes');
    }
};
