<?php

namespace Database\Seeders;

use App\Actions\CastVote;
use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Models\VoteReason;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Sample content: the stories that were in Zebra's database in September 2012,
 * moved forward in time so they read as this week's front page.
 *
 * Every demo account's password is "password".
 */
class DemoSeeder extends Seeder
{
    private const STORIES = [
        // [author, title, url, text, posted (2012 unix time)]
        ['zebra', 'How We Deploy At Github', 'https://github.com/blog/1241-deploying-at-github', null, 1346366020],
        ['zebra', 'How Tracking Down My Stolen Computer Triggered a Drug Bust', 'http://blog.makezine.com/2012/08/31/how-tracking-down-my-stolen-computer-triggered-a-drug-bust/', null, 1346464971],
        ['zebra', 'Building Atari With CreateJS', 'http://atari.com/arcade/developers/building-atari-createjs', null, 1346465070],
        ['zebra', 'A Lesson In Timing Attacks', 'http://codahale.com/a-lesson-in-timing-attacks/', null, 1346465121],
        ['zebra', 'Valve Finds Value In Open-Source Drivers', 'http://www.phoronix.com/scan.php?page=article&item=intel_valve_linux&num=1', null, 1346465182],
        ['zebra', 'What Powers Etsy', 'http://codeascraft.etsy.com/2012/08/31/what-hardware-powers-etsy-com/', null, 1346465205],
        ['zebra', 'What Is Good API Design?', 'http://richardminerich.com/2012/08/what-is-good-api-design/', null, 1346465234],
        ['zebra', 'Open WebOS Beta Officially Released', 'http://blog.openwebosproject.org/post/30593510898/open-webos-august-edition', null, 1346465259],
        ['zebra', 'Moving From Heroku To Hardware', 'http://justcramer.com/2012/08/30/how-noops-works-for-sentry/', null, 1346288196],
        ['zebra', 'The Humans Who Outrun Horses', 'http://www.smh.com.au/world/science/the-humans-who-outrun-horses-20120606-1zv96.html', null, 1346469806],
        ['zebra', "Birds Hold 'Funerals' For Dead", 'http://www.bbc.co.uk/nature/19421217', null, 1346470110],
        ['maxxx', 'Left Alone by Its Owner, Reddit Soars', 'http://www.nytimes.com/2012/09/03/business/media/reddit-thrives-after-advance-publications-let-it-sink-or-swim.html', null, 1346637769],
        ['maxxx', 'Apple Never Invented Anything', 'http://www.mondaynote.com/2012/09/02/apple-never-invented-anything/', null, 1346639849],
        ['zebra', 'When A Kickstarter Campaign Fails, Does Anyone Get Their Money Back?', 'http://www.npr.org/blogs/alltechconsidered/2012/09/03/160505449/when-a-kickstarter-campaign-fails-does-anyone-get-their-money-back', null, 1346726598],
        ['zebra', 'Nintendo Almost Made a Knitting Add-On for NES', 'http://www.ign.com/articles/2012/08/31/nintendo-almost-made-a-knitting-add-on-for-nes', null, 1346726686],
        ['zebra', 'SQL vs. NoSQL', 'http://www.linuxjournal.com/article/10770', null, 1346726735],
        ['zebra', 'Foggy: jQuery plugin for blurring page elements', 'http://nbartlomiej.github.com/foggy/', null, 1346726807],
        ['zebra', 'Pass: The Standard Unix Password Manager', 'http://zx2c4.com/projects/password-store/', null, 1346727252],
        ['zebra', 'Ask: How do you find clients?', null, "Hi, I'm thinking about doing some freelance web design and marketing for a bit of extra cash.\n\nAside from cold calls what are some good ways to obtain clients?", 1346731355],
        ['zebra', 'AntiSec leaks 1,000,001 Apple UDIDs, Device Names/Types', 'http://pastebin.com/nfVT7b0Z', null, 1346733681],
        ['zebra', 'Cable lacing on the Curiosity rover', 'http://igkt.net/sm/index.php?topic=4028', null, 1346735888],
        ['galazy', 'Google Search is only 18% Search', 'http://blog.jitbit.com/2012/09/googles-serp-is-only-25-serp.html', null, 1346803080],
        ['galazy', "FBI Says Laptop Wasn't Hacked; Never Possessed File of Apple Device IDs", 'http://www.wired.com/threatlevel/2012/09/fbi-says-laptop-wasnt-hacked-never-possessed-file-of-apple-device-ids', null, 1346803101],
        ['galazy', "What's a $4000 Suit Worth?", 'http://www.nytimes.com/2012/09/09/magazine/whats-a-4000-suit-worth.html?pagewanted=all', null, 1346803216],
    ];

