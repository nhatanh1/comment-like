<?php

namespace Laravelista\Comments\Events;

use Illuminate\Queue\SerializesModels;
use Laravelista\Comments\Like as CommentsLike;

class Like
{
    use SerializesModels;

    public $like;

    public function __construct(CommentsLike $like)
    {
        $this->like = $like;
    }
}