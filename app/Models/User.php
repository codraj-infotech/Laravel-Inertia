<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'date_of_birth',
        'gender',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * List of fields that are visible by default in API responses.
     * Used by Froiden REST API package.
     *
     * @var array
     */
    protected $default = [
        'id',
        'name',
        'email',
        'profile_image',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'created_at',
        'updated_at'
    ];

    /**
     * List of fields on which filters are allowed in API requests.
     * Used by Froiden REST API package.
     *
     * @var array
     */
    protected $filterable = [
        'id',
        'name',
        'email',
        'gender',
        'phone',
        'created_at'
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
            'date_of_birth' => 'date',
        ];
    }
}
