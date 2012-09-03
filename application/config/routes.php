<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller']  = 'story';

$route['login']    = "user/login";
$route['register'] = "user/register";
$route['logout']   = "user/logout";

$route['user/(:any)/stories/(:num)'] = "story/user_stories/$1/$2";
$route['user/(:any)/stories'] = "story/user_stories/$1";
$route['user/(:any)'] = "user/view/$1";

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