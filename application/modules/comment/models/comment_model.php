<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends MY_Model {

	protected $_table = 'comments';

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

}