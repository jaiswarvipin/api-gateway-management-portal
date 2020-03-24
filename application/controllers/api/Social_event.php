<?php
/***********************************************************************/
/* Purpose 		: Provide REST API For Database Table with Filter.
/* Created By 	: Prashant S. Pawar
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');
/* Adding Reset response controller reference to response the requester */
require APPPATH . 'libraries/REST_Controller.php';

class Social_event extends REST_Controller {

	/* variable deceleration */
	public  $_objDataOperation				= null;
	/*
	private $_strPrimaryTableName	= 'master_social_events';
	*/
	private $_strPrimaryTableName	= 'events_1';

	/********************************************************************/
	/* Purpose 		: Initiating the Default CI Model properties and methods
	/* Inputs		: None.
	/* Returns 		: None.
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	public function __construct(){
		/* Calling RESET construct */
		parent::__construct();
		/* Creating database helper instance */
		$this->load->database();

		/* Creating model comment instance object */
		$this->_objDataOperation	= new Dbrequestprocess_model();

		/* Authorized the request  */
		//$this->_doAuthincation();
	}
	
	/********************************************************************/
	/* Purpose 		: Validating the APPI call
	/* Inputs		: None.
	/* Returns 		: None.
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	private function _doAuthincation(){
		/* Variable initialization */
		$status_code 				= 400;
		/* Creating logger object */
		$objLogger 				= 	new Logger();
		/* Setting the request type */
		$objLogger->strPlatform = 	'api';

		/* Setting error message */
		$response_data = array('status' => false, 'error_code' => 1, 'code' => 400, 'error' => 'Something went wrong!');
		
		/* Getting header values */
		$strHeadersArr = $this->input->request_headers();
		
		/* Checking header array empty */
		if (empty($strHeadersArr) || (!isset($strHeadersArr['authorization']))) {
			/* Setting error message */
			$response_data = array('status' => false, 'error_code' => 1, 'code' => 400, 'error' => 'Header is missing');
			/* Sending the response */
			$this->response($response_data, $status_code);
		}
		/* Verifying token validity */
		$blnStatus 	= 	$objLogger->verifyAuthentication($strHeadersArr['authorization']);

		/* if not authorized API then do needful */
		if (!$blnStatus) {
			/* Setting error message */
			$response_data = array('status' => false, 'error_code' => 1, 'code' => 400, 'error' => 'Sorry, You are not authenticate user !');
			/* Sending the response */
			$this->response($response_data, $status_code);
		}
	}

	/********************************************************************/
	/* Purpose 		: Execute Query on given table with filter query
	/* Inputs		: $table name with filter query
	/* Returns 		: JSON with Matched filtered data of given table
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	public function index_get($eventCode, $cellNumber = 0){
		/* Begin, Set default response of error */
		/* Variable initialization */
		$error_msg				= array('message'=>'Something wrong!');
		$status_code 			= '400';
		$strResponseDataArr 	= array("errors" => $error_msg, 'status_code' => $status_code);

		$sqlQueryWhere = '';

		$sqlQueryWhere 	= " WHERE e.id = $eventCode";

		if (!empty($cellNumber)) {
			$sqlQueryWhere .= " AND sefc.cell_index = $cellNumber";
		}

		$sqlQuery = "SELECT  e.id AS event_id, sefc.id, sefc.id, sefc.cell_index, sefc.is_feed, sefc.refresh_timeout, sef.feeder_name, sef.title, sef.platform_id, sef.likes_count, sfd.data_desc, sfd.data_type, e.company_code, e.name, e.description, sefgc.rows, sefgc.columns FROM events_1 AS e RIGHT JOIN trans_social_event_feeds_wall_grid_config AS sefgc ON (sefgc.event_code = e.id) RIGHT JOIN master_social_feed_data AS sfd ON (sfd.event_code = e.id) RIGHT JOIN trans_social_event_feeds AS sef ON (sef.event_code = e.id) RIGHT JOIN trans_social_event_feeds_wall_cell_config AS sefc ON (sefc.grid_id = sefgc.id AND IF(sefc.is_feed = '1', sefc.content_code = sfd.id, sefc.content_code = sef.id)) $sqlQueryWhere GROUP BY sefc.id ORDER BY sef.id DESC, sfd.id DESC, sefc.id DESC";

		/* Getting the status list */
		$strUserProfileArr	=  $this->_objDataOperation->getDirectQueryResult($sqlQuery);


		$responseCellConfig = array();
		foreach ($strUserProfileArr as $cellConfig) {
			$responseCellConfig[$cellConfig['event_id']]['grid_config'] = array('rows' => $cellConfig['rows'], 'columns' => $cellConfig['columns']);
			$responseCellConfig[$cellConfig['event_id']]['cell'][$cellConfig['cell_index']]['cell_config']['refresh_timeout'] = $cellConfig['refresh_timeout'];
			$responseCellConfig[$cellConfig['event_id']]['cell'][$cellConfig['cell_index']]['cell_config']['social_platform'] = ($cellConfig['is_feed'] == '1') ? 'twitter' : 'site';
			$cellData  = array();
			if ($cellConfig['is_feed'] == '1') {
				$cellData = array('data_type' => $cellConfig['data_type'], 'data_desc' => $cellConfig['data_desc']);
			}else{
				if ($cellConfig['platform_id'] == 2) {
					$cellData = array('type' => 'admin_image', 'src' => $cellConfig['title'], 'admin' => $cellConfig['feeder_name']);
				}elseif ($cellConfig['platform_id'] == 3) {
					$cellData = array('type' => 'user_feed', 'feed_msg' => $cellConfig['title'], 'user_name' => $cellConfig['feeder_name'], 'likes_count' => $cellConfig['likes_count']);
				}else{
					$cellData = array('type' => 'admin_text', 'feed_msg' => $cellConfig['title'], 'admin' => $cellConfig['feeder_name']);
				}
			}
			$responseCellConfig[$cellConfig['event_id']]['cell'][$cellConfig['cell_index']]['cell_data'][] = $cellData;
		}

		if (!empty($responseCellConfig)) {
			$status_code = 200;
			$strResponseDataArr = array("message" => 'Event Data Founded !', 'grid_data' => $responseCellConfig, 'status_code' => $status_code);
		}

		/* Sending the response */
		$this->response($strResponseDataArr, $status_code);
		/* stop the execution process */
		return true;
	}

}
