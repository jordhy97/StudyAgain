<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Question;
use App\Tag;

class QuestionController extends Controller
{
    /**
     * QuestionController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => [
            'store', 'update', 'destroy'
        ]]);
    }

    /**
     * Return all questions from newest to oldest.
     * @return Response
     */
    public function index()
    {
        return Question::orderBy('created_at', 'desc')->get();
    }

    /**
     * Show the specified question, return 404 HTTP response if the question is not found.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function show($id)
    {
        return Question::findOrFail($id);
    }

    /**
     * Store a new question.
     * @param Request $request sent request.
     * @return Response
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $request['user_id'] = $user->id;
        $question =  Question::create($request->except('tags'));

//        $tags = $request->only['tags'];
//        if ($tags) {
//            $tag_ids = Tag::select('id')->whereIn('name', explode(',', $tags['tags']))->get();
//            $question->tags()->attach($tag_ids);
//        }

        return $question;
    }

    /**
     * Update the specified question, return 401 HTTP response if unauthorized.
     * @param int $id id of the specified question.
     * @param Request $request sent request.
     * @return Response
     */
    public function update($id, Request $request)
    {
        $question = Question::findOrFail($id);
        $user = JWTAuth::parseToken()->authenticate();
        if ($question->user_id !== $user->id) {
            return response('Unauthorized — you cannot edit someone else\'s question', 401);
        }

        $question->title = $request->title;
        $question->body = $request->body;
        $question->save();

        return response('Question updated', 200);
    }

    /**
     * Delete the specified question, return 401 HTTP response if unauthorized.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function destroy($id) {
        $question = Question::findOrFail($id);
        $user = JWTAuth::parseToken()->authenticate();
        if ($question->user_id !== $user->id) {
            return response('Unauthorized — you cannot delete someone else\'s question', 401);
        }

        $question->delete();

        return response("Question deleted", 200);
    }

    /**
     * Up vote the specified question.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function upVote($id) {
        $question = Question::findOrFail($id);
        $user = JWTAuth::parseToken()->authenticate();
        $vote_type = $question->votes()->select('vote_type')->where('id', $user->id)->get();
        if ($vote_type == 1) {
            return response('Question already up voted', 200);
        }
        if ($vote_type == -1) {
            $question->votes()->detach($user->id);
        }
        else {
            $question->votes()->attach($user->id, ['vote_type' => 1]);
        }
        return response('Question up voted', 200);
    }

    /**
     * Down vote the specified question.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function downVote($id) {
        $question = Question::findOrFail($id);
        $user = JWTAuth::parseToken()->authenticate();
        $vote_type = $question->votes()->select('vote_type')->where('id', $user->id)->get();
        if ($vote_type == 1) {
            $question->votes()->detach($user->id);
        }
        else if ($vote_type == -1) {
            return response('Question already down voted', 200);
        }
        else {
            $question->votes()->attach($user->id, ['vote_type' => -1]);
        }
        return response('Question do voted', 200);
    }
}
