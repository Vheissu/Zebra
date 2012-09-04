<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->helper('vote/vote');
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

    public function submit()
    {
        $this->data['current_segment'] = "submit";

        if (!$this->input->post())
        {
            $this->parser->parse('add', $this->data);
        }
        else
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
                    'upvotes'       => 1,
                    'created'       => time()
                );

                $insert = $this->story->insert($field_data);

                if ($insert)
                {
                    $this->load->model('user/user_model', 'user');
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