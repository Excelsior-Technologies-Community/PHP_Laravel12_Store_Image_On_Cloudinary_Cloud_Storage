<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudinaryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'public_id',
        'secure_url',
        'format',
        'resource_type',
        'file_size',
        'width',
        'height',
        'folder',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size ?? 0;

        if ($bytes === 0) {
            return '0 Bytes';
        }

        $units = [
            'Bytes',
            'KB',
            'MB',
            'GB',
        ];

        $power = floor(
            log($bytes, 1024)
        );

        $power = min(
            $power,
            count($units) - 1
        );

        return number_format(
            $bytes / pow(1024, $power),
            2
        ) . ' ' . $units[$power];
    }

    /**
     * Get image dimensions.
     */
    public function getDimensionsAttribute(): string
    {
        if (
            !$this->width ||
            !$this->height
        ) {
            return 'N/A';
        }

        return $this->width
            . ' × '
            . $this->height
            . ' px';
    }
}