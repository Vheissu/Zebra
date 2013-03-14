<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Story extends REST_Controller
{
    // Allows a story to be fetched
    public function story_get()
    {
        $this->load->model('story/story_model', 'story_m');

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

    public function debug_get()
    {
        var_dump($this->request->body);
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