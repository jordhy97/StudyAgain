<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Answer;

class AnswerController extends Controller
{
    /**
     * AnswerController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => [
            'store', 'update', 'destroy', 'upVote', 'downVote'
        ]]);
    }

    /**
     * Show the specified answer, return 404 HTTP response if the answer is not found.
     * @param int $id id of the specified answer.
     * @return Response
     */
    public function show($id)
    {
        try {
            $answer = Answer::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'answer not found'], 404);
        }
        return $answer;
    }

    /**
     * Store a new answer for the specified question.
     * @param int $id id of the specified question.
     * @param Request $request sent request.
     * @return Response
     */
    public function store($id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $request['user_id'] = $user->id;
        $request['question_id'] = $id;
        $request['body'] = $request->body;

        return Answer::create($request->all());
    }

    /**
     * Update the specified answer, return 401 HTTP response if unauthorized or 404 HTTP response
     * if the answer is not found.
     * @param int $id id of the specified answer.
     * @param Request $request sent request.
     * @return Response
     */
    public function update($id, Request $request)
    {
        try {
            $answer = Answer::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'answer not found'], 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($answer->user_id !== $user->id) {
            return response()->json(['message'=>'Unauthorized — you cannot edit someone else\'s answer'], 401);
        }

        $answer->body = $request->body;
        $answer->save();

        return $answer;
    }

    /**
     * Delete the specified answer, return 401 HTTP response if unauthorized or 404 HTTP response
     * if the answer is not found.
     * @param int $id id of the specified answer.
     * @return Response
     */
    public function destroy($id) {
        try {
            $answer = Answer::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'answer not found'], 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($answer->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized — you cannot delete someone else\'s answer'], 401);
        }

        $answer->delete();

        return response()->json(['message' => 'Answer deleted'], 200);
    }

    /**
     * Up vote the specified answer, return 404 HTTP response if the answer is not found.
     * @param int $id id of the specified answer.
     * @return Response
     */
    public function upVote($id) {
        try {
            $answer = Answer::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'answer not found'], 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $vote_type = $answer->votes()->select('vote_type')->where('id', $user->id)->get()->first();
        $vote_type = $vote_type['vote_type'];
        if ($vote_type === 1) {
            return response()->json(['message' => 'Answer already up voted'], 200);
        }
        else if ($vote_type === -1) {
            $answer->votes()->detach($user->id);
        }
        else {
            $answer->votes()->attach($user->id, ['vote_type' => 1]);
        }
        return response()->json(['message' => 'Answer up voted'], 200);
    }

    /**
     * Down vote the specified answer, return 404 HTTP response if the answer is not found.
     * @param int $id id of the specified answer.
     * @return Response
     */
    public function downVote($id) {
        try {
            $answer = Answer::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'answer not found'], 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $vote_type = $answer->votes()->select('vote_type')->where('id', $user->id)->get()->first();
        $vote_type = $vote_type['vote_type'];
        if ($vote_type === 1) {
            $answer->votes()->detach($user->id);
        }
        else if ($vote_type === -1) {
            return response()->json(['message' => 'Answer already down voted'], 200);
        }
        else {
            $answer->votes()->attach($user->id, ['vote_type' => -1]);
        }
        return response()->json(['message' => 'Answer down voted'], 200);
    }

}
