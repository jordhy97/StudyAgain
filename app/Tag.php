<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that are visible in the JSON response.
     */
    protected $visible = [
        'id', 'name'
    ];

    /**
     * Get the questions that have this tag.
     */
    public function questions()
    {
        return $this->belongsToMany('App\Question');
    }
}
