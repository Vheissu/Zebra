<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('story_model', 'story');
    }

	public function index()
	{
		// Get all stories
		$this->data['stories'] = $this->story->get_all();

		$this->parser->parse('stories', $this->data);
	}
}

/* End of file story.php */