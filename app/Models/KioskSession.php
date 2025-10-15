<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KioskSession extends Model
{
    protected $fillable = [
        'session_id',
        'phone_number',
        'original_image_path',
        'processed_image_path',
        'status',
        'gemini_response',
        'completed_at'
    ];

    protected $casts = [
        'gemini_response' => 'array',
        'completed_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->session_id)) {
                $model->session_id = Str::uuid();
            }
        });
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }
}
