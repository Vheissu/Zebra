<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends MY_Model {

	protected $_table = 'comments';

    public $before_create = array( 'timestamps' );

    /**
     * Get all comments belonging to a story
     * 
     * @param  [type] $story_id [description]
     * @return [type]
     */
    public function get_comments($story_id)
    {
        $comments = $this->db->get($this->_table);

        return ($comments->num_rows() >= 1) ? $comments->result_array() : FALSE;
    }

    /**
     * Get all popular comments using the ranking formulae that we
     * use for stories as well.
     * 
     * @param  int $limit
     * @param  int $offset
     * @return object or boolean false
     */ 
    public function get_popular_comments($limit = 50, $offset = 0)
    {
        $table = $this->db->dbprefix.$this->_table;

        $comments = $this->db->query('
            SELECT '.$table.'.*,
            (('.$table.'.upvotes-1) - ('.$table.'.downvotes) / power(((unix_timestamp(NOW()) - unix_timestamp('.$table.'.created))/60)/60,1.8)) AS rank
            FROM '.$table.' ORDER BY rank DESC LIMIT '.$offset.', '.$limit.''
        );

        return ($comments->num_rows() >= 1) ? $comments->result() : FALSE;  
    }

    /**
     * Get all newly added comments
     * 
     * @param int $limit
     * @param int $offset
     * @return object or boolean false 
     */ 
    public function get_new_comments($limit = 50, $offset = 0)
    {
        $this->db->order_by("created", "DESC");
        $stories = $this->db->get($this->_table, $limit, $offset);

        return ($stories->num_rows() >= 1) ? $stories->result() : FALSE; 
    }

    /**
     * Saves a comment into the database
     * 
     * @param  integer $story_id The story submission ID
     * @param  integer $user_id  User that owns the comment
     * @param  integer $reply_id In reply to whom?
     * @param  string  $comment  The comment text
     * @return boolean
     */
    public function save_comment($story_id, $user_id, $reply_id, $comment)
    {
		$comment = nl2br($comment);
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

    /**
     * Count the number of comments a story has
     * 
     * @param  integer $story_id The story submission ID
     * @return integer
     */
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
                $upvotes = $upvotes + (1-$result->upvotes);

                if ($result->downvotes !== 0)
                {
                    $downvotes = $downvotes + (1-$result->downvotes); 
                }
            }
        }

        $total = $upvotes - $downvotes;

        return $total;  
    }

    /**
     * A callback function called by this model to add
     * in a created field to every newly added comment.
     * 
     * @param  array $comment
     * @return array
     */ 
    protected function timestamps($comment)
    {
        $this->load->helper('date');

        $comment['created'] = now();

        return $comment;
    }

}