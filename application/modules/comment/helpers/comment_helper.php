<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Count Story Comments
 * 
 * Count the number of total comments made
 * on a submitted story.
 * 
 * @param int $story_id
 * @return object or boolean false
 * 
 */
function count_story_comments($story_id)
{
    $CI =& get_instance();
    $CI->load->model('comment/comment_model', 'comment');

    return $CI->comment->count_story_comments($story_id);    
}

/**
 * Comment Upvoted
 * 
 * Has the currently logged in user already 
 * upvoted a particular comment
 * 
 * @param int $comment_id
 * @return string
 * 
 */
function comment_upvoted($comment_id = 0, $user_id = 0)
{
    $CI =& get_instance();
    $CI->load->model('vote/vote_model', 'vote');

    return $CI->vote->user_has_upvoted_comment($comment_id, $user_id);
}

/**
 * Comment Downvoted
 * 
 * Has the currently logged in user already 
 * downvoted a particular comment
 * 
 * @param int $comment_id
 * @return string
 * 
 */
function comment_downvoted($comment_id = 0, $user_id = 0)
{
    $CI =& get_instance();
    $CI->load->model('vote/vote_model', 'vote');

    return $CI->vote->user_has_downvoted_comment($comment_id, $user_id);
}

function comment_vote($direction = "up", $comment_id = 0, $reason_id = 0)
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
			if (!comment_upvoted($comment_id, $user_id))
			{
				return $CI->vote->cast_comment_vote("up", $comment_id, $user_id);
			}
			else
			{
				return FALSE;
			}
		}
		elseif ($direction == 'down')
		{
			if (!comment_downvoted($comment_id, $user_id))
			{
				return $CI->vote->cast_comment_vote("down", $comment_id, $user_id, $reason_id);
			}
			else
			{
				return FALSE;
			}
		}
	}
}