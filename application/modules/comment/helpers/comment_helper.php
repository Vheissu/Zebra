<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_comments($story_id)
{
    $CI =& get_instance();
    $CI->load->model('comment/comment_model', 'comment');

	$comments = $CI->comment->get_comments($story_id);

	return $comments;
}

function display_comments($story_id)
{
    $CI =& get_instance();
    $CI->load->library('comment/comments_parser');  

    $comments = get_comments($story_id);

    $CI->comments_parser->arrange($comments);
}

/**
 * Get Comment
 * 
 * Get a singular comment from the database
 * based on its ID
 * 
 * @param int $comment_id
 * @return object or boolean false
 * 
 */
function get_comment($comment_id)
{
    $CI =& get_instance();
    $CI->load->model('comment/comment_model', 'comment');

    return $CI->comment->get($comment_id);
}

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

/**
 * Rewind Comment Vote
 * 
 * Will remove a vote from a comment as well as voting
 * history in an instance where a user changes their
 * vote from an up to a down or vice-versa
 * 
 * @param string $direction
 * @param int $comment_id
 * @return string
 * 
 */
function rewind_comment_vote($direction = "up", $comment_id = 0)
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
        return $CI->vote->rewind_comment_vote($direction, $story_id, $user_id);
    }
}

/**
 * Comment Vote
 * 
 * A helper function for performing an upvote or
 * downvote on a particular comment.
 * 
 * @param string $direction
 * @param int $comment_id
 * @param int $reason_id
 * @return string
 * 
 */
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
                rewind_comment_vote("down", $comment_id);
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
                rewind_comment_vote("up", $comment_id);
				return $CI->vote->cast_comment_vote("down", $comment_id, $user_id, $reason_id);
			}
			else
			{
				return FALSE;
			}
		}
	}
}