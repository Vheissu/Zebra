<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends MY_Model {

	protected $_table = 'comments';

    protected $has_many    = array('vote');
    protected $belongs_to  = array('user', 'story');

}