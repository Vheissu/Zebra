<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Already Upvoted
 * 
 * Has the currently logged in user already 
 * upvoted a particular story
 * 
 * @param int $story_id
 * @return string
 * 
 */
function already_upvoted($story_id = 0, $user_id = 0)
{
    $CI =& get_instance();
    $CI->load->model('vote_model', 'vote');

    $CI->vote->user_has_upvoted($story_id, $user_id);

	return FALSE;
}

/**
 * Already Downvoted
 * 
 * Has the currently logged in user already 
 * downvoted a particular story
 * 
 * @param int $story_id
 * @return string
 * 
 */
function already_downvoted($story_id = 0, $user_id = 0)
{
    $CI =& get_instance();
    $CI->load->model('vote_model', 'vote');

    $CI->vote->user_has_downvoted($story_id, $user_id);

    return FALSE;
}

/**
 * Get Domain
 * 
 * Will parse a URL and return the domain portion
 * 
 * @param string $url
 * @return string
 * 
 */
function get_domain($url)
{
	$parse = parse_url($url);

	$url_domain_dot_ar = explode(".", $parse[host]);

	$url_domain = $url_domain_dot_ar[1] . '.' . $url_domain_dot_ar[2];

	if ($url_domain_dot_ar[2] != "com") 
	{ 
		$url_domain = $url_domain . '.' . $url_domain_dot_ar[3];
	}

	return $url_domain;
}