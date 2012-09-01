<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic_model extends MY_Model {

	protected $_table = 'topics';

    protected $has_many  = array('story');

    public function get_story_topics($story_id, $limit = 3)
    {
    	$table = $this->db->dbprefix.$this->_table;

    	$this->db->select("".$table.".name, ".$table.".slug");
    	$this->db->join(''.$this->db->dbprefix.'stories_topics', ''.$this->db->dbprefix.'stories_topics.story_id = '.$table.'.id ');
        $this->db->where($table.'.id', $story_id);

    	$topics = $this->db->get($this->_table, $limit, 0);

    	return ($topics->num_rows() >= 1) ? $topics->result() : FALSE;
    }

}