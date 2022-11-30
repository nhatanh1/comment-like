<?php

namespace Nanhh\CommentLike\Events;

use Illuminate\Queue\SerializesModels;
use Nanhh\CommentLike\Like as CommentsLike;

class Like
{
    use SerializesModels;

    public $like;

    public function __construct(CommentsLike $like)
    {
        $this->like = $like;
    }
}