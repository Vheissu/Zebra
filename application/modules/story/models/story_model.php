<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story_model extends MY_Model {

	protected $_table = 'stories';

    protected $has_many    = array('comment', 'topic', 'vote');
    protected $belongs_to  = array('user');

    public function get_stories($limit = 50, $offset = 0)
    {
        $stories = $this->get_all_paged($limit, $offset);   
        
        return $stories;  
    }

}