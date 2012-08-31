<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story_model extends MY_Model {

	protected $_table = 'stories';

    protected $has_many    = array('comment', 'topic', 'vote');
    protected $belongs_to  = array('user');

    public function get_popular_stories($limit = 50, $offset = 0)
    {
        $stories = $this->db->query('
            SELECT zebra_stories.*,
            ((zebra_stories.upvotes-1) - (zebra_stories.downvotes) / power(((unix_timestamp(NOW()) - unix_timestamp(zebra_stories.created))/60)/60,1.8)) AS rank
            FROM zebra_stories ORDER BY rank DESC'
        );

        return $stories->result();  
    }

}