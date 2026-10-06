<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperFile extends Model
{
    public const TYPE_FULL_PAPER = 'full_paper';
    public const TYPE_PRESENTATION = 'presentation';

    protected $fillable = [
        'paper_id',
        'type',
        'version',
        'original_name',
        'file_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'version' => 'integer',
        'size' => 'integer',
    ];

    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getReadableSizeAttribute(): string
    {
        $size = $this->size;
        if ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        }

        return number_format($size / 1024, 1) . ' KB';
    }
}
