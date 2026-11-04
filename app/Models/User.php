<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'unit', 'phone', 'avatar_path', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class, 'borrower_id');
    }

    public function handledLoans(): HasMany
    {
        return $this->hasMany(AssetLoan::class, 'handled_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AssetActivity::class);
    }

    /** Hanya Admin Utama & Staf Logistik yang boleh masuk panel admin. */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->isStaff();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function avatarUrl(): string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : 'https://ui-avatars.com/api/?background=0D4C1E&color=ffffff&bold=true&name='.urlencode((string) $this->name);
    }
}