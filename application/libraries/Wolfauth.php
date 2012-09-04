<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wolfauth {

	public $CI;

	// An array of valid user permissions
	protected $_permissions = array();

	protected $_messages = array();
	protected $_errors   = array();
	protected $_config   = array();

	public function __construct()
	{
		$this->CI =& get_instance();

		// Load needed Codeigniter Goodness
		$this->CI->load->database();
        $this->CI->load->library('session');
        $this->CI->load->helper('session');
		$this->CI->load->helper('cookie');
		$this->CI->load->helper('url');
		$this->CI->load->helper('wolfauth');
		$this->CI->load->library('email');

		$this->CI->load->model('wolfauth_m');
		$this->CI->load->model('wolfauth_roles_m', 'roles_m');
		$this->CI->load->model('wolfauth_permissions_m', 'permissions_m');

        // Empty out any messages/errors
        $this->_messages = array();
        $this->_errors   = array();

		// Load the config file
		$this->CI->config->load('wolfauth');

		// Store the config options locally
		$this->_config = $this->CI->config->item('wolfauth');

		// Get permissions for the current user ID (if any)
		$this->_populate_permissions();

		// Do we remember this user and want to log them in?
		$this->_check_remember_me();
	}

	/**
	 * User ID
	 *
	 * Returns user ID of currently logged in user
	 *
	 * @access public
	 * @return mixed (user ID on success or false on failure)
	 *
	 */
	public function user_id()
	{
		return $this->CI->session->userdata('user_id');
	}

	/**
	 * User Role
	 *
	 * Return the current user role name
	 *
	 * @access public
	 * @return mixed (role name on success or false on failure)
	 *
	 */
	public function user_role()
	{
		return $this->CI->session->userdata('role_name');
	}

	/**
	 * Is Admin
	 *
	 * Is the currently logged in user an admin?
	 *
	 * @access public
	 * @return bool - True if user is an admin, false if not
	 *
	 */
	public function is_admin()
	{
		$role = $this->user_role();

		return (in_array($role, $this->_config['roles.admin'])) ? TRUE : FALSE;
	}

	/**
	 * Logged In
	 *
	 * Is there a currently logged in user?
	 * Hellooooo?
	 *
	 * @access public
	 * @return bool - True on success or False on failure
	 *
	 */
	public function logged_in()
	{
		return (!$this->user_id()) ? FALSE : TRUE;
	}

	/**
	 * Login
	 *
	 * Log a user in via their username
	 *
	 * @param string $username - Username
	 * @param string $password - Password unencrypted
	 * @param string $redirect_to - The location to redirect to
	 *
	 */
	public function login($username, $password, $redirect_to = '')
	{
		// Find the user
		$user = get_user(strtolower($username));

		// If we have a user
		if ($user)
		{
			// Passwords match
			if ($this->hash($password) === $user->row('password'))
			{
				$this->CI->session->set_userdata(array(
					'user_id'       => $user->row('id'),
					'username'      => $user->row('username'),
                    'nice_username' => $user->row('nice_username'),
					'role_id'       => $user->row('role_id'),
					'role_name'     => $user->row('role_name')
				));

				// Remember me?
				if ($this->CI->input->post('remember_me') == 'yes')
				{
					$this->_set_remember_me($user->row('id'));
				}

				// If we want to redirect somewhere
				if ($redirect_to !== '')
				{
					redirect($redirect_to, 'refresh');
				}

				return TRUE;
			}
			else
			{
                $this->set_error('Unknown username and or incorrect password.');
				return FALSE;
			}
		}
        else
        {
            $this->set_error('Unknown username and or incorrect password.');
        }

		return FALSE;
	}

	/**
	 * Force Login
	 *
	 * Forcefully login as any user without needing
	 * a password or any other details.
	 *
	 * @access public
	 * @param string $username - Username
	 * @return bool - True on success or False on failure
	 *
	 */
	public function force_login($username)
	{
		$user = get_user($username);

		if ($user)
		{
			$this->CI->session->set_userdata(array(
				'user_id'       => $user->row('id'),
				'username'      => $user->row('username'),
                'nice_username' => $user->row('nice_username'),
				'role_id'       => $user->row('role_id'),
				'role_name'     => $user->row('role_name')
			));

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Logout
	 *
	 * That's right, it logs you out.
	 *
	 * @access public
	 * @param string $redirect_to - Redirect after logging out
	 * @return bool - True on success or False on failure
	 *
	 */
	public function logout($redirect_to = '')
	{
		$user_id = $this->user_id();

		delete_cookie('rememberme');

		$user_data = array(
			'id'          => $user_id,
			'remember_me' => ''
		);

        $this->CI->session->set_userdata(array(
            'user_id'       => FALSE,
            'username'      => FALSE,
            'nice_username' => FALSE,
            'role_id'       => FALSE,
            'role_name'     => FALSE
        ));

		// Update the user
		if (update_user($user_data))
		{
			// If we want to redirect somewhere
			if ($redirect_to !== '')
			{
				redirect($redirect_to, 'refresh');
			}

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Add User
	 *
	 * Insert a new user into the users table.
	 *
	 * @access public
	 * @param $username
	 * @param $email
	 * @param $password
	 * @param $role_id
	 * @param $additional_data
	 * @return mixed
	 *
	 */
	public function add_user($username, $email, $password, $role_id = 1, $status = "active", $additional_data = array())
	{
		// Hash the password ASAP
		$password = $this->hash($password);

        // Lowercase the username
        $username = strtolower($username);

        // Create a nice username
        $nicename = ucfirst($username);

		// Call the add user function and return the result
		return $this->CI->wolfauth_m->add_user($username, $nicename, $email, $password, $role_id, $status, $additional_data);
	}

    /**
     * User Can
     *
     * Does the user have a permission to do something?
     *
     * @access public
     * @param $permission
     * @return bool
     *
     */
    public function user_can($permission)
    {
    	// Refresh permissions if we somehow logged in but didn't update
    	$this->_populate_permissions();

        // Is the permission in our array of allowed role permissions?
        return (in_array($permission, $this->_permissions)) ? TRUE : FALSE;
    }

	/**
	 * Has Role
	 *
	 * Does a particular user have a role?
	 *
     * @access public
	 * @param $user_id
     * @return bool
	 *
	 */
	public function has_role($user_id, $role_id)
	{
		$user = get_user_by_id($user_id);

		if ($user)
		{
			if ($role_id == $user->row('role_id'))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

    /**
     * Add Role
     *
     * Add a new role into the roles table
     *
     * @access public
     * @param $role_name
     * @param $role_display_name
     * @return mixed
     */
	public function add_role($role_name, $role_display_name = '')
	{
		return $this->CI->roles_m->add_role($role_name, $role_display_name);
	}

    /**
     * Get User Permissions
     *
     * Get users permissions as an array
     *
     * @access public
     * @param $user_id
     * @return mixed
     */
	public function get_user_permissions($user_id)
	{
		return $this->CI->permissions_m->get_user_permissions($user_id);
	}

    /**
     * Get Role Permissions
     *
     * Get all permissions assigned to a role
     *
     * @access public
     * @param $role_id
     * @return mixed
     */
	public function get_role_permissions($role_id)
	{
		return $this->CI->permissions_m->get_role_permissions($role_id);
	}

    /**
     * Add Permission
     *
     * Add a new permission to the permissions table
     *
     * @access public
     * @param $permission
     * @return mixed
     */
	public function add_permission($permission)
	{
		return $this->CI->permissions_m->add_permission($permission);
	}

    /**
     * Update Permission
     *
     * Update an existing permission in the database
     *
     * @access public
     * @param $data
     * @return mixed
     */
	public function update_permission($data)
	{
		return $this->CI->permissions_m->update_permission($data);
	}

    /**
     * Delete Permission
     *
     * Delete a permission via its permission string
     * from the permissions table.
     *
     * @access public
     * @param $permission
     * @return mixed
     */
	public function delete_permission($permission)
	{
		return $this->CI->permissions_m->delete_permission($permission);
	}

    /**
     * Delete Permission By ID
     *
     * Delete a permission by it's ID
     * from the permissions table.
     *
     * @param $permission_id
     * @return mixed
     */
	public function delete_permission_by_id($permission_id)
	{
		return $this->CI->permissions_m->delete_permission_by_id($permission_id);
	}

    /**
     * Get User Meta
     *
     * Get user meta for a particular user from the user_meta
     * table.
     *
     * @param $permission_id
     * @return array
     */
    public function get_user_meta($user_id)
    {
        return $this->CI->wolfauth_m->get_user_meta($user_id);
    }

	/**
	 * Hash
	 *
	 * Hashes a password using hmac
	 *
	 * @access public
	 * @param $value
	 *
	 */
	public function hash($value)
	{
		return hash_hmac($this->_config['hash.method'], $value, $this->_config['hash.key']);
	}

	/**
	 * Populate Permissions
     *
     * Populate the permissions array for the
     * currently logged in user.
     *
     * @access private
     * @return void
     *
     */
	private function _populate_permissions()
	{
		$this->_permissions = $this->CI->permissions_m->get_user_permissions($this->user_id());
	}

	/**
	 * Set Remember Me
	 *
	 * Updates the remember me cookie and database information
	 *
	 * @access  private
	 * @param	$user_id
	 * @return	void
	 *
	 */
	private function _set_remember_me($user_id)
	{
		$this->CI->load->library('encrypt');

		$token = md5(uniqid(rand(), TRUE));
		$timeout = 60 * 60 * 24 * 7; // One week

		$remember_me = $this->CI->encrypt->encode($user_id.':'.$token.':'.(time() + $timeout));

		// Set the cookie and database
		$cookie = array(
			'name'		=> 'rememberme',
			'value'		=> $remember_me,
			'expire'	=> $timeout
		);

		set_cookie($cookie);
		update_user(array('id' => $user_id, 'remember_me' => $remember_me));
	}

	/**
	 * Checks if a user is logged in and remembered
	 *
	 * @access	private
	 * @return	bool
	 *
	 */
	private function _check_remember_me()
	{
		$this->CI->load->library('encrypt');

		// Is there a cookie to eat?
		if($cookie_data = get_cookie('rememberme'))
		{
			$user_id = '';
			$token   = '';
			$timeout = '';

			$cookie_data = $this->CI->encrypt->decode($cookie_data);
			
			if (strpos($cookie_data, ':') !== FALSE)
			{
				$cookie_data = explode(':', $cookie_data);
				
				if (count($cookie_data) == 3)
				{
					list($user_id, $token, $timeout) = $cookie_data;
				}
			}

			// Cookie expired
			if ((int) $timeout < time())
			{
				return FALSE;
			}

			if ($data = get_user_by_id($user_id))
			{
				// Set session values
				$this->CI->session->set_userdata(array(
					'user_id'   => $user_id,
					'username'  => $data->row('email'),
					'role_id'   => $data->row('role_id'),
					'role_name' => $data->row('role_name')
				));

				$this->_set_remember_me($user_id);

				return TRUE;
			}

			delete_cookie('rememberme');
		}

		return FALSE;
	}

    /**
     * Set Error
     *
     * Sets an error message
     *
     * @param $error
     */
    public function set_error($error)
    {
        $this->_errors[] = $error;
    }

    /**
     * Auth Errors
     *
     * Display error messages
     *
     * @param string $before
     * @param string $after
     * @return bool|string
     */
    public function auth_errors($before = '<p class="error">', $after = '</p>')
    {
        $html = FALSE;

        if (!empty($this->_errors))
        {
            foreach ($this->_errors AS $error)
            {
                $html .= $before.$error.$after;
            }
        }

        return $html;
    }

}