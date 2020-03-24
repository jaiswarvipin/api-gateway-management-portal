<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

class Social_feed extends CI_Controller {

	/* variable deceleration */
	public  $_objDataOperation		= 	null;
	private $_strPrimaryTableName 	= 	'events_1';
	private $_configTwitter;

	function __construct()
	{
		parent::__construct();

		// this controller can only be called from the command line
		//if (!$this->input->is_cli_request()) show_error('Direct access is not allowed', 401, 'Not authorized !');

		/* Creating model comment instance object */
		$this->_objDataOperation	= new Dbrequestprocess_model();

		/* load twitter */
		$this->config->load("twitter", TRUE);
		$this->_configTwitter 		= 	$this->config->item('twitter');

		/* getting twitter feed */
		/*
		$this->ACCESS_TOKEN 		= 	$this->_configTwitter['access_token'];
		$this->ACCESS_TOKEN_SECRET 	= 	$this->_configTwitter['access_token_secret'];
		*/
		$this->CONSUMER_KEY 		= 	$this->_configTwitter['consumer_key'];
		$this->CONSUMER_SECRET 		= 	$this->_configTwitter['consumer_secret'];

	}

	public function message($to = 'World') {
		echo "Hello {$to}!".PHP_EOL;
	}

	public function fetch_social_feeds(){
		$this->_fetch_tweeter_feeds();
	}

