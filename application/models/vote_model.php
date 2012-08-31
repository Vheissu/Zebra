<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vote_model extends MY_Model {

	protected $_table = 'votes';

    protected $has_one  = array('user', 'comment', 'story');

    public function user_has_upvoted($story_id, $user_id)
    {
    	
    }

    public function user_has_downvoted($story_id, $user_id)
    {
        
    }

}