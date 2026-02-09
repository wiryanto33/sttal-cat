<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPeriod extends Model
{
    //

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'is_active' => 'boolean', // Tambahkan ini
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Scope untuk mengambil periode aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
