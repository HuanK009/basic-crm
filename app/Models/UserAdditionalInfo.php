<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAdditionalInfo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'address',
        'country',
        'postal_code',
        'date_of_birth',
        'gender',
        'marital_status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
