<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'id';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'email',
        'password',
        'name',
        'identification_number',
        'department',
        'college',
        'certificate',
        'general_jurisdiction',
        'exact_jurisdiction',
        'picture',
        'current_promotion',
        'date_current_promotion',
        'next_promotion',
        'date_promotion',
        'user',
        'supervisor',
        'first_member',
        'second_member',
        'third_member',
        'forth_member',
        'fifth_member',
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
    ];

    public function package_file()
    {
        return $this->hasOne('App\Models\package_file');
    }
    public function quoteSc_Member()
    {
        return $this->hasOne('App\Models\quoteSc_Member');
    }
}
