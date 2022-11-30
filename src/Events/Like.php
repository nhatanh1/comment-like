<?php

namespace Nanhh\Comments\Events;

use Illuminate\Queue\SerializesModels;
use Nanhh\Comments\Like as CommentsLike;

class Like
{
    use SerializesModels;

    public $like;

    public function __construct(CommentsLike $like)
    {
        $this->like = $like;
    }
}