<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_flashdata'))
{
    function get_flashdata($key)
    {
        $CI =& get_instance();

        return $CI->session->flashdata($key);
    }
}

if (!function_exists('set_flashdata'))
{
    function set_flashdata($key, $value)
    {
        $CI =& get_instance();

        return $CI->session->set_flashdata($key, $value);
    }
}

if (!function_exists('auth_errors'))
{
    function auth_errors($before = '<p class="error">', $after = '</p>')
    {
        $CI =& get_instance();

        return $CI->wolfauth->auth_errors($before, $after);
    }
}

if (!function_exists('logged_in'))
{
	/**
	 * Logged In
	 *
	 * Helper function for checking if a user is currently
	 * logged in or not.
	 *
	 */
	function logged_in()
	{
        $CI =& get_instance();
        $CI->load->library('wolfauth');

		return $CI->wolfauth->logged_in();
	}
}

if (!function_exists('login'))
{
	/**
	 * Login
	 *
	 * Helper function for logging a user in
	 *
	 * @param string $email - Email address
	 * @param string $password - The password
	 * @param string $redirect_to - Redirect here after login
	 *
	 */
	function login($email, $password, $redirect_to = '')
	{
        $CI =& get_instance();
        $CI->load->library('wolfauth');

		return $CI->wolfauth->login($email, $password, $redirect_to);
	}
}

if (!function_exists('force_login'))
{
	/**
	 * Force Login
	 *
	 * Helper function for logging in as a user
	 * without needing a password.
	 *
	 * @param string $email - Email address
	 *
	 */
	function force_login($email)
	{
        $CI =& get_instance();
        $CI->load->library('wolfauth');

		return $CI->wolfauth->force_login($email);
	}
}

if (!function_exists('logout'))
{
	/**
	 * Logout
	 *
	 * Helper function for logging a
	 * user out and destroying the session.
	 *
	 * @param string $redirect_to - Redirect here after login
	 *
	 */
	function logout($redirect_to = '')
	{
        $CI =& get_instance();
        $CI->load->library('wolfauth');

		return $CI->wolfauth->logout($redirect_to);
	}
}

if (!function_exists('is_admin'))
{
	/**
	 * Is Admin
	 *
	 * Helper function for determining whether
	 * or not the currently logged in user
	 * is deemed to be an administrator.
	 *
	 * @return bool (True if user is an admin or false if not)
	 *
	 */
	function is_admin()
	{
        $CI =& get_instance();
        $CI->load->library('wolfauth');

		return $CI->wolfauth->is_admin();
	}
}

if (!function_exists('user_can'))
{
	/**
	 * User Can
	 *
	 * Helper function for checking if a user
	 * has a particular permission to do something.
	 *
	 * @param string $permission - Permission to check
	 *
	 */
	function user_can($permission)
	{
        $CI =& get_instance();
        $CI->load->library('wolfauth');

		return $CI->wolfauth->user_can($permission);
	}
}

if (!function_exists('get_user'))
{
	/**
	 * Get User
	 *
	 * Helper function for getting a user from the database
	 * if the user exists or not.
	 *
	 * @param string $email - Email address
	 *
	 */
	function get_user($email)
	{
		$CI =& get_instance();
		$CI->load->model('wolfauth_m');

		return $CI->wolfauth_m->get_user($email);
	}
}

if (!function_exists('get_user_by_id'))
{
	/**
	 * Get User By ID
	 *
	 * Helper function for getting a user from the database
	 * by their user ID
	 *
	 * @param int $user_id - User ID
	 *
	 */
	function get_user_by_id($user_id)
	{
		$CI =& get_instance();
		$CI->load->model('wolfauth_m');

		return $CI->wolfauth_m->get_user_by_id($user_id);
	}
}

if (!function_exists('update_user'))
{
	/**
	 * Update User
	 *
	 * Helper function for updating a users info
	 * stored in the database.
	 *
	 * @param array $data - User information
	 * @param array $additional_data - User meta updates
	 *
	 */
	function update_user($data, $additional_data = array())
	{
		$CI =& get_instance();
		$CI->load->model('wolfauth_m');

		return $CI->wolfauth_m->update_user($data, $additional_data);
	}
}