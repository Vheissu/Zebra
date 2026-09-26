<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Models\VoteReason;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'story' => Story::class,
            'comment' => Comment::class,
        ]);

        Gate::define('admin', fn (User $user) => $user->is_admin);

        // In production, also refuse passwords that appear in known breaches (checked via Have I Been Pwned).
        Password::defaults(fn () => $this->app->isProduction() ? Password::min(8)->uncompromised(3) : Password::min(8));

        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.more');

        // The downvote dialog is only rendered for people allowed to downvote.
        View::composer('components.layout', function ($view) {
            if (auth()->user()?->canDownvote()) {
                $view->with('voteReasons', VoteReason::ordered()->get());
            }
        });

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        $key = fn (Request $request) => $request->user()?->id ?: $request->ip();

        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by(mb_strtolower((string) $request->input('username')).'|'.$request->ip()),
            Limit::perMinute(20)->by($request->ip()),
        ]);

        RateLimiter::for('register', fn (Request $request) => Limit::perHour(5)->by($request->ip()));

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perHour(5)->by($request->ip()));

        RateLimiter::for('submit', fn (Request $request) => Limit::perHour(10)->by($key($request)));

        RateLimiter::for('comment', fn (Request $request) => Limit::perMinute(6)->by($key($request)));

        RateLimiter::for('vote', fn (Request $request) => Limit::perMinute(60)->by($key($request)));

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($key($request)));
    }
}
