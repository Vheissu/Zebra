<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends MY_Controller {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('date');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('comment/comment_model');
    }

    /**
     * View a list of comments
     * 
     * @param  integer $page Paging offset for DB query
     * @return void
     */
	public function index($limit = 50, $page = 0)
	{
        // Page 1 is technically page zero
        if ($page == 1)
        {
            $page = 0;
        }

		// Get all stories
		$this->data['comments'] = $this->comment_model->get_popular_comments($limit, $page);

        // Load the story template see: themes/zebra/views/comments.tpl
		$this->parser->parse('comments', $this->data);
	}

    /**
     * View a singular comment
     * 
     * @param  integer $comment_id Comment to view
     * @return void
     */
    public function view($comment_id)
    {
        // Assign submission info to the global data variable
        $this->data['comment'] = $this->comment_model->get($comment_id);

        // Load the story template see: themes/zebra/views/comment.tpl
        $this->parser->parse('comment', $this->data);
    }

    /**
     * Get recently added site comments
     * 
     * @param  integer $limit Show this many comments at once
     * @param  integer $page  Pagination offset
     * @return void
     */
    public function new_comments($limit = 50, $page = 0)
    {
        $this->data['current_segment'] = "new";

        // Page 1 is technically page zero
        if ($page == 1)
        {
            $page = 0;
        }

        // Get all stories
        $this->data['comments'] = $this->comments_model->get_new_comments($limit, $page);

        // Load the story template see: themes/zebra/views/comments.tpl
        $this->parser->parse('comments', $this->data); 
    }

    /**
     * Display comments from a particular user
     * 
     * @param  string  $username The username
     * @param  integer $limit    Show this many comments at once
     * @param  integer $page     Pagination offset
     * @return void
     */
    public function user_comments($username, $limit = 50, $page = 0)
    {
        // Page 1 is technically page zero
        if ($page == 1)
        {
            $page = 0;
        }

        $user = get_user($username);
        $user_id = $user->row('id');

        // Get all stories
        $this->data['comments'] = $this->comments_model->get_user_comments($limit, $page, $user_id);

        // Load the story template see: themes/zebra/views/comments.tpl
        $this->parser->parse('comments', $this->data);     
    }
}

/* End of file comment.php */