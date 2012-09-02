<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic_model extends MY_Model {

	protected $_table = 'topics';

    protected $has_many  = array('story');

    public function get_story_topics($story_id, $limit = 3)
    {
    	$table  = $this->db->dbprefix.$this->_table;
        $prefix = $this->db->dbprefix;

    	$this->db->select('zebra_topics.name, zebra_topics.slug');
        $this->db->from('stories_topics');
        $this->db->join('zebra_topics', 'zebra_topics.id = zebra_stories_topics.topic_id');
        $this->db->where('zebra_stories.id', $story_id);
        $this->db->limit($limit, 0);

    	$topics = $this->db->get();

    	return ($topics->num_rows() >= 1) ? $topics->result() : FALSE;
    }

}