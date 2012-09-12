<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->model('story/story_model');
    }

    public function index()
    {
        $obj = new stdClass;
        $obj->last_submission   = $this->story_model->last_submission();
        $obj->total_submissions = $this->story_model->total_stories();

        $this->data['analytics'] = $obj;
        $this->parser->parse('admin/index.tpl', $this->data);  
    }

}