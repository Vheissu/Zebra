<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id', 'value', 'vote_reason_id'])]
class Vote extends Model
{
    public const UP = 1;

    public const DOWN = -1;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(VoteReason::class, 'vote_reason_id');
    }
}
