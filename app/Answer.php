<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', 'question_id', 'body'
    ];

    /**
     * Additional attributes.
     */
    protected $appends = [
        'author', 'votes'
    ];

    /**
     * The attributes that are visible in the JSON response.
     */
    protected $visible = [
        'id', 'body', 'author', 'votes', 'created_at', 'updated_at'
    ];

    /**
     * Get the question that owns this answer.
     */
    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    /**
     * Get the user that creates this answer.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the votes for this question.
     */
    public function votes()
    {
        return $this->belongsToMany('App\User', 'answer_votes')
            ->withPivot('vote_type');
    }

    /**
     * Get the author attribute of this answer.
     */
    public function getAuthorAttribute() {
        return $this->user()->first()->name;
    }

    /**
     * Get the votes attribute of this question.
     */
    public function getVotesAttribute() {
        return $this->votes()->sum('vote_type');
    }
}
