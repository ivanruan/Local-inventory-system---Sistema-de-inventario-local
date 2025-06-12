<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    	
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

        /**
         * Get the name of the unique identifier for the user.
         */
    //public function getAuthIdentifierName()
    //{
    //    return 'nombre';
    //}

        /**
         * Get the unique identifier for the user.
         */
    //public function getAuthIdentifier()
    //{
    //    return $this->getAttribute('nombre');
    //}

    /**
     * Find the user instance for the given username.
     */
    public function findForPassport($username)
    {
        return $this->where('nombre', $username)->first();
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function isOperador(): bool
    {
        return $this->rol === 'operador';
    }

    public function isSupervisor(): bool
    {
        return $this->rol === 'supervisor';
    }
}
