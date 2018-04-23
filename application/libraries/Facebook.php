<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Facebook library for CodeIgniter with Ion auth.
 *
 * @author Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 *
 */
class Facebook
{
    /** @var CI_Controller */
    private $ci;

    /** @var Facebook\Facebook */
    private $fb;

    /** @var string */
    public $app_id;

    /** @var string */
    private $appSecret;

    /** @var string */
    private $graph_version;

    /** @var string */
    private $accessToken;

    /** @var Facebook\Helpers\FacebookRedirectLoginHelper */
    private $fbRedirectLoginHelper;

    /** @var Array */
    private $scope;


	public function __construct()
	{
		$this->ci =& get_instance();
    $this->ci->load->library('session');
		$this->ci->load->config('facebook', TRUE);
		$this->app_id = $this->ci->config->item('app_id', 'facebook');
		$this->appSecret = $this->ci->config->item('app_secret', 'facebook');
		$this->graph_version = $this->ci->config->item('default_graph_version', 'facebook');
		$this->scope = $this->ci->config->item('scope', 'facebook');
		$this->fb = new Facebook\Facebook([
			'app_id'                => $this->app_id,
			'app_secret'            => $this->appSecret,
			'default_graph_version' => $this->graph_version,
		]);
		$this->fbRedirectLoginHelper = $this->fb->getRedirectLoginHelper();

		// Set Ion auth hooks
		$this->ci->ion_auth->set_hook('logout', 'facebook_logout', 'Facebook', 'logout', []);
		$this->ci->ion_auth->set_hook('user', 'facebook_user', 'Facebook', 'user', []);

		$this->ci->load->model('facebook_model');
	}


	/**
	 * Generates the facebook connection link
	 * @param string $redirectUrl redirection url (eg: 'auth/fb_callback')
	 * @return string
	 */
	public function getLoginUrl($redirectUrl = '')
	{
	    $redirectUrl = $redirectUrl ? site_url($redirectUrl) : site_url($this->ci->config->item('login_redirect_url', 'facebook'));
	    return $this->fbRedirectLoginHelper->getLoginUrl($redirectUrl, $this->scope);
	}


	public function fb_callback()
	{
	    log_message('debug', 'Facebook::fb_callback()');

	    $accessToken = $this->getAccessToken(true);

	    // The OAuth 2.0 client handler helps us manage access tokens
	    $oAuth2Client = $this->fb->getOAuth2Client();

	    // Get the access token metadata from /debug_token
	    $tokenMetadata = $oAuth2Client->debugToken($accessToken);

	    // Validation (these will throw FacebookSDKException's when they fail)
	    try {
	       $tokenMetadata->validateAppId($this->app_id);
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
           // When validation fails or other local issues
           log_message('error', 'Facebook SDK returned an error: ' . $e->getMessage());
           show_error('Facebook SDK returned an error: ' . $e->getMessage());
        }

	    // If you know the user ID this access token belongs to, you can validate it here
	    //$tokenMetadata->validateUserId('123');
	    $tokenMetadata->validateExpiration();

	    if (!$accessToken->isLongLived()) {
	        // Exchanges a short-lived access token for a long-lived one
	        try {
	            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	        } catch (Facebook\Exceptions\FacebookSDKException $e) {
	            log_message('error', 'Error getting long-lived access token: ' . $this->fbRedirectLoginHelper->getMessage());
	            show_error('Error getting long-lived access token: ' . $this->fbRedirectLoginHelper->getMessage());
	        }
	    }

	    $this->ci->session->set_userdata('fb_access_token', $accessToken->getValue());

	    // User is logged in with a long-lived access token.
	    // You can redirect them to a members-only page.
	    //header('Location: https://example.com/members.php');

	    try {
	        // Returns a `Facebook\FacebookResponse` object
	        $response = $this->fb->get('/me?fields=id,name,email', $accessToken);
	    } catch(Facebook\Exceptions\FacebookResponseException $e) {
	        log_message('error', 'Graph returned an error: ' . $e->getMessage());
	        show_error('Graph returned an error: ' . $e->getMessage());
	    } catch(Facebook\Exceptions\FacebookSDKException $e) {
	        log_message('error', 'Facebook SDK returned an error: ' . $e->getMessage());
	        show_error('Facebook SDK returned an error: ' . $e->getMessage());
	    }

	    $user = $response->getGraphUser();

	    // get all user graph data
	    $email            = $user->getEmail();
 	    $facebook_user_id = $user->getId();
// 	    $full_name        = $user->getName();
// 	    $first_name       = $user->getFirstName();
// 	    $middle_name      = $user->getMiddleName();
// 	    $last_name        = $user->getLastName();
// 	    $gender           = $user->getGender();
// 	    $profile_link     = $user->getLink();
// 	    $birthday         = $user->getBirthday();
// 	    $location         = $user->getLocation();
// 	    $hometown         = $user->getHometown();

	    $rememberMe = $this->ci->config->item('remember_me', 'facebook');

 	    // Check if facebook user id is register
	    if ($this->identity_check($facebook_user_id)) {
	        return $this->ci->facebook_model->facebook_login($facebook_user_id, $rememberMe);
	    } else {
	        // check if facebook email already exist in user database
	        if ($this->ci->ion_auth->email_check($email)) {
	            $query = $this->ci->db->select('id')->get_where('users', ['email' => $email]);
	            $this->connectFacebookAccount($query->row()->id);
	        } else {
	            // add new user
                $this->addNewUser($user);
	        }
	        return $this->ci->facebook_model->facebook_login($facebook_user_id, $rememberMe);
	    }

	}


