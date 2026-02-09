<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $guarded = ['id'];

    // Pastikan kolom ini masuk fillable agar bisa diupdate
    protected $fillable = [
        'candidate_id',
        'exam_packet_id',
        'status',
        'start_time',
        'end_time',
        'total_score',
        'score_tpa_aggregate',   // Nilai Otomatis (Multiple Choice)
        'score_essay_aggregate', // Nilai Manual (Essay)
        'tpa_score_details'      // JSON Rincian
    ];

    protected $casts = [
        'tpa_score_details' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // DEFINISI STATUS AGAR KODE LEBIH MUDAH DIBACA
    const STATUS_BELUM_MULAI = 0;
    const STATUS_ONGOING = 1;
    const STATUS_WAITING_CORRECTION = 2; // Menunggu Koreksi Dosen/Admin
    const STATUS_FINISHED = 3;           // Nilai Final Keluar

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function examPacket()
    {
        return $this->belongsTo(ExamPacket::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    // Warna Badge untuk Filament Resource
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_BELUM_MULAI => 'gray',
            self::STATUS_ONGOING => 'info',      // Biru
            self::STATUS_WAITING_CORRECTION => 'warning', // Oranye (Perlu Tindakan)
            self::STATUS_FINISHED => 'success',  // Hijau
            default => 'gray',
        };
    }

    // Label Status agar Manusia Mengerti
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_BELUM_MULAI => 'Belum Mulai',
            self::STATUS_ONGOING => 'Sedang Mengerjakan',
            self::STATUS_WAITING_CORRECTION => 'Menunggu Koreksi',
            self::STATUS_FINISHED => 'Selesai (Nilai Final)',
            default => 'Unknown',
        };
    }
}
