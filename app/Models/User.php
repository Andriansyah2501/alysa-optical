<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Apakah user boleh membuka modul transaksi kasir (POS)?
     * Satu sumber kebenaran untuk middleware PosAccess & tombol menu di layout.
     */
    public function canAccessPos(): bool
    {
        if (in_array($this->role, ['super_admin', 'admin'], true)) {
            return true;
        }

        $allowed = array_filter(array_map(
            'trim',
            explode(',', (string) StoreSetting::get('pos_enabled_roles', 'cashier'))
        ));

        return in_array($this->role, $allowed, true);
    }
}
