<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vote_model extends MY_Model {

	protected $_table = 'votes';

    /**
     * Get Downvote Reasons
     * 
     * Return a list of valid downvote reasons
     * and ID's to display on the front-end.
     * 
     * @return object or boolean false
     * 
     */ 
    public function get_downvote_reasons()
    {
        $query = $this->db->get('vote_reasons');

        return ($query->num_rows() >= 1) ? $query->result() : FALSE;
    }

    /**
     * User Has Upvoted Story
     * 
     * Will check if a user has upvoted a particular
     * story item in the database.
     * 
     * @access public
     * @param int $story_id
     * @param int $user_id
     * 
     * @return boolean
     * 
     */ 
    public function user_has_upvoted_story($story_id, $user_id)
    {
        $this->db->where('story_id', $story_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('vote_type', 'upvote');

        $query = $this->db->get($this->_table);

        return ($query->num_rows() >= 1) ? TRUE : FALSE;
    }

    /**
     * User Has Downvoted Story
     * 
     * Will check if a user has downvoted a particular
     * story item in the database.
     * 
     * @access public
     * @param int $story_id
     * @param int $user_id
     * 
     * @return boolean
     * 
     */ 
    public function user_has_downvoted_story($story_id, $user_id)
    {
        $this->db->where('story_id', $story_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('vote_type', 'downvote');

        $query = $this->db->get($this->_table);

        return ($query->num_rows() >= 1) ? TRUE : FALSE;    
    }

    /**
     * Cast Story Vote
     * 
     * Will insert a vote record into the votes table
     * if a user votes up or down a particular story
     * 
     * @access public
     * @param string $type
     * @param int $story_id
     * @param int $user_id
     * @param int $reason_id
     * 
     * @return boolean
     * 
     */ 
    public function cast_story_vote($type = "up", $story_id, $user_id, $reason_id = 0)
    {
        $field_data = array();

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

        $result = $this->insert($field_data); 

    	return ($result !== FALSE) ? TRUE : FALSE;
    }

}