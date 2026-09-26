<?php

namespace App\Models;

use App\Models\Concerns\Votable;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

#[Fillable(['body'])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory, SoftDeletes, Votable;

    protected function casts(): array
    {
        return [
            'edited_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Keep the story's comment count in step with its visible comments.
        static::created(fn (Comment $c) => $c->adjustStoryCount(1));
        static::softDeleted(fn (Comment $c) => $c->adjustStoryCount(-1));
        static::restored(fn (Comment $c) => $c->adjustStoryCount(1));
    }

    private function adjustStoryCount(int $by): void
    {
        Story::withTrashed()->whereKey($this->story_id)->toBase()->increment('comments_count', $by);
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function permalink(): string
    {
        return route('comments.show', $this);
    }

    /**
     * Arrange a flat list of comments into a tree.
     *
     * Siblings are ordered by score, then oldest first. Deleted comments are
     * kept as placeholders while they still have visible replies, so threads
     * don't lose their shape, and dropped entirely once they don't.
     *
     * @param  Collection<int, Comment>  $comments
     * @return Collection<int, Comment> the top-level comments, each with a `children` relation
     */
    public static function tree(Collection $comments, ?int $rootId = null): Collection
    {
        $byParent = $comments->groupBy(fn (Comment $c) => $c->parent_id ?? 0);

        $build = function (int $parentId) use (&$build, $byParent): Collection {
            return ($byParent[$parentId] ?? collect())
                ->sortBy([['score', 'desc'], ['created_at', 'asc'], ['id', 'asc']])
                ->map(function (Comment $comment) use ($build) {
                    $comment->setRelation('children', $build($comment->id));

                    return $comment;
                })
                ->reject(fn (Comment $c) => $c->trashed() && $c->children->isEmpty())
                ->values();
        };

        return $build($rootId ?? 0);
    }
}