	private function _fetch_tweeter_feeds(){

		$strWhereClauseArr 	= 	array('from-date <=' => date('YmdHis'), 'to-date >=' => date('YmdHis'));

		$strFilterArr 		= 	array(
									'table' 	=> 	array('master_social_feed_data', $this->_strPrimaryTableName),
									'column' 	=> 	array('master_social_feed_data.id', 'master_social_feed_data.event_code', 'master_social_feed_data.data_desc', 'master_social_feed_data.event_code', 'master_social_feed_data.data_type', 'auto_approve_comments'),
									'join' 		=> 	array('', 'master_social_feed_data.event_code = '.$this->_strPrimaryTableName.'.id' ),
									'where' 	=> 	$strWhereClauseArr,
							);

		/* if requested page number is > 0 then do needful */ 
		$intCurrentPageNumber = 0;
		if($intCurrentPageNumber >= 0){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}

		try {

		/* Getting the status list */
		$socialFeedDataArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);

		if (empty($socialFeedDataArr)) {
			return false;
		}

		$cronValueArr = array();

		$strCronWhereClauseArr = array('cron_name' => 'TwitterFetchTweet');

		$strFilterCronArr 		= 	array(
										'table' 	=> 	'trans_cron',
										'column' 	=> 	array('id', 'cron_name', 'cron_value', 'process_status'),
										'where' 	=> 	$strCronWhereClauseArr,
								);

		$cronDataArr	=  $this->_objDataOperation->getDataFromTable($strFilterCronArr);

		if (!empty($cronDataArr) && !empty($cronDataArr[0]) && $cronDataArr[0]['process_status'] != '0') {
			return $this->output->set_content_type('application/json')
								->set_status_header(200)
								->set_output(json_encode(array('message' => 'Previous cron Execution not yet completed !')));
			exit;
		}

		$strCronDataArr = 	array(
								'table' => 	'trans_cron',
								'data' 	=> 	array(
									'cron_name' => 'TwitterFetchTweet',
									'process_status' 	=> 	'1',
								),
						);

		if (!empty($cronDataArr) && is_array($cronDataArr)) {
			$cronValueArr 				= 	!empty($cronDataArr[0]['cron_value']) ? json_decode($cronDataArr[0]['cron_value'], true) : array();
			//$sinceId 					= 	!empty($cronValueArr) ? array_values(array_slice($cronValueArr, -1))[0] : '';

			$strCronDataArr['where'] 	= 	array('id' => $cronDataArr[0]['id']);
			$intOperationStatus 		= 	$this->_objDataOperation->setUpdateData($strCronDataArr);

		}else{
			$transCronInsertId 			= 	$this->_objDataOperation->setDataInTable($strCronDataArr);
		}


		$settings 			= 	array(
									'oauth_access_token' 			=> 	$this->_configTwitter['access_token'],
									'oauth_access_token_secret' 	=> 	$this->_configTwitter['access_token_secret'],
									'consumer_key' 					=> 	$this->_configTwitter['consumer_key'],
									'consumer_secret' 				=> 	$this->_configTwitter['consumer_secret'],
							);

		$this->load->library('TwitterAPIExchange', $settings, 'TwitterAPIExchange');

		$twitter 			= 	new TwitterAPIExchange($settings);

		foreach ($socialFeedDataArr as $socialFeedData) {


			if ($socialFeedData['data_type'] == 'handle' ) {
				$searchWord 	= 	'@'.$socialFeedData['data_desc'];
			}else{
				$searchWord 	= 	'#'.$socialFeedData['data_desc'];
			}

			$sinceIdQuery = '';

			if (!empty($cronValueArr[$searchWord])) {
				$sinceIdQuery 	= 	'&since_id='.$cronValueArr[$searchWord];
			}

			$url 			= 	'https://api.twitter.com/1.1/search/tweets.json';
			$getfield 		= 	"?q=$searchWord&result_type=recent&count=10&tweet_mode=extended&include_entities=true".$sinceIdQuery;
			$requestMethod 	= 	'GET';

			//try {
				$responseTweetDataJson 	= 	$twitter->setGetfield($getfield)
													->buildOauth($url, $requestMethod)
													->performRequest();

				$responseTweetDataArr 	= 	json_decode($responseTweetDataJson, true);
				unset($responseTweetDataJson);
				$response[$searchWord] 	= 	$responseTweetDataArr;
			//} catch (Exception $e) {
				/*
				$msg 					= 	"Campaign getMentions: Twitter API Exception @" . date('Y-m-d H:i:s') . ' message => ' . $e;
				$response[$searchWord] 	= 	$twitter_data['statuses'] = array();

				$strCronDataArr = 	array(
										'table' => 	'trans_cron',
										'data' 	=> 	array(
											'cron_value' 		=> 	json_encode($cronValueArr),
											'process_status' 	=> 	'2',
										),
								);

				$strCronDataArr['where'] 	= 	array('id' => $cronDataArr[0]['id']);
				$intOperationStatus 		= 	$this->_objDataOperation->setUpdateData($strCronDataArr);

				log_message('error', $msg);
				*/
			//}

			foreach ($responseTweetDataArr as $tweetKey => $tweetData) {

				if ($tweetKey != 'statuses') {
					continue;
				}
				$firstTweetId = 0;

				if (!empty($tweetData) && is_array($tweetData)) {
					foreach ($tweetData as $tweet) {
						$feedContent = array('tweet_content' => $tweet);

						if (empty($firstTweetId)) {
							$firstTweetId 				= 	$tweet['id_str'];
							$cronValueArr[$searchWord] 	= 	$firstTweetId;
						}

						/**/
						$oembedApiUrl 					= 	'https://publish.twitter.com/oembed';
						$handerName 					= 	!empty($tweet['user']['screen_name']) ? $tweet['user']['screen_name'] : '';
						$oembedApiUrlParameter 			= 	'https://twitter.com/' . $handerName . '/status/'.$tweet['id_str'];
						$oembedApiGetfield 				= 	"?url=".$oembedApiUrlParameter;
						$oembedRequestMethod 			= 	'GET';

						$oembedResponseTweetDataJson 	= 	$twitter->setGetfield($oembedApiGetfield)
																	->buildOauth($oembedApiUrl, $oembedRequestMethod)
																	->performRequest();

						$oembedResponseTweetDataArr 	= 	json_decode($oembedResponseTweetDataJson, true);
						$feedContent['oembed'] 			=  	(!empty($oembedResponseTweetDataArr) && is_array($oembedResponseTweetDataArr)) ? $oembedResponseTweetDataArr : array();
						unset($oembedResponseTweetDataJson, $oembedResponseTweetDataArr);
						/**/

						$strDataArr = 	array(
							'table' => 	'trans_social_event_feeds',
							'data' 	=> 	array(
								//'feeder_name' 	=> 	!empty($tweet['user']['screen_name']) ? '@'.$tweet['user']['screen_name'] : '',
								'feeder_name' 	=> 	'@'.$handerName,
								'data_code' 	=> 	$socialFeedData['id'],
								'title' 		=> 	$tweet['full_text'],
								//'content' 		=> 	json_encode($tweet),
								'content' 		=> 	json_encode($feedContent),
								'feed_type' 	=> 	'twitter',
								'is_approved' 	=> 	$socialFeedData['auto_approve_comments'],
							),
						);

						$transSocialEventFeedInsertId = $this->_objDataOperation->setDataInTable($strDataArr);

						$tweetMediaArr = (!empty($tweet['entities']) && !empty($tweet['entities']['media']) && is_array($tweet['entities']['media'])) ? $tweet['entities']['media'] : array();

						foreach ($tweetMediaArr as $tweetMedia) {

							$strDataArr = 	array(
								'table' => 	'trans_social_event_feed_images_or_videos',
								'data' 	=> 	array(
									'feed_data_code' 		=> 	$transSocialEventFeedInsertId,
									'image_or_video_url' 	=> 	$tweetMedia['media_url_https'],
									'type' 					=> 	$tweetMedia['type'],
								),
							);

							$transSocialEventFeedInsertId = $this->_objDataOperation->setDataInTable($strDataArr);
						}

					}
				}
			}

		}

		if (!empty($cronValueArr) && !empty($cronDataArr[0])) {

			$strCronDataArr = 	array(
									'table' => 	'trans_cron',
									'data' 	=> 	array(
										'cron_value' 		=> 	json_encode($cronValueArr),
										'process_status' 	=> 	'0',
									),
							);

			$strCronDataArr['where'] 	= 	array('id' => $cronDataArr[0]['id']);
			$intOperationStatus 		= 	$this->_objDataOperation->setUpdateData($strCronDataArr);

		}


		} catch (Exception $e) {
			$msg 				= 	"Cron Execution is failed at " . date('Y-m-d H:i:s') . ' message => ' . $e->message;
			$strCronDataArr 	= 	array(
										'table' => 	'trans_cron',
										'data' 	=> 	array(
											'cron_value' 		=> 	json_encode($cronValueArr),
											'process_status' 	=> 	'2',
										),
								);

			$strCronDataArr['where'] 	= 	array('id' => $cronDataArr[0]['id']);
			$intOperationStatus 		= 	$this->_objDataOperation->setUpdateData($strCronDataArr);

			log_message('error', $msg);
		}

