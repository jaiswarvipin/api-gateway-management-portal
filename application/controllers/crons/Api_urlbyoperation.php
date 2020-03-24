<?php
/* Setting time limit */
set_time_limit (0);
/***********************************************************************/
/* Purpose 		: Get the API URLS using operations.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_urlbyoperation extends Requestprocess {
	/* variable deceleration */
	public  $_objDataOperation		= null;
	private $_strPrimaryTableName 	= 'master_api_url_by_operation';
	private $_strDataArr			= array();
	private $_blnTruncate			= false;
	private $_strBeareToken			= '';
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		/* Set value */
		$this->_blnTruncate	= isset($_REQUEST['truncate'])?true:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Returns	: Execute the query.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initilization */
		$strApiNameArr	= array();
		
		/* Getting the json object */
		$strResponseArr = $this->_getAPINameList();
		
		/* if no response found then do needful */
		if(empty($strResponseArr)){
			debugVar('No API Name found',true);
		}
		//debugVar($strResponseArr, true);
		/* iterating the loop */
		foreach($strResponseArr['value'] as $strApiNameKey => $strAPiNameObj){
			/* Set the value */
			$strApiNameArr[$strAPiNameObj->name] = array('api_operation_code'=>$strAPiNameObj->name, 'service_url'=>$strAPiNameObj->properties->serviceUrl, 'subscription_required'=>$strAPiNameObj->properties->subscriptionRequired, 'api_operation_url'=>$strAPiNameObj->properties->path,'action_url'=>'','api_operation_name'=>'','api_full_details_json'=>'');
		}
		
		/* if truncate rqeust comes then do needful */
		if($this->_blnTruncate){
			/* Truncate master table table */
			$strReurnObject	= $this->_objDataOperation->getDirectQueryResult("SELECT COUNT(id) as rowcount from master_api_url_by_operation");
			if($strReurnObject[0]['rowcount'] > 0){
				$this->_objDataOperation->getDirectQueryResult("TRUNCATE TABLE master_api_url_by_operation_history");
				$this->_objDataOperation->getDirectQueryResult("INSERT INTO `master_api_url_by_operation_history` (`api_operation_url`, `api_operation_code`, `api_operation_name`, `action_url`, service_url, `api_full_details_json`, `updated_by`, `updated_date`, `record_date`, `deleted`) select `api_operation_url`, `api_operation_code`, `api_operation_name`, `action_url`, service_url, `api_full_details_json`, `updated_by`, `updated_date`, `record_date`, `deleted` From `master_api_url_by_operation`");
			}			
			$this->_objDataOperation->getDirectQueryResult("TRUNCATE TABLE ".$this->_strPrimaryTableName);
		}
		
		/* Calling the API Operation List */
		$strApiNameArr	= $this->_getApiOperationList($strApiNameArr);
		debugVar($strApiNameArr);
				
		/* bulk insert */
		//$this->_objDataOperation->setBulkInset(array('table'=>$this->_strPrimaryTableName,'data'=>$strApiNameArr));
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the API operation list.
	/*Inputs	: $pStrApiNameArr :: API name array.
	/*Returns	: API Operation List.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getApiOperationList($pStrApiNameArr = array()){
		/* if api name array is empty then do needful */
		if (empty($pStrApiNameArr)){
			return false;
		}
		
		/* if Baeare Token not found then do needful */
		if($this->_strBeareToken == ''){
			/* Display the error */
			debugVar('Error occured while generating the Bearer Tokens', true);
		}
		/* variable initilization */
		$strApiOperationArr	= array();
		
		/* Setting header */
		$strHeaderArr	= array('Content-Type:application/json','Authorization: Bearer '. $this->_strBeareToken);
		
		/* iterating the API name list */
		foreach($pStrApiNameArr as $pStrApiNameArrKey => $pStrApiNameArrValue){
			/* Return the DevOps Story Details */
			$requestObj	= new Request();
			/* Sending the request */
			$requestObj->send(array('desitnationURL'=>'https://management.azure.com/subscriptions/e87bad11-bde3-4df3-8cc9-1a9782fe1ba3/resourceGroups/BFL-APIgroup-Prod/providers/Microsoft.ApiManagement/service/BFLAPIProd/apis/'.$pStrApiNameArrValue['api_operation_code'].'/operations?api-version=2019-01-01','headers'=>$strHeaderArr,'method'=>"get"));
			//$requestObj->send(array('desitnationURL'=>'https://management.azure.com/subscriptions/e87bad11-bde3-4df3-8cc9-1a9782fe1ba3/resourceGroups/BFL-APIgroup-Prod/providers/Microsoft.ApiManagement/service/BFLAPIProd/apis/amazon-services-api/operations?api-version=2019-01-01','headers'=>$strHeaderArr,'method'=>"get"));
			
			/* if any error occured then do needful */
			if($requestObj->getResponseError() != ''){
				/* Return the empty array */
				return array();
			}else{
				/* Varaible initilization the response */
				$strReponseObj	= (array)json_decode($requestObj->getResponse());
				
				/* chceking the value */
				if(isset($strReponseObj['value'])){
					/* Iterating the operation loop */
					foreach($strReponseObj['value'] as $strReponseObjKey => $strReponseObject){
						$strReponseObject->properties->description = '';
						/* Set the value */
						$strApiOperationArr	= array('api_operation_code'=>$pStrApiNameArrValue['api_operation_code'], 'api_operation_url'=>$pStrApiNameArrValue['api_operation_url'],'action_url'=>PROD_API_DOMAIN.$pStrApiNameArrValue['api_operation_url'].$strReponseObject->properties->urlTemplate,'api_operation_name'=>$strReponseObject->name,'api_full_details_json'=>json_encode($strReponseObject));
						$this->_objDataOperation->setDataInTable(array('table'=>$this->_strPrimaryTableName,'data'=>$strApiOperationArr));
					}
				}
				/* emoved used variables */
				unset($strReponseObj);
			}
			/* pause loop for 2 seconds */
			sleep(2);
		}
		
		/* return the API operation list */
		return $strApiOperationArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the API name List.
	/*Inputs	: None.
	/*Returns	: API Name list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getAPINameList(){
		/* Berare token arrange */
		$this->_getBearerToken();
		
		if($this->_strBeareToken == ''){
			debugVar('Error occured while generating the Bearer Tokens', true);
		}
			
		/* Setting header */
		$strHeaderArr	= array('Content-Type:application/json','Authorization: Bearer '. $this->_strBeareToken);
		
		/* Return the DevOps Story Details */
		$requestObj	= new Request();
		/* Sending the request */
		$requestObj->send(array('desitnationURL'=>'https://management.azure.com/subscriptions/e87bad11-bde3-4df3-8cc9-1a9782fe1ba3/resourceGroups/BFL-APIgroup-Prod/providers/Microsoft.ApiManagement/service/BFLAPIProd/apis?api-version=2019-01-01','headers'=>$strHeaderArr,'method'=>"get"));
		
		/* if any error occured then do needful */
		if($requestObj->getResponseError() != ''){
			/* Return the empty array */
			return array();
		}else{
			/* return the response */
			return (array)json_decode($requestObj->getResponse());
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Generate the Bearer token.
	/*Inputs	: None.
	/*Returns	: Bearer Token.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getBearerToken(){
		/* Return the DevOps Story Details */
		$requestObj	= new Request();
		/* Sending the request */
		$requestObj->send(
							array(
									'desitnationURL'=>'https://login.microsoftonline.com/bajajfinance.in/oauth2/token',
									'grant_type'=>'client_credentials',
									'method'=>"POST",
									'client_id'=>'5b37f0fd-b61b-4773-a810-4e7be3c4cd5e',
									'client_secret'=>'EEoYlJFTt4Aca4Jyf1wHyDu1hiU+/LUusjW5jRVFrzU=',
									'resource'=>'https://management.azure.com/',
									'scope'=>'https%3A%2F%2Fgraph.microsoft.com%2F.default'
								)
						);
		
		/* if any error occured then do needful */
		if($requestObj->getResponseError() != ''){
			/* Return the empty array */
			return array();
		}else{
			$this->_strBeareToken	= (array)json_decode($requestObj->getResponse());
			$this->_strBeareToken	= $this->_strBeareToken['access_token'];
			/* return the response */
			return $this->_strBeareToken;
		}
	}
}