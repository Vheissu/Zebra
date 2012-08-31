<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wolfauth_roles_m extends CI_Model {

	/**
     * Get Roles
     *
     * Return a list of roles with support for
     * limit and offset attributes
     *
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
	public function get_roles($limit = 10, $offset = 0)
	{
        $roles = $this->db->get('roles', $limit, $offset);

        return ($roles->num_rows() >= 1) ? $roles->result() : FALSE;
	}

    /**
     * Get Role
     *
     * Get a role from the database by its slug
     *
     * @param $role_name
     * @return bool
     */
	public function get_role($role_name)
	{	
		$this->db->select('role_id, role_name, role_display_name AS display_name');
		$this->db->where('role_name', $role_name);

		$role = $this->db->get('roles', 1, 0);

		return ($role->num_rows() == 1) ? $role->result() : FALSE;
	}

    /**
     * Get Role By ID
     *
     * Get a role from the database by its ID.
     *
     * @param $role_id
     * @return bool
     */
	public function get_role_by_id($role_id)
	{	
		$this->db->select('role_id, role_name, role_display_name AS display_name');
		$this->db->where('role_id', $role_id);

		$role = $this->db->get('roles', 1, 0);

		return ($role->num_rows() == 1) ? $role->result() : FALSE;
	}

	/**
	 * Add Role
	 *
	 * Adds a new role into the database
	 *
	 * @access public
	 * @param $role_name
	 * @param $role_display_name
	 * @return mixed
	 */
	public function add_role($role_name, $role_display_name = '')
	{
		$data = array(
			'role_name'         => $role_name,
			'role_display_name' => $role_display_name
		);

		$insert = $this->db->insert('roles', $data);

		return $insert;
	}

    /**
     * Update Role
     *
     * Update an existing role in the roles table
     *
     * @param $data
     * @return mixed
     */
	public function update_role($data)
	{
		$this->db->where('role_id', $data['role_id']);
		return $this->db->update('roles', $data);
	}

    /**
     * Delete Role
     *
     * Deletes a role by its name. Support for deleting
     * join table relationships is also added.
     *
     * @param $role_name
     * @param bool $delete_relationships
     * @return bool
     */
	public function delete_role($role_name, $delete_relationships = TRUE)
	{
		$role = $this->get_role($role_name);

		$this->db->where('role_name', $role_name);
		$this->db->delete('roles');

		if ($delete_relationships === TRUE)
		{
			$this->db->where('role_id', $role->role_id);
			$this->db->delete('permissions_roles');
		}

		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE;
	}

    /**
     * Delete Role By ID
     *
     * Deletes a role by its ID. Support for deleting
     * join table relationships is also added.
     *
     * @param $role_id
     * @param bool $delete_relationships
     * @return bool
     */
	public function delete_role_by_id($role_id, $delete_relationships = TRUE)
	{
		$this->db->where('role_id', $role_id);
		$this->db->delete('roles');

		if ($delete_relationships === TRUE)
		{
			$this->db->where('role_id', $role_id);
			$this->db->delete('permissions_roles');
		}

		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE;
	}

}