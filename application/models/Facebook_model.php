<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 */
class Facebook_model extends Ion_auth_model
{
    /**
     * 
     * @param int $fbId
     * @param int $password
     * @param bool $remember
     * @return boolean
     */
    public function facebook_login($fbId, $password, $remember = FALSE)
    {
        $this->trigger_events('pre_login');
        
        if (empty($fbId))
        {
            $this->set_error('login_unsuccessful');
            return FALSE;
        }
        
        $this->trigger_events('extra_where');
        
        $query = $this->db->select($this->identity_column . ', email, id, password, active, last_login')
                                        ->join('facebook_user', 'facebook_user.users_id = users.id', 'left')
                                        ->where('idfacebook_user', $fbId)
                                        ->limit(1)
                                        ->order_by('id', 'desc')
                                        ->get($this->tables['users']);
        
        if ($query->num_rows() === 1)
        {
            $user = $query->row();
        
            if ($user->active == 0)
            {
                $this->trigger_events('post_login_unsuccessful');
                $this->set_error('login_unsuccessful_not_active');
    
                return FALSE;
            }
    
            $this->set_session($user);
    
            $this->update_last_login($user->id);
    
            $this->clear_login_attempts($fbId);
    
            if ($remember && $this->config->item('remember_users', 'ion_auth'))
            {
                $this->remember_user($user->id);
            }
    
            $this->trigger_events(array('post_login', 'post_login_successful'));
            $this->set_message('login_successful');
    
            return TRUE;
        }
        
        $this->trigger_events('post_login_unsuccessful');
        $this->set_error('login_unsuccessful');
        
        return FALSE;
    }
    
	
    /**
     * Add a facebook user in database
     * @param int $userId
     * @param int $fbId
     * @return bool TRUE on success, FALSE on failure
     */
	public function addFacebookUser($userId, $fbId)
	{
	    return $this->db->insert('facebook_user',
	                             [
	                                 'users_id' => $userId,
	                                 'idfacebook_user' => $fbId,
	                             ]);
	}
	
	
	/**
	 * register
	 *
	 * @param $identity
	 * @param $password
	 * @param $email
	 * @param array $additional_data
	 * @param array $group_ids
	 * @author Mathew
	 * @return bool
	 */
	public function register($identity, $password, $email, $additional_data = array(), $group_ids = array()) //need to test email activation
	{
	    $return = parent::register($identity, $password, $email, $additional_data, $group_ids);
	    if ($return) {
	        if ($additional_data['facebook_uid']) {
	            $this->addFacebookUser($return, $additional_data['facebook_uid']);
	        }
	    }
	    return $return;
	}
	
}
