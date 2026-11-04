<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'full_name',
        'position',
        'branch',
        'activity_type',
        'event_date',
        'meeting_leader',
        'material_summary',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public static function branchOptions(): array
    {
        return [
            'Semarang 1' => 'Semarang 1',
            'Semarang 2' => 'Semarang 2',
            'Bandung' => 'Bandung',
            'other' => 'Yang lain:',
        ];
    }

    public static function activityTypeOptions(): array
    {
        return [
            'Rapat Manajemen' => 'Rapat Manajemen',
            'Rapat Teknis' => 'Rapat Teknis',
            'Rapat Keuangan' => 'Rapat Keuangan',
            'Rapat Pusat PT Purifikasi Andalan Diponegoro (SMG1, SMG2, Bandung)' => 'Rapat Pusat PT Purifikasi Andalan Diponegoro (SMG1, SMG2, Bandung)',
            'Rapat K3LH' => 'Rapat K3LH',
            'other' => 'Yang lain:',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function nextCode(): string
    {
        $prefix = 'HDR.'.date('Y.m.');
        $latest = static::query()
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('code');

        $sequence = 1;
        if ($latest && preg_match('/(\d+)$/', $latest, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
