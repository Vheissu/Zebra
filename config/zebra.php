<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site
    |--------------------------------------------------------------------------
    |
    | The name comes from APP_NAME. The tagline is shown in the page title
    | of the front page and in the footer.
    |
    */

    'tagline' => env('ZEBRA_TAGLINE', 'Links worth reading, voted on by people who read them.'),

    'per_page' => (int) env('ZEBRA_PER_PAGE', 30),

    /*
    |--------------------------------------------------------------------------
    | Ranking
    |--------------------------------------------------------------------------
    |
    | The front page is ordered by a "hot" value that combines score and age:
    |
    |     hot = sign(score) * log10(max(|score|, 1)) + (created - epoch) / gravity
    |
    | Every `gravity` seconds of age costs a story a factor of ten in score,
    | so with the default of 45000 (12.5 hours) a story needs ten times the
    | points of one posted half a day later to sit above it. Lower values
    | turn the front page over faster.
    |
    | The value only depends on score and submission time, so it is stored
    | on the row and only recalculated when someone votes.
    |
    */

    'ranking' => [
        'gravity' => (int) env('ZEBRA_RANK_GRAVITY', 45000),
        'epoch' => 1346366020, // The first story ever posted to Zebra, 31 August 2012.
    ],

    /*
    |--------------------------------------------------------------------------
    | Moderation
    |--------------------------------------------------------------------------
    |
    | downvote_karma:  karma a member needs before they can downvote.
    |                  Administrators are exempt.
    | edit_window:     minutes after posting during which a member can edit
    |                  their own story or comment. Administrators are exempt.
    | duplicate_days:  a link submitted again within this many days sends
    |                  the submitter to the existing discussion instead.
    |
    */

    'downvote_karma' => (int) env('ZEBRA_DOWNVOTE_KARMA', 20),

    'edit_window' => (int) env('ZEBRA_EDIT_WINDOW', 120),

    'duplicate_days' => (int) env('ZEBRA_DUPLICATE_DAYS', 30),

];
