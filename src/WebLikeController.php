<?php

namespace Nanhh\Comments;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class WebLikeController extends LikeController
{
    private CommentService $commentService;

    public function __construct(
        CommentService $commentService
    ) {
        parent::__construct();

        $this->commentService = $commentService;
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