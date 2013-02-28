<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('story/story');
        $this->load->helper('user/user');
        $this->load->helper('vote/vote');
    }

	public function index()
	{
		die('Access Denied');
	}

    /**
     * Story Vote
     * 
     * Performs an upvote or downvote for a particular
     * story based on its ID.
     * 
     */ 
    public function story_vote()
    {
        // Result to return
        $result = 'Invalid action or URL parameter(s).';

        // Make sure we are logged in first
        if (logged_in())
        {
            if ($this->input->post('action'))
            {
                $action   = $this->input->post('action');
                $story_id = $this->input->post('story_id');
                $reason   = $this->input->post('downvote_reason');

                switch ($action)
                {
                    case 'upvote':
                        if (story_vote('up', $story_id))
                        {
                            $result = "success|upvote|".$story_id."";
                        }
                    break;

                    case 'downvote':
                        if (story_vote('down', $story_id, $reason))
                        {
                            $result = "success|downvote|".$story_id."";
                        }
                    break;
                }
            }
        }
        else
        {
            $result = 'error|Please login to vote';
        }

        $this->output->set_status_header(200);
        $this->output->set_output($result);
    }

    /**
     * Comment Vote
     * 
     * Performs an upvote or downvote for a particular
     * comment based on its ID.
     * 
     */ 
    public function comment_vote()
    {
        // Story helper allows us to perform votes, etc
        $this->load->helper('comment/comment');

        // User helper for user related things
        $this->load->helper('user/user');

        // Result to return
        $result = 'Invalid action or URL parameter(s).';

        // Make sure we are logged in first
        if (logged_in())
        {
            if ($this->input->post('action'))
            {
                $action     = $this->input->post('action');
                $comment_id = $this->input->post('comment_id');
                $reason     = $this->input->post('downvote_reason');

                switch ($action)
                {
                    case 'upvote':
                        if (comment_vote('up', $comment_id))
                        {
                            $result = "success|upvote|".$comment_id."";
                        }
                    break;

                    case 'downvote':
                        if (comment_vote('down', $comment_id, $reason))
                        {
                            $result = "success|downvote|".$story_id."";
                        }
                    break;
                }
            }
        }
        else
        {
            $result = 'You must be logged in to vote.';  
        }

        $this->output->set_status_header(200);
        $this->output->set_output($result);
    }
}

/* End of file ajax.php */