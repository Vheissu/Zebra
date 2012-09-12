<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story_model extends MY_Model {

	protected $_table = 'stories';

    public $before_create = array( 'timestamps' );

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('story/story');
        $this->load->model('user/user_model');
    }

    /**
     * Get one particular story submission via an ID
     * 
     * @param int $story_id
     * @return object or boolean false
     */ 
    public function get_story($story_id)
    {
        $this->db->cache_on();
        return $this->get($story_id);
    }

    /**
     * Get all popular stories using the ranking formulae.
     * 
     * @param int $limit
     * @param int $offset
     * @return object or boolean false
     */ 
    public function get_popular_stories($limit = 50, $offset = 0)
    {
        $this->db->cache_on();
        $table = $this->db->dbprefix.$this->_table;

        $stories = $this->db->query('
            SELECT '.$table.'.*,
            (('.$table.'.upvotes-1) - ('.$table.'.downvotes) / power(((unix_timestamp(NOW()) - unix_timestamp('.$table.'.created))/60)/60,1.8)) AS rank
            FROM '.$table.' ORDER BY rank DESC LIMIT '.$offset.', '.$limit.''
        );

        return ($stories->num_rows() >= 1) ? $stories->result() : FALSE;  
    }

    /**
     * Get all newly added stories
     * 
     * @param int $limit
     * @param int $offset
     * @return object or boolean false 
     */ 
    public function get_new_stories($limit = 50, $offset = 0)
    {
        $this->db->cache_on();
        $this->db->order_by("created", "DESC");
        $stories = $this->db->get($this->_table, $limit, $offset);

        return ($stories->num_rows() >= 1) ? $stories->result() : FALSE; 
    }

    /**
     * Get all stories by a particular user with 
     * support for limit and page values
     * 
     * @param int $limit
     * @param int $offset
     * @param int $user_id
     * @return object or boolean false  
     */ 
    public function get_user_stories($limit = 50, $offset = 0, $user_id)
    {
        $this->db->cache_on();
        $this->db->order_by("created", "DESC");
        $this->db->where('user_id', $user_id);
        $stories = $this->db->get($this->_table, $limit, $offset);

        return ($stories->num_rows() >= 1) ? $stories->result() : FALSE; 
    }

    /**
     * Gets the total number of votes with downvotes
     * subtracted from the final score.
     * 
     * @param int $user_id
     * @return int
     */ 
    public function get_user_story_votes($user_id)
    {
        $this->db->cache_on();
        $this->db->select('upvotes, downvotes');
        $this->db->where('user_id', $user_id);
        $stories = $this->db->get($this->_table);

        $upvotes   = 0;
        $downvotes = 0;

        if ($stories->num_rows() >= 1)
        {
            foreach ($stories->result() AS $result)
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
     * Return the number of stories in the stories table
     * @return integer The number of stories in the database
     */
    public function total_stories()
    {
        $this->db->cache_on();
        return $this->db->count_all_results('stories');
    }

    /**
     * Return the last submission submitted to the site
     * 
     * @return object The submission row from the database
     */
    public function last_submission()
    {
        $this->db->cache_on();
        $this->db->order_by("created", "DESC");
        $story = $this->db->get($this->_table, 1, 0);

        return ($story->num_rows() == 1) ? $story->row() : FALSE; 
    }

    /**
     * A callback function called by this model to add
     * in a created field to every newly added story.
     * 
     * @param array $story
     * @return array
     */ 
    protected function timestamps($story)
    {
        $this->load->helper('date');

        $story['created'] = now();

        return $story;
    }

}