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
        Schema::create('landing_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul Kegiatan
            $table->string('image_path'); // Foto
            $table->date('event_date')->nullable(); // Tanggal Kegiatan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_galleries');
    }
};
