<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'name',
        'leave_type',
        'days_count',
        'description',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'days_count' => 'integer',
            'attachment_size' => 'integer',
        ];
    }

    public static function leaveTypeOptions(): array
    {
        return [
            'Cuti' => 'Cuti',
            'Tahunan' => 'Tahunan',
            'Izin sakit' => 'Izin sakit',
            'Izin keperluan pribadi' => 'Izin keperluan pribadi',
            'Izin pelatihan / sertifikasi' => 'Izin pelatihan / sertifikasi',
            'WFA' => 'WFA',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path ? asset('storage/'.$this->attachment_path) : null;
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->attachment_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function fileExtension(): string
    {
        return strtolower(pathinfo($this->attachment_name, PATHINFO_EXTENSION));
    }

    public function isPdf(): bool
    {
        return $this->fileExtension() === 'pdf';
    }

    public function isImage(): bool
    {
        return in_array($this->fileExtension(), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }

    public static function nextCode(): string
    {
        $prefix = 'IZN.'.now()->format('Y.m');
        $last = self::query()->where('code', 'like', $prefix.'.%')->orderByDesc('id')->value('code');
        $sequence = $last ? ((int) Str::afterLast($last, '.')) + 1 : 1;

        return sprintf('%s.%04d', $prefix, $sequence);
    }
}
