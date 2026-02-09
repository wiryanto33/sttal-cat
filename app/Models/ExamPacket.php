<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamPacket extends Model
{
    // Agar semua kolom bisa diisi
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relasi ke Tahun / Periode
    public function examPeriod(): BelongsTo
    {
        return $this->belongsTo(ExamPeriod::class);
    }

    // Relasi ke Strata (D3/S1)
    public function strata(): BelongsTo
    {
        return $this->belongsTo(Strata::class);
    }

    // Relasi ke Mata Uji (Matematika/Fisika)
    public function examCategory(): BelongsTo
    {
        return $this->belongsTo(ExamCategory::class);
    }

    // Relasi ke Soal-soal (Satu paket punya banyak soal)
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    // app/Models/ExamPacket.php

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}
