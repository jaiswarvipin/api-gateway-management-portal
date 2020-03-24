<?php
/***********************************************************************/
/* Purpose 		: Application user profile.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Userprofiles extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName				= 'master_user';
	private $_strModuleName						= "User Profile";
	private $_strModuleForm						= "frmUserProfile";
	private $_strVerticalAssocationTableName	= "trans_user_vertical_association";
	
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
		$intCurrentPageNumber					= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting user role list */
		$strUserRoleArr['dataSet'] 				= $this->_getUserProfilDetails(0,'',false,false, $intCurrentPageNumber);
		$strUserRoleArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strUserRoleArr['pagination'] 			= getPagniation($this->_getUserProfilDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strUserRoleArr['moduleTitle']			= $this->_strModuleName;
		$strUserRoleArr['moduleForm']			= $this->_strModuleForm;
		$strUserRoleArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strUserRoleArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strUserRoleArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getUserProfileDetailsByCode';
		$strUserRoleArr['strCustomUri']			= SITE_URL.'settings/'.__CLASS__.'/getLocationByCode';
		$strUserRoleArr['strDataAddEditPanel']	= 'userProfileModel';
		$strUserRoleArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strUserRoleArr['strCustomRoleArr']		= $this->_getRoleDetails();
		$strUserRoleArr['strSystemRoleArr']		= $this->_getRoleDetails(1);
		$strUserRoleArr['strVerticalArr']		= array();//$this->_getVerticalDetails();
		$strUserRoleArr['strUserStatsArr']		= $this->_objForm->getDropDown(array('1'=>'Active','0'=>'In-Active'),'',false);
		
		
		/* Load the login */
		$dataArr['body']	= $this->load->view('settings/userprofiles', $strUserRoleArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get user profile details by code.
	/*Inputs	: None.
	/*Returns 	: User Role Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getUserProfileDetailsByCode(){
		/* Setting the user profile code */
		$intUserCode 				= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strUserArr					= array();
		/* Checking the user profile Code shared */
		if($intUserCode > 0){
			/* getting requested user profile code details */
			$strUserArr				= $this->_getUserProfilDetails($intUserCode);
			
			/* if record not found then do needful */
			if(empty($strUserArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strUserArr, true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid user profile code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the user profile details.
	/*Inputs	: $pUserCodeCode :: User profile description,
				: $pStrUserEmailAddress :: User email address name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Status Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserProfilDetails($pUserCodeCode = 0, $pStrUserEmailAddress = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strUserRoleArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array($this->_strPrimaryTableName.'.company_code'=>$this->getCompanyCode());
		
		/* if user profile filter code is passed then do needful */
		if($pUserCodeCode < 0){
			/* Adding User profile code filter */
			$strWhereClauseArr	= array('company_code'=>1);
		/* if profile filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strUserName			= ($this->input->post('txtUserName') != '')?$this->input->post('txtUserName'):'';
			$strEmailAddress		= ($this->input->post('txtEmail') != '')?$this->input->post('txtEmail'):'';
			$intUserRoleCode		= ($this->input->post('cboRoleCode') != '')?getDecyptionValue($this->input->post('cboRoleCode')):0;
			$intUserSystemRoleCode	= ($this->input->post('cboUserSystemRole') != '')?getDecyptionValue($this->input->post('cboUserSystemRole')):0;
			$intUserStatusCode		= ($this->input->post('cboUserStatus') != '')?getDecyptionValue($this->input->post('cboUserStatus')):0;
			
			if($strUserName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('user_name like'=>$strUserName));
			}
			if($strEmailAddress != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('user_email like'=>$strEmailAddress));
			}
			if($intUserRoleCode != 0){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('role_code'=>$intUserRoleCode));
			}
			if($intUserRoleCode != 0){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('system_role_code'=>$intUserSystemRoleCode));
			}
			if($intUserRoleCode != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('is_active'=>$intUserStatusCode));
			}
		}else{
			/* Getting status categories */
			if($pUserCodeCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'.id !='=>$pUserCodeCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array($this->_strPrimaryTableName.'.id'=>$pUserCodeCode));
				}
			}
		}
		
		/* filter by email name */
		if($pStrUserEmailAddress !=''){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('email like'=>$pStrUserEmailAddress));
		}
		
		/* Filter array */
		$strFilterArr	= array(
									'table'=>array($this->_strPrimaryTableName,'master_role'),
									'column'=>array($this->_strPrimaryTableName.'.*','master_role.description as role_name'),
									'join'=>array('',$this->_strPrimaryTableName.'.role_code =  master_role.id'),
									'where'=>$strWhereClauseArr,
									'order'=>array('user_name'=>'asc')
								);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count('.$this->_strPrimaryTableName.'.id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pUserCodeCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the status list */
		$strUserProfileArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if edit request then do needful */
		if((int)$pUserCodeCode > 0){
			$strUserProfileArr[0]['role_code']			= getEncyptionValue($strUserProfileArr[0]['role_code']);
			$strUserProfileArr[0]['system_role_code']	= getEncyptionValue($strUserProfileArr[0]['system_role_code']);
			$strUserProfileArr[0]['is_active']			= getEncyptionValue($strUserProfileArr[0]['is_active']);
			$strUserProfileArr[0]['vertical_codes']		= array();//$this->_getUserVerticalAssocationDetails($strUserProfileArr[0]['id']);
		}
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strUserProfileArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting the user profile details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setUserProfile(){
		/* variable initialization */
		$intUserCode			= ($this->input->post('txtUserCode') != '')?$this->input->post('txtUserCode'):0;
		$strUserName			= ($this->input->post('txtUserName') != '')?$this->input->post('txtUserName'):'';
		$strEmailAddress		= ($this->input->post('txtEmail') != '')?$this->input->post('txtEmail'):'';
		$strPassword			= ($this->input->post('txtPassword') != '')?$this->input->post('txtPassword'):'';
		$intUserRoleCode		= ($this->input->post('cboRoleCode') != '')?getDecyptionValue($this->input->post('cboRoleCode')):0;
		$intUserSystemRoleCode	= ($this->input->post('cboUserSystemRole') != '')?getDecyptionValue($this->input->post('cboUserSystemRole')):0;
		$intUserStatusCode		= ($this->input->post('cboUserStatus') != '')?getDecyptionValue($this->input->post('cboUserStatus')):0;
		$strVerticalArr			= ($this->input->post('txtVertcalName') != '')?$this->input->post('txtVertcalName'):array();
		$blnEditRequest			= (($intUserCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		$strBranchCodeArr		= array();
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strUserName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'User name field is empty.'), true);
		}else if(($strEmailAddress == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'User email field is empty.'), true);
		}else if(($strPassword == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Password field is empty.'), true);
		}else if(($intUserRoleCode == 0)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'User Custom Role is not selected.'), true);
		}else if(empty($strVerticalArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Associated vertical is not selected.'), true);
		}
		
		/* Checking enter email address is already register or not */
		if($blnEditRequest){
			$strUserDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('user_email'=>$strEmailAddress, 'id !='=>$intUserCode)));
		}else{
			$strUserDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('user_email'=>$strEmailAddress)));
		}
		
		/* if status already exists then do needful */
		if(!empty($strUserDataArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested User is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
											'data'=>array(
														'user_name'=>$strUserName,
														'user_email'=>$strEmailAddress,
														'password'=>md5($strPassword),
														'company_code'=>$this->getCompanyCode(),
														'is_active'=>$intUserStatusCode,
														'is_admin'=>0,
														'role_code'=>$intUserRoleCode,
														'system_role_code'=>$intUserSystemRoleCode
													)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intUserCode);
				/* Updating user profile in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding user profile in the database */
				$intUserCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			
			/* Setting the user and vertical assocation details */
			//$this->_setUserVerticalAssocation($intUserCode, $strVerticalArr);
			
			/* checking last insert id / updated record count */
			if($intUserCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'User Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'User added successfully.'), true);
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
	/* Purpose 		: Get role details by type.
	/* Inputs		: pIntRoleType :: Role Type.
	/* Returns 		: Role details
	/* Created By	: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getRoleDetails($pIntRoleType = 0){
		/* variable initialization */
		$roleObj	= new Role($this->_objDataOperation, $this->getCompanyCode());
		/* Get System Role */
		$strRoleArr	= $roleObj->getCustomRoleDetails();
		/* Removed used variables */
		unset($roleObj);
		
		/* Return drop down list */
		return $this->_objForm->getDropDown(getArrByKeyvaluePairs($strRoleArr,'id','description'),'',false);
	}
	
	/**********************************************************************/
	/* Purpose 		: Get vertical details by type.
	/* Inputs		: pIntVerticalCode :: Vertical Code.
	/* Returns 		: Vertical details
	/* Created By	: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getVerticalDetails($pIntVerticalCode = 0){
		/* variable initialization */
		$verticalObj	= new Vertical($this->_objDataOperation, $this->getCompanyCode());
		/* Get Vertical Details */
		$strVerticalArr	= $verticalObj->getVerticalDetails();
		/* Removed used variables */
		unset($verticalObj);
		
		/* Return drop down list */
		return $strVerticalArr;
	}
	
	/**********************************************************************/
	/* Purpose 		: Get vertical and user assocation details by user code.
	/* Inputs		: pIntUserCode :: User Code.
	/* Returns 		: Vertical and User Array
	/* Created By	: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserVerticalAssocationDetails($pIntUserCode = 0){
		/* variable initialization */
		$verticalObj	= new Vertical($this->_objDataOperation, $this->getCompanyCode());
		$strReturnArr	= array();
		
		/* if user code is not pass then do needful */
		if($pIntUserCode == 0){
			/* return the empty array */
			return $strReturnArr;
		}
		
		/* Get Vertical assocation details with user code */
		$strVerticalArr	= $verticalObj->getVerticalUserAssocationDetails($pIntUserCode);
		/* Removed used variables */
		unset($verticalObj);
		
		/* if vertical array is not empty then do needful */
		if(!empty($strVerticalArr)){
			/* Iterating the loop */
			foreach($strVerticalArr as $strVerticalArrKey => $strVerticalArrValue){
				/* Setting the values */
				$strReturnArr[] = array('vertical_code'=>getEncyptionValue($strVerticalArrValue['vertical_code']));
			}
		}
		/* removed used variables */
		unset($strVerticalArr);
		
		/* Return pre-selected vertical list */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/* Purpose 		: Set the vertical and user assocation details.
	/* Inputs		: pIntUserCode :: User Code,
					: pStrVerticalCodeArr :: Vertical Code array.
	/* Returns 		: Vertical and User Array.
	/* Created By	: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setUserVerticalAssocation($pUserCode = 0, $pStrVerticalCodeArr = array()){
		/* if Vertical code and user code is passed then do needful */
		if(!empty($pStrVerticalCodeArr) && ($pUserCode > 0)){
			/* De-activating all previous assocation */
			$strUpdatedArr	= array(
									'table'=>$this->_strVerticalAssocationTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$pUserCode
											)

								);
			/* Updating the requested record set */
			$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);
		
			/* Iterating the loop */
			foreach($pStrVerticalCodeArr as $pStrVerticalCodeArrKey => $pStrVerticalCodeArrValue){
				/* Setting the value */
				$strDataArr	= array(
									'table'=>$this->_strVerticalAssocationTableName,
									'data'=>array(
												'user_code'=>$pUserCode,
												'vertical_code'=>getDecyptionValue($pStrVerticalCodeArrValue),
												'company_code'=>$this->getCompanyCode()
											)
								);
				/* Insert data */
				$intUserCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
		}
	}
}