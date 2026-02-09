<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data dasar
        $stratas = DB::table('stratas')->get();
        $activePeriod = DB::table('exam_periods')->where('is_active', true)->first();

        if (!$activePeriod) {
            $this->command->error("Tidak ada periode ujian yang aktif!");
            return;
        }

        $periodId = $activePeriod->id;

        // 1. SEEDING SOAL UMUM (MTK, Fisika, B.Inggris, TPA)
        // Filter kategori agar tidak menyertakan kategori "Pengetahuan Prodi" di loop ini
        $generalCategories = DB::table('exam_categories')
            ->where('name', '!=', 'Pengetahuan Prodi')
            ->get();

        foreach ($generalCategories as $category) {
            foreach ($stratas as $strata) {
                $packetId = DB::table('exam_packets')->insertGetId([
                    'exam_period_id' => $periodId,
                    'exam_category_id' => $category->id,
                    'strata_id' => $strata->id,
                    'prodi_id' => null, // NULL karena soal umum
                    'title' => "Paket {$category->name} - {$strata->name} 2026",
                    'duration_minutes' => 90,
                    'is_active' => true,
                    'start_time' => now(),
                    'end_time' => now()->addDays(7),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->generateQuestions($packetId, $strata->name, $category->name);
            }
        }

        // 2. SEEDING SOAL KHUSUS PRODI (Hanya untuk Strata D3 sebagai contoh)
        $categoryProdi = DB::table('exam_categories')->where('name', 'Pengetahuan Prodi')->first();
        $prodis = DB::table('prodis')->get();

        if ($categoryProdi) {
            foreach ($prodis as $prodi) {
                $packetId = DB::table('exam_packets')->insertGetId([
                    'exam_period_id' => $periodId,
                    'exam_category_id' => $categoryProdi->id,
                    'strata_id' => $prodi->strata_id,
                    'prodi_id' => $prodi->id, // Terkunci ke prodi tertentu
                    'title' => "Ujian Kejuruan - {$prodi->name}",
                    'duration_minutes' => 60,
                    'is_active' => true,
                    'start_time' => now(),
                    'end_time' => now()->addDays(7),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->generateQuestions($packetId, "Khusus", $prodi->name);
            }
        }
    }

    /**
     * Helper untuk membuat 15 PG dan 5 Essay
     */
    private function generateQuestions($packetId, $strataName, $subjectName)
    {
        // 15 Soal Pilihan Ganda
        for ($i = 1; $i <= 15; $i++) {
            DB::table('questions')->insert([
                'exam_packet_id' => $packetId,
                'strata_level' => $strataName,
                'type' => 'multiple_choice',
                'content' => "<p>Pertanyaan PG nomor {$i} untuk {$subjectName}?</p>",
                'options' => json_encode([
                    'A' => "Jawaban Benar",
                    'B' => "Salah",
                    'C' => "Salah",
                    'D' => "Salah"
                ]),
                'correct_answer' => 'A',
                'weight' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5 Soal Essay
        for ($j = 16; $j <= 20; $j++) {
            DB::table('questions')->insert([
                'exam_packet_id' => $packetId,
                'strata_level' => $strataName,
                'type' => 'essay',
                'content' => "<p>Jelaskan analisis Anda mengenai topik {$subjectName} ke-{$j}!</p>",
                'options' => null,
                'correct_answer' => 'Pedoman penilaian essay.',
                'weight' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
