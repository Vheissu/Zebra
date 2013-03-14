<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Story extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('story/story_model', 'story_m');
    }

    // Allows a story to be fetched
    public function story_get()
    {
        if (!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        // Load in a story
        $story = $this->story_m->get_story($this->get('id'));

        // If we have a story
        if ($story)
        {
            // Return the story
            $this->response($story, 200);
        }
        else
        {
            // Story does not exist
            $this->response(array('error' => 'Story could not be found'), 404);
        }
    }

    public function stories_get()
    {
        $type    = (string) (isset($this->get('type'))) ? $this->get('type') : FALSE;
        $total   = (int) (isset($this->get('total'))) ? $this->get('total') : 50;
        $offset  = (int) (isset($this->get('offset'))) ? $this->get('offset') : 0;
        $user_id = (int) (isset($this->get('user_id'))) ? $this->get('user_id') : 0;

        // Prevent totals from exceeding 100
        if ($total > 100)
        {
            $total = 100;
        }

        if ($type == 'popular')
        {
            $stories = $this->story_m->get_popular_stories($total, $offset);
        }
        elseif ($type == 'new')
        {
            $stories = $this->story_m->get_new_stories($total, $offset);
        }
        elseif ($type == 'user')
        {
            $stories = $this->story_m->get_user_stories($total, $offset, $user_id);
        }
        else
        {
            $stories = $this->story_m->get_popular_stories($total, $offset);    
        }

        // If we have a story
        if ($stories)
        {
            // Return the stories
            $this->response($stories, 200);
        }
        else
        {
            // No stories found
            $this->response(array('error' => 'There are no stories'), 404);
        }
    }

    // A new story submission
    public function story_post()
    {
        $title = $this->post('title');
        $text  = $this->post('text');
        $url   = $this->post('url');

        $return = array('status' => '');

        $this->response();
    }
}