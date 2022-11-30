<?php

namespace Laravelista\Comments\Events;

use Illuminate\Queue\SerializesModels;
use Laravelista\Comments\Like;

class Unlike
{
    use SerializesModels;

    public $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }
}