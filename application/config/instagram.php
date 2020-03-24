<?php
	if($_SERVER['HTTP_HOST'] == 'localhost'){
		$config['apiKey'] = 'cdff4c38fdfe4a7b9d76054010963d27';
	 	$config['apiSecret'] = '798ef49d3441459e8502a7a6728c8690';
		$config['apiCallback'] = 'http://localhost/social/home/instagram_redirect/';
	}
        else if($_SERVER['HTTP_HOST'] == 'cmsproduct.local.com'){
                $config['apiKey'] = '5a717dfbe5764355a974e8fb09a26932'; // this value is from Muzaffar's pocketapp ID instagram account.
	 	$config['apiSecret'] = '8d19ea4c081e45f6a9dbff98fc4feb46'; // this value is from Muzaffar's pocketapp ID instagram account.
		$config['apiCallback'] = 'http://cmsproduct.local.com/social_wall/instagram_redirect/'; // make sure this value and callback value in instagram app setting is the same
        }
	else{
		$config['apiKey'] = 'a8caf22d7e0c4276a2926bc21eef0b24';
	 	$config['apiSecret'] = '7efad9f57bc54c9d83f1616686a3467a';
		$config['apiCallback'] = 'http://anglertrack.net/developer3/index.php/home/instragram_redirect/';
	}	
?>