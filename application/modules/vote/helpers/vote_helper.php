<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_downvote_reasons()
{
    $CI =& get_instance();
    $CI->load->model('vote/vote_model', 'vote');

    return $CI->vote->get_downvote_reasons();
}