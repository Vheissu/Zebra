<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Installer extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->load->helper('form');
    }

	public function index($step = 1)
	{
		$this->parser->parse('step1.tpl');
	}
}

/* End of file installer.php */