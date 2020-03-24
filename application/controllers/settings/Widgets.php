<?php
/***********************************************************************/
/* Purpose 		: Application Widget List management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Widgets extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_widget';
	private $_strModuleName			= "Widgets";
	private $_strModuleForm			= "frmWidget";
	
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
		
		/* Getting widget access list */
		$strDataArr['dataSet'] 				= $this->_getWidgetsDetails(0,'',false,false, $intCurrentPageNumber);
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation($this->_getWidgetsDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strDataArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strDataArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getWidgetDetailsByCode';
		$strDataArr['strDataAddEditPanel']	= 'widgetModel';
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/widgets', $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get widget details by code.
	/*Inputs	: None.
	/*Returns 	: Widget Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getWidgetDetailsByCode(){
		/* Setting the role code */
		$intWidgetCode 					= ($this->input->post('txtCode') != '') ? $this->input->post('txtCode') : 0;
		$strWidgetArr						= array();
		/* Checking the Widget Code shared */
		if($intWidgetCode > 0){
			/* getting requested Widget code details */
			$strWidgetArr					= $this->_getWidgetsDetails($intWidgetCode);
			
			/* if record not found then do needful */
			if(empty($strWidgetArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strWidgetArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid widget requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the widgets details.
	/*Inputs	: $pModuleCode :: Module code,
				: $pStrModuleName :: Module Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Widget details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getWidgetsDetails($pModuleCode = 0, $pStrModuleName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strDataArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode());
		
		/* if widget filter code is passed then do needful */
		if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strWidgetDetails	= ($this->input->post('txtWidgetDescription') != '') ? $this->input->post('txtWidgetDescription') : '';
			/* if widiget details passed then do needful */
			if($strWidgetDetails != ''){
				/* applying filters */
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$strWidgetDetails));
			}
		}else{
			/* Getting widget code check  */
			if($pModuleCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding widget code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pModuleCode));
				}else{
					/* Adding widget code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pModuleCode));
				}
			}
		}
		
		/* filter by role name */
		if($pStrModuleName !=''){
			/* Adding Status code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$pStrModuleName));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pModuleCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the module list */
		$strModuleArr					=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		//$strModuleArr[0]['role_code'] 	= $pModuleCode;
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strModuleArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting the widget details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setWidgetDetails(){
		/* variable initialization */
		$strWidgetDetails		= ($this->input->post('txtWidgetDescription') != '')?$this->input->post('txtWidgetDescription'):'';
		$intWidgetCode			= ($this->input->post('txtWidgetCode') != '')?$this->input->post('txtWidgetCode'):0;
		$blnEditRequest			= (($intWidgetCode > 0)?true:false);
		$blnSearch				= ($this->input->post('txtSearch') != '')?true:false;
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strWidgetDetails == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested mandatory field(s) are empty.'), true);
		}
		
		/* Fetching any widget with same name */
		$strWidgetArr 	= $this->_getWidgetsDetails($intWidgetCode, $strWidgetDetails, $blnEditRequest);
		
		/* if status already exists then do needful */
		if(!empty($strWidgetArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Widget is already exists.'), true);	
		}else{
			/* Varaible initialization */
			$strSchemaName	= getSlugify($strWidgetDetails, true)."_".$this->getCompanyCode();
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
														'description'=>$strWidgetDetails,
														'schema_slug'=>$strSchemaName,
														'company_code'=>$this->getCompanyCode()
													)
									);
			/* Checking for edit request */
			if($blnEditRequest){
				/* Fetching any widget of requested widget code */
				$strWidgetArr 	= $this->_getWidgetsDetails($intWidgetCode);
		
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intWidgetCode);
				/* Adding widget in the database */
				$intWidgetCode = $this->_objDataOperation->setUpdateData($strDataArr);
				
				/* if widget details found then do needful */
				if((!empty($strWidgetArr)) && (isset($strWidgetArr[0]))){
					/* Updating the existing table name */
					$this->_setTranscationSchema($strSchemaName, $strWidgetArr[0]['schema_slug']);
				}
				
				/* removed used variables */
				unset($strWidgetArr);
			}else{
				/* Adding widget in the database */
				$intWidgetCode = $this->_objDataOperation->setDataInTable($strDataArr);
				/* Setting the new schema */
				$this->_setTranscationSchema($strSchemaName);
			}
			
			/* Removed used variables */
			unset($strDataArr);
			/* checking last insert id / updated record count */
			if($intWidgetCode > 0){
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Widget Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Widget added successfully.'), true);
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
		$intWidgetCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? $this->input->post('txtDeleteRecordCode') : 0;

		/* if not role code pass then do needful */
		if($intWidgetCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid widget code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intWidgetCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Widget deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Creating requested widget table.
	/*Inputs	: $pStrWidgetSchemaName :: Schema name,
				: $pStrExistingSchemaName :: Existing schema name.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setTranscationSchema($pStrWidgetSchemaName = '', $pStrExistingSchemaName = ''){
		/* if company code is not pass the do needful */
		if (trim($pStrWidgetSchemaName) == ''){
			/* return the pointer */
			return false;
		}
		
		/* if table name is already exists then do needful */
		if($pStrExistingSchemaName != ''){
			/* Rename existing schema */
			$this->_objDataOperation->getDirectQueryResult("RENAME TABLE ".$pStrExistingSchemaName." TO ".$pStrWidgetSchemaName." ;");
			$this->_objDataOperation->getDirectQueryResult("RENAME TABLE ".$pStrExistingSchemaName."_log TO ".$pStrWidgetSchemaName."_log ;");
		}else{
			/* Creating Schema */
			$this->_objDataOperation->getDirectQueryResult("CREATE TABLE ".$pStrWidgetSchemaName." (id bigint(20) NOT NULL AUTO_INCREMENT, company_code bigint(20) NOT NULL, updated_date bigint(20) NOT NULL DEFAULT '0', updated_by bigint(20) NOT NULL DEFAULT '0', record_date bigint(20) NOT NULL DEFAULT '0', deleted int(1) NOT NULL DEFAULT '0', PRIMARY KEY (id), KEY company_code (company_code), KEY updated_date (updated_date), KEY updated_by (updated_by), KEY record_date (record_date), KEY deleted (deleted));");
			$this->_objDataOperation->getDirectQueryResult("CREATE TABLE ".$pStrWidgetSchemaName."_log (id bigint(20) NOT NULL AUTO_INCREMENT, log_id bigint(20) NOT NULL DEFAULT '0', company_code bigint(20) NOT NULL, updated_date bigint(20) NOT NULL DEFAULT '0', updated_by bigint(20) NOT NULL DEFAULT '0', record_date bigint(20) NOT NULL DEFAULT '0', log_recorded_date bigint(20) NOT NULL DEFAULT '0', deleted int(1) NOT NULL DEFAULT '0', PRIMARY KEY (id), KEY company_code (company_code), KEY updated_date (updated_date), KEY updated_by (updated_by), KEY log_id (log_id), KEY record_date (record_date), KEY deleted (deleted), KEY log_recorded_date (log_recorded_date));");
		}
		/* removed used variables */
		unset($ObjdbOperation);
	}
}