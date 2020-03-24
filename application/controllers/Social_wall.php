<?php
/***********************************************************************/
/* Purpose 		: Socail Wall - Social media login and fetching the feeds.
/* Created By	: Vipin Kumar R. Jaiswar.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Social_wall extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName				= 'events';
	private $_configTwitter						= array();
	private $_strFeedTypesArr					= array();
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Vipin Kumar R. Jaiswar.
	/**********************************************************************/
    public function __construct() {
		/* calling parent construct */
		parent::__construct();
		debugVar(array(), true);
		/* Set the primary scheme */
		$this->_strPrimaryTableName	= $this->_strPrimaryTableName.'_'.$this->getCompanyCode();
		
		/* Setting feed types */
		$this->_strFeedTypesArr		= array('user_feed', 'admin_text', 'admin_image', 'social_twitter');
    }

    /**********************************************************************/
    /*Purpose 	: Setup the social wall grid
    /*Inputs	: None.
    /*Returns	: Creating the wall grid.
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
    public function grid_setup(){
		/* variable initialization */
		$intEventCode		= ($this->input->get('event-code') != '')?getDecyptionValue($this->input->get('event-code')):0;
		$blnDeleteRequest	= ($this->input->post('txtDeleteRecordCode'))?true:false;
		$strGridCellDataArr = $strGridConfigSetArr	= $dataArr	= array();
		$strModuleTitle		= 'Event Wall';
		
		/* Checking for event code is set of not */
		if ($intEventCode == 0) {
			/* return on the event list */
			redirect(SITE_URL.'/mod/event-wall', 'refresh');
		}
		
		/* if user feed delete request then do needful  */
		if($blnDeleteRequest){
			/* calling deactivation process. */
			$this->_setInactiveUserFeed();
		}
		
		
		/* Set the filter clause */
		$strWhereClauseArr 	= 	array($this->_strPrimaryTableName.'.id' => $intEventCode, $this->_strPrimaryTableName.'.company_code' => $this->getCompanyCode());
		
		/* Setting the data filter array */
		$strFilterArr 		= 	array(
										'table' 		=> 	array($this->_strPrimaryTableName, 'trans_social_event_feeds_wall_grid_config'),
										'column' 		=> 	array($this->_strPrimaryTableName.'.id', $this->_strPrimaryTableName.'.name', $this->_strPrimaryTableName.'.description', $this->_strPrimaryTableName.'.event_public_code', 'trans_social_event_feeds_wall_grid_config.id as grid_id', 'trans_social_event_feeds_wall_grid_config.event_code', 'trans_social_event_feeds_wall_grid_config.rows', 'trans_social_event_feeds_wall_grid_config.columns', 'trans_social_event_feeds_wall_grid_config.type'),
										'join' 			=> 	array('', array('table' => 'trans_social_event_feeds_wall_grid_config.event_code='.$this->_strPrimaryTableName.'.id', 'type' => 'left') ),
										'group' 		=> 	$this->_strPrimaryTableName.'.id',
										'ignoreDelete' 	=> 	array('trans_social_event_feeds_wall_grid_config' => 'trans_social_event_feeds_wall_grid_config'),
										'where' 		=> 	$strWhereClauseArr,
									);

		/* Getting the grid configuration list */
		$strGridConfigArr	= $strGridConfigSetArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if gird configuration is not set then do needful */
		if(empty($strGridConfigArr)){
			/* return on the event list */
			redirect(SITE_URL.'/mod/event-wall', 'refresh');
			/* Process with configuration details */
		}else{
			/* variables initialization */
			$contentMasterArr = $contentTransArr = $feedCntAndTimeArr = array();
			
			/* Set the gird configuration filter clause */
			$strWhereClauseArr 	= 	array('trans_social_event_feeds_wall_grid_config.event_code' => $intEventCode);
			/* Setting the gird configuration data filter array */
			$strFilterArr 		= 	array(
											'table' 	=> 	array('trans_social_event_feeds_wall_cell_config', 'trans_social_event_feeds_wall_grid_config'),
											'column' 	=> 	array('trans_social_event_feeds_wall_cell_config.grid_id', 'trans_social_event_feeds_wall_cell_config.cell_index', 'trans_social_event_feeds_wall_cell_config.content_code', 'trans_social_event_feeds_wall_cell_config.is_feed', ', count(`trans_social_event_feeds_wall_cell_config`.`cell_index`) AS feed_cnt', '`trans_social_event_feeds_wall_cell_config`.`refresh_timeout`'),
											'join' 		=> 	array('', 'trans_social_event_feeds_wall_grid_config.id=trans_social_event_feeds_wall_cell_config.grid_id' ),
											'where' 	=> 	$strWhereClauseArr,
											'group' 	=> 	array('trans_social_event_feeds_wall_cell_config.grid_id', '`trans_social_event_feeds_wall_cell_config`.`cell_index`'),
											'order'=>array('grid_id' => 'ASC', 'cell_index' => 'ASC', 'trans_social_event_feeds_wall_cell_config.id' => 'DESC'),
									);
			/* get the grid cell configuration details */
			$socialCellFeedDataArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
			
			/* if grid cell details found then do needful */
			if(!empty($socialCellFeedDataArr)){
				/* iterating the loop */
				foreach ($socialCellFeedDataArr as $index => $socialFeedData) {
					/* Setting post/feeds count */
					$feedCntAndTimeArr['count'][$socialFeedData['cell_index']] 		= $socialFeedData['feed_cnt'];
					/* Setting refresh time */
					$feedCntAndTimeArr['time'][$socialFeedData['cell_index']] 		= $socialFeedData['refresh_timeout'];
					/* checking got twitter feed */
					if (!empty($socialFeedData['is_feed'])) {
						/* Setting the twitter handler or tags array */
						$contentMasterArr[$socialFeedData['cell_index']] = $socialFeedData['content_code'];
					}else{
						/* Setting other feeds such as manual / user / CUSTOM  IMAGE / CUSTOM TEXT */
						$contentTransArr[$socialFeedData['cell_index']] = $socialFeedData['content_code'];
					}
				}
			}
			
			/* Checking for twitter handler or tags (H/T) array if not empty then do needful*/
			if (!empty($contentMasterArr)) {
				/* Setting twitter H/T filter array */
				$strWhereClauseArr = array( 'tags_'.$this->getCompanyCode().'.id' => $contentMasterArr );
				/* Setting twitter H/T query array */
				$strFilterArr 		= 	array(
												'table' 	=> 	array('tags_'.$this->getCompanyCode(), 'master_widget_attributes_list'),
												'column' 	=> 	array( 'tags_'.$this->getCompanyCode().'.id', 'event_code', 'hash-tag- as data_desc', 'tag-type as data_type', 'master_widget_attributes_list.description AS data_type_desc' ),
												'join' 		=> 	array('', array('table'=>'tags_'.$this->getCompanyCode().'.tag-type=master_widget_attributes_list.id ', 'type'=>'left')),
												'where' 	=> 	$strWhereClauseArr,
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
						$strGridCellDataArr[$cellIndex]['platform_id'] 		= 4;
						$strGridCellDataArr[$cellIndex]['feed_cnt'] 		= isset($feedCntAndTimeArr['count'][$cellIndex])?$feedCntAndTimeArr['count'][$cellIndex]:0;
						$strGridCellDataArr[$cellIndex]['refresh_timeout'] 	= isset($feedCntAndTimeArr['time'][$cellIndex])?$feedCntAndTimeArr['time'][$cellIndex]:0;
					}
				}
			}
			
			/* checking CUSTOM  IMAGE / CUSTOM TEXT / USER FEED*/
			if (!empty($contentTransArr)) {
				/* Setting feed filter array */
				$strWhereClauseArr = array( 'id' => $contentTransArr );
				/* Setting feed query array */
				$strFilterArr 		= 	array(
												'table' 	=> 	'trans_social_event_feeds',
												'column' 	=> 	array( 'id', 'event_code', 'data_code', 'feeder_name', 'title', 'content', 'platform_id' ),
												'where' 	=> 	$strWhereClauseArr,
											);
				/* get feeds */
				$strUserAdminFeedDataArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
				
				/* if feed details found then do needful */
				if(!empty($strUserAdminFeedDataArr)){
					/* Iterating the loop */
					foreach ($contentTransArr as $cellIndex => $contentCode) {
						/* Getting content array index */
						$intKeyIndex 										= array_search($contentCode, array_column($strUserAdminFeedDataArr, 'id'));
						/* Setting the feed details */
						$strGridCellDataArr[$cellIndex]['id'] 				= $strUserAdminFeedDataArr[$intKeyIndex]['id'];
						$strGridCellDataArr[$cellIndex]['feed'] 			= $strUserAdminFeedDataArr[$intKeyIndex]['title'];
						$strGridCellDataArr[$cellIndex]['feeder_name'] 		= $strUserAdminFeedDataArr[$intKeyIndex]['feeder_name'];
						$strGridCellDataArr[$cellIndex]['platform_id'] 		= $strUserAdminFeedDataArr[$intKeyIndex]['platform_id'];
						$strGridCellDataArr[$cellIndex]['feed_cnt'] 		= isset($feedCntAndTimeArr['count'][$cellIndex])?$feedCntAndTimeArr['count'][$cellIndex]:0;
						$strGridCellDataArr[$cellIndex]['refresh_timeout'] 	= isset($feedCntAndTimeArr['time'][$cellIndex])?$feedCntAndTimeArr['time'][$cellIndex]:0;
					}
				}
			}
			
			/* removed the used variables */
			unset($contentMasterArr, $contentTransArr, $feedCntAndTimeArr, $strUserAdminFeedDataArr);
		}
		
		/* if event code is not found then do needful */
		if(empty($strGridConfigSetArr)){
			/* Setting event details filter by code */
			$strWhereClauseArr 	= 	array($this->_strPrimaryTableName.'.id' => $intEventCode);
			/* Setting the event details data filter array */
			$strFilterArr 		= 	array('table' 	=> 	array($this->_strPrimaryTableName),'where' 	=> 	$strWhereClauseArr);
			/* get the grid cell configuration details */
			$strGridConfigSetArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		}
		
		/* if grid / event details found then do needful */
		if(!empty($strGridConfigSetArr) && (isset($strGridConfigSetArr[0]))){
			/* Tile value overwriting */
			$strModuleTitle	= '<a href="'.SITE_URL.'mod/event-wall">'.$strGridConfigSetArr[0]['name'].'</a> > '.$strModuleTitle;
		}
		/* removed user variables */
		unset($strGridConfigSetArr);
		
		/* Getting company list */
		$strResponseArr['dataSet'] 				= array();
		$strResponseArr['moduleTitle']			= $strModuleTitle;
		$strResponseArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strResponseArr['deleteUri']			= '';
		$strResponseArr['getRecordByCodeUri']	= '';
		$strResponseArr['strDataAddEditPanel']	= 'moduleModel';
		$strResponseArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strResponseArr['gridConfig']			= $strGridConfigArr;

		$strResponseArr['eventUrl'] 			= (!empty($strGridConfigArr) && !empty($strGridConfigArr[0]) && !empty($strGridConfigArr[0]['event_public_code'])) ? str_replace(array('www.','://'), array('','://'.$strGridConfigArr[0]['event_public_code'].'.'), SITE_URL) : '';

		$strResponseArr['strGridCellDataArr'] 	= $strGridCellDataArr;
		$strResponseArr['noAction'] 			= true;
		$strResponseArr['strUserAndCustomTextFeedCharLimitArr'] = getUserAndCustomTextFeedCharLimit();
	 
		/* Get grid template */
		$dataArr['body']						= $this->load->view('social/grid-config', $strResponseArr, true);

		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);
    }

	/**********************************************************************/
    /*Purpose 	: Display user feeds details.
    /*Inputs	: None.
    /*Returns	: User feed HTML.
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
    public function load_feed_by_event_code($eventCodeEnc, $edit = 0){
		/* Variable initialization */
    	$responseArr 		= $strSelectedFeedDataArr	= $strSelecetdDataArr = array();
    	$eventCode 			= getDecyptionValue($eventCodeEnc);
    	$response 			= array('message' => 'Error: Something wrong!', 'status'=> false);
    	$sort 				= !empty($this->input->get('sort')) ? (is_numeric($this->input->get('sort'))?'recent':$this->input->get('sort')) : 'recent';
    	$box_number 		= !empty($this->input->get('box_number')) ? $this->input->get('box_number') : 0;
    	$strPlateformType	= !empty($this->input->get('pStrPlatformType')) ? $this->input->get('pStrPlatformType') : 'user_feed';
		
		/* if event code is not pass or wrong then do needful */
    	if (empty($eventCode)) {
    		$response 		= array('message' => 'Error: Event Code invalid!', 'status'=> false);
    	}
		
		/* Checking for feed type */
		if($strPlateformType == 'user_feed'){
			/* Set query filter */
			$strFilterArr 		= 	array(
												'table' 	=> 'trans_social_event_feeds',
												'column' 	=> array('trans_social_event_feeds.id', 'trans_social_event_feeds.event_code', 'trans_social_event_feeds.data_code', 'trans_social_event_feeds.feeder_name', 'trans_social_event_feeds.title', 'trans_social_event_feeds.content', 'trans_social_event_feeds.platform_id', 'trans_social_event_feeds.likes_count' ),
												'where' 	=> array('trans_social_event_feeds.platform_id' => 3, 'trans_social_event_feeds.event_code' => $eventCode),
												'order'		=> array('id' => 'DESC')
											);
			
		}else{
			/* Setting query filter for social handler and hast tags */
			$strFilterArr 		= 	array(
											'table' 	=> 	array('tags_'.$this->getCompanyCode(), 'master_widget_attributes_list'),
											'column' 	=> 	array('tags_'.$this->getCompanyCode().'.id', 'tags_'.$this->getCompanyCode().'.event_code', 'tags_'.$this->getCompanyCode().'.hash-tag- as data_desc', 'tags_'.$this->getCompanyCode().'.tag-type as data_type', 'master_widget_attributes_list.description AS data_type_desc' ),
											'join' 		=> 	array('', array('table'=>'tags_'.$this->getCompanyCode().'.tag-type=master_widget_attributes_list.id ', 'type'=>'left')),
											'where' 	=> 	array('tags_'.$this->getCompanyCode().'.event_code' => $eventCode),
											'order'		=> array('id' => 'DESC')
										);
		}
		
		/* if requested the sort options then do needful */
		if ($sort == 'likes') {
			/* Sort by number of likes */
			$strFilterArr['order'] = array_merge(array('likes_count' => 'DESC'), $strFilterArr['order']);
		} 

		/* Getting the user feed details list */
		$socialFeedDataArr 				= 	$this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if social feed array not found then do needful */
		if(!empty($socialFeedDataArr)){
			/* Checking for feed type */
			if($strPlateformType == 'user_feed'){
				/* Set filter array */
				$strFilterArr 		= 	array(
												'table' 	=> 	array('trans_social_event_feeds', 'trans_social_event_feeds_wall_cell_config'),
												'join' 		=> 	array('', array('table'=>'trans_social_event_feeds.id=trans_social_event_feeds_wall_cell_config.content_code ', 'type'=>'left')),
												'column' 	=> 	array('`trans_social_event_feeds_wall_cell_config`.`content_code`'),
												'where' 	=> 	array('trans_social_event_feeds.platform_id' => 3, 'trans_social_event_feeds.event_code' => $eventCode, 'trans_social_event_feeds_wall_cell_config.cell_index' => $box_number),
												'group' 	=> 	'trans_social_event_feeds.id'
											);
			}else{
				/* Setting the social handler and has tags filter array */
				$strFilterArr 		= 	array(
												'table' 	=> 	array('tags_'.$this->getCompanyCode(), 'trans_social_event_feeds_wall_cell_config'),
												'join' 		=> 	array('', array('table'=>'tags_'.$this->getCompanyCode().'.id=trans_social_event_feeds_wall_cell_config.content_code ', 'type'=>'left')),
												'column' 	=> 	array('`trans_social_event_feeds_wall_cell_config`.`content_code`'),
												'where' 	=> 	array('tags_'.$this->getCompanyCode().'.event_code' => $eventCode, 'trans_social_event_feeds_wall_cell_config.cell_index' => $box_number),
												'group' 	=> 	'tags_'.$this->getCompanyCode().'.id'
											);
			}
										
			/* Getting the selected user feed details list */
			$strSelectedFeedDataArr 	= 	$this->_objDataOperation->getDataFromTable($strFilterArr);
			/* if selected feed code found then do needful */
			if(!empty($strSelectedFeedDataArr)){
				/* Iterating the loop */
				foreach($strSelectedFeedDataArr as $strSelectedFeedDataArrKey => $strSelectedFeedDataArrVal){
					/* Setting value */
					$strSelecetdDataArr[$strSelectedFeedDataArrVal['content_code']]	= $strSelectedFeedDataArrVal['content_code'];
				}
			}
			/* removed used dataset */
			unset($strSelectedFeedDataArr);
		}
		 
		/* Setting the data array for displaying the user feeds */
		$responseArr['feedsArr'] 			= 	$socialFeedDataArr;
		$responseArr['strSelectedFeedsArr'] = 	$strSelecetdDataArr;
		$responseArr['eventCodeEnc'] 		= 	$eventCodeEnc;
		$responseArr['box_number'] 			= 	$box_number;
		$responseArr['strFilterBy'] 		= 	$sort;
		/* get the user feed details */

		if($strPlateformType == 'user_feed'){
			$this->load->view('social/load-feed-by-event-code', $responseArr);
		}else{
			$this->load->view('social/load-twitter-by-event-code', $responseArr);
		}
    }
	
	/**********************************************************************/
    /*Purpose 	: Set the cell content.
    /*Inputs	: None.
    /*Returns	: Transaction status.
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
    public function add_update_grid_cell($eventCodeEnc, $cellNumber){
		/* Variable initialization */
    	$transStatus 	 	= false;
    	$setInsUpfeedArr 	= $strGridCellDataArr = $strResponseArr = $dataArr = array();
		$response 		 	= array('message' => 'Error: Something wrong!', 'status'=> false);
    	$eventCode 			= getDecyptionValue($eventCodeEnc);
    	$platform 			= $this->input->post('platform');
    	$feed_id 			= !empty($this->input->post('feed_id')) ? getDecyptionValue($this->input->post('feed_id')) : 0;
    	$feedPlatformId 	= !empty($this->input->post('feed_platform_id')) ? getDecyptionValue($this->input->post('feed_platform_id')) : 0;
		
		if((int)$eventCode <= 0){
			/* Return the JSON string */
			jsonReturn(array('message'=>"Invalid event code",'status'=>0), true);
		}
		
		/* if cell index is not pass then do needful */
		if((int)$cellNumber <= 0){
			/* Return the JSON string */
			jsonReturn(array('message'=>"Invalid cell index",'status'=>0), true);
		}
		
		/* Set the event grid configuration details */
		$strFilterArr 		= 	array(
										'table' 	=> 	array('trans_social_event_feeds_wall_grid_config', 'trans_social_event_feeds_wall_cell_config'),
										'join' 		=> 	array('', array('table'=>'trans_social_event_feeds_wall_grid_config.id=trans_social_event_feeds_wall_cell_config.grid_id AND trans_social_event_feeds_wall_cell_config.cell_index ='. $cellNumber, 'type'=>'left')),
										'column' 	=> 	array('trans_social_event_feeds_wall_grid_config.id', 'trans_social_event_feeds_wall_grid_config.event_code', 'trans_social_event_feeds_wall_grid_config.rows', 'trans_social_event_feeds_wall_grid_config.columns', 'trans_social_event_feeds_wall_grid_config.type', 'trans_social_event_feeds_wall_cell_config.refresh_timeout'),
										'where' 	=> 	array('trans_social_event_feeds_wall_grid_config.event_code' => $eventCode),
										'order' 	=> 	array('trans_social_event_feeds_wall_grid_config.id' => 'DESC'),
										'group' 	=> 	'trans_social_event_feeds_wall_grid_config.id',
										'ignoreDelete'=>array('trans_social_event_feeds_wall_cell_config'=>'trans_social_event_feeds_wall_cell_config'),
										'offset' 	=> 	0,
										'limit' 	=> 	1,
									);

		/* Getting the status list */
		$strEventGridConfigArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if event grid configuration details not found then do needful */
		if(empty($strEventGridConfigArr)){
			/* Return the JSON string */
			jsonReturn(array('message'=>"Grid configuration details not found. Looks like some one temping with codes.",'status'=>0), true); 
		}
		
		$eventGridConfigId 	= (isset($strEventGridConfigArr[0]['id']) ? $strEventGridConfigArr[0]['id'] : 0);
		$refreshTimeout 	= (isset($strEventGridConfigArr[0]['refresh_timeout']) ? $strEventGridConfigArr[0]['refresh_timeout'] : 0);
		
		/* if event grid cell configuration details not found then do needful */
		if($eventGridConfigId == 0){
			/* Return the JSON string */
			jsonReturn(array('message'=>"Grid cell configuration details not found. Looks like some one temping with codes.",'status'=>0), true); 
		}

		$this->db->trans_begin();
		
		/* base on the platform type doing needful */
		switch($platform){
			case 'user_feed':
				/* Setting user feeds */
				$setInsUpfeedArr	= $this->_setUserFeeds($eventCode, $cellNumber, $eventGridConfigId, $refreshTimeout);
				break;
			case 'admin_text':
				/* Setting administrator text */
				$setInsUpfeedArr	= $this->_setAdminText($eventCode, $cellNumber, $eventGridConfigId, $refreshTimeout,$feed_id);
				break;
			case 'admin_image':
				/* Setting administrator text */
				$setInsUpfeedArr	= $this->_setCustomImage($eventCode, $cellNumber, $eventGridConfigId, $refreshTimeout,$feed_id);
				break;
			case 'social_twitter':
				/* Setting administrator text */
				$setInsUpfeedArr	= $this->_setSocialFeeds($eventCode, $cellNumber, $eventGridConfigId);
				break;
			case 'set_timer':
				/* Setting time interval of cell */
				$setInsUpfeedArr	= $this->_setCellInterval($cellNumber, $eventGridConfigId);
				break;
		}
 
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$response 		= array('message' => 'Error: Something wrong with insert or update in database !', 'status'=> false);
		}else{
			$transStatus = $this->db->trans_commit();
			if ($transStatus) {
				$response 		= array('message' => 'Grid cell config set successfully', 'status'=> true);
			}
		}
		
		/* Variable initialization */
		$strUserAdminFeedDataArr	= $strResponseArr	= $strGridCellDataArr	= array();
		
	 	/* checking for feeds type */
		if(in_array($platform, $this->_strFeedTypesArr)){
			/* if feed type not found then do needful */
			if (!empty($setInsUpfeedArr['is_feed'])) {
				/* Get the data from master feed table */
				$strFilterArr 		= 	array(
												'table' 	=> 	'master_social_feed_data',
												'column' 	=> 	array( 'id', 'event_code', 'data_desc', 'data_type' ),
												'where' 	=> 	array( 'id' => $setInsUpfeedArr['feed_id'] )
											);

				
			}else{
				/* Get the data from feed transaction table */
				$strFilterArr 		= 	array(
												'table' 	=> 	'trans_social_event_feeds',
												'column' 	=> 	array( 'id', 'event_code', 'data_code', 'feeder_name', 'title', 'content', 'platform_id' ),
												'where' 	=> 	array( 'id' => $setInsUpfeedArr['feed_id'] ),
											);
			}
			/* Get the feed details */
			$strUserAdminFeedDataArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
			/* if feed details found then do needful */
			if(!empty($strUserAdminFeedDataArr)){
				/* Setting the values */
				$strGridCellDataArr[$cellNumber]['id'] 				= $strUserAdminFeedDataArr[0]['id'];
				$strGridCellDataArr[$cellNumber]['feed'] 			= isset($strUserAdminFeedDataArr[0]['data_desc'])?$strUserAdminFeedDataArr[0]['data_desc']:$strUserAdminFeedDataArr[0]['title'];
				$strGridCellDataArr[$cellNumber]['feeder_name'] 	= isset($strUserAdminFeedDataArr[0]['data_type'])?$strUserAdminFeedDataArr[0]['data_type']:$strUserAdminFeedDataArr[0]['feeder_name'];
				$strGridCellDataArr[$cellNumber]['platform_id'] 	= (!empty($setInsUpfeedArr['is_feed']))?4:$strUserAdminFeedDataArr[0]['platform_id'];
				$strGridCellDataArr[$cellNumber]['feed_cnt'] 		= $setInsUpfeedArr['feed_cnt'];
				if (!empty($refreshTimeout)) {
					$strGridCellDataArr[$cellNumber]['refresh_timeout'] = $refreshTimeout;
				}
			}
			/* removed used variable */
			unset($strUserAdminFeedDataArr);
			
			/* checking for grid cell data set */
			if (!empty($strGridCellDataArr)) {
				/* variable initialization */
				$strResponseArr['i'] 					= $cellNumber;
				$strResponseArr['columns'] 				= !empty($this->input->post('columns')) ? $this->input->post('columns') : 1;
				$strResponseArr['eventCodeEnc'] 		= $eventCodeEnc;
				$strResponseArr['strGridCellDataArr'] 	= !empty($strGridCellDataArr[$cellNumber]) ? $strGridCellDataArr[$cellNumber] : array();
				$strResponseArr['cell_html']			= $this->load->view('social/grid-config-cell', $strResponseArr, true);		
			}
			/* removed used variable */
			unset($strGridCellDataArr);
			
		/* checking for custom settings */
		}else if ($platform == 'set_timer') {
			$strResponseArr['timer'] 		= !empty($this->input->post('timer')) ? $this->input->post('timer') : '';
			$strResponseArr['timer_type'] 	= !empty($this->input->post('timer_type')) ? $this->input->post('timer_type') : '';
		}
		
		/* return the response */
		jsonReturn(array_merge(array('status'=>1, 'message'=>'Grid cell configuration done successfully.'),$strResponseArr), true);
    }
	
	/**********************************************************************/
    /*Purpose 	: Set user feeds into the grid cell.
    /*Inputs	: $pIntEventCode :: Event code,
				: $pIntCellCode	:: Cell index,
				: $pIntEventGridCode :: Grid cell code,
				: $pIntCellRefreshInterval :: Grid cell refresh interval.
    /*Returns	: Transaction status.
    /*Created By: Jaiswar Vipin Kumar R.
    /**********************************************************************/
	private function _setUserFeeds($pIntEventCode = 0, $pIntCellCode = 0, $pIntEventGridCode = 0, $pIntCellRefreshInterval = 0){
		/* variable initialization */
		$strUserFeedArr 	= 	(!empty($this->input->post('sel_feed')) && is_array($this->input->post('sel_feed'))) ? $this->input->post('sel_feed') : array();
		
		/* if user feeds are not selected then do needful */
		if(empty($strUserFeedArr)){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'No user feed(s) is selected.'), true);
		}
		
		/* if user selected more then pre configured feed then do needful */
		if(count($strUserFeedArr) > USER_FEED_VISIBLE_IN_CELL){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'You can select Max '.USER_FEED_VISIBLE_IN_CELL.' numbers of user feeds.'), true);
		}
		
		/* Deactivating the previous configuration */
		$this->_objDataOperation->setUpdateData(array('table'=>'trans_social_event_feeds_wall_cell_config','data'=>array('deleted'=>1,'updated_by'=>$this->getUserCode()),'where'=>array('grid_id'=>$pIntEventGridCode,'cell_index'=>$pIntCellCode)));
		
		/* iterating the loop */
		foreach ($strUserFeedArr as $strUserFeedArrKey => $strUserFeedArrValue) {
			/* Adding grid cell configuration */
			$gridstrGridCellDataArrArr = 	array(
													'table' 	=> 	'trans_social_event_feeds_wall_cell_config',
													'data' 		=> 	array(
														'grid_id' 		=> 	$pIntEventGridCode,
														'cell_index' 	=> 	$pIntCellCode,
														'content_code' 	=> 	getDecyptionValue($strUserFeedArrValue),
														'is_feed' 		=> 	0,
													),
												);
			
			/* if refresh interval is not empty then do needful */
			if (!empty($pIntCellRefreshInterval)) {
				/* Set the grid refresh interval */
				$gridstrGridCellDataArrArr['data']['refresh_timeout'] = $pIntCellRefreshInterval;
			}
			/* Adding the data in the configuration table */
			$this->_objDataOperation->setDataInTable($gridstrGridCellDataArrArr);
		}
		
		/* return array */
		return array('is_feed'=>0, 'feed_cnt'=> count($strUserFeedArr), 'feed_id'=>getDecyptionValue($strUserFeedArrValue));
	}
	
	/**********************************************************************/
    /*Purpose 	: Set Admin feeds into the grid cell.
    /*Inputs	: $pIntFeedCode :: Feed code,
				: $pIntCellCode	:: Cell index,
				: $pIntEventGridCode :: Grid cell code,
				: $pIntCellRefreshInterval :: Grid cell refresh interval,
				: $pIntFeedCode :: Feed code.
    /*Returns	: Transaction status.
    /*Created By: Jaiswar Vipin Kumar R.
    /**********************************************************************/
	private function _setAdminText($pIntEventCode = 0, $pIntCellCode = 0, $pIntEventGridCode = 0, $pIntCellRefreshInterval = 0, $pIntFeedCode = 0){
		/* variable initialization */
		$strAdminText 	= 	(!empty($this->input->post('custom_text'))) ? trim($this->input->post('custom_text')) : '';
		
		/* if user feeds are not selected then do needful */
		if(empty($strAdminText)){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'Custom text field is empty.'), true);
		}

		// get value for limit of characters for admin text
		$userAndCustomTextFeedCharLimit = getUserAndCustomTextFeedCharLimit();
		
		/* checking for character limits */
		if (calculateStringLength($strAdminText) > $userAndCustomTextFeedCharLimit['custom_text']['char']) {
			/* Validating the length */
			return jsonReturn(array('status'=>0,'message'=>'Custom text field is not more than '.$userAndCustomTextFeedCharLimit['custom_text']['char'].' character.'), true);
		}

		/* Deactivating the previous configuration */
		$this->_objDataOperation->setUpdateData(array('table'=>'trans_social_event_feeds_wall_cell_config','data'=>array('deleted'=>1,'updated_by'=>$this->getUserCode()),'where'=>array('grid_id'=>$pIntEventGridCode,'cell_index'=>$pIntCellCode)));
		
		/* Setting the data set array */
		$strFilterArr 		= 	array(
										'table' => 	'trans_social_event_feeds',
										'data' 	=> 	array(
															'event_code' 	=> 	$pIntEventCode,
															'data_code' 	=> 	0,
															'feeder_name' 	=> 	'',
															'title' 		=> 	$strAdminText,
															'platform_id' 	=> 	1,
														)
									);
		
		/* if feed code is pass then do needful */
		if((int) $pIntFeedCode > 0){
			/* Setting the where clause */
			$strFilterArr['where']		= array('id' => $pIntFeedCode);
			
			/* Update the record set */
			$intTransStatus 					= $this->_objDataOperation->setUpdateData($strFilterArr);
			$intTransStatus						= $pIntFeedCode;
		}else{
			/* Update the record set */
			$intTransStatus 					= $this->_objDataOperation->setDataInTable($strFilterArr);
		}
		
		/* Set data filter */
		$strFilterArr						= array(
													'table' => 	'trans_social_event_feeds_wall_cell_config',
													'data' 	=> 	array(
																		'grid_id' 		=> 	$pIntEventGridCode,
																		'cell_index' 	=> 	$pIntCellCode,
																		'content_code' 	=> 	$intTransStatus,
																		'is_feed' 		=> 	0,
																	),
												);
		
		/* Update the configuration */
		$intTransStatus 					= 	$this->_objDataOperation->setDataInTable($strFilterArr);
		
		/* return array */
		return array('is_feed'=>0, 'feed_cnt'=> 1, 'feed_id'=>getDecyptionValue($intTransStatus));
	}
	
	/**********************************************************************/
    /*Purpose 	: Set Admin feeds into the grid cell.
    /*Inputs	: $pIntFeedCode :: Feed code,
				: $pIntCellCode	:: Cell index,
				: $pIntEventGridCode :: Grid cell code,
				: $pIntCellRefreshInterval :: Grid cell refresh interval,
				: $pIntFeedCode :: Feed code.
    /*Returns	: Transaction status.
    /*Created By: Jaiswar Vipin Kumar R.
    /**********************************************************************/
	private function _setCustomImage($pIntEventCode = 0, $pIntCellCode = 0, $pIntEventGridCode = 0, $pIntCellRefreshInterval = 0, $pIntFeedCode = 0){
		/* variable initialization */
		$strFileInfoArr	=  isset($_FILES['admin_image'])? pathinfo($_FILES['admin_image']['name']):array();
		$configArr  	=  array();
		 
		/* if file info array is not selected then do needful */
		if((empty($strFileInfoArr)) || ($strFileInfoArr['filename'] == '')){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'Image is not selected.'), true);
		}
		
		/* Deactivating the previous configuration */
		$this->_objDataOperation->setUpdateData(array('table'=>'trans_social_event_feeds_wall_cell_config','data'=>array('deleted'=>1,'updated_by'=>$this->getUserCode()),'where'=>array('grid_id'=>$pIntEventGridCode,'cell_index'=>$pIntCellCode)));
		
		/* Creating file object */
		$filesObj 	= 	new Files($this->_objDataOperation, $this->getCompanyCode());

		/* Variable initialization for CI to initialized the I/O operation */
		$configArr['allowed_types'] 				= 	'gif|jpg|png|bmp|jpeg';
		$configArr['max_size'] 						= 	DEFAULT_FILE_UPLOAD_SIZE_IN_KB;
		$configArr['file_name'] 					= 	$strFileInfoArr['filename'].'_'.time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', 'admin_image').'.'.$strFileInfoArr['extension'];

		/* Upload file with file object with data and driver */
		$fileUpload = $filesObj->uploadFile('admin_image', $_FILES['admin_image'], $configArr, array());
		
		/* if file uploading not done successfully then do needful  */
		if ($fileUpload['status'] == false) {
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=> (!empty($fileUpload) && !empty($fileUpload['message'])) ? $fileUpload['message'] : 'Something wrong with upload file.'), true);
		}
		
		/* Setting the data set array */
		$strFilterArr 		= 	array(
										'table' => 	'trans_social_event_feeds',
										'data' 	=> 	array(
															'event_code' 	=> 	$pIntEventCode,
															'data_code' 	=> 	0,
															'feeder_name' 	=> 	'',
															'title' 		=> 	$fileUpload['filepath'],
															'platform_id' 	=> 	2,
														)
									);
		
		/* if feed code is pass then do needful */
		if((int) $pIntFeedCode > 0){
			/* Setting the where clause */
			$strFilterArr['where']		= array('id' => $pIntFeedCode);
			
			/* Update the record set */
			$intTransStatus 					= $this->_objDataOperation->setUpdateData($strFilterArr);
			$intTransStatus						= $pIntFeedCode;
		}else{
			/* Update the record set */
			$intTransStatus 					= $this->_objDataOperation->setDataInTable($strFilterArr);
		}
		
		/* Set data filter */
		$strFilterArr						= array(
													'table' => 	'trans_social_event_feeds_wall_cell_config',
													'data' 	=> 	array(
																		'grid_id' 		=> 	$pIntEventGridCode,
																		'cell_index' 	=> 	$pIntCellCode,
																		'content_code' 	=> 	$intTransStatus,
																		'is_feed' 		=> 	0,
																	),
												);
		
		/* Update the configuration */
		$intTransStatus 					= 	$this->_objDataOperation->setDataInTable($strFilterArr);
		
		/* return array */
		return array('is_feed'=>0, 'feed_cnt'=> 1, 'feed_id'=>getDecyptionValue($intTransStatus));
	}
	
	/**********************************************************************/
    /*Purpose 	: Set Interval of every grid cell.
				: $pIntCellCode	:: Cell index,
				: $pIntEventGridCode :: Grid cell code.
    /*Returns	: Transaction status.
    /*Created By: Jaiswar Vipin Kumar R.
    /**********************************************************************/
	private function _setCellInterval($pIntCellCode = 0, $pIntEventGridCode = 0){
		/* variable initialization */
		$intTimer 		= !empty($this->input->post('timer')) ? $this->input->post('timer') : 0;
		$strTimerType 	= !empty($this->input->post('timer_type')) ? $this->input->post('timer_type') : '';

		/* if interval is not selected then do needful */
		if($intTimer == 0){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'Cell content refresh interval is not selected.'), true);
		}
		
		/* if interval measure unit is not selected then do needful */
		if($strTimerType == ''){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'Cell content refresh interval measure unit is not selected.'), true);
		}
		
		/* if interval measure unit in the minutes then do needful */
		if ($strTimerType == 'min') {
			/* value overwriting */
			$intTimer = $intTimer*60;
		}
		
		/* Setting the data set array */
		$strFilterArr 		= 	array(
										'table' 	=> 'trans_social_event_feeds_wall_cell_config',
										'data' 		=> array('refresh_timeout' 	=> 	(int)$intTimer),
										'where' 	=> array('grid_id' => $pIntEventGridCode, 'cell_index' => $pIntCellCode)
									);
		
		/* Deactivating the previous configuration */
		return $this->_objDataOperation->setUpdateData($strFilterArr);
	}
	
	/**********************************************************************/
    /*Purpose 	: Setting social feeds.
				: $pIntEventCode :: Event code,
				: $pIntCellCode	:: Cell index,
				: $pIntEventGridCode :: Grid cell code.
    /*Returns	: Transaction status.
    /*Created By: Jaiswar Vipin Kumar R.
    /**********************************************************************/
	private function _setSocialFeeds($pIntEventCode = 0, $pIntCellCode = 0, $pIntEventGridCode = 0){
		/* variable initialization */
		$strSocialFeedArr 	= 	(!empty($this->input->post('sel_feed')) && is_array($this->input->post('sel_feed'))) ? $this->input->post('sel_feed') : array();
		
		/* if social feeds are not selected then do needful */
		if(empty($strSocialFeedArr)){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'No social handler(s) or hash tag(s) is selected.'), true);
		}
		
		/* Deactivating the previous configuration */
		$this->_objDataOperation->setUpdateData(array('table'=>'trans_social_event_feeds_wall_cell_config','data'=>array('deleted'=>1,'updated_by'=>$this->getUserCode()),'where'=>array('grid_id'=>$pIntEventGridCode,'cell_index'=>$pIntCellCode)));
		
		/* iterating the loop */
		foreach ($strSocialFeedArr as $strSocialFeedArrKey => $strSocialFeedArrValue) {
			/* Adding grid cell configuration */
			$gridstrGridCellDataArrArr = 	array(
													'table' 	=> 	'trans_social_event_feeds_wall_cell_config',
													'data' 		=> 	array(
														'grid_id' 		=> 	$pIntEventGridCode,
														'cell_index' 	=> 	$pIntCellCode,
														'content_code' 	=> 	getDecyptionValue($strSocialFeedArrValue),
														'is_feed' 		=> 	1,
													),
												);
			
			/* Adding the data in the configuration table */
			$this->_objDataOperation->setDataInTable($gridstrGridCellDataArrArr);
		}
		
		/* return array */
		return array('is_feed'=>0, 'feed_cnt'=> 1, 'feed_id'=>getDecyptionValue($strSocialFeedArrValue));
	}
	
	/**********************************************************************/
    /*Purpose 	: Set user feed details.
    /*Inputs	: None.
    /*Returns	: Transaction status.
    /*Created By: Jaiswar Vipin Kumar R.
    /**********************************************************************/
	public function  setUserFeedDetails(){
		/* variable initialization */
		$intUserFeedCode 	= 	(!empty($this->input->post('txtUserFeedCode')) ? getDecyptionValue($this->input->post('txtUserFeedCode')) : 0);
		$strUserFeeds 		= 	(!empty($this->input->post('txtUserFeedComments')) ? $this->input->post('txtUserFeedComments') : '');
		$strUserName 		= 	(!empty($this->input->post('txtFeedUserName')) ? $this->input->post('txtFeedUserName') : '');
		
		/* if user feeds code is in valid then do needful */
		if(empty($intUserFeedCode)){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'Invalid user feed code.'), true);
		}
		
		/* if user feeds comments is in empty then do needful */
		if(empty($strUserFeeds)){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'User comments field is empty.'), true);
		}
		
		$userAndCustomTextFeedCharLimit = getUserAndCustomTextFeedCharLimit();

		if (mb_strlen($strUserFeeds) > $userAndCustomTextFeedCharLimit['user_feed']['char']) {
			return jsonReturn(array('status'=>0,'message'=>'User comments field is not more than '.$userAndCustomTextFeedCharLimit['user_feed']['char'].' character.'), true);
		}

		/* if user name comments is in empty then do needful */
		if(empty($strUserName)){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'User name field is empty.'), true);
		}
		
		/* Set data array to update the details */
		$strFilterArr	= array(
									'table'=>'trans_social_event_feeds',
									'data'=>array('feeder_name'=>$strUserName, 'title'=>$strUserFeeds, 'updated_by'=>$this->getUserCode()),
									'where'=>array('id'=>$intUserFeedCode)
								);
		
		/* Updating the user feed */
		$intTranscationStatus	= $this->_objDataOperation->setUpdateData($strFilterArr);
		
		/* return the error message */
		jsonReturn(array('status'=>1,'message'=>'Record update successfully.'), true);
	}
	
	/**********************************************************************/
    /*Purpose 	: Set user feed details inactive.
    /*Inputs	: None.
    /*Returns	: Transaction status.
    /*Created By: Jaiswar Vipin Kumar R.
    /**********************************************************************/
	private function _setInactiveUserFeed(){
		/* variable initialization */
		$intUserFeedCode	= $this->input->post('txtDeleteRecordCode')? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;
		
		/* if user feed code is not pass then do needful */
		if($intUserFeedCode <= 0){
			/* return the error message */
			jsonReturn(array('status'=>0,'message'=>'Invalid user feed code.'), true);
		}
		
		/* Set data array to update the details */
		$strFilterArr	= array(
									'table'=>'trans_social_event_feeds',
									'data'=>array('deleted'=>1, 'updated_by'=>$this->getUserCode()),
									'where'=>array('id'=>$intUserFeedCode)
								);
		
		/* Updating the user feed */
		$intTranscationStatus	= $this->_objDataOperation->setUpdateData($strFilterArr);
		
		/* return the error message */
		jsonReturn(array('status'=>1,'message'=>'User feed deleted successfully.'), true);
	}
  
	/**********************************************************************/
    /*Purpose 	: Manage the Message data.
    /*Inputs	: $eventCodeEnc :: Event Code,
				: $alertMsgId :: Message code (Available in the update mode)
    /*Returns	: Transaction status.
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
	public function add_update_alert_message($eventCodeEnc, $alertMsgId = 0){
		/* variable initialization */
		$intEventCode 	= ($eventCodeEnc !='')?getDecyptionValue($eventCodeEnc): 0;
		$todayDateTime 	= ((new \DateTime())->format('YmdHi'));
		$alertMsgId 	= (($alertMsgId == 0) && (!empty($this->input->post('alert_msg_id')))) ? getDecyptionValue($this->input->post('alert_msg_id')) : getDecyptionValue($alertMsgId);
		
		$alertMsg 		= (!empty($this->input->post('alert_msg'))) ? $this->input->post('alert_msg') : '';
		$fromDate 		= (!empty($this->input->post('from_date'))) ? $this->input->post('from_date') : '';
		$fromTime 		= (!empty($this->input->post('from_time'))) ? $this->input->post('from_time') : '';
		$endDate 		= (!empty($this->input->post('to_date'))) ? $this->input->post('to_date') : '';
		$endTime 		= (!empty($this->input->post('to_time'))) ? $this->input->post('to_time') : '';
		$alertMsgDataArr= array();
		$strOperationType= 'added';

		
		/* if event code is not pass then do needful */
		if ($intEventCode == 0) {
			/* return the message */
			jsonReturn(array('message' => 'Feed Id Not Founded or incorrect feed id!', 'status' => false), true);
		}
		/* checking for request */
		if(!empty($_REQUEST)){
			/* removed used variable */
			unset($_REQUEST['alert_msg_id'], $_REQUEST['txtUserRoleCode'], $_REQUEST['txtSearch']);
			
			/* iterating the loop */
			foreach($_REQUEST as $strRequestKey => $strRequestValues){
				/* if data is empty then do needful */
				if(trim($strRequestValues) == ''){
					/* return the empty field error */
					jsonReturn(array('status'=>0,'message'=> 'Requested mandatory field ('.ucfirst(str_replace('_',' ',$strRequestKey)).') is empty.'), true);
				}
			}
		}
		/* Checking for from date format */
		$fromDateDtObj 	= verifyDateWithReturnObj($fromDate, 'Y/m/d');
		/* if object not found do needful */
		if (!$fromDateDtObj) {
			/* return the message */
			jsonReturn(array('message' => 'Please valid from date format.', 'status' => false), true);
		}
		
		/* Checking for from time format */
		$fromTimeDtObj 	= verifyDateWithReturnObj($fromTime, 'h:i A');
		/* if object not found do needful */
		if (!$fromTimeDtObj) {
			/* return the message */
			jsonReturn(array('message' => 'Please valid from date format.', 'status' => false), true);
		}
		
		/* Checking for end date format */
		$endDateDtObj 	= verifyDateWithReturnObj($endDate, 'Y/m/d');
		/* if object not found do needful */
		if (!$endDateDtObj) {
			/* return the message */
			jsonReturn(array('message' => 'Please valid to date format.', 'status' => false), true);
		}
		
		/* Checking for end date format */
		$endTimeDtObj 	= verifyDateWithReturnObj($endTime, 'h:i A');
		/* if object not found do needful */
		if (!$endTimeDtObj) {
			/* return the message */
			jsonReturn(array('message' => 'Please valid to date format.', 'status' => false), true);
		}
		
		/* Format the dates & time */
		$fromDateTime 	= $fromDateDtObj->format('Ymd').$fromTimeDtObj->format('Hi');
		$endDateTime 	= $endDateDtObj->format('Ymd').$endTimeDtObj->format('Hi');
		
		/* removed used variables */
		unset($fromDateDtObj, $fromTimeDtObj, $endDateDtObj, $endTimeDtObj);

		/* if alert message code found then do needful */
		if (!empty($alertMsgId)) {
			/* get the previous message details for date comparison */
			$alertMsgDataArr = $this->_fetchAlertMessageById($alertMsgId);
		}

		/* if previous message details found then do needful */
		if (!empty($alertMsgDataArr) && $alertMsgDataArr['from_date'] != $fromDateTime) {
			/* return the message */
			jsonReturn(array('message' => 'Can\'t try to change from date.', 'status' => false), true); 
		}
		/* from date can not be back dated */
		if (empty($alertMsgDataArr) && $todayDateTime >= $fromDateTime) {
			/* return the message */
			jsonReturn(array('message' => 'From date should be greater then today date/ time.', 'status' => false), true); 
		}
		/* end time can not be less then todays time */
		if (empty($alertMsgDataArr) && ($todayDateTime >= $endDateTime)) {
			/* return the message */
			jsonReturn(array('message' => 'End date should be greater then today date/ time.', 'status' => false), true); 
		}
		/* end date can not be less then from date */
		if ($endDateTime <= $fromDateTime) {
			/* return the message */
			jsonReturn(array('message' => 'End date/ time should be greater then from date.', 'status' => false), true); 
		} 
			
		/* Creating array filter for checking alert message existence time frame */
		$strFilterArr	= array(
									'table'=>'trans_event_alert_messages',
									'column'=>array('id'),
									'where'=>array('event_code' => $intEventCode, 'from_date <=' => $endDateTime, 'to_date >=' => $fromDateTime)
							);
		
		/* if message if pass than do needful */
		if ($alertMsgId > 0) {
			$strFilterArr['where']	= array_merge(array('id <>' =>$alertMsgId),$strFilterArr['where']);
		}
		/* get message data interval */
		$strResultArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if record found then do needful */
		if(!empty($strResultArr)){
			/* return the message */
			jsonReturn(array('message' => 'Must be require end date/ time is greater than from date.', 'status' => false), true);
		}
		
		/* removed used variable */
		unset($strResultArr, $strFilterArr);
	 
		/* Setting Data array */
		$gridstrGridCellDataArrArr = 	array(
												'table' 	=> 	'trans_event_alert_messages',
												'data' 		=> 	array(
													'event_code' 	=> 	$intEventCode,
													'alert_message' => 	$alertMsg,
													'from_date' 	=> 	$fromDateTime,
													'to_date' 		=> 	$endDateTime,
												),
											);
		
		/* if alert message code is set (edit mode) then do needful */
		if (!empty($alertMsgId)) {
			/* Setting the primary key to update the date */
			$gridstrGridCellDataArrArr['where'] = array('id' => $alertMsgId);
			/* Update the details */
			$affectedRows 						= $this->_objDataOperation->setUpdateData($gridstrGridCellDataArrArr);
			/* value overwriting */
			$strOperationType					= "updated";
		}else{
			/* Add the message */
			$affectedRows 						= $this->_objDataOperation->setDataInTable($gridstrGridCellDataArrArr);
		}
		
		/* return the message */
		jsonReturn(array('status'=>1,'message'=>'Record '.$strOperationType.' successfully.'), true);
    }

	/**********************************************************************/
    /*Purpose 	: Load the event wall message details filter by event code.
    /*Inputs	: $eventCodeEnc :: Event Code.
    /*Returns	: Event Wall Message.
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
    public function load_alert_msg_by_event_code($eventCodeEnc){
		/* Variable initialization */
    	$responseArr 	= array();
    	$eventCode 		= ($eventCodeEnc != '')? getDecyptionValue($eventCodeEnc): 0;
    	$response 		= array('message' => 'Error: Something wrong!', 'status'=> false);
    	$sort 			= !empty($this->input->get('sort')) ? $this->input->get('sort') : 'recent';
    	$box_number 	= !empty($this->input->get('box_number')) ? $this->input->get('box_number') : 0;

		/* Checking for event code is set of not */
		if ($eventCode == 0) {
			/* return on the event list */
			redirect(SITE_URL.'/mod/event-wall', 'refresh');
		}
		
		/* if event code is not pass or wrong then do needful */
    	if (empty($eventCode)) {
    		$response 		= array('message' => 'Error: Event Code invalid!', 'status'=> false);
    	}
		
		/* Setting the filter */
		$strFilterArr 		= 	array(
										'table' 	=> 	array($this->_strPrimaryTableName, 'trans_event_alert_messages'),
										'column' 	=> 	array($this->_strPrimaryTableName.'.name AS event_name', 'trans_event_alert_messages.id', 'trans_event_alert_messages.event_code', 'trans_event_alert_messages.alert_message', 'trans_event_alert_messages.from_date', 'trans_event_alert_messages.to_date', ),
										'join' 		=> 	array('', 'trans_event_alert_messages.event_code='.$this->_strPrimaryTableName.'.id' ),
										'where' 	=> 	array('trans_event_alert_messages.event_code' => $eventCode),
									);

		/* Getting the user feed details list */
		$socialFeedDataArr 					= 	$this->_objDataOperation->getDataFromTable($strFilterArr);

		/* Setting the data array for displaying the user feeds */
		$responseArr['eventAlterMsgArr'] 	= 	$socialFeedDataArr;
		$responseArr['eventCodeEnc'] 		= 	$eventCodeEnc;
		$responseArr['box_number'] 			= 	$box_number;
		$responseArr['blnShowAction'] 		= 	true;

		$strModuleTitle = 'Event Alert Message';

		/* if grid / event details found then do needful */
		if(!empty($socialFeedDataArr) && (isset($socialFeedDataArr[0]))){
		/* Tile value overwriting */
			$strModuleTitle	= '<a href="'.SITE_URL.'mod/event-wall">'.$socialFeedDataArr[0]['event_name'].'</a> > <a href="'.SITE_URL.'social-wall/grid-setup?event-code='.$eventCodeEnc.'">Event Grid </a> > '.$strModuleTitle;
		}else{
			/* Setting the filter */
			$strFilterArr 		= 	array(
											'table' 	=> 	$this->_strPrimaryTableName,
											'column' 	=> 	array($this->_strPrimaryTableName.'.name AS event_name'),
											'where' 	=> 	array('id' => $eventCode),
										);

			/* Getting the user feed details list */
			$socialFeedDataArr 				= 	$this->_objDataOperation->getDataFromTable($strFilterArr);
			
			/* if grid / event details found then do needful */
			if(!empty($socialFeedDataArr) && (isset($socialFeedDataArr[0]))){
				/* Tile value overwriting */
				$strModuleTitle	= '<a href="'.SITE_URL.'mod/event-wall">'.$socialFeedDataArr[0]['event_name'].'</a> > <a href="'.SITE_URL.'social-wall/grid-setup?event-code='.$eventCodeEnc.'">Event Grid </a> > '.$strModuleTitle;
			}else{
				/* return on the event list */
				redirect(SITE_URL.'/mod/event-wall', 'refresh');
			}
		}

		//$responseArr['blnShowAction'] 	= 	isAjaxRequest();
		/* get the user feed details */
		if (isAjaxRequest()) {
			$this->load->view('social/load-alert-msg-by-event-code', $responseArr);
		}else{
			/* Getting company list */
			$responseArr['intPageNumber'] 		= 1;
			$responseArr['pagination'] 			= '';
			$responseArr['moduleTitle']			= $strModuleTitle;
			$responseArr['moduleForm']			= 'frmAlertMsgAddEdit';
			$responseArr['moduleUri']			= SITE_URL.__CLASS__;
			$responseArr['deleteUri']			= SITE_URL.__CLASS__.'/delete-alert-msg-by-id';
			$responseArr['getRecordByCodeUri']	= SITE_URL.__CLASS__.'/get-alert-msg-details-by-id';
			$responseArr['strDataAddEditPanel']	= 'divAlterMsg';
			$responseArr['noAction']				= true;
			$responseArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());

			$dataArr['body']	= $this->load->view('social/load-alert-msg-by-event-code', $responseArr, true);
			/* Loading the template for browser rending */
			$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);	
		}
    }
 
	/**********************************************************************/
    /*Purpose 	: Delete Alert Message
    /*Inputs	: None.
    /*Returns	: Delete Status
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
	public function delete_alert_msg_by_id(){
		/* variable initialization */
		$intDataPrimaryCode 		= 	($this->input->post('txtDeleteRecordCode') != '') ? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;
		
		/* if request is not contains any data then do needful */
		if(empty($intDataPrimaryCode)){
			/* return response */
			jsonReturn(array('status'=>0,'message'=>'Invalid requested.'), true);
		}
		
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>'trans_event_alert_messages',
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intDataPrimaryCode
											)

								);
								
		// Updating the requested record set
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);
		
		/* on successful update do needful  */
		if($intNunberOfRecordUpdated > 0){
			/* return response */
			jsonReturn(array('status'=>1,'message'=>'Requested alert message deleted successfully.'), true);
		}else{
			/* return response */
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	

    /**********************************************************************/
    /*Purpose 	: Get message alert message details for ajax on edit page.
    /*Inputs	: $txtCode :: Alert code.
    /*Returns	: Message details
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
	public function get_alert_msg_details_by_id(){
		/* Variable initialization */
		$intAlertMsgId 	= !empty($this->input->post('txtCode')) ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$response 		= array();
		
		/* if message code is not found then do needful */
		if ($intAlertMsgId == 0) {
			jsonReturn(array('message' => 'Invalid message code passed.', 'status' => false), true);
		}
		
		/* get message by the message code */
		$alertMsgDataArr = $this->_fetchAlertMessageById($intAlertMsgId);
		/* if message details found then do needful */
		if (!empty($alertMsgDataArr)) {
			$alertMsgDataArr['id'] 			= getEncyptionValue($alertMsgDataArr['id']);
			$alertMsgDataArr['event_code'] 	= getEncyptionValue($alertMsgDataArr['event_code']);

			$fromDate 						= strtotime($alertMsgDataArr['from_date']);
			$toDate 						= strtotime($alertMsgDataArr['to_date']);

			$alertMsgDataArr['from_time'] = date('h:i A', $fromDate);
			$alertMsgDataArr['from_date'] = date('Y/m/d', $fromDate);
			$alertMsgDataArr['to_time'] = date('h:i A', $toDate);
			$alertMsgDataArr['to_date'] = date('Y/m/d', $toDate);
			/* return the message details */
			jsonReturn($alertMsgDataArr, true);
		}else{
			/* return the response */
			jsonReturn(array('status' => false, 'message' => 'Data not founded'), true); 
		}
	}

	/**********************************************************************/
    /*Purpose 	: Get message alert by message code.
    /*Inputs	: $alertMsgId :: Alert code.
    /*Returns	: Message details
    /*Created By: Vipin Kumar R. Jaiswar.
    /**********************************************************************/
	private function _fetchAlertMessageById($alertMsgId){

		$strFilterArr 	= 	array(
			'table' 	=> 	'trans_event_alert_messages',
			'column' 	=> 	array('id', 'event_code', 'alert_message', 'from_date', 'to_date'),
			'where' 	=> 	array('id' => $alertMsgId),
		);

		$alertMsgDataArr 	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		return !empty($alertMsgDataArr) && !empty($alertMsgDataArr[0]) ? $alertMsgDataArr[0] : array();

	}
}