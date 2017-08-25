# ci-fb-ion-auth
This is a php and CodeIgniter (3.x) library to use Facebook php SDK with Ion-Auth.

## Requirements
- PHP 5.4+
- [CodeIgniter 3](https://www.codeigniter.com/)
- [CodeIgniter session library](https://www.codeigniter.com/userguide3/libraries/sessions.html)
- [Ion-Auth] (http://benedmunds.com/ion_auth/)
- [Facebook PHP SDK v5](https://developers.facebook.com/docs/php/gettingstarted/5.0.0)
- [Composer](https://getcomposer.org/)

## Installation
1. Download the library files and add the files to your CodeIgniter installation. Only the library, model, config and composer.json files are required.
2. In your CodeIgniter `/application/config/config.php` file, set `$config['composer_autoload']` to `TRUE`. [Read more](https://www.codeigniter.com/user_guide/general/autoloader.html)
3. Update the `facebook.php` config file in `/application/config/facebook.php` with your Facebook App details.
4. Install the Facebook PHP SDK by navigating to your applications folder in the terminal and run Composer with `composer install`. [Read more](https://developers.facebook.com/docs/php/gettingstarted#install-composer)
6. Autoload the library in `application/config/autoload.php` or load it in needed controllers with `$this->load->library('facebook');`
7. Autoload the helper in `application/config/autoload.php` or load it in needed controllers with `$this->load->helper('facebook_helper');`

## Usages
In the package you will find simple example usage code in the controllers and views folders.