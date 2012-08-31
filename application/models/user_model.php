<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {

	protected $_table = 'users';

    protected $has_many = array('story', 'comment', 'vote');

}