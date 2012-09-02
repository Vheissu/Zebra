<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('wolfauth');
    }

	public function index($page = 0)
	{

	}

    public function login()
    {
        $this->parser->parse('login.tpl', $this->data);
    }
}

/* End of file story.php */