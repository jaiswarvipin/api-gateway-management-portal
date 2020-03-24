<?php
/* Setting time limit */
set_time_limit (0);
/***********************************************************************/
/* Purpose 		: Get the API URLS using operations.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Show_apichanges extends Requestprocess {
	/* variable deceleration */
	public  $_objDataOperation		= null;
	private $_strPrimaryTableName 	= 'master_api_url_by_operation';
	private $_strHistoryTableName 	= 'master_api_url_by_operation_history';
	private $_strDataArr			= array();
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
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
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the Primary and History API Diffrence.
	/*Inputs	: None.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getAPINameList(){
		/* Get the Primary table */
		$strPrimaryResultSet =  $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName));
		/* Get the Primary table */
		$strHistoryResultSet =  $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strHistoryTableName));
		
		//debugVar(array_diff($strPrimaryResultSet, $strHistoryResultSet), true);
		
		/* variable initialization */
		$strPrimaryAPIArr = $strHistoryAPIArr = $strDiffrance = array(); 
		
		/* if Primary Resultset it not empty */
		if(!empty($strPrimaryResultSet)){
			/* iterating the array */
			foreach($strPrimaryResultSet as $strPrimaryResultSetKey => $strPrimaryResultSetValue){
				/* Set the value */
				$strPrimaryAPIArr[$strPrimaryResultSetValue['api_operation_url'].'/'.$strPrimaryResultSetValue['api_operation_code'].'/'.$strPrimaryResultSetValue['api_operation_name']] = $strPrimaryResultSetValue;
			}
		}
		/* Removed unuased variable */
		unset($strPrimaryResultSet);
		
		/* If Secondary Resultset it not empty */
		if(!empty($strHistoryResultSet)){
			/* iterating the array */
			foreach($strHistoryResultSet as $strHistoryResultSetKey => $strHistoryResultSetValue){
				/* Set the value */
				$strHistoryAPIArr[$strHistoryResultSetValue['api_operation_url'].'/'.$strHistoryResultSetValue['api_operation_code'].'/'.$strHistoryResultSetValue['api_operation_name']] = $strHistoryResultSetValue;
			}
		}
		/* Removed unuased variable */
		unset($strHistoryResultSet);
		
		/* if primary array found then do needful */
		if(!empty($strPrimaryAPIArr)){
			/* Iterating the Primary array */
			foreach($strPrimaryAPIArr as $strPrimaryAPIArrKey => $strPrimaryAPIArrValue){
				$strPrimaryArr	= $strPrimaryAPIArrValue;
				$strHistioryArr	=  (isset($strHistoryAPIArr[$strPrimaryAPIArrKey]))?$strHistoryAPIArr[$strPrimaryAPIArrKey]:array();
				if(!isset($strDiffrance[$strPrimaryAPIArrKey])){
					$strDiffrance[$strPrimaryAPIArrKey]	= $this->_getAPIDiff($strPrimaryArr, $strHistioryArr);
				}
			}
		}
		
		echo '<table border="1">';
		echo '<thead><th>Operation Name / Operation Code / Operation Name</th>';
		foreach($strDiffrance as $strDiffranceKey => $strDiffranceValueArr){
			foreach($strDiffranceValueArr as $strDiffranceValueArrKey => $strDiffranceValue){
				echo '<th>'.$strDiffranceValueArrKey.'</th>';
			}
			break;
		}
		echo '</thead><tbody>';
		foreach($strDiffrance as $strDiffranceKey => $strDiffranceValueArr){
			echo '<tr><td>'.$strDiffranceKey.'</td>';
			foreach($strDiffranceValueArr as $strDiffranceValueArrKey => $strDiffranceValue){
				echo '<td>'.$strDiffranceValue.'</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table>';
		debugVar($strDiffrance);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the API Primary and Secondary API.
	/*Inputs	: $pStrPrimaryAPIDetailsArr :: Primary API Details,
				: $pStrHistoryAPIDetailsArr :: History API Details.
	/*Returns	: Diffrence Result.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getAPIDiff($pStrPrimaryAPIDetailsArr, $pStrHistoryAPIDetailsArr){
		$strReturnFinalDiff			= array('api_operation_url'=>'','api_operation_code'=>'', 'api_operation_name'=>'', 'action_url'=>'', 'service_url'=>'', 'subscription_required'=>'', 'id'=>'', 'name'=>'', 'type'=>'', 'method'=>'', 'policies'=>'', 'displayName'=>'', 'urlTemplate'=>'');
		$pStrHistoryAPIDetailsArr	= (array)$pStrHistoryAPIDetailsArr;
		
		foreach($pStrPrimaryAPIDetailsArr as $pStrPrimaryAPIDetailsArrKey => $pStrPrimaryAPIDetailsArrValue){
			$strHistoryArrRef 	= (isset($pStrHistoryAPIDetailsArr[$pStrPrimaryAPIDetailsArrKey]))?$pStrHistoryAPIDetailsArr[$pStrPrimaryAPIDetailsArrKey]:'';
			switch($pStrPrimaryAPIDetailsArrKey){
				case "api_operation_url":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['api_operation_url']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "api_operation_code":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['api_operation_code']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "api_operation_name":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['api_operation_name']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "action_url":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['action_url']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "service_url":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['service_url']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "subscription_required":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['subscription_required']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
			}
		}
		
		$pStrPrimaryAPIDetailsArr		= json_decode($pStrPrimaryAPIDetailsArr['api_full_details_json']);
		$pStrHistoryAPIDetailsArr		= (array)json_decode($pStrHistoryAPIDetailsArr['api_full_details_json']);
		
		foreach($pStrPrimaryAPIDetailsArr as $pStrPrimaryAPIDetailsArrKey => $pStrPrimaryAPIDetailsArrValue){
			$strHistoryArrRef 	= (isset($pStrHistoryAPIDetailsArr[$pStrPrimaryAPIDetailsArrKey]))?$pStrHistoryAPIDetailsArr[$pStrPrimaryAPIDetailsArrKey]:'';
			switch($pStrPrimaryAPIDetailsArrKey){
				case "id":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['id']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "name":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['name']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "type":
					if($strHistoryArrRef != $pStrPrimaryAPIDetailsArrValue){
						$strReturnFinalDiff['type']	= $pStrPrimaryAPIDetailsArrValue.'<br/>'.$strHistoryArrRef;
					}
					break;
				case "properties":
					$strPrimaryPropertiesArr	= (array)$pStrPrimaryAPIDetailsArrValue;
					$strHistoryPropertiesArr	= (array)$strHistoryArrRef;
					
					if(!empty($strPrimaryPropertiesArr)){
						foreach($strPrimaryPropertiesArr as $strPrimaryPropertiesArrKey => $strPrimaryPropertiesArrValue){
							$strHistoryPropRef	= (isset($strHistoryPropertiesArr[$strPrimaryPropertiesArrKey]))?$strHistoryPropertiesArr[$strPrimaryPropertiesArrKey]:'';
							switch($strPrimaryPropertiesArrKey){
								case "method":
									if($strHistoryPropRef != $strPrimaryPropertiesArrValue){
										$strReturnFinalDiff['method']	= $strPrimaryPropertiesArrValue.'<br/>'.$strHistoryPropRef;
									}
									break;
								case "policies":
									if($strHistoryPropRef != $strPrimaryPropertiesArrValue){
										$strReturnFinalDiff['policies']	= $strPrimaryPropertiesArrValue.'<br/>'.$strHistoryPropRef;
									}
									break;
								case "displayName":
									if($strHistoryPropRef != $strPrimaryPropertiesArrValue){
										$strReturnFinalDiff['displayName']	= $strPrimaryPropertiesArrValue.'<br/>'.$strHistoryPropRef;
									}
									break;
								case "urlTemplate":
									if($strHistoryPropRef != $strPrimaryPropertiesArrValue){
										$strReturnFinalDiff['urlTemplate']	= $strPrimaryPropertiesArrValue.'<br/>'.$strHistoryPropRef;
									}
									break;
							}
						}
					}
					break;
			}
		}
		return $strReturnFinalDiff;
	}
}