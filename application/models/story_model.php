<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story_model extends MY_Model {

	protected $_table = 'stories';

    protected $has_many = array('comment', 'vote');
    protected $has_one  = array('user');

}