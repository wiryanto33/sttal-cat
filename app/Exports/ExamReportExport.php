<?php

namespace App\Exports;

use App\Models\ExamSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExamReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $results;

    public function collection()
    {
        // 1. Tambahkan eager loading 'examPacket' agar tidak berat saat proses
        $this->results = ExamSession::with(['candidate.user', 'candidate.strata', 'candidate.prodi1', 'examPacket'])
            ->where('status', 3)
            ->where('is_disqualified', false)
            ->get()
            ->sortBy([
                ['candidate.strata.name', 'asc'],
                ['candidate.prodi1.name', 'asc'],
                ['total_score', 'desc']
            ]);

        return $this->results;
    }

    public function headings(): array
    {
        // 2. Tambahkan kolom "PAKET UJIAN" di posisi yang sesuai (contoh setelah Prodi)
        return [
            "STRATA",
            "PRODI PILIHAN 1",
            "PAKET UJIAN",
            "NAMA PESERTA",
            "PANGKAT",
            "KORPS",
            "NRP",
            "SATUAN",
            "NILAI PG",
            "NILAI ESSAY",
            "TOTAL SKOR"
        ];
    }

    public function map($row): array
    {
        // 3. Masukkan data Paket Ujian ke dalam array mapping
        return [
            $row->candidate->strata->name ?? '-',
            $row->candidate->prodi1->name ?? '-',
            $row->examPacket->title ?? '-', // Mengambil judul paket ujian
            $row->candidate->user->name ?? '-',
            $row->candidate->pangkat ?? '-',
            $row->candidate->korps ?? '-',
            $row->candidate->nrp ?? '-',
            $row->candidate->satuan ?? '-',
            $row->score_tpa_aggregate,
            $row->score_essay_aggregate,
            $row->total_score,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 4. Update range style karena kolom bertambah menjadi A sampai K (11 kolom)
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $data = $this->results->values();
        $grouped = $this->results->groupBy('candidate.prodi_1_id');

        foreach ($grouped as $prodiId => $sessions) {
            $maxScore = $sessions->max('total_score');
            $minScore = $sessions->min('total_score');

            foreach ($data as $index => $row) {
                if ($row->candidate->prodi_1_id == $prodiId) {
                    $rowNumber = $index + 2;

                    // Highlight Hijau untuk Nilai Tertinggi
                    if ($row->total_score == $maxScore && $sessions->count() > 1) {
                        $sheet->getStyle("A{$rowNumber}:K{$rowNumber}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C6EFCE');
                    }
                    // Highlight Merah untuk Nilai Terendah
                    elseif ($row->total_score == $minScore && $sessions->count() > 1) {
                        $sheet->getStyle("A{$rowNumber}:K{$rowNumber}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7CE');
                    }
                }
            }
        }

        // Otomatis atur lebar kolom dari A sampai K
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
