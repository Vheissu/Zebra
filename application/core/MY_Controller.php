<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->data['site'] = array(
			'name'        => 'Zebra',
			'description' => 'An open source social news application'
		);

		$this->parser->set_theme('zebra');
	}

}