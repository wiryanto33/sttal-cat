<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamViolation extends Model
{
    protected $fillable = [
        'exam_session_id',
        'violation_type',
        'description',
        'detected_at'
    ];

    protected $casts = [
        'detected_at' => 'datetime'
    ];

    /**
     * Relasi ke ExamSession
     */
    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    /**
     * Tipe-tipe pelanggaran yang tersedia
     */
    const TYPE_TAB_SWITCH = 'tab_switch';
    const TYPE_COPY_ATTEMPT = 'copy_attempt';
    const TYPE_RIGHT_CLICK = 'right_click';
    const TYPE_DEV_TOOLS = 'dev_tools';
    const TYPE_FULLSCREEN_EXIT = 'fullscreen_exit';
    const TYPE_FOCUS_LOSS = 'focus_loss';
    const TYPE_PASTE_ATTEMPT = 'paste_attempt';
    const TYPE_PRINT_ATTEMPT = 'print_attempt';
    const TYPE_SCREENSHOT_ATTEMPT = 'screenshot_attempt';

    /**
     * Scope untuk filter berdasarkan tipe pelanggaran
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('violation_type', $type);
    }

    /**
     * Scope untuk pelanggaran dalam sesi tertentu
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('exam_session_id', $sessionId);
    }
}
