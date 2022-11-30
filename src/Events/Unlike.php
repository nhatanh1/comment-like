<?php

namespace Nanhh\CommentLike\Events;

use Illuminate\Queue\SerializesModels;
use Nanhh\CommentLike\Like;

class Unlike
{
    use SerializesModels;

    public $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }
}