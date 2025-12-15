<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];

    protected $with = ['reactions'];

    protected $appends = ['reactions_summary', 'user_reaction'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function getReactionsSummaryAttribute()
    {
        return $this->reactions
            ->groupBy('type')
            ->map(function ($reactions) {
                return $reactions->count();
            });
    }

    public function getUserReactionAttribute()
    {
        return $this->reactions->firstWhere('user_id', auth()->id())?->type;
    }
}
