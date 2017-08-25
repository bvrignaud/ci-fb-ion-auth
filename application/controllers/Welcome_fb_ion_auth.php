<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * Sample controller for facebook Ion-Auth Plug-in
 * @author Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 *
 * This function could be in the standard Auth.php (Ion-Auth) controller
 */
class Welcome_fb_ion_auth extends CI_Controller {

	public function index()
	{
	    $user = $this->ion_auth->user()->row();
	    $data['user'] = $user;
		$this->load->view('fb_ion_auth_demo', $data);
	}
	
	/**
	 * Callback to log with facebook account
	 */
	public function fb_login_callback()
	{
	    if ($this->facebook->fb_callback()) {
	        redirect('Welcome_fb_ion_auth');
	    } else {
	        show_error('erreur');
	    }
	}
	
	/**
	 * Callback to disconnect the facebook acount
	 */
	public function disconnectFacebookAccount_callback()
	{
	    $this->facebook->disconnectFacebookAccount();
	    redirect('Welcome_fb_ion_auth');
	}
	
	/**
	 * Callback to connect the user's facebook acount
	 */
	public function connectFacebookAccount_callback()
	{
	    $this->facebook->connectFacebookAccount();
	    redirect('Welcome_fb_ion_auth');
	}
	
	// log the user out ion-auth
	public function logout()
	{
	    $this->data['title'] = 'Logout';
	    
	    // log the user out
	    $logout = $this->ion_auth->logout();
	    
	    // redirect them to the login page
	    $this->session->set_flashdata('message', $this->ion_auth->messages());
	    redirect('Welcome_fb_ion_auth');
	}
}
