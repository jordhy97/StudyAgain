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
        'author', 'status', 'voteCounts', 'answerCounts', 'answers', 'tags'
    ];

    /**
     * The attributes that are visible in the JSON response.
     */
    protected $visible = [
        'id', 'title', 'body', 'author', 'editable', 'status', 'updated_at', 'voteCounts', 'answerCounts',
        'answers', 'tags', 'voteStatus'
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
        $author["id"] = $this->user()->first()->id;
        $author["name"] = $this->user()->first()->name;
        return $author;
    }

    /**
     * Get the status attribute of this question.
     */
    public function getStatusAttribute() {
        if($this['created_at'] < $this['updated_at']) {
            return "modified";
        }
        else {
            return "asked";
        }
    }


    /**
     * Get the votes attribute of this question.
     */
    public function getVoteCountsAttribute() {
        return $this->votes()->sum('vote_type');
    }

    /**
     * Get the number of answers attribute of this question.
     */
    public function getAnswerCountsAttribute() {
        return $this->answers()->count();
    }

    /**
     * Get the answers attribute of this question.
     */
    public function getAnswersAttribute() {
        return $this->answers()->paginate(15);
    }

    /**
     * Get the tags attribute of this question.
     */
    public function getTagsAttribute() {
        return $this->tags()->get();
    }
}
