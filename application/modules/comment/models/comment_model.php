<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends MY_Model {

	protected $_table = 'comments';

    public $before_create = array( 'timestamps' );

    public function get_comments($story_id)
    {
        return $this->get_many_by('story_id', $story_id);
    }

    public function save_comment($story_id, $user_id, $reply_id, $comment)
    {
        $comment_id =  $this->insert(array(
            'user_id'   => $user_id,
            'story_id'  => $story_id,
            'parent_id' => $reply_id,
            'comment'   => $comment,
            'upvotes'   => 1
        ));

        if ($comment_id)
        {
            $this->load->model('user/user_model', 'user');
            $this->user->add_comment_vote_record(current_user_id(), $comment_id);

            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function count_story_comments($story_id)
    {
        return $this->count_by('story_id', $story_id);
    }

    public function get_user_comment_votes($user_id)
    {
        $this->db->select('upvotes, downvotes');
        $this->db->where('user_id', $user_id);
        $comments = $this->db->get($this->_table);

        $upvotes   = 0;
        $downvotes = 0;

        if ($comments->num_rows() >= 1)
        {
            foreach ($comments->result() AS $result)
            {
                $upvotes = $upvotes + $result->upvotes;

                if ($result->downvotes !== 0)
                {
                    $downvotes = $downvotes + $result->downvotes; 
                }
            }
        }

        $total = $upvotes - $downvotes;

        return $total;  
    }

    /**
     * Timestamps
     * 
     * A callback function called by this model to add
     * in a created field to every newly added comment.
     * 
     * @param array $comment
     * 
     * @return array
     * 
     */ 
    protected function timestamps($comment)
    {
        $this->load->helper('date');

        $comment['created'] = now();

        return $comment;
    }

}