<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_user_karma($user_id = 0)
{
    $CI =& get_instance();
    $CI->load->model('user/user_model', 'user');

    if ($user_id == 0)
    {
        $user_id = current_user_id();
    }

    return $CI->user->get_user_karma($user_id);
}