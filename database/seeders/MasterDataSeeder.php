<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Exam Periods
        DB::table('exam_periods')->insert([
            ['year' => '2026', 'name' => 'Seleksi Masuk STTAL 2026 Gelombang 1', 'is_active' => true],
        ]);

        // 2. Stratas
        $s2 = DB::table('stratas')->insertGetId(['name' => 'S2', 'description' => 'Pascasarjana']);
        $s1 = DB::table('stratas')->insertGetId(['name' => 'S1', 'description' => 'Sarjana']);
        $d3 = DB::table('stratas')->insertGetId(['name' => 'D3', 'description' => 'Diploma']);

        // 3. Prodis
        DB::table('prodis')->insert([
            ['strata_id' => $s2, 'name' => 'Asanalisis Sistem Riset dan Operasi', 'code' => 'ASRO'],
            ['strata_id' => $s2, 'name' => 'HidroOseanografi', 'code' => 'S2-HO'],
            ['strata_id' => $s1, 'name' => 'Teknik Hidrografi', 'code' => 'S1-HO'],
            ['strata_id' => $s1, 'name' => 'Teknik Elektro', 'code' => 'S1-TE'],
            ['strata_id' => $s1, 'name' => 'Teknik Mesin', 'code' => 'S1-TM'],
            ['strata_id' => $s1, 'name' => 'Teknik Manajemen Industri', 'code' => 'S1-TMI'],
            ['strata_id' => $d3, 'name' => 'Teknik Elektronika', 'code' => 'D3-TE'],
            ['strata_id' => $d3, 'name' => 'Teknik Informatika', 'code' => 'D3-TI'],
            ['strata_id' => $d3, 'name' => 'Teknik Mesin', 'code' => 'D3-TM'],
            ['strata_id' => $d3, 'name' => 'Teknik Hidrografi', 'code' => 'D3-HO'],
        ]);

        // 4. Exam Categories
        DB::table('exam_categories')->insert([
            ['name' => 'Matematika', 'is_active' => true],
            ['name' => 'Fisika Terapan', 'is_active' => true],
            ['name' => 'Bahasa Inggris', 'is_active' => true],
            ['name' => 'TPA', 'is_active' => true],
            ['name' => 'Pengetahuan Prodi', 'is_active' => true],
        ]);
    }
}
