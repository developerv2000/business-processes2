<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Support\Traits\DestroysModelRecords;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use DestroysModelRecords;

    public $model = Comment::class; // used in multiple destroy trait

    public function index(Request $request)
    {
        $model = $request->route('commentable_type');
        $instanceId = $request->route('commentable_id');

        $instance = $model::find($instanceId);
        $instance->load('comments');

        return view('comments.index', compact('instance'));
    }

    public function edit(Comment $instance)
    {
        return view('comments.edit', compact('instance'));
    }

    public function update(Request $request, Comment $instance)
    {
        $instance->update($request->all());

        return redirect($request->input('previous_url'));
    }

    public function store(Request $request)
    {
        $model = $request->input('commentable_type');
        $instanceId = $request->input('commentable_id');

        $instance = $model::find($instanceId);
        $instance->storeComment($request->body);

        return redirect()->back();
    }
}
