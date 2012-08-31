<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller']  = 'story';

// Story routes
$route['stories']     = 'story';
$route['stories/(:num)'] = 'story/index/$1';
$route['stories/new'] = 'story/new';
$route['stories/recently-popular'] = 'story/recently_popular';
$route['story/(:num)/(:any)'] = 'story/view/$1/$2';
$route['story/submit'] = 'story/new';

// Comment routes
$route['comments/latest'] = 'comment/latest';


$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */