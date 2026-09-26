<?php

namespace App\Console\Commands;

use App\Models\Story;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('zebra:rerank')]
#[Description('Recalculate every story\'s ranking, e.g. after changing ZEBRA_RANK_GRAVITY')]
class RerankCommand extends Command
{
    public function handle(): int
    {
        $count = 0;

        Story::withTrashed()->select('id', 'score', 'created_at')->chunkById(500, function ($stories) use (&$count) {
            foreach ($stories as $story) {
                Story::withTrashed()->whereKey($story->id)->toBase()->update([
                    'hot' => Story::hotValue($story->score, $story->created_at),
                ]);
                $count++;
            }
        });

        $this->components->info("Reranked {$count} ".str('story')->plural($count).'.');

        return self::SUCCESS;
    }
}
