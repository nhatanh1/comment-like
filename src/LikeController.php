<?php

namespace Nanhh\Comments;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Spatie\Honeypot\ProtectAgainstSpam;

abstract class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
    }
}