<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('wolfauth');
    }

	public function index()
	{

	}

    public function login()
    {
        if (!$this->input->post())
        {
            $this->parser->parse('login.tpl', $this->data);
        }
        else
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if ($this->wolfauth->login($username, $password))
            {
                redirect(base_url());
            }
        }
    }

    public function register()
    {
        if (!$this->input->post())
        {
            $this->parser->parse('register.tpl', $this->data);
        }
        else
        {
            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $this->input->post('password');

            if ($this->wolfauth->add_user($username, $email, $password))
            {
                $this->session->set_flashdata('success', lang('register_success'));
                redirect(base_url());
            }
        }
    }
}

/* End of file story.php */