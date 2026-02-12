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
        // Ambil data yang selesai, urutkan berdasarkan Strata, Prodi, lalu Nilai Tertinggi
        $this->results = ExamSession::with(['candidate.user', 'candidate.strata', 'candidate.prodi1'])
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
        return [
            "STRATA",
            "PRODI PILIHAN 1",
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
        return [
            $row->candidate->strata->name ?? '-',
            $row->candidate->prodi1->name ?? '-',
            $row->candidate->user->name ?? '-',
            $row->candidate->pangkat ?? '-', // Memperbaiki typo pangkkat
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
        // Tebalkan Header (A1 sampai J1 karena sekarang ada 10 kolom)
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $data = $this->results->values();
        $grouped = $this->results->groupBy('candidate.prodi_1_id');

        foreach ($grouped as $prodiId => $sessions) {
            $maxScore = $sessions->max('total_score');
            $minScore = $sessions->min('total_score');

            foreach ($data as $index => $row) {
                if ($row->candidate->prodi_1_id == $prodiId) {
                    $rowNumber = $index + 2;

                    // Highlight Hijau untuk Nilai Tertinggi di Prodi tersebut
                    if ($row->total_score == $maxScore && $sessions->count() > 1) {
                        $sheet->getStyle("A{$rowNumber}:J{$rowNumber}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C6EFCE');
                    }
                    // Highlight Merah untuk Nilai Terendah di Prodi tersebut
                    elseif ($row->total_score == $minScore && $sessions->count() > 1) {
                        $sheet->getStyle("A{$rowNumber}:J{$rowNumber}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7CE');
                    }
                }
            }
        }

        // Otomatis atur lebar kolom agar rapi
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
