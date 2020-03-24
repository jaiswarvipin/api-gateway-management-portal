<?php
/* Setting time limit */
set_time_limit (0);
/***********************************************************************/
/* Purpose 		: Synch the DevOps work item based on the UserStory code.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Git_commit extends Requestprocess {
	/* variable deceleration */
	public  $_objDataOperation		= null;
	private $_strPrimaryTableName 	= 'master_gits_repo';
	private $_strSecondaryTableName = 'api_list_3';
	private $_strDataArr			= array();
	private $_intDayDiff			= 0;
	private $_intFromDate			= '';
	private $_intToDate				= '';
	private $_blnShowGitResponse	= false;
	
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		/* Setting the day diff on demand */
		$this->_intDayDiff			= ((isset($_REQUEST['day'])) && ($_REQUEST['day'] != ''))?$_REQUEST['day']:0;
		$this->_blnShowGitResponse	= ((isset($_REQUEST['gitresp'])) && ($_REQUEST['gitresp'] != ''))?true:false;
		
		/* Set date */
		$this->_intFromDate	= getDates($this->_intDayDiff,6);
		
		if($this->_blnShowGitResponse){
			debugVar($this->_intFromDate);
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Returns	: Execute the query.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* Process the Git Repo List */
		$this->_setGitRepoDetails($this->_getGitRepoList());
		/* Process the git commit history */
		$this->_getGitCommitHistory();
	}
	
	/**********************************************************************/
	/*Purpose 	: Set the Git Repo Details.
	/*Inputs	: $strGitRepoListArr :: Git Repo details array .
	/*Returns	: None .
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setGitRepoDetails($strGitRepoListArr = array()){
		/* if send data is empty then do needful */
		if((empty($strGitRepoListArr)) || (!isset($strGitRepoListArr['value']))){
			/* do not process further */
			return false;
		}
		/* variable initilziation */
		$strEixtingRepoNameArr = $strReceivedRepoNameArr = $strVerticalArr = array();
		
		/* Get the repo list */
		$strReceivedRepoNameArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName,'column'=>array('git_name')));
		/* if Existing repo name found then do needful */
		if(!empty($strReceivedRepoNameArr)){
			/* iterating the loop */
			foreach($strReceivedRepoNameArr as $strEixtingRepoNameArrKey => $strEixtingRepoNameArrValue){
				/* Set the value */
				$strEixtingRepoNameArr[$strEixtingRepoNameArrValue['git_name']]	= $strEixtingRepoNameArrValue['git_name'];
			}
		}
		
		/* Get vertical list */
		$strVerticalArr =  $this->_getVerticalList();
		
		/* re-initialization of variable */
		$strReceivedRepoNameArr	= array();
		/* variable initialization */
		foreach($strGitRepoListArr['value'] as $strGitRepoListArrKey => $strGitRepoListArrValue){
			/* Get the vertical text code */
			$strRepCode			= strtoupper(substr($strGitRepoListArrValue->name,0,3));
			$intVertcialCode	= isset($strVerticalArr[$strRepCode])?$strVerticalArr[$strRepCode]:0;
			/* if no in the list */
			if((!isset($strEixtingRepoNameArr[$strGitRepoListArrValue->name])) && ($intVertcialCode > 0)){
				/* settign the received repo list */
				$strReceivedRepoNameArr[$strGitRepoListArrValue->name] = array('git_id'=>$strGitRepoListArrValue->id,'git_name'=>$strGitRepoListArrValue->name,'git_url'=>$strGitRepoListArrValue->url,'vertical_cdoe'=>$intVertcialCode,'record_date'=>date('YmdHis'));
			}			
		}
		
		/* if New Repo found then do needful */
		if(!empty($strReceivedRepoNameArr)){
			/* bulk insert */
			$this->_objDataOperation->setBulkInset(array('table'=>$this->_strPrimaryTableName,'data'=>$strReceivedRepoNameArr));
		}
		
		/* removed used variables */
		unset($strReceivedRepoNameArr, $strEixtingRepoNameArr);
		/* return the operation status */
		return true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get vertical List.
	/*Inputs	: None.
	/*Returns	: Git Vertical List .
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getVerticalList(){
		/* variable initialization */
		$strReturnArr	= array();
		
		/* Get the repo list */
		$strVerticalArr	= $this->_objDataOperation->getDataFromTable(array('table'=>'system_vertical_3','column'=>array('id','code')));
		/* if verticals found then do needful */
		if(!empty($strVerticalArr)){
			/* iterating the loop */
			foreach($strVerticalArr as $strVerticalArrKey => $strVerticalArrValue){
				/* Set the value */
				$strReturnArr[$strVerticalArrValue['code']]	= $strVerticalArrValue['id'];
			}
		}
		
		/* removed used variable */
		unset($strVerticalArr);
		
		/* return vertical */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the Git Repo List.
	/*Inputs	: None.
	/*Returns	: Git Repo List .
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getGitRepoList(){
		/* Setting header */
		$strHeaderArr	= array('Content-Type:application/json','Authorization: Basic '. base64_encode(DEVOPS_USERNAME.":".DEVOPS_KEY));
		/* Return the DevOps Story Details */
		$requestObj	= new Request();
		/* Sending the request */
		$requestObj->send(array('desitnationURL'=>'https://dev.azure.com/BFLDevOpsOrg/API_Repository_Projects/_apis/git/repositories?api-version=5.0','headers'=>$strHeaderArr));
		
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
	/*Purpose 	: Get the Git Repo commit history.
	/*Inputs	: $strGutRepoNameArr :: Get Repo Name array.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getGitCommitHistory(){
		/* Variable initialization */
		$strResultArr	= $strEmpResultArr	= $strEmpResultSetArr = $strCommitArr =  array();
		/* Setting header */
		$strHeaderArr	= array('Content-Type:application/json','Authorization: Basic '. base64_encode(DEVOPS_USERNAME.":".DEVOPS_KEY));
		/* Return the DevOps Story Details */
		$requestObj	= new Request();
		
		/* get the repo list */
		$strResultArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName,'column'=>array('git_name','id')));
		
		/* if Existing repo name found then do needful */
		if(!empty($strResultArr)){
			
			/* get the repo list */
			$strEmpResultArr	= $this->_objDataOperation->getDataFromTable(array('table'=>'master_user','column'=>array('id','user_email')));
		
			/* if employee details not found then do needful */
			if(!empty($strEmpResultArr)){
				/* Iterating the loop */
				foreach($strEmpResultArr as $strEmpResultArrKey => $strEmpResultArrValue){
					/* Set values */
					$strEmpResultSetArr[$strEmpResultArrValue['user_email']]	= $strEmpResultArrValue['id'];
				}
			}
			
			/* if employee found then do needful */
			if(empty($strEmpResultSetArr)){
				/* do not process further */
				return false;
			}
			/* removed used variable */
			unset($strEmpResultArr);
			
			/* delete the older data */
			$this->_objDataOperation->getDirectQueryResult("DELETE FROM trans_gits_repo_commit_details WHERE date_format(record_date,'%Y%m%d') =".$this->_intFromDate);
	
			/* iterating the loop */
			foreach($strResultArr as $strResultArrKey => $strResultArrValue){
				/* Sending the request */
				$requestObj->send(array('desitnationURL'=>'https://dev.azure.com/BFLDevOpsOrg/API_Repository_Projects/_apis/git/repositories/'.$strResultArrValue['git_name'].'/commits?searchCriteria.itemVersion.version=dev&searchCriteria.toDate='.urlencode(getDateFormat($this->_intFromDate.'235959',9)).'&searchCriteria.fromDate='.urlencode(getDateFormat($this->_intFromDate.'000000',9)),'headers'=>$strHeaderArr));
				//$requestObj->send(array('desitnationURL'=>'https://dev.azure.com/BFLDevOpsOrg/API_Repository_Projects/_apis/git/repositories/HTS-SD-Cibil_PSBL_Tele/commits?searchCriteria.itemVersion.versionType=dev&searchCriteria.toDate='.urlencode(getDateFormat(date('Ymd235959'),9)).'&searchCriteria.fromDate='.urlencode(getDateFormat(date('Ymd000000'),9)),'headers'=>$strHeaderArr));
				/* Get the response */
				$strResponseArr	= (array)json_decode($requestObj->getResponse());
				
				/* if any error occured then do needful */
				if((!empty($strResponseArr)) && (isset($strResponseArr['count'])) && ($strResponseArr['count'] > 0) && (!$this->_blnShowGitResponse)){
					/* get the WS response */
					$strGitResponseArr	= (array)json_decode($requestObj->getResponse());
					
					/* iterating the loop */
					foreach($strGitResponseArr['value'] as $strGitResponseArrKey => $strGitResponseArrValue){
						/* Get the employee code */
						$intUserCode		= isset($strEmpResultSetArr[$strGitResponseArrValue->committer->email])?$strEmpResultSetArr[$strGitResponseArrValue->committer->email]:0;
						/* if employee code found then do needful */
						if($intUserCode > 0){
							/* Set insert array */
							$strCommitArr	= array(
												'repo_code'=>$strResultArrValue['id'],
												'user_code'=>$intUserCode,
												'committer_date'=>$strGitResponseArrValue->committer->date,
												'record_date'=>$this->_intFromDate.''.date('His'),
											);
											
							/* if git commit history found then do needful */
							if(!empty($strCommitArr)){
								/* bulk insert */
								$this->_objDataOperation->setDataInTable(array('table'=>'trans_gits_repo_commit_details','data'=>$strCommitArr));
							}
						}
					}
				}
				
				/* if request for display the response */
				if($this->_blnShowGitResponse){
					debugVar($strResponseArr);
					flush();
				}
				
				sleep(3);
			}
		}
	}
}