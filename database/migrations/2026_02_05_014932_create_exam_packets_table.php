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
        Schema::create('exam_packets', function (Blueprint $table) {
            $table->id();
            // Relasi ke Master Data
            $table->foreignId('exam_period_id')->constrained()->cascadeOnDelete(); // Tahun (2025)
            $table->foreignId('exam_category_id')->constrained()->cascadeOnDelete(); // Matpel (Matematika)
            $table->foreignId('strata_id')->constrained()->cascadeOnDelete(); // Level (D3)

            // Setting Ujian
            $table->string('title'); // Nama Paket (misal: "Matematika D3 2025")
            $table->integer('duration_minutes')->default(90); // Batas Waktu (Menit)
            $table->text('description')->nullable();

            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete();

            $table->boolean('is_active')->default(false);

            // Waktu Mulai Ujian (Kapan tombol bisa diklik)
            $table->dateTime('start_time')->nullable();

            // Waktu Selesai Ujian (Kapan link kedaluwarsa - Opsional)
            $table->dateTime('end_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_packets');
    }
};
