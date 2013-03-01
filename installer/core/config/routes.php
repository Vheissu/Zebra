<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller']  = 'installer';
$route['step/(:num)'] = "installer/index/$1"; 


$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */