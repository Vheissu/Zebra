<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function calculate_ranking($upvotes = 0, $downvotes = 0, $item_age, $gravity = 1.8)
{
    // Score is upvotes minus downvotes
    $score    = $upvotes - $downvotes;

    return ($score - 1) / pow(($item_age+2), $gravity);
}