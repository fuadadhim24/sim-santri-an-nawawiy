<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'whatsapp',
        'email',
        'password',
        'role',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function setWhatsappAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['whatsapp'] = null;
            return;
        }

        $number = preg_replace('/\D/', '', $value);

        if (str_starts_with($number, '62')) {
            $this->attributes['whatsapp'] = $number;
        } elseif (str_starts_with($number, '0')) {
            $this->attributes['whatsapp'] = '62' . substr($number, 1);
        } else {
            $this->attributes['whatsapp'] = '62' . $number;
        }
    }
}
