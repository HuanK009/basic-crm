<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSpouseInfo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'salutation',
        'first_name',
        'last_name',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
