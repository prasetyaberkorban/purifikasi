<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelaksanaanProject extends Model
{
    use HasFactory;

    protected $table = 'pelaksanaan_projects';

    protected $fillable = [
        'kode_project',
        'nama_project',
        'pengelola',
        'status_pekerjaan',
        'progress_pekerjaan',
        'pic',
        'kontrak_awal',
        'kontrak_akhir',
        'remaining_days',
        'user_id',
    ];

    protected $casts = [
        'progress_pekerjaan' => 'float',
        'kontrak_awal' => 'date',
        'kontrak_akhir' => 'date',
        'remaining_days' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung sisa hari dinamis jika kontrak_akhir ada
     */
    public function getCalculatedRemainingDaysAttribute(): ?int
    {
        if (!$this->kontrak_akhir) {
            return $this->remaining_days;
        }

        $today = Carbon::today();
        return (int) $today->diffInDays($this->kontrak_akhir, false);
    }

    /**
     * Badge status warna sisa hari:
     * - Merah jika negatif (terlambat)
     * - Oranye jika 0-14 hari (kritis/mendesak)
     * - Hijau jika > 14 hari (aman)
     */
    public function getRemainingDaysBadgeClassAttribute(): string
    {
        $days = $this->calculated_remaining_days;

        if ($days === null) {
            return 'bg-slate-100 text-slate-700 border-slate-200';
        }

        if ($days < 0) {
            return 'bg-rose-50 text-rose-700 border-rose-200';
        }

        if ($days <= 14) {
            return 'bg-amber-50 text-amber-700 border-amber-200';
        }

        return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('nama_project', 'like', "%{$keyword}%")
                ->orWhere('kode_project', 'like', "%{$keyword}%")
                ->orWhere('status_pekerjaan', 'like', "%{$keyword}%")
                ->orWhere('pengelola', 'like', "%{$keyword}%")
                ->orWhere('pic', 'like', "%{$keyword}%");
        });
    }
}
