<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * Return all tags from highest count to lowest.
     * @return Response
     */
    public function index(Request $request)
    {
        $search_term = $request->input('q');

        if($search_term) {
            $tags = DB::table('tags')
                ->join('question_tag', 'tags.id', '=', 'question_tag.tag_id')
                ->selectRaw('tags.id, tags.name, count(*) AS count')
                ->where('name', 'LIKE', "%$search_term%")
                ->groupBy('tags.id', 'tags.name')
                ->orderBy('count', 'DESC')
                ->paginate(45);
        }
        else {
            $tags = DB::table('tags')
                ->join('question_tag', 'tags.id', '=', 'question_tag.tag_id')
                ->selectRaw('tags.id, tags.name, count(*) AS count')
                ->groupBy('tags.id', 'tags.name')
                ->orderBy('count', 'DESC')
                ->paginate(45);
        }
        return $tags;
    }
}
