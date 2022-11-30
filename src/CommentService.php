<?php

namespace Nanhh\Comments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CommentService
{
    /**
     * Handles creating a new comment for given model.
     * @return mixed the configured comment-model
     */
    public function store(Request $request)
    {
        // If guest commenting is turned off, authorize this action.
        if (Config::get('comments.guest_commenting') == false) {
            Gate::authorize('create-comment', Comment::class);
        }

        // Define guest rules if user is not logged in.
        if (!Auth::check()) {
            $guest_rules = [
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|string|email|max:255',
            ];
        }

        // Merge guest rules, if any, with normal validation rules.
        Validator::make($request->all(), array_merge($guest_rules ?? [], [
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|string|min:1',
            'message' => 'required|string'
        ]))->validate();

        $model = $request->commentable_type::findOrFail($request->commentable_id);

        $commentClass = Config::get('comments.model');
        $comment = new $commentClass;

        if (!Auth::check()) {
            $comment->guest_name = $request->guest_name;
            $comment->guest_email = $request->guest_email;
        } else {
            $comment->commenter()->associate(Auth::user());
        }

        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->approved = !Config::get('comments.approval_required');
        $comment->save();

        return $comment;
    }

    /**
     * Handles updating the message of the comment.
     * @return mixed the configured comment-model
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('edit-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string'
        ])->validate();

        $comment->update([
            'comment' => $request->message
        ]);

        return $comment;
    }

    /**
     * Handles deleting a comment.
     * @return mixed the configured comment-model
     */
    public function destroy(Comment $comment): void
    {
        Gate::authorize('delete-comment', $comment);

        if (Config::get('comments.soft_deletes') == true) {
            $comment->delete();
        } else {
            $comment->forceDelete();
        }
    }

    /**
     * Handles creating a reply "comment" to a comment.
     * @return mixed the configured comment-model
     */
    public function reply(Request $request, Comment $comment)
    {
        Gate::authorize('reply-to-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string'
        ])->validate();

        $commentClass = Config::get('comments.model');
        $reply = new $commentClass;
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->approved = !Config::get('comments.approval_required');
        $reply->save();

        return $reply;
    }

    public function like($request)
    {
        $user_id = Auth::user()->id;
        $comment_id = $request->comment_id;

        $likeClass = Config::get('comments.like');

        $like = $likeClass::where('user_id', $user_id)->where('comment_id', $comment_id)->first();

        if (isset($like)) {
            $like->delete();
        } else {
            $like = new $likeClass();
            $like->user_id = $user_id;
            $like->comment_id = $comment_id;
            $like->save();
        }

        $data = $likeClass::where('comment_id', $comment_id)->get();

        return count($data);
    }

    public function check_like($id)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;

            $likeClass = Config::get('comments.like');

            $like = $likeClass::where('user_id', $user_id)->where('comment_id', $id)->first();

            return $like;
        }
        return null;
    }

    public function like_all($id)
    {
        $commentClass = Config::get('comments.model');

        $comments = $commentClass::where('commentable_id', $id)->get();

        $comment_id = [];
        foreach ($comments as $comment) {
            $comment_id[] = $comment->id;
        }

        $result = [];
        foreach ($comment_id as $id) {
            $data = Like::where('comment_id', $id)->get();
            $result[] = ['id' => $id, 'count' => $data->count()];
        }

        return $result;
    }

    public function user_like($id)
    {
        $likeClass = Config::get('comments.like');

        $like = $likeClass::where('comment_id', $id)->get();

        $user_id = [];

        foreach ($like as $value) {
            $user_id[] = $value->user_id;
        }

        $userClass = Config::get('comments.user');

        $user = $userClass::whereIn('id', $user_id)->get();

        return $user;
    }

    public function like_total($id)
    {
        $likeClass = Config::get('comments.like');

        $comments = $likeClass::where('comment_id', $id)->get();

        return ['comment_id' => $id, 'count' => $comments->count()];
    }
}