<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->helper('form');
        $this->load->helper('vote/vote');
        $this->load->library('form_validation');
        $this->load->model('story_model', 'story');
    }

	public function index($page = 0)
	{
        // Page 1 is technically page zero
        if ($page == 1)
        {
            $page = 0;
        }

		// Get all stories
		$this->data['stories'] = $this->story->get_popular_stories(50, $page);

		$this->parser->parse('stories', $this->data);
	}

    public function view($story_id)
    {
        $this->data['story'] = $this->story->get_story($story_id);

        $this->parser->parse('story', $this->data);
    }

    public function submit()
    {
        $this->data['page']['title']   = "Submit new news story";
        $this->data['current_segment'] = "submit";

        if ($this->form_validation->run('story') !== FALSE)
        {
            if (logged_in())
            {
                $title = $this->input->post('title');
                $slug  = url_title($this->input->post('title'), '-', TRUE);
                $link  = $this->input->post('link', '');
                $text  = $this->input->post('text', '');

                $field_data = array(
                    'user_id'       => current_user_id(),
                    'title'         => $title,
                    'slug'          => $slug,
                    'external_link' => $link,
                    'description'   => $text,
                    'upvotes'       => 1
                );

                $insert = $this->story->insert($field_data);

                if ($insert)
                {
                    $this->load->model('user/user_model', 'user');
                    $this->user->add_story_vote_record(current_user_id(), $this->db->insert_id());
                    $this->user->increment_submission_count(current_user_id());
                    $this->session->set_flashdata('success', lang('submission_success'));
                    redirect('stories/new');
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('submission_login'));
                redirect(base_url());
            }    
        }
        else
        {
            $this->parser->parse('add', $this->data);    
        }   
    }

    public function new_stories($page = 0)
    {
        $this->data['current_segment'] = "new";

        // Page 1 is technically page zero
        if ($page == 1)
        {
            $page = 0;
        }

        // Get all stories
        $this->data['stories'] = $this->story->get_new_stories(50, $page);

        $this->parser->parse('stories', $this->data); 
    }

    public function user_stories($username, $page = 0)
    {
        // Page 1 is technically page zero
        if ($page == 1)
        {
            $page = 0;
        }

        $user = get_user($username);
        $user_id = $user->row('id');

        // Get all stories
        $this->data['stories'] = $this->story->get_user_stories(50, $page, $user_id);

        $this->parser->parse('stories', $this->data);     
    }
}

/* End of file story.php */