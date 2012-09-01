<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller']  = 'story';

// Story routes
$route['stories']     = 'story';
$route['stories/new'] = 'story/new_stories';
$route['stories/new/(:num)'] = 'story/new_stories/$1';
$route['stories/(:num)'] = 'story/index/$1';
$route['story/(:num)/(:any)'] = 'story/view/$1/$2';
$route['story/submit'] = 'story/submit';

// Comment routes
$route['comments/latest'] = 'comment/latest';


$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */