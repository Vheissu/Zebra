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

class Admin_Controller extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		if (!is_admin())
		{
			set_flashdata("error", lang('no_permission'));
			redirect('/');
		}
	}

}