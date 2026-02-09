<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    //

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function period()
    {
        return $this->belongsTo(ExamPeriod::class, 'exam_period_id');
    }
    public function strata()
    {
        return $this->belongsTo(Strata::class);
    }
    public function prodi1()
    {
        return $this->belongsTo(Prodi::class, 'prodi_1_id');
    }
    public function prodi2()
    {
        return $this->belongsTo(Prodi::class, 'prodi_2_id');
    }

    // Relasi ke Sesi Ujian
    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}
