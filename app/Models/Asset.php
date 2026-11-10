<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'branch', 'serial_number', 'brand', 'category_id', 'location_id', 'room',
        'owner_unit', 'quantity', 'minimum_stock', 'borrowed_quantity', 'status', 'condition',
        'purchase_date', 'purchase_price', 'photo_path', 'description', 'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssetStatus::class,
            'condition' => AssetCondition::class,
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'last_checked_at' => 'datetime',
            'quantity' => 'integer',
            'minimum_stock' => 'integer',
            'borrowed_quantity' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AssetActivity::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetItem::class)->orderBy('unit_number');
    }

    public function syncItems(): self
    {
        $existingCount = $this->items()->count();
        $target = max(1, (int) $this->quantity);

        if ($existingCount < $target) {
            for ($i = $existingCount + 1; $i <= $target; $i++) {
                $this->items()->create([
                    'item_code' => sprintf('%s-%02d', $this->code, $i),
                    'unit_number' => $i,
                    'condition' => $this->condition ?? AssetCondition::Baik,
                    'status' => AssetStatus::Tersedia,
                    'photo_path' => null,
                    'room' => $this->room,
                ]);
            }
        } elseif ($existingCount > $target) {
            $this->items()->where('unit_number', '>', $target)->where('status', '!=', AssetStatus::Dipinjam->value)->delete();
        }

        return $this;
    }

    public function availableQuantity(): int
    {
        return max(0, $this->quantity - $this->borrowed_quantity);
    }

    public function isCritical(): bool
    {
        return $this->availableQuantity() <= $this->minimum_stock;
    }

    /**
     * Status diturunkan dari stok & jumlah yang sedang dipinjam.
     */
    public function derivedStatus(): AssetStatus
    {
        if ($this->quantity < 1) {
            return AssetStatus::Habis;
        }

        if ($this->availableQuantity() < 1) {
            return AssetStatus::Habis;
        }

        return $this->borrowed_quantity > 0 ? AssetStatus::Dipinjam : AssetStatus::Tersedia;
    }

    public function syncStatus(): static
    {
        $this->status = $this->derivedStatus();

        return $this;
    }

    public function primaryPhotoPath(): ?string
    {
        if ($this->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo_path)) {
            return $this->photo_path;
        }

        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        // Ambil foto dari daftar unit pertama
        $firstItem = $items->first();
        if ($firstItem?->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($firstItem->photo_path)) {
            return $firstItem->photo_path;
        }

        // Atau unit pertama yang memiliki foto
        $itemWithPhoto = $items->first(fn ($item) => ! empty($item->photo_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->photo_path));
        if ($itemWithPhoto) {
            return $itemWithPhoto->photo_path;
        }

        return null;
    }

    public function photoUrl(): ?string
    {
        $path = $this->primaryPhotoPath();
        if ($path) {
            return asset('storage/'.$path);
        }

        return null;
    }

    public function totalValue(): float
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();
        $itemsSum = (float) $items->sum('purchase_price');

        if ($itemsSum > 0) {
            return $itemsSum;
        }

        return (float) (($this->purchase_price ?? 0) * ($this->quantity ?? 1));
    }

    public static function branchCode(?string $branch): string
    {
        return match (trim((string) $branch)) {
            'Semarang 1' => 'SMG1',
            'Semarang 2' => 'SMG2',
            'Bandung' => 'BDG',
            'SMG1' => 'SMG1',
            'SMG2' => 'SMG2',
            'BDG' => 'BDG',
            default => 'SMG1',
        };
    }

    /** Prefix kode aset: [CABANG].[KATEGORI]/[TAHUN] (contoh SMG1.PL/2026) */
    public static function codePrefix(Category $category, ?string $branch = null, ?Location $location = null): string
    {
        $branchCode = self::branchCode($branch);

        return sprintf('%s.%s/%s', $branchCode, Str::upper($category->code), now()->format('Y'));
    }

    public static function nextCode(Category $category, ?string $branch = null, ?Location $location = null): string
    {
        $prefix = self::codePrefix($category, $branch, $location).'/';
        $last = self::query()->where('code', 'like', $prefix.'%')->orderByDesc('id')->value('code');
        $sequence = $last ? ((int) Str::afterLast($last, '/')) + 1 : 1;

        return sprintf('%s%03d', $prefix, $sequence);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('branch', 'like', "%{$term}%")
                ->orWhere('serial_number', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhereHas('category', fn (Builder $c) => $c->where('name', 'like', "%{$term}%"))
                ->orWhereHas('location', fn (Builder $l) => $l->where('name', 'like', "%{$term}%"));
        });
    }
}