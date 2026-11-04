<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssetProcurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'pic_name',
        'realization_date',
        'item_name',
        'brand_type',
        'quantity_size',
        'store_name',
        'proof_file_path',
        'proof_file_name',
        'proof_file_type',
        'proof_file_size',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'realization_date' => 'date',
            'proof_file_size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proofFileUrl(): ?string
    {
        return $this->proof_file_path ? asset('storage/'.$this->proof_file_path) : null;
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->proof_file_size;
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
        return strtolower(pathinfo($this->proof_file_name, PATHINFO_EXTENSION));
    }

    public function isPdf(): bool
    {
        return $this->fileExtension() === 'pdf';
    }

    public function isSpreadsheet(): bool
    {
        return in_array($this->fileExtension(), ['xlsx', 'xls', 'csv', 'ods']);
    }

    public function isDocument(): bool
    {
        return in_array($this->fileExtension(), ['doc', 'docx', 'odt', 'rtf', 'txt']);
    }

    public static function nextCode(): string
    {
        $prefix = 'RLS.'.now()->format('Y.m');
        $last = self::query()->where('code', 'like', $prefix.'.%')->orderByDesc('id')->value('code');
        $sequence = $last ? ((int) Str::afterLast($last, '.')) + 1 : 1;

        return sprintf('%s.%04d', $prefix, $sequence);
    }
}
