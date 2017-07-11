<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', 'title', 'body'
    ];

    /**
     * Additional attributes.
     */
    protected $appends = [
        'author', 'votes', 'numberOfAnswers', 'answers', 'tags'
    ];

    /**
     * The attributes that are visible in the JSON response.
     */
    protected $visible = [
        'id', 'title', 'body', 'author', 'created_at', 'updated_at', 'votes', 'numberOfAnswers',
        'answers', 'tags'
    ];

    /**
     * Get the user that creates this question.
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
        return $this->belongsToMany('App\User', 'question_votes')
            ->withPivot('vote_type');
    }

    /**
     * Get the answers for this question.
     */
    public function answers() {
        return $this->hasMany('App\Answer');
    }

    /**
     * Get the tags for this question.
     */
    public function tags() {
        return $this->belongsToMany('App\Tag');
    }

    /**
     * Get the author attribute of this question.
     */
    public function getAuthorAttribute() {
        return $this->user()->first()->name;
    }

    /**
     * Get the votes attribute of this question.
     */
    public function getVotes() {
        return $this->votes->sum('vote_type');
    }

    /**
     * Get the number of answers attribute of this question.
     */
    public function getNumberOfAnswersAttribute() {
        return $this->answers->count();
    }

    /**
     * Get the answers attribute of this question.
     */
    public function getAnswersAttribute() {
        return $this->answers()->get();
    }

    /**
     * Get the tags attribute of this question.
     */
    public function getTagsAttribute() {
        return $this->tags()->get();
    }
}
