<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic_model extends MY_Model {

	protected $_table = 'topics';

    protected $has_many  = array('story');

}