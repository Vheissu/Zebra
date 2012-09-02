<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller {

    public function __construct()
    {
        // Story helper allows us to perform votes, etc
        $this->load->helper('story/story');
    }

	public function index()
	{
		die('Access Denied');
	}

    /**
     * Vote
     * 
     * Performs an upvote or downvote for a particular
     * story based on its ID.
     * 
     */ 
    public function vote()
    {
        // Result to return
        $result = 'Invalid action or URL parameter(s).';

        if ($this->input->post('action'))
        {
            $action   = $this->input->post('action');
            $story_id = $this->input->post('story_id');
            $reason   = $this->input->post('downvote_reason');

            switch ($action)
            {
                case 'upvote':
                    if (cast_vote('up', $story_id))
                    {
                        $result = "success|upvote|".$story_id."";
                    }
                break;

                case 'downvote':
                    if (cast_vote('down', $story_id, $reason))
                    {
                        $result = "success|downvote|".$story_id."";
                    }
                break;
            }
        }

        die ($result);
    }
}

/* End of file ajax.php */