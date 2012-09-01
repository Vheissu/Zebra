<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vote_model extends MY_Model {

	protected $_table = 'votes';

    protected $belongs_to  = array('user', 'comment', 'story');

    public function user_has_upvoted($story_id, $user_id)
    {
    	
    }

    public function user_has_downvoted($story_id, $user_id)
    {
        
    }

    public function cast_story_vote($type = "up", $object_id, $user_id, $reason_id = 0)
    {
    	if ($type == 'up')
    	{
    		$field_data = array(
    			'user_id'  => $user_id,
    			'story_id' => $object_id,
    			'votetype' => 'upvote'	
    		);
    	}
    	elseif ($type == 'down')
    	{
    		$field_data = array(
    			'user_id'   => $user_id,
    			'story_id'  => $object_id,
    			'reason_id' => $reason_id,
    			'votetype'  => 'downvote'	
    		);
    	}

    	return ($this->insert($field_data) !== FALSE) ? TRUE : FALSE;
    }

}