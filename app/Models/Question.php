<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    // TAMBAHKAN INI (WAJIB)
    protected $casts = ['options' => 'array']; // Pastikan ini ada

    // Definisikan Tipe Soal
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_ESSAY = 'essay';

    // Question sekarang milik Packet
    public function examPacket()
    {
        return $this->belongsTo(ExamPacket::class);
    }

    public function relatedProdi()
    {
        return $this->belongsTo(Prodi::class, 'related_prodi_id');
    }
}
