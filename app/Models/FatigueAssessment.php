<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FatigueAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'name',
        'assessment_date',
        'recent_illness',
        'medication_drowsy',
        'personal_distraction',
        'sleep_hours',
        'fit_to_drive',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFullyFit(): bool
    {
        return $this->recent_illness === 'Tidak'
            && $this->medication_drowsy === 'Tidak'
            && $this->personal_distraction === 'Tidak'
            && $this->sleep_hours === 'lebih dari 6 jam'
            && $this->fit_to_drive === 'Ya';
    }

    public function evaluationLabel(): string
    {
        return $this->isFullyFit() ? 'Fit / Kondisi Prima' : 'Perlu Perhatian / Berisiko';
    }

    public static function nextCode(): string
    {
        $prefix = 'FTG.'.now()->format('Y.m');
        $last = self::query()->where('code', 'like', $prefix.'.%')->orderByDesc('id')->value('code');
        $sequence = $last ? ((int) Str::afterLast($last, '.')) + 1 : 1;

        return sprintf('%s.%04d', $prefix, $sequence);
    }
}