	/**
	 * @param \Facebook\GraphNodes\GraphUser $user
	 */
	private function addNewUser(\Facebook\GraphNodes\GraphUser $user)
	{
	    $this->ci->load->helper('string');

	    $uploadPath = $this->ci->config->item('upload_path', 'facebook');
	    $img = file_get_contents('https://graph.facebook.com/'.$user->getId().'/picture?type=large');
	    $file = $uploadPath . $user->getId() . '.jpg';
	    file_put_contents($file, $img);

	    $email = $user->getEmail();
	    $additional_data = [
	        'facebook_uid'  => $user->getId(),
	        'username'      => $user->getName(),
	        'first_name'    => $user->getFirstName(),
	        'last_name'     => $user->getLastName(),
	        //'facebook_link' => $user->getLink(),
	        'avatar'        => $user->getId() . '.jpg',
	    ];

	    return $this->ci->facebook_model->register($user->getName(), random_string(), $email, $additional_data);
	}


	/**
	 * Get stored access token
	 *
	 * @param bool $new True if you want a new AccessToken
	 * @return mixed
	 */
	private function getAccessToken($new = false)
	{
	    if (!$this->accessToken || $new === false) {
	        if ($new === false && $this->ci->session->userdata('fb_access_token')) {
	            $this->accessToken = $this->ci->session->userdata('fb_access_token');
	        } else {
	            try {
	                $this->accessToken = $this->fbRedirectLoginHelper->getAccessToken();
	            } catch(Facebook\Exceptions\FacebookResponseException $e) {
	                // When Graph returns an error
	                log_message('error', 'Graph returned an error: ' . $e->getMessage());
	                show_error($e->getMessage(), null, 'Graph returned an error');
	            } catch(Facebook\Exceptions\FacebookSDKException $e) {
	                // When validation fails or other local issues
	                $message = 'Facebook SDK returned an error: ' . $e->getMessage();
	                log_message('error', $message);
	                show_error($message);
	            }

	            if (!isset($this->accessToken)) {
	                if ($this->fbRedirectLoginHelper->getError()) {
	                    $message = [
	                        "Error: " . $this->fbRedirectLoginHelper->getError(),
	                        "Error Code: " . $this->fbRedirectLoginHelper->getErrorCode(),
	                        "Error Reason: " . $this->fbRedirectLoginHelper->getErrorReason(),
	                        "Error Description: " . $this->fbRedirectLoginHelper->getErrorDescription()
	                        ];
	                    show_error($message, 401);
	                } else {
	                    show_error('Bad request', 400);
	                }
	            }
	            $this->ci->session->set_userdata('fb_access_token', $this->accessToken->getValue());
	        }
	    }

	    return $this->accessToken;
	}


	/**
	 * Connect a facebook account with the application user account
	 * @param int $userId
	 */
	public function connectFacebookAccount($userId = null)
	{
	    try {
	        // Returns a `Facebook\FacebookResponse` object
	        $response = $this->fb->get('/me?fields=id', $this->getAccessToken());
	    } catch(Facebook\Exceptions\FacebookResponseException $e) {
	        show_error('Graph returned an error: ' . $e->getMessage());
	    } catch(Facebook\Exceptions\FacebookSDKException $e) {
	        show_error('Facebook SDK returned an error: ' . $e->getMessage());
	    }

	    $user = $response->getGraphUser();

	    $this->ci->db->insert('facebook_user', [
	        'idfacebook_user' => $user->getId(),
	        'users_id' => $userId ? $userId : $this->ci->facebook_model->user()->row()->id,
	    ]);
	}


	/**
	 * Disconnect a facebook account with the application account
	 */
	public function disconnectFacebookAccount()
	{
	    $userId = $this->ci->session->user_id;
	    $this->ci->db->select('idfacebook_user');
	    $query = $this->ci->db->get_where('facebook_user', ['users_id' => $userId]);
	    log_message('debug', 'Facebook::disconnectFacebookAccount() : ' . $this->ci->db->last_query());
	    $idFbUser = $query->row()->idfacebook_user;

	    $accessToken = $this->app_id . '|' . $this->appSecret;
	    $request = new Facebook\FacebookRequest($this->fb->getApp(), $accessToken, 'DELETE', "$idFbUser/permissions");
	    $response = $this->fb->getClient()->sendRequest($request);

	    $this->ci->db->where('idfacebook_user', $idFbUser);
	    $this->ci->db->delete('facebook_user');

	    $this->ci->session->unset_userdata('fb_access_token');
	}


	/**
	 * Identity check
	 * @param string $identity
	 * @return boolean
	 */
	private function identity_check($identity = '')
	{
	    if (empty($identity)) {
	        return FALSE;
	    }

        return $this->ci->db->where('idfacebook_user', $identity)
	                           ->count_all_results('facebook_user') > 0;
	}


	// =========== Ion_auth hooks =============================================


	/**
	 * Destroy the facebook acces token session
	 */
	public static function logout()
	{
	    $ci =& get_instance();
	    $ci->session->unset_userdata('fb_access_token');
	}

	/**
	 * Add database facebook datas to Ion-Auth
	 */
	public static function user()
	{
	    $ci =& get_instance();
	    $ci->ion_auth_model->db->select('facebook_user.idfacebook_user');
	    $ci->ion_auth_model->db->join('facebook_user', 'facebook_user.users_id = users.id', 'left');
	}

}
