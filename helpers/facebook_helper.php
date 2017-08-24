<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Facebook Helpers
 *
 * @package     ci-fb-ion-auth
 * @subpackage  Helpers
 * @category    Helpers
 * @author      Benoit VRIGNAUD
 * @link        https://github.com/bvrignaud/ci-fb-ion-auth
 */



/**
 * Generate a Facebook login url
 * @param string redirect url  (eg: 'user/profile/connectFacebookAccount')
 * @return string Facebook login url
 */
function getFacebookLoginUrl($redirectUrl = '')
{
    $ci =& get_instance();
    return $ci->facebook->getLoginUrl($redirectUrl);
}
