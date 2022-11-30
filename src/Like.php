<?php

namespace Nanhh\Comments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Nanhh\Comments\Events\Like as EventsLike;
use Nanhh\Comments\Events\Unlike;

class Like extends Model
{
    use SoftDeletes;

    protected $table = 'like_comment';
    
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