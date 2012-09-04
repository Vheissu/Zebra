<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('user');
        $this->load->helper('story/story');
        $this->load->helper('vote/vote');
        $this->load->library('form_validation');
        $this->load->library('wolfauth');
    }

	public function index()
	{

	}

    public function view($username)
    {
        $user = get_user($username);

        if ($user)
        {
            $this->data['page']['title'] = "Profile of ".$user->row('username')."";

            $this->data['user'] = array(
                'username'            => $user->row('username'),
                'email'               => $user->row('email'),
                'register_date'       => $user->row('register_date'),
                'karma'               => get_user_karma($user->row('id')),
                'average_karma'       => calculate_average_karma($user->row('id')),
                'average_submissions' => calculate_average_submissions($user->row('id')),
                'meta'                => $this->wolfauth->get_user_meta($user->row('id'))
            );

            $this->parser->parse('profile', $this->data);
        }
    }

    public function login()
    {
        $this->data['page']['title'] = "Login";

        if ($this->form_validation->run('login') !== FALSE)
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if ($this->wolfauth->login($username, $password))
            {
                redirect(base_url());
            }
            else
            {
                $this->session->set_flashdata('error', $this->wolfauth->auth_errors());
                $this->parser->parse('login', $this->data);        
            }
        }
        else
        {
            $this->parser->parse('login', $this->data);   
        }
    }

    public function register()
    {
        $this->data['page']['title'] = "Register";

        if ($this->form_validation->run('register') !== FALSE)
        {
            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $this->input->post('password');

            if ($this->wolfauth->add_user($username, $email, $password))
            {
                $this->session->set_flashdata('success', lang('register_success'));
                $this->wolfauth->force_login($username);
                redirect(base_url());
            }
            else
            {
                $this->session->set_flashdata('error', $this->wolfauth->auth_errors());
                $this->parser->parse('register', $this->data);    
            }
        }
        else
        {
            $this->parser->parse('register', $this->data);
        }
    }

    public function logout()
    {
        $this->session->set_flashdata('success', lang('logout_success'));
        $this->wolfauth->logout(base_url());
    }
}

/* End of file user.php */