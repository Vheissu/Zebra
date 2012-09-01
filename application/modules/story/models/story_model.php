<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story_model extends MY_Model {

	protected $_table = 'stories';

    protected $has_many    = array('comment', 'topic', 'vote');
    protected $belongs_to  = array('user');

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('story');
        $this->load->helper('topic/topic');
        $this->load->model('user/user_model');
    }

    public function get_popular_stories($limit = 50, $offset = 0)
    {
        $table = $this->db->dbprefix.$this->_table;

        $stories = $this->db->query('
            SELECT '.$table.'.*,
            (('.$table.'.upvotes-1) - ('.$table.'.downvotes) / power(((unix_timestamp(NOW()) - unix_timestamp('.$table.'.created))/60)/60,1.8)) AS rank
            FROM '.$table.' ORDER BY rank DESC LIMIT '.$offset.', '.$limit.''
        );

        return ($stories->num_rows() >= 1) ? $stories->result() : FALSE;  
    }

    public function get_new_stories($limit = 50, $offset = 0)
    {
        $this->db->order_by("created", "DESC");
        $stories = $this->db->get($this->_table, $limit, $offset);

        return ($stories->num_rows() >= 1) ? $stories->result() : FALSE; 
    }

}