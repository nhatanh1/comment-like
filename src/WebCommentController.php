<?php

namespace Nanhh\CommentLike;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class WebCommentController extends CommentController
{
    private CommentService $commentService;

    public function __construct(
        CommentService $commentService
    ) {
        parent::__construct();

        $this->commentService = $commentService;
    }

    /**
     * Creates a new comment for given model.
     */
    public function store(Request $request)
    {
        $comment = $this->commentService->store($request);

        return Redirect::to(URL::previous() . '#comment-' . $comment->getKey());
    }

    /**
     * Updates the message of the comment.
     */
    public function update(Request $request, Comment $comment)
    {
        $comment = $this->commentService->update($request, $comment);

        return Redirect::to(URL::previous() . '#comment-' . $comment->getKey());
    }

    /**
     * Deletes a comment.
     */
    public function destroy(Comment $comment)
    {
        $this->commentService->destroy($comment);

        return Redirect::back();
    }

    /**
     * Creates a reply "comment" to a comment.
     */
    public function reply(Request $request, Comment $comment)
    {
        $reply = $this->commentService->reply($request, $comment);

        return Redirect::to(URL::previous() . '#comment-' . $reply->getKey());
    }

    public function like(Request $request)
    {
        $data = $this->commentService->like($request);

        return $data;
    }

    public function like_total($id)
    {
        $data = $this->commentService->like_total($id);
        return $data;
    }

    public function check_like($id)
    {
        $data = $this->commentService->check_like($id);

        return $data;
    }

    public function like_all($id)
    {
        $data = $this->commentService->like_all($id);

        return $data;
    }

    public function user_like($id)
    {
        $data = $this->commentService->user_like($id);

        return $data;
    }
}