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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_period_id')->constrained(); // Mendaftar di tahun berapa

            // Data Identitas & Militer
            $table->string('nrp')->unique();
            $table->string('pangkat'); // Letda, Kapten, Serka, dll
            $table->string('korps'); // Pelaut, Teknik, dll
            $table->string('satuan'); // Satuan Asal
            $table->string('jabatan_terakhir')->nullable();

            // Data Administrasi Ujian
            $table->string('exam_number')->unique(); // No Ujian (Input Manual Peserta)
            $table->string('photo_path')->nullable(); // Foto Resmi

            // Pilihan Akademik
            $table->foreignId('strata_id')->constrained();
            $table->foreignId('prodi_1_id')->constrained('prodis'); // Pilihan Utama
            $table->foreignId('prodi_2_id')->nullable()->constrained('prodis'); // Pilihan Kedua (Opsional)

            // Status Validasi Admin
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('admin_note')->nullable(); // Alasan jika ditolak

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
