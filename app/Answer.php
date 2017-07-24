<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        'author', 'voteCounts', 'editable', 'status', 'voteStatus'
    ];

    /**
     * The attributes that are visible in the JSON response.
     */
    protected $visible = [
        'id', 'body', 'author', 'voteCounts', 'updated_at', 'editable', 'status', 'voteStatus'
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
        $author["id"] = $this->user()->first()->id;
        $author["name"] = $this->user()->first()->name;
        return $author;
    }

    /**
     * Get the votes attribute of this question.
     */
    public function getVoteCountsAttribute() {
        return $this->votes()->sum('vote_type');
    }

    /**
     * Get the editable attribute of this question.
     */
    public function getEditableAttribute() {
        $val = false;
        if (JWTAuth::getToken()) {
            $user = JWTAuth::parseToken()->authenticate();
            if ($this->user()->first()->id === $user->id) {
                $val = true;
            }
        }
        return $val;
    }

    /**
     * Get the status attribute of this question.
     */
    public function getStatusAttribute() {
        if($this['created_at'] < $this['updated_at']) {
            return "modified";
        }
        else {
            return "answered";
        }
    }

    /**
     * Get the vote status attribute of this question.
     */
    public function getVoteStatusAttribute() {
        $val = "none";
        if (JWTAuth::getToken()) {
            $user = JWTAuth::parseToken()->authenticate();
            $vote_type = $this->votes()->select('vote_type')->where('id', $user->id)->get()->first();
            $vote_type = $vote_type['vote_type'];
            if ($vote_type === 1) {
                $val = 'up';
            }
            else if ($vote_type === -1) {
                $val = 'down';
            }
        }
        return $val;
    }
}
