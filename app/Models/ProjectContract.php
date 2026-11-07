<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
