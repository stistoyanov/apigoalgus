<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadedFile extends Model
{
    public const GB = 1073741824; // 1024 ** 3

    public const TOTAL_LIMIT_BYTES = 100 * self::GB; // 100 GB

    /**
     * Effective per-file upload limit in bytes.
     *
     * Picks the smaller of PHP's upload_max_filesize and post_max_size
     * (since post must fit both the file and the form overhead).
     */
    public static function perFileLimitBytes(): int
    {
        $upload = self::iniToBytes((string) ini_get('upload_max_filesize'));
        $post = self::iniToBytes((string) ini_get('post_max_size'));

        $candidates = array_filter([$upload, $post], fn ($v) => $v > 0);

        return $candidates === [] ? 0 : min($candidates);
    }

    private static function iniToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $value,
        };
    }

    protected $fillable = [
        'user_id',
        'original_name',
        'stored_name',
        'mime_type',
        'size_bytes',
        'share_token',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'download_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relativePath(): string
    {
        return 'files/'.$this->user_id.'/'.$this->stored_name;
    }

    public function isShared(): bool
    {
        return ! empty($this->share_token);
    }

    public static function totalUsedBytes(): int
    {
        return (int) static::query()->sum('size_bytes');
    }

    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) min(floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);

        $decimals = $power === 0 ? 0 : $precision;

        return number_format($value, $decimals).' '.$units[$power];
    }
}
