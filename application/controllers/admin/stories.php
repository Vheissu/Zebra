<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stories extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->helper('story/story');
        $this->load->helper('comment/comment');
        $this->load->model('story/story_model');
        $this->load->model('comment/comment_model');
    }

    public function index()
    {
        $page  = $this->uri->segment(3, 0);
        $limit = $this->uri->segment(4, 50);

        $this->data['stories'] = $this->story_model->get_new_stories($limit, $page);

        $this->parser->parse('admin/stories.tpl', $this->data);  
    }

}