<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">
	<h1>Welcome to ci-fb-ion-auth !</h1>

	<div id="body">
		<p>The page you are looking is a simple exemple.</p>
		
		<?php if (!$this->ion_auth->logged_in()):?>
			<p>You are not logged !</p>
		
    		<h2>Register with Facebook</h2>
    		<a href="<?=getFacebookLoginUrl()?>" class="btn btn-block btn-social btn-facebook">
               	<span class="fa fa-facebook"></span>
               	Register with Facebook
            </a>
            
            <h2>Connexion with facebook</h2>
    		<p>
    			<a href="<?=getFacebookLoginUrl('welcome_fb_ion_auth/fb_login_callback')?>" class="btn btn-block btn-social btn-facebook">
                   	<span class="fa fa-facebook"></span>
                   	Connexion with Facebook
                </a>
            </p>
            
    	<?php else: ?>
    		<p>You are logged as <?=$user->username?> !</p>
        
            <?php if ($user->idfacebook_user): ?>
            	<h2>Disconnect a facebook accout with an ion-auth account</h2>
    			<a href="<?=site_url('welcome_fb_ion_auth/disconnectFacebookAccount_callback')?>" class="btn btn-social btn-facebook"
    			   onclick="return confirm('Are you sure you want to disconnect your facebook account from this application ?')">
                  <span class="fa fa-facebook"></span>
                  Disconnect my Facebook account
                </a>
            <?php else: ?>
                <h2>Connect a facebook accout with an ion-auth account</h2>
                <a href="<?=getFacebookLoginUrl('welcome_fb_ion_auth/connectFacebookAccount_callback')?>" class="btn btn-social btn-facebook">
                  <span class="fa fa-facebook"></span>
                  Connect my Facebook account
                </a>
            <?php endif; ?>
            
            <h2>Logout</h2>
            <!-- You certainly should use auth/logout -->
            <a href="<?=site_url('welcome_fb_ion_auth/logout')?>" class="btn">
              <span class="fa fa-logout"></span>
              Log out
            </a>
        	
        <?php endif;?>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

</body>
</html>