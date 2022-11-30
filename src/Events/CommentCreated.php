<?php

namespace Nanhh\CommentLike\Events;

use Illuminate\Queue\SerializesModels;
use Nanhh\CommentLike\Comment;

class CommentCreated
{
    use SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
}