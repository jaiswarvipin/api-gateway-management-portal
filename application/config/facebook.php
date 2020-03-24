<?php
//Account used for app: Nikunj Dhimar
//$config['facebook_app_id']              = '720379188021627';
//$config['facebook_app_secret']          = 'd2a215c941c9dad05060c5a3a1ff1a9b';
$config['facebook_app_id']              = '690065607741248'; // this value is from Muzaffar's personal facebook account.
$config['facebook_app_secret']          = '2c209d9db2f727de1fb5a11af60916ca'; // this value is from Muzaffar's personal facebook account.
$config['facebook_login_type']          = 'web';
$config['facebook_login_redirect_url']  = 'social_wall/facebook_redirect/';
$config['facebook_logout_redirect_url'] = 'login/lougout';
$config['facebook_permissions']         = array('email');
$config['facebook_graph_version']       = 'v2.6';
$config['facebook_auth_on_load']        = TRUE;
?>