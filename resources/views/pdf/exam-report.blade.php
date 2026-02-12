<!DOCTYPE html>
<html>

<head>
    <title>Laporan Nilai Ujian</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .high {
            background-color: #c6efce;
            color: #006100;
            font-weight: bold;
        }

        .low {
            background-color: #ffc7ce;
            color: #9c0006;
            font-weight: bold;
        }

        .prodi-header {
            background-color: #e2e8f0;
            text-align: left;
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN HASIL UJIAN SELEKSI</h2>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    @foreach ($strataData as $strata)
        <h3 style="border-bottom: 2px solid #000;">STRATA: {{ $strata->name }}</h3>

        @foreach ($strata->prodis as $prodi)
            @php
                // Ambil semua sesi yang sudah selesai di prodi ini
                $sessions = $prodi->candidates->flatMap->examSessions
                    ->where('status', 3)
                    ->where('is_disqualified', false)
                    ->sortByDesc('total_score');

                $max = $sessions->max('total_score');
                $min = $sessions->min('total_score');
            @endphp

            @if ($sessions->count() > 0)
                <div class="prodi-header">Program Studi: {{ $prodi->name }}</div>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>NRP</th>
                            <th>Total Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            @php
                                // Tentukan kelas warna
                                $rowClass = '';
                                if ($sessions->count() > 1) {
                                    if ($session->total_score == $max) {
                                        $rowClass = 'high';
                                    } elseif ($session->total_score == $min) {
                                        $rowClass = 'low';
                                    }
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $session->candidate->user->name ?? 'N/A' }}</td>
                                <td>{{ $session->candidate->nrp ?? '-' }}</td>
                                <td>{{ number_format($session->total_score, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    @endforeach
</body>

</html>