		return $this->output->set_content_type('application/json')
							->set_status_header(200)
							->set_output(json_encode($response));

	}

	public function test(){

		$settings = array(
			'oauth_access_token' 			=> 	$this->_configTwitter['access_token'],
			'oauth_access_token_secret' 	=> 	$this->_configTwitter['access_token_secret'],
			'consumer_key' 					=> 	$this->_configTwitter['consumer_key'],
			'consumer_secret' 				=> 	$this->_configTwitter['consumer_secret'],
		);

		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$searchWord = '#PaWTAER';
		$getfield = "?q=$searchWord&result_type=mixed&count=100&with_replies=true";
		$requestMethod = 'GET';

		$this->load->library('TwitterAPIExchange', $settings, 'TwitterAPIExchange');

		$twitter = new TwitterAPIExchange($settings);
		try {
			$response = $twitter->setGetfield($getfield)
								->buildOauth($url, $requestMethod)
								->performRequest();
		} catch (Exception $e) {
			$msg = "Campaign getMentions: Twitter API Exception @" . date('Y-m-d H:i:s') . ' message => ' . $e;
			log_message('error', $msg);
			$response = $twitter_data['statuses'] = array();
		}

		return $this->output->set_content_type('application/json')
							->set_status_header(200)
							->set_output($response);

	}

}