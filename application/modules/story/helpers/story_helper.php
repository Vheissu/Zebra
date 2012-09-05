<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get Username
 * 
 * Get a username based on the user ID
 * 
 * @param int $user_id
 * @return string or boolean false
 * 
 */
function get_username($user_id)
{
	$user = get_user_by_id($user_id);

	$array = array(
		'username'      => $user->row('username'),
		'nice_username' => $user->row('nice_username')
	);

	return (!empty($array)) ? $array : FALSE;
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

/**
 * Rewind Story Vote
 * 
 * Will remove a vote from a story as well as voting
 * history in an instance where a user changes their
 * vote from an up to a down or vice-versa
 * 
 * @param string $direction
 * @param int $story_id
 * @param int $reason_id
 * @return string
 * 
 */
function rewind_story_vote($direction = "up", $story_id = 0)
{
    $user_id = current_user_id();

    $CI =& get_instance();
    $CI->load->model('vote/vote_model', 'vote');

    if (!$user_id)
    {
        return lang('user_not_logged_in');
    }
    else
    {
        return $CI->vote->rewind_story_vote($direction, $story_id, $user_id);
    }
}

/**
 * Story Vote
 * 
 * A helper function for performing an upvote or
 * downvote on a particular story submission.
 * 
 * @param string $direction
 * @param int $story_id
 * @param int $reason_id
 * @return string
 * 
 */
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
                rewind_story_vote('down', $story_id);
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
                rewind_story_vote('up', $story_id);
				return $CI->vote->cast_story_vote("down", $story_id, $user_id, $reason_id);
			}
			else
			{
				return FALSE;
			}
		}
	}
}