<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Question;
use App\Tag;
use Carbon\Carbon;

class QuestionController extends Controller
{
    /**
     * QuestionController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => [
            'store', 'update', 'destroy', 'upVote', 'downVote'
        ]]);
    }

    /**
     * Return all questions or questions that matched the given search query (if there is a query) from newest to oldest.
     * @return Response
     */
    public function index(Request $request)
    {
        $search_term = $request->input('search');

        if($search_term) {
            $questions = Question::orderBy('updated_at', 'DESC')->where('body', 'LIKE', "%$search_term%")->
            orWhere('title', 'LIKE', "%$search_term%")->paginate(15);
        }
        else {
            $questions = Question::orderBy('updated_at', 'DESC')->paginate(15);
        }
        if (JWTAuth::getToken()) {
            $user = JWTAuth::parseToken()->authenticate();
            foreach($questions as $question) {
                if ($question->author["id"] === $user->id) {
                    $question['editable'] = true;
                }
            }
        }
        return $questions;
    }

    /**
     * Show the specified question, return 404 HTTP response if the question is not found.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function show($id)
    {
        try {
            $question = Question::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'question not found'], 404);
        }

        if (JWTAuth::getToken()) {
            $user = JWTAuth::parseToken()->authenticate();
            if ($question->author["id"] === $user->id) {
                $question['editable'] = true;
            }
            $vote_type = $question->votes()->select('vote_type')->where('id', $user->id)->get()->first();
            $vote_type = $vote_type['vote_type'];
            if ($vote_type === 1) {
               $question['voteStatus'] = 'up';
            }
            else if ($vote_type === -1) {
                $question['voteStatus'] = 'down';
            }
            else {
                $question['voteStatus'] = 'none';
            }
        }
        return $question;
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
        $tags = $request->only('tags');
        $tags = array_filter(explode(',', $tags['tags']));
        if ($tags) {
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $tag_id = Tag::firstOrCreate(['name' => $tag])->id;
                if (!$question->tags()->where('id', $tag_id)->exists()) {
                    $question->tags()->attach($tag_id);
                }
            }
        }

        return $question;
    }

    /**
     * Update the specified question, return 401 HTTP response if unauthorized, or 404 HTTP response if the question
     * is not found.
     * @param int $id id of the specified question.
     * @param Request $request sent request.
     * @return Response
     */
    public function update($id, Request $request)
    {
        try {
            $question = Question::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'question not found'], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if ($question->user_id !== $user->id) {
            return response()->josn(['error' => 'Unauthorized — you cannot edit someone else\'s question'], 401);
        }

        $question->title = $request->title;
        $question->body = $request->body;
        $question->tags()->detach();
        $tags = $request->tags;
        $tags = array_filter(explode(',', $tags));
        if ($tags) {
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $tag_id = Tag::firstOrCreate(['name' => $tag])->id;
                if (!$question->tags()->where('id', $tag_id)->exists()) {
                    $question->tags()->attach($tag_id);
                }
            }
        }

        $question['updated_at'] = Carbon::now();
        $question->save();

        return $question;
    }

    /**
     * Delete the specified question, return 401 HTTP response if unauthorized, or 404 HTTP response if the question
     * is not found.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function destroy($id) {
        try {
            $question = Question::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'question not found'], 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($question->user_id !== $user->id) {
            return response('Unauthorized — you cannot delete someone else\'s question', 401);
        }

        $question->delete();

        return response()->json(['message' => 'Question deleted'], 200);
    }

    /**
     * Up vote the specified question, return 404 HTTP response if the question is not found.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function upVote($id) {
        try {
            $question = Question::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'question not found'], 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $vote_type = $question->votes()->select('vote_type')->where('id', $user->id)->get()->first();
        $vote_type = $vote_type['vote_type'];
        if ($vote_type === 1) {
            return response()->json(['message' => 'Question already up voted'], 200);
        }
        else if ($vote_type === -1) {
            $question->votes()->detach($user->id);
        }
        else {
            $question->votes()->attach($user->id, ['vote_type' => 1]);
        }
        return response()->json(['message' => 'Question up voted'], 200);
    }

    /**
     * Down vote the specified question, return 404 HTTP response if the question is not found.
     * @param int $id id of the specified question.
     * @return Response
     */
    public function downVote($id) {
        try {
            $question = Question::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' =>'question not found'], 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $vote_type = $question->votes()->select('vote_type')->where('id', $user->id)->get()->first();
        $vote_type = $vote_type['vote_type'];
        if ($vote_type === 1) {
            $question->votes()->detach($user->id);
        }
        else if ($vote_type === -1) {
            return response()->json(['message' => 'Question already down voted'], 200);
        }
        else {
            $question->votes()->attach($user->id, ['vote_type' => -1]);
        }
        return response()->json(['message'=>'Question down voted'], 200);
    }

    /**
     * Get all questions tagged with tagName.
     * @param $tagName name of the tag.
     * @return Response
     */
    public function tagged($tagName) {
        $tag = Tag::where('name', $tagName)->get()->first();
        if ($tag) {
             return $tag->questions()->orderBy('updated_at', 'DESC')->paginate(15);
        }
        else {
            return;
        }
    }
}
