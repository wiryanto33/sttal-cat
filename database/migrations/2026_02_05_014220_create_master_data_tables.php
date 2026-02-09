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
        // 1. Periode Ujian (Manajemen Bank Soal per Tahun)
        Schema::create('exam_periods', function (Blueprint $table) {
            $table->id();
            $table->string('year', 4); // Contoh: "2026"
            $table->string('name'); // Contoh: "Seleksi Masuk STTAL 2026 Gelombang 1"
            $table->boolean('is_active')->default(false); // Hanya 1 record yang true
            $table->timestamps();
        });

        // 2. Strata (Jenjang Pendidikan)
        Schema::create('stratas', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // S1, S2, D3
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 3. Program Studi
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('strata_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: Teknik Hidrografi
            $table->string('code')->nullable(); // Kode Prodi
            $table->timestamps();
        });

        //4. exam categories
        Schema::create('exam_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Matematika", "Fisika Terapan", "Pengetahuan Umum"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_tables');
    }
};
