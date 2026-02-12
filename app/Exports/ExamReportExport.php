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
        // Ambil data yang selesai, dikelompokkan berdasarkan Strata dan Prodi
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
        return ["STRATA", "PRODI PILIHAN 1", "NAMA PESERTA", "NRP", "NILAI PG", "NILAI ESSAY", "TOTAL SKOR"];
    }

    public function map($row): array
    {
        return [
            $row->candidate->strata->name ?? '-',
            $row->candidate->prodi1->name ?? '-',
            $row->candidate->user->name ?? '-',
            $row->candidate->pangkkat ?? '-',
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
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $data = $this->results->values();
        $grouped = $this->results->groupBy('candidate.prodi_1_id');

        foreach ($grouped as $prodiId => $sessions) {
            $maxScore = $sessions->max('total_score');
            $minScore = $sessions->min('total_score');

            foreach ($data as $index => $row) {
                if ($row->candidate->prodi_1_id == $prodiId) {
                    $rowNumber = $index + 2; // +2 karena header dan base-1

                    if ($row->total_score == $maxScore && $sessions->count() > 1) {
                        $sheet->getStyle("A{$rowNumber}:G{$rowNumber}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C6EFCE'); // Hijau
                    } elseif ($row->total_score == $minScore && $sessions->count() > 1) {
                        $sheet->getStyle("A{$rowNumber}:G{$rowNumber}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7CE'); // Merah
                    }
                }
            }
        }
    }
}
