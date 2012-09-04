<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vote_model extends MY_Model {

	protected $_table = 'votes';

    public function get_downvote_reasons()
    {
        $query = $this->db->get('vote_reasons');

        return ($query->num_rows() >= 1) ? $query->result() : FALSE;
    }

    public function user_has_upvoted_story($story_id, $user_id)
    {
        $this->db->where('story_id', $story_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('vote_type', 'upvote');

        $query = $this->db->get($this->_table);

        return ($query->num_rows() >= 1) ? TRUE : FALSE;
    }

    public function user_has_downvoted_story($story_id, $user_id)
    {
        $query = $this->db->get_where($this->_table, array('story_id' => $story_id, 'user_id' => $user_id, 'vote_type' => 'downvote'));

        return ($query->num_rows() >= 1) ? TRUE : FALSE;    
    }

    public function cast_story_vote($type = "up", $story_id, $user_id, $reason_id = 0)
    {
    	if ($type == 'up')
    	{
    		$field_data = array(
    			'user_id'  => $user_id,
    			'story_id' => $story_id,
    			'votetype' => 'upvote'	
    		);
    	}
    	elseif ($type == 'down')
    	{
    		$field_data = array(
    			'user_id'   => $user_id,
    			'story_id'  => $story_id,
    			'reason_id' => $reason_id,
    			'votetype'  => 'downvote'	
    		);
    	}

    	return ($this->insert($field_data) !== FALSE) ? TRUE : FALSE;
    }

}