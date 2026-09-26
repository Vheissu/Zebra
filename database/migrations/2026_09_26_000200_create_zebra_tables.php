<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('slug', 200);
            $table->string('url', 2048)->nullable();
            $table->char('url_hash', 40)->nullable()->index();
            $table->string('domain')->nullable()->index();
            $table->text('text')->nullable();
            $table->integer('score')->default(1);
            $table->unsignedInteger('upvotes')->default(0);
            $table->unsignedInteger('downvotes')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->double('hot')->default(0)->index();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('body');
            $table->integer('score')->default(1);
            $table->unsignedInteger('upvotes')->default(0);
            $table->unsignedInteger('downvotes')->default(0);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });

        Schema::create('vote_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('votable');
            $table->tinyInteger('value');
            $table->foreignId('vote_reason_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'votable_type', 'votable_id']);
        });

        // The downvote reasons Zebra shipped with in 2012.
        $reasons = [
            'Spam',
            'Hateful',
            'This made absolutely no sense',
            "I'm offended",
            'Too controversial',
            'Too many spelling and grammar errors',
            'Too biased',
            'Fails to make a compelling argument',
            'Lacking facts',
            "Quite clearly don't know what they're on about",
        ];

        DB::table('vote_reasons')->insert(array_map(fn ($reason, $position) => [
            'reason' => $reason,
            'position' => $position,
            'created_at' => now(),
            'updated_at' => now(),
        ], $reasons, array_keys($reasons)));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
        Schema::dropIfExists('vote_reasons');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('stories');
    }
};
