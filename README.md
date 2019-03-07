# ci-fb-ion-auth
This is a php and CodeIgniter (3.x) library to use Facebook php SDK with Ion-Auth.

## Requirements
- PHP 5.4.0 or later (5.6 or later is recommended)
- [CodeIgniter 3](https://www.codeigniter.com/)
- [CodeIgniter session library](https://www.codeigniter.com/userguide3/libraries/sessions.html)
- [Ion-Auth] (http://benedmunds.com/ion_auth/)
- [Facebook PHP SDK v5](https://developers.facebook.com/docs/php/gettingstarted/5.0.0)
- [Composer](https://getcomposer.org/)

## Installation

Install the Facebook PHP SDK with Composer with `composer install` ([Read more](https://developers.facebook.com/docs/php/gettingstarted#install-composer)) :

```sh
$ cd /path/to/codeigniter_project/
$ composer require facebook/graph-sdk
```
In your CodeIgniter `/application/config/config.php` file, set `$config['composer_autoload']` to `TRUE`. [Read more](https://www.codeigniter.com/user_guide/general/autoloader.html).

Copy the files from this package to the corresponding folder in your application folder. For example, copy application/config/facebook.php to application/config/facebook.php

You can also copy the libraries and models directories into your third_party/ci-fb-ion-auth folder. For example, copy to /application/third_party/ci-fb-ion-auth/. The directory structure would be :

    config/facebook.php
    third_party/ci-fb-ion-auth/helpers/facebook_helper.php
    third_party/ci-fb-ion-auth/libraries/Facebook.php
    third_party/ci-fb-ion-auth/models/Facebook_model.php
Only the library, model and config files are required.

Edit your `facebook.php` config file in `/application/config/facebook.php` with your Facebook App details.

Autoload the library in `application/config/autoload.php` or load it in needed controllers with `$this->load->library('facebook');`.

Autoload the helper in `application/config/autoload.php` or load it in needed controllers with `$this->load->helper('facebook_helper');`.

## Relational DB Setup
Run the SQL file `sql/ci-fb-ion-auth`.

## Usage
In the package you will find simple example usage code in the controllers and views folders.

### Register with Facebook
view:

```php
<a href="<?=getFacebookLoginUrl('welcome/fb_register_callback')?>">Register with Facebook</a>
```
controller (welcome.php):

```php
public function fb_register_callback()
{
    if ($this->facebook->fb_callback()) {
        redirect('user/account');
    } else {
        show_error('erreur');
    }
}
```

### Connexion with Facebook
view:

```php
<a href="<?=getFacebookLoginUrl('welcome/fb_login_callback')?>">Connexion with Facebook</a>
```
controller (welcome_fb_ion_auth.php):

```php
public function fb_login_callback()
{
    if ($this->facebook->fb_callback()) {
        echo 'Your are now logged in !";
    } else {
        show_error('erreur');
    }
}
```
### Connect/Disconnect user's Facebook account
view :
 
```php
if ($this->ion_auth->logged_in()) {
    if ($this->ion_auth->user()->row()->idfacebook_user) {
        echo '<a href="'.site_url('welcome/disconnectFacebookAccount_callback')."
                onclick="return confirm(\'Are you sure you want to disconnect your facebook account from this application ?\')">
                 Disconnect my Facebook account
            </a>';
    } else {
        echo '<a href="'.getFacebookLoginUrl('welcome/connectFacebookAccount_callback').'">
                  Connect my Facebook account
              </a>';
    }
}
```
controller (welcome.php):

```php
public function disconnectFacebookAccount_callback()
{
    $this->facebook->disconnectFacebookAccount();
    redirect();
}

public function connectFacebookAccount_callback()
{
    $this->facebook->connectFacebookAccount();
    redirect();
}
``` 
