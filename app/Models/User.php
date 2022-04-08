<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Profile;
use App\Models\Contract;
use App\Models\Cashbox;
use App\Models\Saving;
use App\Models\Referal;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function cashbox()
    {
        return $this->hasOne(Cashbox::class);
    }

    public function referals()
    {
        return $this->hasMany(Referal::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Package::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function njangiGroups()
    {
        return $this->belongsToMany(NjangiGroup::class);
    }

    public function campayTransactions()
    {
        return $this->hasMany(CampayTransaction::class);
    }
}