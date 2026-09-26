<?php

namespace App\Models;

use App\Models\Concerns\Votable;
use App\Support\Url;
use Database\Factories\StoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

#[Fillable(['title', 'url', 'text'])]
class Story extends Model
{
    /** @use HasFactory<StoryFactory> */
    use HasFactory, SoftDeletes, Votable;

    protected function casts(): array
    {
        return [
            'edited_at' => 'datetime',
            'score' => 'integer',
            'hot' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Story $story) {
            if ($story->isDirty('title')) {
                $slug = trim(Str::substr(Str::slug($story->title), 0, 120), '-') ?: 'story';
                // "/s/{id}/edit" is the edit form, so a story can't have that as its slug.
                $story->slug = $slug === 'edit' ? 'edit-story' : $slug;
            }

            if ($story->isDirty('url')) {
                $story->url = $story->url ?: null;
                $story->url_hash = $story->url ? Url::hash($story->url) : null;
                $story->domain = $story->url ? Url::domain($story->url) : null;
            }

            if (! $story->exists || $story->isDirty('score', 'created_at')) {
                $story->refreshRanking();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeHot(Builder $query): void
    {
        $query->orderByDesc('hot')->orderByDesc('id');
    }

    public function scopeNewest(Builder $query): void
    {
        $query->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * Text-only posts, the "Ask Zebra" section.
     */
    public function scopeAsk(Builder $query): void
    {
        $query->whereNull('url');
    }

    public function isAsk(): bool
    {
        return $this->url === null;
    }

    /**
     * Where the title links: the external URL, or the discussion for text posts.
     */
    public function href(): string
    {
        return $this->url ?? $this->permalink();
    }

    public function permalink(): string
    {
        return route('stories.show', [$this, $this->slug]);
    }

    public static function hotValue(int $score, Carbon $createdAt): float
    {
        $order = log10(max(abs($score), 1));
        $sign = $score <=> 0;
        $seconds = $createdAt->getTimestamp() - config('zebra.ranking.epoch');

        return round($sign * $order + $seconds / config('zebra.ranking.gravity'), 7);
    }

    protected function refreshRanking(): void
    {
        $this->hot = static::hotValue($this->score ?? 1, $this->created_at ?? now());
    }
}
