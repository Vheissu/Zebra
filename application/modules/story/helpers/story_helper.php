<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_username($user_id)
{
	$user = get_user_by_id($user_id);

	return ($user) ? $user->row('username') : FALSE;
}

/**
 * Story Upvoted
 * 
 * Has the currently logged in user already 
 * upvoted a particular story
 * 
 * @param int $story_id
 * @return string
 * 
 */
function story_upvoted($story_id = 0, $user_id = 0)
{
    $CI =& get_instance();
    $CI->load->model('vote/vote_model', 'vote');

    if ($user_id == 0)
    {
    	$user_id = current_user_id();
    }

    return $CI->vote->user_has_upvoted_story($story_id, $user_id);
}

/**
 * Story Downvoted
 * 
 * Has the currently logged in user already 
 * downvoted a particular story
 * 
 * @param int $story_id
 * @return string
 * 
 */
function story_downvoted($story_id = 0, $user_id = 0)
{
    $CI =& get_instance();
    $CI->load->model('vote/vote_model', 'vote');

    if ($user_id == 0)
    {
    	$user_id = current_user_id();
    }

    return $CI->vote->user_has_downvoted_story($story_id, $user_id);
}

function story_vote($direction = "up", $story_id = 0, $reason_id = 0)
{
	// Get the currently logged in user ID
	$user_id = current_user_id();

    $CI =& get_instance();
    $CI->load->model('vote/vote_model', 'vote');

	if (!$user_id)
	{
		return lang('user_not_logged_in');
	}
	else
	{
		if ($direction == 'up')
		{
			if (!story_upvoted($story_id, $user_id))
			{
				return $CI->vote->cast_story_vote("up", $story_id, $user_id);
			}
			else
			{
				return FALSE;
			}
		}
		elseif ($direction == 'down')
		{
			if (!story_downvoted($story_id, $user_id))
			{
				return $CI->vote->cast_story_vote("down", $story_id, $user_id, $reason_id);
			}
			else
			{
				return FALSE;
			}
		}
	}
}