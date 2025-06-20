<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPersonalPref extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hobbies',
        'sports',
        'music',
        'movies',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
