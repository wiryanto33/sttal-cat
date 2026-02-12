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
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();

            // Monitoring Waktu
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            // Status: 0 (Merah), 1 (Kuning), 2 (Hijau)
            $table->integer('status')->default(0);

            $table->boolean('is_disqualified')->default(false);
            $table->text('disqualification_reason')->nullable();

            $table->decimal('total_score', 10, 2)->default(0)->nullable();

            // --- DETAIL NILAI UTAMA ---
            // Bahasa Inggris biasanya berdiri sendiri (bukan sub-TPA)
            $table->float('score_english')->default(0);

            // Nilai Tes Prodi
            $table->float('score_prodi_1')->default(0);
            $table->float('score_prodi_2')->nullable();

            // Tambahkan kolom exam_packet_id setelah candidate_id
            $table->foreignId('exam_packet_id')
                ->constrained('exam_packets')
                ->cascadeOnDelete();

            // --- NILAI TPA DINAMIS ---
            // 1. Kita simpan AGREGAT (Rata-rata/Total) TPA untuk keperluan Rumus Ranking
            $table->float('score_tpa_aggregate')->default(0);

            $table->decimal('score_essay_aggregate', 10, 2)->default(0)->nullable();

            // 2. Kita simpan RINCIAN TPA dalam bentuk JSON
            // Contoh isi: {"Matematika": 80, "Fisika": 70, "Wawasan Kebangsaan": 90}
            // Ini memungkinkan Admin menambah kategori apapun tanpa merusak database
            $table->json('tpa_score_details')->nullable();

            // --- NILAI AKHIR (RANKING) ---
            // Rumus: (score_tpa_aggregate * 30%) + (score_english * 20%) + (score_prodi * 50%)
            $table->float('final_score_1')->default(0);
            $table->float('final_score_2')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
