<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCategory extends Model
{
    //

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'is_active' => 'boolean', // Tambahkan ini (sesuai migrasi sebelumnya)
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
