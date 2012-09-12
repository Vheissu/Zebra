<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

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
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */