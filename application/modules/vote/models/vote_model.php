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
    			'user_id'   => $user_id,
    			'story_id'  => $story_id,
    			'vote_type' => 'upvote'	
    		);
    	}
    	elseif ($type == 'down')
    	{
    		$field_data = array(
    			'user_id'    => $user_id,
    			'story_id'   => $story_id,
    			'reason_id'  => $reason_id,
    			'vote_type'  => 'downvote'	
    		);
    	}

        $result = $this->insert($field_data);

        if ($result)
        {
            if ($type == 'up')
            {
                $this->db->where('id', $story_id);
                $this->db->set('upvotes', 'upvotes + 1', FALSE);
                $this->db->update('stories');
            }
            elseif ($type == 'down')
            {
                $this->db->where('id', $story_id);
                $this->db->set('downvotes', 'downvotes + 1', FALSE);
                $this->db->update('stories');
            }
        } 

    	return ($result !== FALSE) ? TRUE : FALSE;
    }

    /**
     * Rewind Story Vote
     * 
     * Will remove a vote from a story as well as voting
     * history in an instance where a user changes their
     * vote from an up to a down or vice-versa
     * 
     * @access public
     * @param string $direction
     * @param int $story_id
     * @return string
     * 
     */
    public function rewind_story_vote($type = "up", $story_id, $user_id)
    {
        $field_data = array();

        if ($type == 'up')
        {
            $this->db->where('user_id', $user_id);
            $this->db->where('story_id', $story_id);
            $this->db->where('vote_type', 'upvote');
        }
        elseif ($type == 'down')
        {
            $this->db->where('user_id', $user_id);
            $this->db->where('story_id', $story_id);
            $this->db->where('vote_type', 'downvote');
        }

        $result = $this->db->delete($this->_table);

        if ($result)
        {
            if ($type == 'up')
            {
                $this->db->where('id', $story_id);
                $this->db->set('upvotes', 'upvotes - 1', FALSE);
                $this->db->update('stories');
            }
            elseif ($type == 'down')
            {
                $this->db->where('id', $story_id);
                $this->db->set('downvotes', 'downvotes - 1', FALSE);
                $this->db->update('stories');
            }
        } 

        return ($result !== FALSE) ? TRUE : FALSE;
    }

    /**
     * Cast Comment Vote
     * 
     * Will insert a vote record into the votes table
     * if a user votes up or down a particular comment
     * 
     * @access public
     * @param string $type
     * @param int $comment_id
     * @param int $user_id
     * @param int $reason_id
     * 
     * @return boolean
     * 
     */ 
    public function cast_comment_vote($type = "up", $comment_id, $user_id, $reason_id = 0)
    {
        $field_data = array();

        if ($type == 'up')
        {
            $field_data = array(
                'user_id'    => $user_id,
                'comment_id' => $comment_id,
                'vote_type'  => 'upvote' 
            );
        }
        elseif ($type == 'down')
        {
            $field_data = array(
                'user_id'    => $user_id,
                'comment_id' => $comment_id,
                'reason_id'  => $reason_id,
                'vote_type'  => 'downvote'  
            );
        }

        $result = $this->insert($field_data);

        if ($result)
        {
            if ($type == 'up')
            {
                $this->db->where('id', $comment_id);
                $this->db->set('upvotes', 'upvotes + 1', FALSE);
                $this->db->update('comments');
            }
            elseif ($type == 'down')
            {
                $this->db->where('id', $comment_id);
                $this->db->set('downvotes', 'downvotes + 1', FALSE);
                $this->db->update('comments');
            }
        } 

        return ($result !== FALSE) ? TRUE : FALSE;
    }

    /**
     * Rewind Comment Vote
     * 
     * Will remove a vote from a comment as well as voting
     * history in an instance where a user changes their
     * vote from an up to a down or vice-versa
     * 
     * @access public
     * @param string $direction
     * @param int $comment_id
     * @return string
     * 
     */
    public function rewind_comment_vote($type = "up", $comment_id, $user_id)
    {
        $field_data = array();

        if ($type == 'up')
        {
            $this->db->where('user_id', $user_id);
            $this->db->where('comment_id', $comment_id);
            $this->db->where('vote_type', 'upvote');
        }
        elseif ($type == 'down')
        {
            $this->db->where('user_id', $user_id);
            $this->db->where('comment_id', $comment_id);
            $this->db->where('vote_type', 'downvote');
        }

        $result = $this->db->delete($this->_table);

        if ($result)
        {
            if ($type == 'up')
            {
                $this->db->where('id', $comment_id);
                $this->db->set('upvotes', 'upvotes - 1', FALSE);
                $this->db->update('comments');
            }
            elseif ($type == 'down')
            {
                $this->db->where('id', $comment_id);
                $this->db->set('downvotes', 'downvotes - 1', FALSE);
                $this->db->update('comments');
            }
        } 

        return ($result !== FALSE) ? TRUE : FALSE;
    }

}