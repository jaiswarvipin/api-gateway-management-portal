<?php
/***********************************************************************/
/* Purpose 		: Application Environment Setting.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Environment extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_company_config';
	private $_strModuleName			= "Environment";
	private $_strModuleForm			= "frmEnvironmentSetting";
	
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
		
		/* Getting environment list */
		$strRecordsetArr['dataSet'] 				= $this->_getEnvironmentData(0,'',false,false, $intCurrentPageNumber);
		$strRecordsetArr['intPageNumber'] 			= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strRecordsetArr['pagination'] 				= getPagniation($this->_getEnvironmentData(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strRecordsetArr['moduleTitle']				= $this->_strModuleName;
		$strRecordsetArr['moduleForm']				= $this->_strModuleForm;
		$strRecordsetArr['moduleUri']				= SITE_URL.'settings/'.__CLASS__;
		$strRecordsetArr['deleteUri']				= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strRecordsetArr['getRecordByCodeUri']		= SITE_URL.'settings/'.__CLASS__.'/getEnvironmentDetailsByCode';
		$strRecordsetArr['noSearchAdd']				= 'yes';
		$strRecordsetArr['strDataAddEditPanel']		= 'environmentModel';
		$strRecordsetArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the environment list */
		$dataArr['body']	= $this->load->view('settings/environment', $strRecordsetArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get the environment details by code.
	/*Inputs	: None.
	/*Returns 	: Environment Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getEnvironmentDetailsByCode(){
		/* Setting the environment code */
		$intEnvironmentCode 					= ($this->input->post('txtCode') != '') ? $this->input->post('txtCode') : 0;
		$strRecordsetArr						= array();
		/* Checking the environment code */
		if($intEnvironmentCode > 0){
			/* getting requested environment code details */
			$strRecordsetArr						= $this->_getEnvironmentData($intEnvironmentCode);
			
			/* if record not found then do needful */
			if(empty($strRecordsetArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strRecordsetArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid environment code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the environment details.
	/*Inputs	: $pIntEnvironmentCode :: Environment Code,
				: $pStrEnvironmentName :: Environment name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Environment Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getEnvironmentData($pIntEnvironmentCode = 0, $pStrEnvironmentName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strRecordsetArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* if environment code is set the do needful */
		if((int)$pIntEnvironmentCode > 0){
			/* Setting filter clause */
			$strWhereClauseArr	= array_merge(array('id'=>$pIntEnvironmentCode), $strWhereClauseArr);
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pIntEnvironmentCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}

		/* Getting the environment list */
		$strRecordsetArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if record found then do needful */
		if(!empty($strRecordsetArr)){
			/* iterating the loop  */
			foreach($strRecordsetArr as $strRecordsetArrKey => $strRecordsetArrValue){
				if(isset($strRecordsetArrValue['key_description'])){
					/* Variable initialization */
					$valueDescriptionArr = array();
					$valueDescriptionStr = '';

					if (!empty($strRecordsetArr[$strRecordsetArrKey]['value_description']) && isJSON($strRecordsetArr[$strRecordsetArrKey]['value_description'])) {
						/* decoding the the JSON value array */
						$valueDescriptionArr = json_decode($strRecordsetArr[$strRecordsetArrKey]['value_description'], true);
						/* Creating the value array */
						$valueDescriptionArr = array_map(function($value, $key) {
							return $key.'="'.$value.'"';
						}, array_values($valueDescriptionArr), array_keys($valueDescriptionArr));
						/* Formatting */
						$valueDescriptionStr = implode('<br />', $valueDescriptionArr);
					}
					/* Setting the value */
					$strRecordsetArr[$strRecordsetArrKey]['value_description']	= $valueDescriptionStr;
				}
			}
		}
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strRecordsetArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting the environment details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setEnvironment(){
		/* variable initialization */
		$strKeyDescription		= ($this->input->post('txtKeyDescription') != '')? $this->input->post('txtKeyDescription'):'';
		$strValueDescription	= 	(!empty($this->input->post('ValueDescription')) && is_array($this->input->post('ValueDescription'))) ? $this->input->post('ValueDescription') : array();
		$strValueDescription 	= 	json_encode($strValueDescription);

		$intEnvironmentCode		= ($this->input->post('txtEnvironmentCode') != '')?$this->input->post('txtEnvironmentCode'):0;
		$blnEditRequest			= (($intEnvironmentCode > 0)?true:false);
		
		if(!$strValueDescription){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested mandatory field(s) are empty.'), true);
		}
		
		/* Setting where clause */
		$strWhereArr	= array('id'=>$intEnvironmentCode,'company_code'=>$this->getCompanyCode());
		
		/* if filter clause found then do needful */
		if(!empty($strWhereArr)){
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array('value_description'=>$strValueDescription),
										'where'=>$strWhereArr
									);
									
			/* updating environment in the database */
			$intEnvironmentCode = $this->_objDataOperation->setUpdateData($strDataArr);
		}
		
		/* Removed used variables */
		unset($strDataArr);
		/* checking last insert id / updated record count */
		if($intEnvironmentCode > 0){
			/* Checking for edit request */
			if($blnEditRequest){
				jsonReturn(array('status'=>1,'message'=>'Environment Updated successfully.'), true);
			}else{
				jsonReturn(array('status'=>1,'message'=>'Environment added successfully.'), true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
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
		$intEnvironmentCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not environment pass then do needful */
		if($intEnvironmentCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid environment code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intEnvironmentCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Environment deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
}