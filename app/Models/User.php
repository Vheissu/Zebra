<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['username', 'email', 'password', 'about'])]
#[Hidden(['password', 'remember_token', 'email'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'is_admin' => 'boolean',
            'karma' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * Profile URLs use the username, matched case-insensitively.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->newQuery()->whereUsername($value)->firstOrFail();
    }

    public function getRouteKey(): string
    {
        return $this->username;
    }

    public function scopeWhereUsername(Builder $query, string $username): void
    {
        $query->whereRaw('lower(username) = ?', [mb_strtolower($username)]);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    public function canDownvote(): bool
    {
        return $this->is_admin || $this->karma >= config('zebra.downvote_karma');
    }

    /**
     * The user's votes on the given stories and comments, keyed "story:12" / "comment:7".
     *
     * @param  iterable<Story|Comment>  $items
     * @return Collection<string, int>
     */
    public function votesOn(iterable $items): Collection
    {
        $ids = collect($items)->groupBy(fn ($item) => $item->getMorphClass())
            ->map(fn ($group) => $group->pluck('id'));

        if ($ids->isEmpty()) {
            return collect();
        }

        return $this->votes()
            ->where(function (Builder $query) use ($ids) {
                foreach ($ids as $type => $keys) {
                    $query->orWhere(fn (Builder $q) => $q->where('votable_type', $type)->whereIn('votable_id', $keys));
                }
            })
            ->get()
            ->mapWithKeys(fn (Vote $vote) => [$vote->votable_type.':'.$vote->votable_id => $vote->value]);
    }
}
