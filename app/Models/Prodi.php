<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    //

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function strata()
    {
        return $this->belongsTo(Strata::class);
    }

    public function candidates()
    {
        // Karena di tabel candidates kolomnya adalah prodi_1_id atau prodi_2_id
        // Kita arahkan ke prodi_1_id sesuai permintaan Anda (Pilihan Pertama)
        return $this->hasMany(Candidate::class, 'prodi_1_id');
    }
}
