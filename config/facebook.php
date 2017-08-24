<?php
/**
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Facebook config file for Facebook library.
 *
 * @author Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
|  Facebook App details
| -------------------------------------------------------------------
|
| To get an facebook app details you have to be a registered developer
| at https://developer.facebook.com and create an app for your project.
|
|  app_id                string   Your facebook app ID.
|  app_secret            string   Your facebook app secret.
|  default_graph_version string   Set Facebook Graph version to be used. Eg v2.9
|  login_redirect_url    string   URL tor redirect back to after login. Do not include domain.
|  logout_redirect_url   string   URL tor redirect back to after login. Do not include domain.
|  scope                 array    The permissions you need : 'email', 'public_profile', 'user_location', ...
|  remember_me           bool
|  upload_path           string   The path where you want to store the avatar
*/
$config['app_id']                = '';
$config['app_secret']            = '';
$config['default_graph_version'] = 'v2.9';
$config['login_redirect_url']    = 'auth/fb_callback';
//$config['logout_redirect_url']   = 'example/logout';  // Not implemented
$config['scope']                 = ['email'];
$config['remember_me']           = TRUE;
$config['upload_path']           = './assets/uploads/avatars/';
