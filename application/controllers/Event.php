<?php
/***********************************************************************/
/* Purpose 		: Front-end Event management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends CI_Controller {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'events_1';
	public  $_objDataOperation		= null;
	public  $_objDevice				= null;
	private $_configTwitter			= array();

	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Vipin Kumar R. Jaiswar.
	/**********************************************************************/
	public function __construct($pBlnRequestFromHook = false){
		/* CI call execution */
		parent::__construct();

		/* Creating model comment instance object */
		$this->_objDataOperation	= new Dbrequestprocess_model();
		/* load twitter */
		$this->config->load("twitter", TRUE);
		/* Set the twitter configuration */
		$this->_configTwitter 		= 	$this->config->item('twitter');
		
		/* loading device identification */
		$this->load->helper('device');
		/* Creating device instance object */
		$this->_objDevice = new Device();
	}

	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$eventPublicCode 	= $this->_getEventCode();
		$dataArr			= array();
		$strSiteURL			= $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
		
		/* checking for the event code */
		if (!in_array($eventPublicCode, array('www','dev','stg','localhost'))){
			/* Checking for event feed existence */
			$eventDataArr = $this->_checkEventPublicCodeExist($eventPublicCode);
			
			/* if event code is valid then do needful */
			if (!empty($eventDataArr)) {
				/* Setting event code */
				$strResponseArr['eventCodeEnc'] 		= (!empty($eventDataArr) && !empty($eventDataArr[0]) && !empty($eventDataArr[0]['id'])) ? getEncyptionValue($eventDataArr[0]['id']) : '';
				$strResponseArr['strFeedLength'] 		= getUserAndCustomTextFeedCharLimit();
				$strResponseArr['strEventName'] 		= (isset($eventDataArr[0]['name']) && ($eventDataArr[0]['name'] !=''))?$eventDataArr[0]['name']:'Event Wall';
				$strResponseArr['strEventCode'] 		= (!empty($eventDataArr) && !empty($eventDataArr[0]) && !empty($eventDataArr[0]['event_public_code'])) ? ($eventDataArr[0]['event_public_code']) : 'XXXXXX';
				$strResponseArr['sort'] 				= 'recent';
				$strResponseArr['blnFrontRequest'] 		= true;
				$strResponseArr['blnFrontRequest'] 		= true;
				$strResponseArr['blnDevice'] 			= $this->_objDevice->isMobile();
				$strResponseArr['blnShowPostInput'] 	= $this->input->get('pOsTyOuRoWn',true)?true:false;
				$strResponseArr['moduleTitle'] 			= 'Home';
				$strResponseArr['siteURL'] 				= $strSiteURL;
				/* get the user feed wall view */
				$dataArr['body']	= $this->load->view('social/user-feed', $strResponseArr, true);
				
				/* Loading the template for browser rending */
				$this->load->view(DEFAULT_TEMPLATE, $dataArr);
			/* if event not fund then do nothing */
			}else{
				/* refresh the page */
				return redirect(SITE_URL, 'refresh');
			}
			/* removed used variable */
			unset($eventDataArr);
		}else{
			/* Load the event code input view */
			$dataArr['body']			= $this->load->view('social/event', array(), true);
			$dataArr['moduleTitle'] 	= 'Home';
			
			/* Loading the template for browser rending */
			$this->load->view(DEFAULT_TEMPLATE, $dataArr);
		}
		/* Removed used variable */
		unset($dataArr, $strResponseArr);
	}

	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function wall() {
		/* variable initialization */
    	$strEventCode 		= $this->_getEventCode();
		$socialEventDataArr	= array();
		
		/* if event code is not pass then do needful */
		if($strEventCode == ''){
		
		}else{
			/* Setting the event filter */
			$strFilterArr 		= 	array(
											'table' 	=> 	array($this->_strPrimaryTableName, 'trans_social_event_feeds_wall_grid_config'),
											'column' 	=> 	array($this->_strPrimaryTableName.'.id', $this->_strPrimaryTableName.'.event_public_code', $this->_strPrimaryTableName.'.company_code', $this->_strPrimaryTableName.'.name', $this->_strPrimaryTableName.'.description', $this->_strPrimaryTableName.'.from-date', $this->_strPrimaryTableName.'.to-date', $this->_strPrimaryTableName.'.status', 'trans_social_event_feeds_wall_grid_config.id AS grid_id', 'trans_social_event_feeds_wall_grid_config.rows', 'trans_social_event_feeds_wall_grid_config.columns', 'trans_social_event_feeds_wall_grid_config.type'),
											'join' 		=> 	array('', $this->_strPrimaryTableName.'.id=trans_social_event_feeds_wall_grid_config.event_code' ),
											'where' 	=> 	array($this->_strPrimaryTableName.'.event_public_code' => $strEventCode)
										);
			/* Get event details */
			$socialEventDataArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		}
		
		/* Getting company list */
		$strResponseArr['gridConfig'] 			= $socialEventDataArr;
		$strResponseArr['strGridCellDataArr'] 	= $socialEventDataArr;
		$strResponseArr['strEventName'] 		= (isset($socialEventDataArr[0]['name']) && ($socialEventDataArr[0]['name'] !=''))? $socialEventDataArr[0]['name'] : '';
		$strResponseArr['strEventCode'] 		= $strEventCode;
		$strResponseArr['blnFrontRequest'] 		= true;
		$strResponseArr['strUserAndCustomTextFeedCharLimitArr'] 		= getUserAndCustomTextFeedCharLimit();
		
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('social/grid-config', $strResponseArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(DEFAULT_TEMPLATE, $dataArr);
    }
	
	/**********************************************************************/
	/*Purpose 	: get the configured element of the cell.
	/*Inputs	: $eventCodeEnc :: Event code,
				: $gridIdEnc :: Grid code,
				: $cellNumber :: Cell number.
	/*Returns	: Cell content
	/*Created By: Vipin Kumar R. Jaiswar.
	/**********************************************************************/
	public function load_feed_by_event_code_grid_code_cell_number($eventCodeEnc, $gridIdEnc, $cellNumber = 1){
		/* variable initialization */
		$eventCode 						= getDecyptionValue($eventCodeEnc);
		$gridCode 						= getDecyptionValue($gridIdEnc);
		$contentMasterArr 				= $contentTransArr = $contentArr = $feedTimerArr = array();
		$strGridCellDataArr 			= array();
		
		$responseArr 					= array();
		$responseArr['cellNumber'] 		= $cellNumber;
		$responseArr['eventCodeEnc'] 	= $eventCodeEnc;
		$responseArr['gridIdEnc'] 		= $gridIdEnc;
		$responseArr['eventCode'] 		= $eventCode;
		$responseArr['gridCode'] 		= $gridCode;
		
		/* Get grid cell type details */
		$strFilterArr 		= 	array(
										'table' 	=> 'trans_social_event_feeds_wall_cell_config',
										'column' 	=> array('id', 'grid_id', 'cell_index', 'content_code', 'is_feed', 'refresh_timeout'),
										'where' 	=> array('grid_id' => $gridCode, 'cell_index' => $cellNumber),
										'order'		=> array('id' => 'DESC'),
									);
		/* Get cell configuration */
		$socialCellFeedDataArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
			
		if(empty($socialCellFeedDataArr)){
			/* Return blank row */
			die('<p class="center-aling">No Feed Set for this cell.</p>');
		}
		
		/* iterating configuration loop */
		foreach ($socialCellFeedDataArr as $index => $socialFeedData) {
			/* Setting refresh time */
			$feedTimerArr[$socialFeedData['cell_index']] 	= $socialFeedData['refresh_timeout'];
			/* Checking for feeds type */
			if (!empty($socialFeedData['is_feed'])) {
				/* Setting social category */
				$contentMasterArr[$index] = $socialFeedData['content_code'];
			}else{
				/* Setting user feeds */
				$contentTransArr[$index] = $socialFeedData['content_code'];
			}
		}
		
		/* Checking for system feeds including social media configuration */
		if(!empty($contentMasterArr)) {
			/* Setting the feed type */
			$responseArr['feed_type'] 		= 'social';
			/* Setting the twitter configuration setting */
			$settings 	= 	array(
									'oauth_access_token' 			=> 	$this->_configTwitter['access_token'],
									'oauth_access_token_secret' 	=> 	$this->_configTwitter['access_token_secret'],
									'consumer_key' 					=> 	$this->_configTwitter['consumer_key'],
									'consumer_secret' 				=> 	$this->_configTwitter['consumer_secret'],
								);
			/* load the twitter library */
			$this->load->library('TwitterAPIExchange', $settings, 'TwitterAPIExchange');
			/* Creating twitter object */
			$twitter 	= 	new TwitterAPIExchange($settings);

			/* Setting twitter H/T query array */
			$strFilterArr 		= 	array(
											'table' 	=> 	array('tags_1', 'master_widget_attributes_list'),
											'column' 	=> 	array( 'tags_1.id', 'event_code', 'hash-tag- as data_desc', 'tag-type as data_type', 'master_widget_attributes_list.description AS data_type_desc' ),
											'join' 		=> 	array('', array('table'=>'tags_1.tag-type=master_widget_attributes_list.id ', 'type'=>'left')),
											'where' 	=> 	array('tags_1.id' => $contentMasterArr),
										);
			/* get twitter H/T feeds */
			$strUserAdminFeedDataArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
			
			/* if H/T details found then do needful */
			if(!empty($strUserAdminFeedDataArr)){
				/* iterating the loop */
				foreach ($contentMasterArr as $cellIndex => $contentCode) {
					/* Setting H/T details */
					$key	 											= array_search($contentCode, array_column($strUserAdminFeedDataArr, 'id'));
					$strGridCellDataArr[$cellIndex]['id'] 				= $strUserAdminFeedDataArr[$key]['id'];
					$strGridCellDataArr[$cellIndex]['feed'] 			= $strUserAdminFeedDataArr[$key]['data_desc'];
					$strGridCellDataArr[$cellIndex]['feeder_name'] 		= $strUserAdminFeedDataArr[$key]['data_type'];
					$strGridCellDataArr[$cellIndex]['data_type_desc'] 	= $strUserAdminFeedDataArr[$key]['data_type_desc'];
					$strGridCellDataArr[$cellIndex]['refresh_timeout'] 	= isset($feedTimerArr[$cellNumber])?$feedTimerArr[$cellNumber]:0;
					
					/* Set the response type */
					$responseArr['social_type'] 						= 'twitter';
					/* Variable initialization */
					$searchWord 										= '';
					$sinceIdArr 										= array();
					$sinceIdQuery 										= '';
					
					/* Based on the handler type do needful */
					if ($strUserAdminFeedDataArr[$key]['data_type'] == 'handle' ) {
						/* Value overwriting */
						$searchWord 	= 	'@'.$strUserAdminFeedDataArr[$key]['data_desc'];
					}else{
						/* Value overwriting */
						$searchWord 	= 	'#'.$strUserAdminFeedDataArr[$key]['data_desc'];
					}
					/* Set the search type */
					$strGridCellDataArr[$index]['key'] 					= 	$searchWord;

					if (!empty($sinceIdArr) && !empty($sinceIdArr[$searchWord])) {
						$sinceIdQuery 	= 	'&since_id='.$sinceIdArr[$searchWord];
					}
					
					/* Variable initialization */
					$url 			= 	'https://api.twitter.com/1.1/search/tweets.json';
					$getfield 		= 	"?q=$searchWord&result_type=recent&count=5".$sinceIdQuery;
					$requestMethod 	= 	'GET';
					
					/* get twitter feed from data */
					$responseTweetDataJson 	= 	$twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();

					$responseTweetDataArr 	= 	json_decode($responseTweetDataJson, true);
					//debugVar($responseTweetDataArr, true);
					unset($responseTweetDataJson);
					
					if(empty($responseTweetDataArr)){
						$strGridCellDataArr[$index]					= array('feeder_name'=>'','feed'=>'No Feed fund for '.$strGridCellDataArr[$index]['feed'],'likes_count'=>0,'id'=>0);
					}else{
						foreach ($responseTweetDataArr as $tweetKey => $tweetData) {

							if ($tweetKey != 'statuses') {
								continue;
							}

							$firstTweetId = 0;
							$tweetSinceId = 0;

							if (!empty($tweetData) && is_array($tweetData)) {
								foreach ($tweetData as $tweet) {
									$feedContent = array('tweet_content' => $tweet);

									if (empty($firstTweetId)) {
										$firstTweetId 				= 	$tweet['id_str'];
										$tweetSinceId 				= 	$firstTweetId;
										$strGridCellDataArr[$index]['since_id']  		=	$tweetSinceId;
										$strGridCellDataArr[$index]['since_id'] = $tweetSinceId;
									}

									if((isset($tweet['user']['screen_name']) && isset($tweet['user']['name']) && isset($tweet['retweeted_status']['text'])) && (trim($tweet['user']['screen_name']) == "" || trim($tweet['user']['name']) == "" || trim($tweet['retweeted_status']['text']) == "")){
										//debugVar($tweet, true);
										continue;
									}

									$responseTweetDataJson['feeder_name'] 	= (!empty($tweet['user']['name'])) ? $tweet['user']['name'] : "";
									$responseTweetDataJson['feed'] 			= (!empty($tweet['retweeted_status']['text'])) ?  $tweet['retweeted_status']['text'] : "";
									$responseTweetDataJson['likes_count'] 	= (!empty($tweet['retweeted_status']['favorite_count'])) ? $tweet['retweeted_status']['favorite_count'] : "";
									$responseTweetDataJson['id'] 			= (!empty($tweet['retweeted_status']['id'])) ? $tweet['retweeted_status']['id'] : "";
									$strGridCellDataArr[$index] 			= (!empty($responseTweetDataJson) && is_array($responseTweetDataJson) ) ? $responseTweetDataJson : array();

									unset($responseTweetDataJson);
								}
							}else{
								$strGridCellDataArr[$index]					= array('feeder_name'=>'','feed'=>'No Feed fund for '.$strGridCellDataArr[$index]['feed'],'likes_count'=>0,'id'=>0);
							}
						}
					}
				}
			}
		}
			
		/* checking CUSTOM  IMAGE / CUSTOM TEXT / USER FEED*/
		if(!empty($contentTransArr)) {
			/* Setting feed filter array */
			$strWhereClauseArr = 
			/* Setting feed query array */
				$strFilterArr 			= 	array(
													'table' 	=> 	'trans_social_event_feeds',
													'column' 	=> 	array( 'id', 'event_code', 'data_code', 'feeder_name', 'title', 'content', 'platform_id', 'likes_count' ),
													'where' 	=> 	array( 'id' => $contentTransArr ),
												);
			/* get feeds */
			$strUserAdminFeedDataArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
			/* Setting feed type */
			$responseArr['feed_type'] 		= 'adminOrUserFeed';
			//debugVar($strUserAdminFeedDataArr, true);
			/* if feed details found then do needful */
			if(!empty($strUserAdminFeedDataArr)){
				/* Iterating the loop */
				foreach ($strUserAdminFeedDataArr as $cellIndex => $contentCode) {
					/* Getting content array index */
					//$intKeyIndex 										= array_search($contentCode, array_column($strUserAdminFeedDataArr, 'id'));
					/* Setting the feed details */
					$strGridCellDataArr[$cellIndex]['id'] 				= $contentCode['id'];
					$strGridCellDataArr[$cellIndex]['feed'] 			= $contentCode['title'];
					$strGridCellDataArr[$cellIndex]['feeder_name'] 		= $contentCode['feeder_name'];
					$strGridCellDataArr[$cellIndex]['platform_id'] 		= $contentCode['platform_id'];
					$strGridCellDataArr[$cellIndex]['likes_count'] 		= $contentCode['likes_count'];
					$strGridCellDataArr[$cellIndex]['refresh_timeout'] 	= $feedTimerArr[$cellNumber];
				}
				/* Checking for platform type */
				if (!empty($contentCode['platform_id'])) {
					/* Switch case for setting the feed type */
					switch ($contentCode['platform_id']) {
						case 1:
							$responseArr['feed_type'] 		= 'admin_text';
							break;
						case 2:
							$responseArr['feed_type'] 		= 'admin_image';
							break;
						case 3:
							$responseArr['feed_type'] 		= 'user_feed';
							break;
						default:
							break;
					}
				}
			}
		}
			
		$responseArr['cellDataArr'] 	= !empty($strGridCellDataArr) ? $strGridCellDataArr : array();
		//debugVar($responseArr, true);

		$this->load->view('social/wall-cell', $responseArr);

	}
	
	/**********************************************************************/
	/*Purpose 	: Event code verification.
	/*Inputs	: None.
	/*Returns	: Event status.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function event_code_verify(){
		/* Variable initialization */
		$eventPublicCode 	= (!empty($this->input->post('event-code'))) ? $this->input->post('event-code',true) : '';
		$strResponseArr 	= array('message' => 'Requested event does not exist.', 'status' => false);
		$eventDataArr 		= array();
		
		/* if event code is passed then verify it */
		if (!empty($eventPublicCode)) {
			/* Get the event details by event code */
			$eventDataArr = $this->_checkEventPublicCodeExist($eventPublicCode);
		}
		
		/* if event details found then do needful */
		if (!empty($eventDataArr)) {
			/* debugVar($_SERVER, true); */
			/* sub domain formation */
			$newSiteUrl 	= str_replace(array('http','https', 's://', '://', 'www.', '/'), array(''), SITE_URL);
			$protocol		= 'http://';
			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {  
				$protocol		= 'https://';
			} 
			/* $protocol 		= strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://'; */
			/* value overwriting */
			$strResponseArr = array('message' => 'Event Found. Redirecting to user feeds wall.', 'status' => true, 'destinationURL' => $protocol.$eventPublicCode.'.'.$newSiteUrl);
		}
		
		/* Return the response */
		jsonReturn($strResponseArr,true);
	}

	/**********************************************************************/
	/*Purpose 	: Get user feed.
	/*Inputs	: $eventCodeEnc :: User feed code.
	/*Returns	: Event status.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function user_feed_ajax($eventCodeEnc){
		/* Variable initialization */
		$intEventCode 	= ($eventCodeEnc !='')?getDecyptionValue($eventCodeEnc) : 0;
		$strSortType 	= ($this->input->post('txtUserFeedsSortOrder',true) !='')?$this->input->post('txtUserFeedsSortOrder',true) : 'recent';
		
		/* checking for event code */
		if($intEventCode == 0){
			/* return response */
			jsonReturn(array('message' => 'Invalid request.', 'status' => false), true);
		}
		
		/* Setting filter request */
		$strFilterArr 		= 	array(
										'table' 	=> 	array('trans_social_event_feeds', $this->_strPrimaryTableName),
										'join' 		=> 	array('', array('table'=>$this->_strPrimaryTableName.'.id=trans_social_event_feeds.event_code', 'type'=>'left')),
										'column' 	=> 	array('trans_social_event_feeds.event_code', $this->_strPrimaryTableName.'.event_public_code', $this->_strPrimaryTableName.'.company_code', $this->_strPrimaryTableName.'.name', $this->_strPrimaryTableName.'.description', 'trans_social_event_feeds.id', 'trans_social_event_feeds.platform_id', 'trans_social_event_feeds.feeder_name', 'trans_social_event_feeds.title', 'trans_social_event_feeds.likes_count'),
										'where' 	=> 	array($this->_strPrimaryTableName.'.id' => $intEventCode, 'trans_social_event_feeds.platform_id' => 3),
										'offset' 	=> 0,
										'limit' 	=> 1000,
										'order'=>array('trans_social_event_feeds.id' => 'DESC'),
									);
		/* if filter type like set the do needful */
		if ($strSortType == 'likes') {
			/* Value overwriting */
			$strFilterArr['order'] = array('trans_social_event_feeds.likes_count' => 'DESC');
		}

		/* Get users feeds */
		$userFeedDataArr 						= $this->_objDataOperation->getDataFromTable($strFilterArr);
	
		/* Getting company list */
		$strResponseArr['dataSet'] 				= $userFeedDataArr;
		$strResponseArr['eventCodeEnc'] 		= $eventCodeEnc;
		$strResponseArr['blnDevice'] 			= $this->_objDevice->isMobile();
		/* removed used variable */
		unset($userFeedDataArr, $strFilterArr);
		
		/* get user feed view */
		$this->load->view('social/user-feed-ajax', $strResponseArr);
	}

	/**********************************************************************/
	/*Purpose 	: Set user feed.
	/*Inputs	: $eventCodeEnc :: User feed code.
	/*Returns	: Transaction Status.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function add_user_feed_comment($eventCodeEnc){
		/* variable initialization */
		$intEventCode 		= ($eventCodeEnc !='') ?getDecyptionValue($eventCodeEnc) : 0;
		$userFeed 			= !empty($this->input->post('txtComment',true)) ? trim($this->input->post('txtComment',true)) : '';
		$FeederName 		= !empty($this->input->post('feeder_name',true)) ? trim($this->input->post('feeder_name',true)) : '';

		/* if event code is not pass then do needful */
		if($intEventCode == 0){
			/* return response */
			jsonReturn(array('status'=>false, 'message'=>'Invalid Request. Kindly contact to event manager.'), true);
		}
		
		/* if feeder name is not entered then do needful*/
		if($FeederName == ''){
			/* return response */
			jsonReturn(array('status'=>false, 'message'=>'You display name field is empty, please enter valid display name.'), true);
		}
		
		/* if feeder comments is not entered then do needful*/
		if($userFeed == ''){
			/* return response */
			jsonReturn(array('status'=>false, 'message'=>'You message field is empty, please enter message.'), true);
		}
		
		/* get value for limit of characters for Admin text */
		$userAndCustomTextFeedCharLimit = getUserAndCustomTextFeedCharLimit();
		
		/* checking for character limits */
		if (calculateStringLength($userFeed) > $userAndCustomTextFeedCharLimit['custom_text']['char']) {
			/* Validating the length */
			return jsonReturn(array('status'=>0,'message'=>'Custom text field is not more than '.$userAndCustomTextFeedCharLimit['custom_text']['char'].' character.'), true);
		}
		
		/* Setting the event filter */
		$gridstrGridCellDataArrArr = 	array(
												'table' 	=> 	'trans_social_event_feeds',
												'data' 		=> 	array(
																		'event_code' 	=> 	$intEventCode,
																		'feeder_name' 	=> 	$FeederName,
																		'title' 		=> 	$userFeed,
																		'platform_id' 	=> 	3,
																),
											);
		/* Adding the event details */
		$feedId 	= 	$this->_objDataOperation->setDataInTable($gridstrGridCellDataArrArr);
		/* removed used variable */
		unset($gridstrGridCellDataArrArr);
		
		/* if operation done successfully then do needful */
		if ($feedId > 0) {
			/* return response */
			jsonReturn(array('status'=>true, 'message'=>'You message posted successfully.','destinationURL'=>$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME']), true);
		}else{
			/* return response */
			jsonReturn(array('status'=>true, 'message'=>DML_ERROR), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Updating the like count.
	/*Inputs	: None.
	/*Returns	: Transaction Status.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function like_user_feed(){
		/* variable initialization */
		$intFeedCode 	= ($this->input->post('txtUserFeedsCode',true)) ? getDecyptionValue($this->input->post('txtUserFeedsCode',true)) : 0;
		$strResponseArr = array();

		/* if feed code is not passed then do needful */
		if($intFeedCode == 0){
			/* return response */
			jsonReturn(array('message' => 'Invalid request, please contact to event manager.', 'status' => false), false);
		}
		 
		/* Setting the like tracking data */
		$strFilterArr 	= 	array(
									'table' 	=> 	'trans_social_event_feed_likes',
									'data' 		=> 	array(
															'ip_address' 	=> 	get_client_ip(),
															'feed_id' 		=> 	$intFeedCode,
															'type' 			=> 	'user_feed',
														),
								);
		/* Set log */
		$this->_objDataOperation->setDataInTable($strFilterArr);
		
		/* Update the like count of requested user feed array filter */
		$strFilterArr 	= 	array(
									'table' 	=> 	'trans_social_event_feeds',
									'data' 		=> 	array(
															'likes_count' 	=> 	'likes_count + 1',
															'feed_id' 		=> 	$intFeedCode,
															'type' 			=> 	'user_feed',
														),
								);
		
		/* Update the like count */
		$this->_objDataOperation->getDirectQueryResult("update trans_social_event_feeds set likes_count = likes_count + 1 where id = ".$intFeedCode);
		/* get the feed details */
		$strFilterArr 		= 	array(
										'table' 	=> 	'trans_social_event_feeds',
										'column' 	=> 	array('likes_count'),
										'where' 	=> 	array('id'=>$intFeedCode),
									);
		
		/* Get user feed details */
		$strFeedDetailsArr 	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		/* Value overwriting */
		$strResponseArr	= array('status'=>true, 'message'=>'It\'s upvoted !!!','userFeedIdEnc'=>getEncyptionValue($intFeedCode),'likeCount'=>0);
		
		/* if user feeds details found then do needful */
		if(!empty($strFeedDetailsArr)){
			/* Setting value */
			$strResponseArr['likeCount']	= $strFeedDetailsArr[0]['likes_count'];
		}
		/* removed used variable */
		unset($strFilterArr);
		
		/* return response */
		jsonReturn($strResponseArr, true); 
	}

	/**********************************************************************/
	/*Purpose 	: get the event details by event code.
	/*Inputs	: $eventPublicCode : :Event code.
	/*Returns	: Event details.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	private function _checkEventPublicCodeExist($eventPublicCode){
		/* variable initialization */
		$strResponseArr		= array();
		
		/* if event code is empty then do needful */
		if($eventPublicCode == ''){
			/* return array */
			return $strResponseArr;
		}
		
		/* set filter where clause array */
		$strWhereArr 		= array('event_public_code' => $eventPublicCode);
		
		/* Set filter array */
		$strFilterArr 	= 	array(
									'table' 	=> 	$this->_strPrimaryTableName,
									'column' 	=> 	array('id', 'event_public_code', 'company_code', 'name', 'description'),
									'where' 	=> 	$strWhereArr,
								);
		/* get event details */
		$strResponseArr =  $this->_objDataOperation->getDataFromTable($strFilterArr);
		/* removed used variable */
		unset($strWhereArr, $strFilterArr);
		
		/* return event details */
		return $strResponseArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get event code details from URL.
	/*Inputs	: None.
	/*Returns	: Event code.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	private function _getEventCode(){
		/* Get URL */
		$newSiteUrl 		= str_replace(array('http','https', 's://', '://', 'www.', '/'), array(''), SITE_URL);
		/* Get the event public code */
		$eventPublicCode 	= str_replace(array($newSiteUrl, '.'), array(''), $_SERVER['SERVER_NAME']);
		
		/* return event code */
		return $eventPublicCode;
	}

	/**********************************************************************/
	/*Purpose 	: Get Event Alert Message
	/*Inputs	: Event Code.
	/*Returns	: JSON Of Alert Message.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function get_active_alert_message_by_event_code($eventCodeEnc){

		$response 		= array('message' => 'Event Code Not Found or incorrect!', 'status' => false);
		$eventCode 		= getDecyptionValue($eventCodeEnc);

		if (empty($eventCode)) {
			return $this->output
								->set_content_type('application/json')
								->set_status_header(200)
								->set_output(json_encode($response));
			exit;
		}

		$currentDateTime 	= (new \DateTime())->format('YmdHis');
		$alertMsgDataArr 	= $this->_fetchActiveAlertMessageByEventCode($eventCode, $currentDateTime);

		if (!empty($alertMsgDataArr)) {
			$alertMsgDataArr['id'] 			= getEncyptionValue($alertMsgDataArr['id']);
			$alertMsgDataArr['event_code'] 	= getEncyptionValue($alertMsgDataArr['event_code']);

			$fromDate 	= strtotime($alertMsgDataArr['from_date']);
			$toDate 	= strtotime($alertMsgDataArr['to_date']);

			$alertMsgDataArr['from_time'] = date('h:i A', $fromDate);
			$alertMsgDataArr['from_date'] = date('Y/m/d', $fromDate);

			$alertMsgDataArr['to_time'] = date('h:i A', $toDate);
			$alertMsgDataArr['to_date'] = date('Y/m/d', $toDate);

			return $this->output
								->set_content_type('application/json')
								->set_status_header(200)
								->set_output(json_encode(array('status' => true, 'message' => 'Data founded' , 'alert_message' => $alertMsgDataArr)));
		}else{
			return $this->output
								->set_content_type('application/json')
								->set_status_header(200)
								->set_output(json_encode(array('status' => false, 'message' => 'Data not founded')));
		}
		exit;

	}


    /**********************************************************************/
    /*Purpose 	: Get Active alert message alert by event code.
    /*Inputs	: $eventCode :: Event code.
    /*Returns	: Alert Message details
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
	private function _fetchActiveAlertMessageByEventCode($eventCode, $dateTime){

		$strFilterArr 	= 	array(
								'table' 	=> 'trans_event_alert_messages',
								'column' 	=> array('id', 'event_code', 'alert_message', 'from_date', 'to_date', 'updated_date'),
								'where' 	=> array('event_code' => $eventCode, 'from_date <= ' => $dateTime, 'to_date >= ' => $dateTime),
								'offset' 	=> 0,
								'limit' 	=> 1,
								'order' 	=> array('id' => 'DESC'),
						);

		$alertMsgDataArr 	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		return !empty($alertMsgDataArr) && !empty($alertMsgDataArr[0]) ? $alertMsgDataArr[0] : array();

	}

}
