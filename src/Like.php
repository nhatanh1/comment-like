<?php

namespace Nanhh\CommentLike;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Nanhh\CommentLike\Events\Like as EventsLike;
use Nanhh\CommentLike\Events\Unlike;

class Like extends Model
{
    use SoftDeletes;

    protected $with = ['commenter'];

    protected $fillable = [
        'id',
        'user_id',
        'commenter_id',
    ];

    protected $dispatchesEvents = [
        'like' => EventsLike::class,
        'unlike' => Unlike::class,
    ];

    public function comment()
    {
        return $this->hasMany(Config::get('comments.model'), 'comment_id');
    }
}