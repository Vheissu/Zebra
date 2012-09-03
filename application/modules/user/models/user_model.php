<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {

	protected $_table = 'users';

    public function get_user_karma($user_id)
    {
        $this->load->model('comment/comment_model', 'comment');
        $this->load->model('story/story_model', 'story');

        $story_votes   = $this->story->get_user_story_votes($user_id);
        $comment_votes = $this->comment->get_user_comment_votes($user_id);

        return $story_votes + $comment_votes;
    }

    public function get_users($limit = 50, $offset = 0)
    {
        
    }

    /**
     * Increment Submission Count
     * 
     * Increments a users submission count.
     * 
     * @param int $user_id
     * 
     */ 
    public function increment_submission_count($user_id)
    {
        $submission_count = $this->has_submission_field($user_id);

        if ($submission_count !== FALSE)
        {
            $this->db->where('umeta_key', 'submissions');
            $this->db->where('user_id', $user_id);
            $this->db->update('user_meta', array('umeta_value' => $submission_count + 1));
        }
        else
        {
            $this->db->insert('user_meta', array('user_id' => $user_id, 'umeta_key' => 'submissions', 'umeta_value' => 1));
        }
    }

    /**
     * Decrement Submission Count
     * 
     * Decrements a users submission count.
     * 
     * @param int $user_id
     * 
     */ 
    public function decrement_submission_count($user_id)
    {
        $submission_count = $this->has_submission_field($user_id);

        if ($submission_count !== FALSE)
        {
            if ($submission_count > 0)
            {
                $submission_count = $submission_count - 1;
            }
            
            $this->db->where('umeta_key', 'submissions');
            $this->db->where('user_id', $user_id);
            $this->db->update('user_meta', array('umeta_value' => $submission_count));
        }
        else
        {
            $this->db->insert('user_meta', array('user_id' => $user_id, 'umeta_key' => 'submissions', 'umeta_value' => 0));   
        }
    }

    /**
     * Has Submission Field
     * 
     * Checks if a user has a submissions field in the user_meta
     * table and if so, returns the value.
     * 
     * @param int $user_id
     * 
     */ 
    private function has_submission_field($user_id)
    {
        $this->db->where('umeta_key', 'submissions');
        $this->db->where('user_id', $user_id);

        $result = $this->db->get('user_meta');

        return ($result->num_rows() == 1) ? $result->umeta_value : FALSE;
    }

}