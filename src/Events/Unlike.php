<?php

namespace Nanhh\Comments\Events;

use Illuminate\Queue\SerializesModels;
use Nanhh\Comments\Like;

class Unlike
{
    use SerializesModels;

    public $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }
}