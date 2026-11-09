<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class ProjectContract extends Model
{
    use HasFactory;

    public const CATEGORY_AMDAL = 'amdal';
    public const CATEGORY_ENGINEERING = 'engineering';

    protected $fillable = [
        'user_id',
        'category',
        'spk_number',
        'spk_date',
        'project_name',
        'second_party_name',
        'job_description',
        'contract_value',
        'duration_months',
        'termin_1',
        'termin_2',
        'termin_3',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'contract_notes',
        'source_document_text',
        'source_document_path',
        'source_document_name',
    ];

    protected function casts(): array
    {
        return [
            'spk_date' => 'date',
            'contract_value' => 'float',
            'duration_months' => 'integer',
            'termin_1' => 'float',
            'termin_2' => 'float',
            'termin_3' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->category)) {
                $model->category = self::CATEGORY_AMDAL;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAmdal(): bool
    {
        return $this->category === self::CATEGORY_AMDAL;
    }

    public function isEngineering(): bool
    {
        return $this->category === self::CATEGORY_ENGINEERING;
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            self::CATEGORY_ENGINEERING => 'Engineering',
            default => 'AMDAL',
        };
    }

    public function scopeAmdal(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_AMDAL);
    }

    public function scopeEngineering(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_ENGINEERING);
    }

    public function sourceDocumentUrl(): ?string
    {
        return $this->source_document_path ? asset('storage/'.$this->source_document_path) : null;
    }

    public function isSourceTextUrl(): bool
    {
        if (empty($this->source_document_text)) {
            return false;
        }

        return str_starts_with($this->source_document_text, 'http://')
            || str_starts_with($this->source_document_text, 'https://');
    }
}
