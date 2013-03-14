<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Story extends REST_Controller
{
    // Allows a story to be fetched
    public function story_get()
    {
        if (!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

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

    // A new story submission
    public function story_post()
    {
        $title = $this->post('title');
        $text  = $this->post('text');
        $url   = $this->post('url');

        $return = array('status' => '');

        $this->response()
    }
    
    function user_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->get('limit') );
        $users = array(
			array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
			array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
		);
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}