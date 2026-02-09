<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingGallery extends Model
{
    //

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
    ];
}
