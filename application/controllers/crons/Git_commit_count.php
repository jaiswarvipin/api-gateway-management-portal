<?php
/***********************************************************************/
/* Purpose 		: Synch the DevOps work item based on the UserStory code.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Git_commit_count extends Requestprocess {
	/* variable deceleration */
	public  $_objDataOperation		= null;
	private $_strPrimaryTableName 	= 'master_gits_repo';
	private $_strSecondaryTableName = 'api_list_3';
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
		$strEixtingRepoNameArr = $strReceivedRepoNameArr = array();
		
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
		
		/* re-initialization of variable */
		$strReceivedRepoNameArr	= array();
		/* variable initialization */
		foreach($strGitRepoListArr['value'] as $strGitRepoListArrKey => $strGitRepoListArrValue){
			/* if no in the list */
			if(!isset($strEixtingRepoNameArr[$strGitRepoListArrValue->name])){
				/* settign the received repo list */
				$strReceivedRepoNameArr[$strGitRepoListArrValue->name] = array('git_id'=>$strGitRepoListArrValue->id,'git_name'=>$strGitRepoListArrValue->name,'git_url'=>$strGitRepoListArrValue->url,'record_date'=>date('YmdHis'));
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
	
			/* iterating the loop */
			foreach($strResultArr as $strResultArrKey => $strResultArrValue){
				/* Sending the request */
				$requestObj->send(array('desitnationURL'=>'https://dev.azure.com/BFLDevOpsOrg/API_Repository_Projects/_apis/git/repositories/'.$strResultArrValue['git_name'].'/commits?searchCriteria.itemVersion.versionType=dev&searchCriteria.toDate='.urlencode(getDateFormat(date('Ymd235959'),9)).'&searchCriteria.fromDate='.urlencode(getDateFormat(date('Ymd000000'),9)),'headers'=>$strHeaderArr));
				//$requestObj->send(array('desitnationURL'=>'https://dev.azure.com/BFLDevOpsOrg/API_Repository_Projects/_apis/git/repositories/HTS-SD-Cibil_PSBL_Tele/commits?searchCriteria.itemVersion.versionType=dev&searchCriteria.toDate='.urlencode(getDateFormat(date('Ymd235959'),9)).'&searchCriteria.fromDate='.urlencode(getDateFormat(date('Ymd000000'),9)),'headers'=>$strHeaderArr));
				/* Get the response */
				$strResponseArr	= (array)json_decode($requestObj->getResponse());
				
				/* if any error occured then do needful */
				if((!empty($strResponseArr)) && ($strResponseArr['count'] > 0)){
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
												'record_date'=>date('YmdHis'),
											);
											
							/* if git commit history found then do needful */
							if(!empty($strCommitArr)){
								/* bulk insert */
								$this->_objDataOperation->setDataInTable(array('table'=>'trans_gits_repo_commit_details','data'=>$strCommitArr));
							}
						}
					}
				}
				sleep(3);
			}
		}
	}
}