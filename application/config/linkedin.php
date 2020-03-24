<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
|  LinkedIn API Configuration
| -------------------------------------------------------------------
|
| To get an facebook app details you have to create a Facebook app
| at Facebook developers panel (https://developers.facebook.com)
|
|  linkedin_api_key        string   Your LinkedIn App Client ID.
|  linkedin_api_secret     string   Your LinkedIn App Client Secret.
|  linkedin_redirect_url   string   URL to redirect back to after login. (do not include base URL)
|  linkedin_scope          array    Your required permissions.
*/
//account used for the APP: Nikunj Dhimar
$config['linkedin_api_key']       = '8123pffx40wrd0';
$config['linkedin_api_secret']    = 'UtFBUkEtpIqiauOS';
$config['linkedin_redirect_url']  = 'social_wall/linkedin_redirect/';
$config['linkedin_scope']         = 'r_basicprofile r_emailaddress';