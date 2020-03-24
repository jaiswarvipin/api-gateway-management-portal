<?php
/***********************************************************************/
/* Purpose 		: Application user role.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Userrole extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_role';
	private $_strModuleName			= "User Role(s)";
	private $_strModuleForm			= "frmUserRoles";
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();
		/* Getting current page number */
		$intCurrentPageNumber	= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting user role list */
		$strUserRoleArr['dataSet'] 				= $this->_getUserRole(0,'',false,false, $intCurrentPageNumber);
		$strUserRoleArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strUserRoleArr['pagination'] 			= getPagniation($this->_getUserRole(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strUserRoleArr['moduleTitle']			= $this->_strModuleName;
		$strUserRoleArr['moduleForm']			= $this->_strModuleForm;
		//$strUserRoleArr['strEventArr']			= $this->_getEventList();
		$strUserRoleArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strUserRoleArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strUserRoleArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getUserRolesDetailsByCode';
		$strUserRoleArr['strDataAddEditPanel']	= 'useRoleModel';
		$strUserRoleArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the login */
		$dataArr['body']	= $this->load->view('settings/userroles', $strUserRoleArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get user roles details by code.
	/*Inputs	: None.
	/*Returns 	: User Role Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getUserRolesDetailsByCode(){
		/* Setting the role code */
		$intUserRoleCode 					= ($this->input->post('txtCode') != '') ? $this->input->post('txtCode') : 0;
		$blnUserEventAssocationDetails		= ($this->input->post('txtEventAssocationFlag'))?$this->input->post('txtEventAssocationFlag'):false;
		$strUserRoleArr						= array();
		/* Checking the Role Code shared */
		if($intUserRoleCode > 0){
			/* Event association details Request*/
			if($blnUserEventAssocationDetails){
				/* getting event association details*/
				$strUserRoleArr					= $this->_getUserRoleEventAssocationDetails($intUserRoleCode);
			}else{
				/* getting requested role code details */
				$strUserRoleArr					= $this->_getUserRole($intUserRoleCode);
			}
			
			/* if record not found then do needful */
			if(empty($strUserRoleArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Event association details Request*/
				if($blnUserEventAssocationDetails){
					/* Return the JSON string */
					jsonReturn($strUserRoleArr, true);
				}else{
					/* Return the JSON string */
					jsonReturn($strUserRoleArr[0], true);
				}
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid user role code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the user role details.
	/*Inputs	: $pUserCodeCode :: User Role description,
				: $pStrUserRoleName :: User role name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserRole($pUserCodeCode = 0, $pStrUserRoleName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strUserRoleArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* if user role filter code is passed then do needful */
		if($pUserCodeCode < 0){
			/* Adding User Role code filter */
			$strWhereClauseArr	= array('company_code'=>1, 'parent_id'=>-1);
		/* if role filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strUserRole	= ($this->input->post('txtUserRole') != '') ? $this->input->post('txtUserRole') : '';
			
			if($strUserRole != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$strUserRole));
			}
		}else{
			/* Getting status categories */
			if($pUserCodeCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pUserCodeCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pUserCodeCode));
				}
			}
		}
		
		/* filter by role name */
		if($pStrUserRoleName !=''){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$pStrUserRoleName));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pUserCodeCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}

		/* Getting the status list */
		$strUserRoleArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strUserRoleArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get selected event filter by role code.
	/*Inputs	: $pIntRoleCode :: Role code.
	/*Returns 	: Associated event code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserRoleEventAssocationDetails($pIntRoleCode = 0){
		/* variable initialization */
		$strReturnArr	= array();
		
		/* Setting filter array */
		$strFilterArr	= array(
									'table'=>'trans_role_event_assocation',
									'column' => array('event_code','role_code'),
									'where' => array('role_code' => $pIntRoleCode)
								);
		
		/* Getting event code list */
		$strReturnArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if event association details found then do needful */
		if(!empty($strReturnArr)){
			/* iterating the loop */
			foreach($strReturnArr as $strReturnArrKey => $strReturnArrValue){
				/* iterating the loop */
				$strReturnArr[$strReturnArrKey]['event_code']	 = getEncyptionValue($strReturnArrValue['event_code']);
			}
		}
		
		/* Return event code list */
		return $strReturnArr;
		 
	}

	/**********************************************************************/
	/*Purpose 	: Setting the user role details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setUserRole(){
		/* variable initialization */
		$strUserRoleName		= ($this->input->post('txtUserRole') != '')?$this->input->post('txtUserRole'):'';
		$intUserRoleCode		= ($this->input->post('txtUserRoleCode') != '')?$this->input->post('txtUserRoleCode'):0;
		$blnEditRequest			= (($intUserRoleCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strUserRoleName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested mandatory field(s) are empty.'), true);
		}
		
		/* Fetching any status with same name */
		$strUserRoleArr 	= $this->_getUserRole($intUserRoleCode, $strUserRoleName, $blnEditRequest);
		
		/* if status already exists then do needful */
		if(!empty($strUserRoleArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested User Role is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
														'description'=>$strUserRoleName,
														'company_code'=>$this->getCompanyCode()
													)
									);
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intUserRoleCode);
				/* Adding user role in the database */
				$intUserRoleCode = $this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding user role in the database */
				$intUserRoleCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			
			/* Removed used variables */
			unset($strDataArr);
			/* checking last insert id / updated record count */
			if($intUserRoleCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'User Role Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'User Role added successfully.'), true);
				}
			}else{
				jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
			}
		}
	}

	/**********************************************************************/
	/*Purpose 	: Delete the record from table of requested code.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function deleteRecord(){
		/* Variable initialization */
		$intUserRoleCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not role code pass then do needful */
		if($intUserRoleCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid user role code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intUserRoleCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested User Role deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get event list filter bu company code.
	/*Inputs	: None.
	/*Returns 	: Event List.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getEventList(){
		/* variable initialization */
		$strWhereClauseArr	= array('company_code' => $this->getCompanyCode());
		$strFilterArr		= array('table'=>'events_'.$this->getCompanyCode());
		/* Getting the status list */
		return	$this->_objDataOperation->getDataFromTable($strFilterArr, $strWhereClauseArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting the role and event association.
	/*Inputs	: None.
	/*Returns 	: Operation Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setUserRoleEvent(){
		/* variable initialization */
		$intRoleCode		= ($this->input->post('txtEventRoleCode') !='')?$this->input->post('txtEventRoleCode'):0;
		$strEventCodesArr	= (!empty($this->input->post('txtEventName[]')))?$this->input->post('txtEventName[]'):array();
		$strEventDataListArr= array();
		/* if invalid role code is pass then do needful */
		if($intRoleCode == 0){
			/* return error */
			jsonReturn(array('status'=>0,'message'=>'Invalid role code.'), true);
		}
		/* if event code is not selected then do needful */
		if(empty($strEventCodesArr)){
			/* return error */
			jsonReturn(array('status'=>0,'message'=>'Event is not selected.'), true);
		}
		
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>'trans_role_event_assocation',
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'role_code'=>$intRoleCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);
		
		/* iterating the event loop */
		foreach($strEventCodesArr as $strEventCodesArrKey => $strEventCodesArrValue){
			/* Setting event and role code association */
			$strEventDataListArr[]	= array(
												'role_code'=>$intRoleCode,
												'event_code'=>getDecyptionValue($strEventCodesArrValue),
												'updated_by'=>$this->getUserCode()
										);
		}
		/* Inserting the data value */
		$intNunberOfRecordUpdated	= $this->_objDataOperation->setBulkInset(array('table'=>'trans_role_event_assocation','data'=>$strEventDataListArr)); 
		
		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Event and Role are associated successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}
	}
}