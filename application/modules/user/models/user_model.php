<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {

	protected $_table = 'users';

    public function increment_submission_count($user_id)
    {
        $submission_count = $this->has_submission_field($user_id);

        if ($submission_count !== FALSE)
        {
            $this->db->where('umeta_key', 'submissions');
            $this->db->update('user_meta', array('umeta_value' => $submission_count + 1));
        }
        else
        {
            $this->db->insert('user_meta', array('user_id' => $user_id, 'umeta_key' => 'submissions', 'umeta_value' => 1));
        }
    }

    private function has_submission_field($user_id)
    {
        $this->db->where('umeta_key', 'submissions')
        $this->db->where('user_id', $user_id);

        $result = $this->db->get('user_meta');

        return ($result->num_rows() == 1) ? $result->umeta_value : FALSE;
    }

}