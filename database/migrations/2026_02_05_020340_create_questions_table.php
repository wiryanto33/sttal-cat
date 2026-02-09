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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // Tambahkan relasi ke Paket Soal
            $table->foreignId('exam_packet_id')->constrained()->cascadeOnDelete();

            // Jika TPA/ENGLISH -> Wajib isi strata_level (S1/S2/D3)
            // Jika PRODI -> Wajib isi related_prodi_id
            $table->string('strata_level')->nullable();
            $table->foreignId('related_prodi_id')->nullable()->constrained('prodis');

            $table->string('type')->default('multiple_choice');

            // Konten Soal
            $table->longText('content'); // Pertanyaan (HTML Support)
            $table->string('image_path')->nullable();

            // Pilihan & Kunci
            $table->json('options')->nullable();
            $table->longText('correct_answer')->nullable(); // "A", "B", "C", dst.

            $table->integer('weight')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
