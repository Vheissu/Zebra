<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->helper('form');
        $this->load->helper('comment/comment');
        $this->load->helper('vote/vote');
        $this->load->library('form_validation');
        $this->load->model('story_model', 'story');
        $this->load->model('comment/comment_model', 'comment');
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

    public function view($story_id, $slug = '')
    {
        // Assign submission info to the global data variable
        $this->data['story']                = $this->story->get_story($story_id);
		$this->data['story']->description	= nl2br($this->data['story']->description);
        $this->data['story']->comment_count = $this->comment->count_story_comments($story_id);


        // A trivial thing, but keeping the slug correct is better for SEO purposes
        if ($slug !== $this->data['story']->slug)
        {
            redirect('story/view/'.$story_id.'/'.$this->data['story']->slug.'', 'location', 301);
        }

        // Load the story template see: themes/zebra/views/story.tpl
        $this->parser->parse('story', $this->data);
    }

    public function comment($story_id)
    {
        if ($this->form_validation->run('comment') !== FALSE)
        {
            // Get the story
            $story = $this->story->get_story($story_id);

            if (logged_in())
            {
                $comment   = $this->input->post('comment');
                $reply_to = $this->input->post('in_reply_to', '0');

                $save = $this->comment->save_comment($story_id, current_user_id(), $reply_to, $comment);

                if ($save)
                {
                    $this->session->set_flashdata('success', lang('comment_success'));
                    redirect('story/view/'.$story->id.'/'.$story->slug.'');
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('comment_login'));
                redirect('story/view/'.$story->id.'/'.$story->slug.'');    
            }
        }    
        
        redirect('story/view/'.$story->id.'/'.$story->slug.'#comments'); 
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
                $text  = nl2br($this->input->post('text', ''));

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