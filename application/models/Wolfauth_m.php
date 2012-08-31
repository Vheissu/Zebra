<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wolfauth_m extends CI_Model {

	/**
	 * Get User
	 *
	 * Get a user via their email address
	 * 
	 * @access public
	 * @param string $email - Email address
	 * @return Object on success, False on failure
	 *
	 */
	public function get_user($email)
	{
		$this->db->select('users.*, roles.role_name, roles.role_display_name');
		$this->db->where('users.email', $email);
		$this->db->join('roles', 'roles.role_id = users.role_id');

		$user = $this->db->get('users', 1, 0);

		return ($user->num_rows() == 1) ? $user : FALSE;
	}

	/**
	 * Get User By ID
	 *
	 * Get a user via their user ID
	 * 
	 * @access public
	 * @param int $user_id - User ID
	 * @return Object on success, False on failure
	 *
	 */
	public function get_user_by_id($user_id)
	{
		$this->db->select('users.*, roles.role_name, roles.role_display_name');
		$this->db->where('users.id', $user_id);
		$this->db->join('roles', 'roles.role_id = users.role_id');
		
		$user = $this->db->get('users', 1, 0);

		return ($user->num_rows() == 1) ? $user : FALSE;
	}

	/**
	 * Add User
	 *
	 * Add a new user into the database
	 * 
	 * @access public
	 * @param string $email - Email address
	 * @param string $password - Hashed password
	 * @param enum $status - The status of the inserted user
	 * @param array $addition_data - User meta to insert (if any)
	 * @return Insert ID on success or False on failure
	 *
	 */
	public function add_user($email, $password, $role_id = 1, $status = "active", $additional_data = array())
	{
		$data = array(
			'role_id'       => $role_id,
			'email'         => $email,
			'password'      => $password,
			'register_date' => time(),
			'user_status'   => $status,
		);

		// Insert into the users table
		$this->db->insert('users', $data);

		// If we have user meta to insert
		if (!empty($addition_data) AND $this->db->insert_id())
		{
			$this->add_user_meta($this->db->insert_id(), $additional_data);
		}

		// Return a result
		return ($this->db->insert_id()) ? $this->db->insert_id() : FALSE;
	}
	
	/**
	 * Update User
	 *
	 * Updates a user in the database with optional argument
	 * for updating user meta as well.
	 *
	 * @access public
	 * @param array $user_data - Users table data to update
	 * @param array $additional_data - Additional meta to update
	 * @return bool - True on success or false on failure
	 *
	 */
	public function update_user($user_data = array(), $additional_data = array())
	{
		// We need an ID to perform updates
		if (isset($user_data['id']))
		{
			// If we have a password, then hash it
			if (isset($user_data['password']))
			{
				$user_data['password'] = $this->wolfauth->hash($user_data['password']);
			}

			// Update the user data
			$this->db->where('id', $user_data['id']);
			$this->db->update('users', $user_data);

			// Updating any user meta?
			if (!empty($additional_data))
			{
				// Add the user data if new, update old if existing data
				$this->add_user_meta($user_data['id'], $additional_data);
			}

			// If any rows were changed we successfully updated
			if ($this->db->affected_rows() >= 1)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Add User Meta
	 *
	 * Adds or updates user meta for a particular user
	 * in the user meta table.
	 *
	 * @param int $user_id - User ID
	 * @param array $meta - An array of data to update
	 * @return bool - True on success or False on failure
	 *
	 */
	public function add_user_meta($user_id, $meta)
	{
		// Empty array for user meta data
		$data = array();

		// Iterate over the meta and construct a more DB appropriate array
		foreach ($meta AS $field_name => $field_value)
		{
			$data[] = array(
				'user_id'     => $user_id,
				'umeta_key'   => $field_name,
				'umeta_value' => $field_value
			);
		}

		// Batch insert the meta
		$this->db->insert_batch('user_meta', $data);

		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE;
	}

}