<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

	public function index()
	{
		$this->load->model('story_model', 'story');

		// Get all stories
		$this->data['stories'] = $this->story->get_all();

		$this->parser->parse('stories', $this->data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */