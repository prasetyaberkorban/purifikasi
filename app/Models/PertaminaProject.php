<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PertaminaProject extends Model
{
    use HasFactory;

    protected $table = 'pertamina_projects';

    protected $fillable = [
        'kode_project',
        'nama_project',
        'nilai_project',
        'client',
        'partner',
        'pengelola',
        'progress_penawaran',
        'progress_pra_kontrak',
        'hasil_penawaran',
        'status_pekerjaan',
        'progress_pekerjaan',
        'pic',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'nilai_project' => 'float',
            'progress_pra_kontrak' => 'float',
            'progress_pekerjaan' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('kode_project', 'like', "%{$term}%")
                ->orWhere('nama_project', 'like', "%{$term}%")
                ->orWhere('client', 'like', "%{$term}%")
                ->orWhere('partner', 'like', "%{$term}%")
                ->orWhere('pengelola', 'like', "%{$term}%")
                ->orWhere('pic', 'like', "%{$term}%")
                ->orWhere('progress_penawaran', 'like', "%{$term}%")
                ->orWhere('hasil_penawaran', 'like', "%{$term}%");
        });
    }

    public function isMenang(): bool
    {
        return strcasecmp($this->hasil_penawaran ?? '', 'Menang') === 0;
    }
}
