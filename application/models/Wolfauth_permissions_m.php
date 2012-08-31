<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wolfauth_permissions_m extends CI_Model {

	/**
	 * Get Role Permissions
	 *
	 * Get all permissions for a particular role
	 *
	 * @access public
	 * @param $role_id
	 * @return array
	 *
	 */
	public function get_role_permissions($role_id)
	{
		$this->db->select(''.$this->db->dbprefix.'permissions.permission_string AS permission');
		$this->db->where(''.$this->db->dbprefix.'permissions_roles.role_id', $role_id);
		$this->db->join(''.$this->db->dbprefix.'permissions_roles', ''.$this->db->dbprefix.'permissions_roles.permission_id = '.$this->db->dbprefix.'permissions.permission_id');

		$permissions = $this->db->get('permissions');

		return ($permissions->num_rows() >= 1) ? $permissions->result_array() : array();
	}

	/**
	 * Get User Permissions
	 *
	 * Allows you to get all permissions user has based
	 * on their user ID
	 *
	 * @access public
	 * @param $user_id
	 * @return array
	 *
	 */
	public function get_user_permissions($user_id)
	{
        $user = get_user_by_id($user_id);

        if ($user)
        {
            $this->db->select(''.$this->db->dbprefix.'permissions.permission_string AS permission');
            $this->db->where(''.$this->db->dbprefix.'permissions_roles.role_id', $user->row('role_id'));
            $this->db->join(''.$this->db->dbprefix.'permissions_roles', ''.$this->db->dbprefix.'permissions_roles.permission_id = '.$this->db->dbprefix.'permissions.permission_id');

            $permissions = $this->db->get('permissions');

            return ($permissions->num_rows() >= 1) ? $permissions->result_array() : array();
        }

        return FALSE;
	}

    /**
     * Permission To Role
     *
     * Assign a permission to a role
     *
     * @access public
     * @param $permission_id
     * @param $role_id
     */
	public function permission_to_role($permission_id, $role_id)
	{
		$data = array(
			'permission_id' => $permission_id,
			'role_id'       => $role_id
		);

		$this->db->insert('permissions_roles', $data);
	}

    /**
     * Add Permission
     *
     * Adds a new permission into the permissions table
     *
     * @access public
     * @param $permission
     * @return mixed
     */
	public function add_permission($permission)
	{
		return $this->db->insert('permissions', array('permission_string' => $permission));
	}

    /**
     * Update Permission
     *
     * Update an existing permission in the permissions table
     *
     * @param $data
     * @return mixed
     */
	public function update_permission($data)
	{
		$this->db->where('permission_id', $data['permission_id']);
		return $this->db->update('permissions', $data);
	}

    /**
     * Delete Permission
     *
     * Deletes a permission with optional flag for deleting
     * all relationships in join tables.
     *
     * @param $permission
     * @param bool $delete_relationships
     * @return bool
     */
	public function delete_permission($permission, $delete_relationships = TRUE)
	{
		$this->db->where('permission_string', $permission);
		$this->db->delete('permissions');

		if ($delete_relationships === TRUE)
		{
			
		}

		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE;
	}

    /**
     * Delete Permission By Id
     *
     * Deletes a permission by its ID with optional flag
     * for deleting all relationships in join tables.
     *
     * @param $permission_id
     * @param bool $delete_relationships
     * @return bool
     */
	public function delete_permission_by_id($permission_id, $delete_relationships = TRUE)
	{
		$this->db->where('permission_id', $permission_id);
		$this->db->delete('permissions');

		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE;
	}

}