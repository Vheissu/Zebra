<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function cast_vote($direction = "up", $story_id = 0, $reason_id = 0)
{
	// Get the currently logged in user ID
	$user_id = current_user_id();

	if (!$user_id)
	{
		return lang('user_not_logged_in');
	}
	else
	{
		if ($direction == 'up')
		{
			if (!already_upvoted($story_id, $user_id))
			{
				
			}
		}
	}
}