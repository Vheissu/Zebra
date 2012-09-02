<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic_model extends MY_Model {

	protected $_table = 'topics';

    protected $has_many  = array('story');

    public function get_story_topics($story_id, $limit = 3)
    {
    	$table  = $this->db->dbprefix.$this->_table;
        $prefix = $this->db->dbprefix;

    	$this->db->select('t.name, t.slug');
        $this->db->from('stories_topics st');
        $this->db->where('st.story_id', $story_id);
        $this->db->join('topics t', 't.id = st.topic_id');
        $this->db->limit($limit, 0);

    	$topics = $this->db->get();

    	return ($topics->num_rows() >= 1) ? $topics->result() : FALSE;
    }

}