    public function run(CastVote $vote): void
    {
        $users = collect([
            ['zebra', 'zebra@example.com', true, 'Keeper of the stripes.'],
            ['maxxx', 'max@example.com', false, null],
            ['galazy', 'galazy@example.com', false, null],
            ['okapi', 'okapi@example.com', false, 'Not a zebra. Related, though.'],
            ['quagga', 'quagga@example.com', false, null],
            ['tapir', 'tapir@example.com', false, null],
        ])->mapWithKeys(function (array $row) {
            [$username, $email, $admin, $about] = $row;

            $user = User::firstOrNew(['username' => $username]);
            $user->forceFill([
                'email' => $email,
                'password' => 'password',
                'about' => $about,
                'is_admin' => $admin,
                'created_at' => now()->subMonths(3),
            ])->save();

            return [$username => $user];
        });

        // Shift 2012 into this week, keeping the gaps between posts.
        $newest = max(array_column(self::STORIES, 4));
        $offset = now()->subMinutes(20)->getTimestamp() - $newest;

        $stories = collect(self::STORIES)->map(function (array $row) use ($users, $offset) {
            [$author, $title, $url, $text, $posted] = $row;

            $story = new Story(['title' => $title, 'url' => $url, 'text' => $text]);
            $story->user()->associate($users[$author]);
            $story->created_at = Carbon::createFromTimestamp($posted + $offset);
            $story->save();

            return $story;
        });

        $webos = $stories[7];
        $first = $this->comment($webos, $users['zebra'], 'Will definitely keep my ears open for further announcements. As much as I avoid webOS due to the lack of quality apps, booting into it even for a short while causes me to realize just how smooth its UX is and how awkward and backwards in a lot of ways the other mobile OSs are.');
        $reply = $this->comment($webos, $users['maxxx'], 'They should have open sourced this thing right from the beginning I reckon.', $first);
        $this->comment($webos, $users['okapi'], 'Card view is still the best multitasking model anyone has shipped. Everybody copied it *eventually*.', $reply);

        $ask = $stories[18];
        $clients = $this->comment($ask, $users['galazy'], "Referrals, by a mile. Do one small job really well for someone who knows a lot of people, then ask them who else needs help.\n\nCold calls worked for me exactly once.");
        $this->comment($ask, $users['quagga'], 'Local meetups. Not to pitch, just to be the person people remember when a friend asks "know anyone who does websites?"', $clients);
        $this->comment($ask, $users['tapir'], 'Write about the work you want more of. One decent case study brought in more than a year of cold emails.');

        $this->comment($stories[3], $users['okapi'], "The fix is one line and people still get it wrong:\n\n  hash_equals(\$expected, \$actual)");
        $this->comment($stories[0], $users['galazy'], 'Deploying from chat felt like a gimmick until I worked somewhere without it.');
        $this->comment($stories[21], $users['zebra'], 'The headline number depends a lot on screen size, but the trend is real.');

        // Everyone upvotes a spread of stories so the front page has some shape.
        mt_srand(2012);
        foreach ($stories as $story) {
            foreach ($users as $user) {
                if ($user->id !== $story->user_id && mt_rand(1, 100) <= 45) {
                    $vote->handle($user, $story, 1);
                }
            }
        }

        foreach (Comment::all() as $comment) {
            foreach ($users as $user) {
                if ($user->id !== $comment->user_id && mt_rand(1, 100) <= 40) {
                    $vote->handle($user, $comment, 1);
                }
            }
        }

        // One downvote, for the admin dashboard.
        $users['zebra']->refresh();
        $vote->handle($users['zebra'], $stories[21], -1, VoteReason::where('reason', 'Too controversial')->value('id'));
    }

    private function comment(Story $story, User $user, string $body, ?Comment $parent = null): Comment
    {
        $comment = new Comment(['body' => $body]);
        $comment->story()->associate($story);
        $comment->user()->associate($user);
        $comment->parent()->associate($parent);
        $comment->created_at = ($parent?->created_at ?? $story->created_at)->copy()->addMinutes(mt_rand(5, 90))->min(now()->subMinute());
        $comment->save();

        return $comment;
    }
}
