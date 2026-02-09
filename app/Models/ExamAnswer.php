<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    //

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = ['is_correct' => 'boolean'];

    public function session()
    {
        return $this->belongsTo(ExamSession::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
