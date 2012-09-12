<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->model('story/story_model');
    }

	public function index()
	{
        $this->login();
	}

    public function login()
    {
        if ($this->form_validation->run('login') !== FALSE)
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if ($this->wolfauth->login($username, $password))
            {
                redirect('admin/dashboard');
            }
            else
            {
                $this->session->set_flashdata('error', $this->wolfauth->auth_errors());
                $this->parser->parse('login', $this->data);        
            }
        }
        else
        {
            if (logged_in() && !is_admin())
            {
                set_flashdata("error", lang('no_permission'));
                redirect('/');
            }

            if (!logged_in())
            {
                $this->parser->parse('admin/login.tpl', $this->data);

                return;
            }

            // If we are logged in and admin, redirect
            if (logged_in() && is_admin())
            {
                redirect('admin/dashboard');
            }
        }
    }

    public function dashboard()
    {
        if (is_admin())
        {
            $obj = new stdClass;
            $obj->last_submission   = $this->story_model->last_submission();
            $obj->total_submissions = $this->story_model->total_stories();

            $this->data['analytics'] = $obj;
            $this->parser->parse('admin/index.tpl', $this->data);  
        }
        else
        {
            set_flashdata("error", lang('no_permission'));
            redirect('admin/login');    
        }
    }
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */