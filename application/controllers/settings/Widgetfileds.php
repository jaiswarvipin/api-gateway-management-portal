<?php
/***********************************************************************/
/* Purpose 		: Application Widget attributes.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Widgetfileds extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_widget_attributes';
	private $_strModuleName			= "Widget Attributes";
	private $_strModuleForm			= "frmWidgetAttributes";
	
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
		$dataArr								= array();
		
		$strWidgetName							= $this->_getWidgetDetails();
		
		/* Getting current page number */
		$intCurrentPageNumber					= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting widget attributes list */
		$strWidgetDataArr['dataSet'] 			= $this->_getWidgetAttributeDetails(0,'',false,false, $intCurrentPageNumber);
		$strWidgetDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strWidgetDataArr['pagination'] 		= getPagniation($this->_getWidgetAttributeDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strWidgetDataArr['moduleTitle']		= $strWidgetName.' - '.$this->_strModuleName;
		$strWidgetDataArr['moduleForm']			= $this->_strModuleForm;
		$strWidgetDataArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strWidgetDataArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strWidgetDataArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getWidgetAttributeByCode';
		$strWidgetDataArr['strDataAddEditPanel']= 'widgetAttriuteModel';
		$strWidgetDataArr['strSearchArr']		= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strWidgetDataArr['strElementsArr']		= $this->_objForm->getDropDown(unserialize(LEAD_ATTRIBUTE_INPUT_ELEMENT),'');
		$strWidgetDataArr['strFileDriverArr'] 	= 	$this->_objForm->getDropDown(unserialize(LEAD_ATTRIBUTE_FILE_DRIVER),'');
		$strWidgetDataArr['strFileDriverArrNew']= 	!empty(LEAD_ATTRIBUTE_FILE_DRIVER) ? unserialize(LEAD_ATTRIBUTE_FILE_DRIVER) : array();
		$strWidgetDataArr['strFileDriverArrNew']= 	array_map( 'getEncyptionValue', $strWidgetDataArr['strFileDriverArrNew']);
		$strWidgetDataArr['strFileDriverArrNew']= implode(DELIMITER, $strWidgetDataArr['strFileDriverArrNew']);

		$strWidgetDataArr['strValidationArr']	= $this->_objForm->getDropDown(unserialize(LEAD_ATTRIBUTE_INPUT_VALIDATION),'');
		$strWidgetDataArr['strWidgetCode']		= (isset($_REQUEST['wIdGetCoDe']) && ($_REQUEST['wIdGetCoDe'] != ''))?$_REQUEST['wIdGetCoDe']:'';
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/widget-attributes', $strWidgetDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get widget attribute details by code.
	/*Inputs	: None.
	/*Returns 	: widget Attributes Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getWidgetAttributeByCode(){
		/* Setting the widget attribute code */
		$intWidgetAttributeCode 	= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$strWidgetAttributeArr		= array();
		
		/* Checking the widget attribute code shared */
		if($intWidgetAttributeCode > 0){
			/* getting requested widget attribute code details */
			$strWidgetAttributeArr	= $this->_getWidgetAttributeDetails($intWidgetAttributeCode);
			
			/* if record not found then do needful */
			if(empty($strWidgetAttributeArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Setting the default value collection values */
				$strWidgetAttributeArr[0]['attri_value_list']	= !empty($this->_getWidgetAttributeListDetails($intWidgetAttributeCode)) ? array_column($this->_getWidgetAttributeListDetails($intWidgetAttributeCode), 'description', 'id') : '';
				/* Return the JSON string */
				jsonReturn($strWidgetAttributeArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid widget attribute code requested.'), true);
		}
	}

	/********************************************************************************************/
	/*Purpose 	: Getting the widget profile details.
	/*Inputs	: $pWidgetAttributeCode :: Widget attribute code description,
				: $pStrSlugName :: Widget attribute slug name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/********************************************************************************************/
	private function _getWidgetAttributeDetails($pWidgetAttributeCode = 0, $pStrSlugName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strWidgetDataArr	= $strWhereClauseArr 	= array();
		$intWidgetCode		= (isset($_REQUEST['wIdGetCoDe']) && ($_REQUEST['wIdGetCoDe'] != ''))? getDecyptionValue($_REQUEST['wIdGetCoDe']):0;
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>$this->getCompanyCode(),'widget_code'=>$intWidgetCode);
		
		/* if profile filter code is passed then do needful */
		if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strSlugName			= ($this->input->post('txtAttrubuteName') != '')?$this->input->post('txtAttrubuteName'):'';
			$strSlugKey				= getSlugify($strSlugName);
			/* if slug key found then do needful */
			if($strSlugKey != ''){
				/* Setting filter key and value */
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('attri_slug_key like'=>$strSlugKey));
			}
		}else{
			/* Getting widget code  */
			if($pWidgetAttributeCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Widget code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pWidgetAttributeCode));
				}else{
					/* Adding Widget code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pWidgetAttributeCode));
				}
			}
		}
		
		/* filter by widget attribute details */
		if($pStrSlugName !=''){
			/* Adding widget attribute description filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('attri_slug_key like'=>getSlugify($pStrSlugName)));
		}
		
		/* Filter array */
		$strFilterArr	= array('table'=>$this->_strPrimaryTableName,'where'=>$strWhereClauseArr);
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column']	 = array(' count(id) as recordCount ');
		}
		
		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0) && ($pWidgetAttributeCode >= 0)){
			$strFilterArr['offset']	 = ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit']	 = DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* Getting the widget attribute list */
		$strWidgetAttArr			=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		//
		/* Getting widget attribute details */
		if($pWidgetAttributeCode > 0){
			$strWidgetAttArr[0]['attri_data_type']	= getEncyptionValue($strWidgetAttArr[0]['attri_data_type']);
			$strWidgetAttArr[0]['attri_validation']	= getEncyptionValue($strWidgetAttArr[0]['attri_validation']);

			/* File Driver Value Encypt */
			$strWidgetAttArr[0]['file_driver'] 		= getEncyptionValue($strWidgetAttArr[0]['file_driver']);

		}
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return widget details */
		return $strWidgetAttArr;
	}

	/**********************************************************************/
	/*Purpose 	: Getting the widget attribute list profile details.
	/*Inputs	: $pWidgetAttributeCode :: Widget attribute code description
	/*Returns 	: Lead attribute details.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	private function _getWidgetAttributeListDetails($pWidgetAttributeCode){

		/* variable initialization */
		$strWhereClauseArr 	= array('attribute_code'=>$pWidgetAttributeCode);

		/* Filter array */
		$strFilterArr	= array(
									'column'=>array('id', 'attribute_code', 'description', 'default_value'), 
									'table'=>'master_widget_attributes_list', 
									'where'=>$strWhereClauseArr
								);

		/* Getting the widget attribute list */
		$strWidgetAttListArr			=  $this->_objDataOperation->getDataFromTable($strFilterArr);

		/* Removed used variables */
		unset($strFilterArr);

		/* return widget details */
		return $strWidgetAttListArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting widget attribute details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setWidgetAttributDetails(){
		/* variable initialization */
		$intWidgetAttributeCode		= ($this->input->post('txtAttributeCode') != '')? $this->input->post('txtAttributeCode'):0;
		$setWidgetAttributName		= ($this->input->post('txtAttrubuteName') != '')?$this->input->post('txtAttrubuteName'):'';
		$strWidgetAttributHelp		= ($this->input->post('txtAttrubuteHelp') != '')?$this->input->post('txtAttrubuteHelp'):'';
		$strWidgetAttributeKey		= getSlugify($setWidgetAttributName);
		$strAttributeTypeCode		= ($this->input->post('cboAttributeType') != '')?getDecyptionValue($this->input->post('cboAttributeType')):'';
		$strAttributeFileTypeDriver	= ($this->input->post('cboAttributeFileTypeDriver') != '')?getDecyptionValue($this->input->post('cboAttributeFileTypeDriver')):'';
		$strAttributeFilePath		= ($this->input->post('txtFilePath') != '')?$this->input->post('txtFilePath'):'';
		$strValidationCode			= ($this->input->post('cboValidation') != '')?getDecyptionValue($this->input->post('cboValidation')):'';
		$isMandatory				= ($this->input->post('rdoisMandatory') != '')?($this->input->post('rdoisMandatory')):'0';
		$blnEditRequest				= (($intWidgetAttributeCode > 0)?true:false);
		$blnSearch					= ($this->input->post('txtSearch') != '')?true:false;
		$strWhereClauseArr			= $strExistingColumnArr 	= array();
		$strAttributeListArr		= !empty($this->input->post('txtWidgetAttributesName')) ? $this->input->post('txtWidgetAttributesName') : array();
		$intWidgetCode				= (isset($_REQUEST['wIdGetCoDe']) && ($_REQUEST['wIdGetCoDe'] != ''))? getDecyptionValue($_REQUEST['wIdGetCoDe']):0;
		
		/* if widget code is not set then do needful */
		if($intWidgetCode == 0){
			/* redirecting to login */
			redirect(SITE_URL.'/settings/widgets');
		}
		
		/* Checking to all valid information passed */
		if(($setWidgetAttributName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Widget attribute description field is empty.'), true);
		/* Checking to all valid information passed */
		}else if(($strWidgetAttributHelp == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Widget attribute quick helps field is empty.'), true);
		}else if(($strAttributeTypeCode == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Widget attribute type is not selected.'), true);
		}else if(($strAttributeTypeCode == 'file') && ($strAttributeFileTypeDriver == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Widget file driver type is not selected.'), true);
		}
		
		/* Adding widget attributed filter array */
		$strWhereClauseArr	= array('widget_code'=>$intWidgetCode,'attri_slug_key'=>$strWidgetAttributeKey,'company_code'=>$this->getCompanyCode());
		
		/* Checking for edit request */
		if($blnEditRequest){
			/* Adding widget attribute code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$intWidgetAttributeCode));
		}
		
		/* Checking enter widget attribute slug address is already register or not */
		$strLeadAttribueDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>$strWhereClauseArr, 'ignoreDelete'=>array($this->_strPrimaryTableName=>$this->_strPrimaryTableName)));
		
		/* if widget attribute already exists then do needful */
		if(!empty($strLeadAttribueDataArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Widget Attribute is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'widget_code'=>$intWidgetCode,
												'attri_slug_key'=>$strWidgetAttributeKey,
												'attri_slug_name'=>$setWidgetAttributName,
												'attri_slug_helps'=>$strWidgetAttributHelp,
												'attri_data_type'=>$strAttributeTypeCode,
												'file_driver' => $strAttributeFileTypeDriver,
												'attri_default_value' => $strAttributeFilePath,
												'attri_value_list'=>serialize($strAttributeListArr),
												'is_mandatory'=>$isMandatory,
												'attri_validation'=>$strValidationCode,
												'company_code'=>$this->getCompanyCode()
											)
								);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intWidgetAttributeCode);
				
				/* Get existing column details */
				$strExistingColumnArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('id'=>$intWidgetAttributeCode)));
				
				/* Updating lead details in the database */
				$intOperationStatus = $updateAffectedRows = $this->_objDataOperation->setUpdateData($strDataArr);

				/* if parent / master record updated then do needful 
				if (($updateAffectedRows) && (!empty($strAttributeListArr))) {
					/* Deactivate all previous options 
					$strDataArr 	 	 	= 	array(
														'table' 	=> 	'master_widget_attributes_list',
														'data' 		=> 	array('deleted' => 1),
														'where' 	=> 	array('attribute_code' => $intWidgetAttributeCode),
													);
					/* Deactivating older listing 
					$intOperationStatus = $this->_objDataOperation->setUpdateData($strDataArr);
				}*/
			}else{
				/* Adding widget attributes in the database */
				$intOperationStatus = $intWidgetAttributeCode = $attributeCode = $this->_objDataOperation->setDataInTable($strDataArr);
			}
			
			/* if Attributes contains the options list then do needful */
			if (!empty($strAttributeListArr)) {
				/* Get existing multiple values for respactive attribute details */
				$strExistingValueArr	= $this->_objDataOperation->getDataFromTable(array('table'=>'master_widget_attributes_list', 'column'=>array('id','description'), 'where'=>array('attribute_code'=>$intWidgetAttributeCode)));
				$strFinalValues			= array();
				
				/* if attribute list is found the do needful */
				if(!empty($strExistingValueArr)){
					/* iterating the loop */
					foreach($strExistingValueArr as $strExistingValueArrKey => $strExistingValueArrValues){
						/* setting the key value paires for widift attribute value */
						$strFinalValues[$strExistingValueArrValues['description']] = $strExistingValueArrValues['description'];
					}
				}
				/* removed used variable */
				unset($strExistingValueArr);
				
				/* iterating the attribute options list */
					foreach($strAttributeListArr as $strAttributeListArrKey => $strAttributeListArrValue){
						if(!isset($strFinalValues[$strAttributeListArrValue])){
								/* Setting the attributes list options */
								$strDataArr = 	array(
														'table' => 	'master_widget_attributes_list',
														'data' 	=> 	array(
																			'description' 		=> 	$strAttributeListArrValue,
																			'attribute_code' 	=> 	$intWidgetAttributeCode,
																		),
														);
							/* Adding attributes options */
							$this->_objDataOperation->setDataInTable($strDataArr);
							/* removed the current index */
							unset($strFinalValues[$strAttributeListArrValue]);
						}
					}
					
					/* deactive teh deleted attribute list */
					if (!empty($strFinalValues)) {
						/* Deactivate all deleted options */
						$strDataArr 	 	 	= 	array(
														'table' 	=> 	'master_widget_attributes_list',
														'data' 		=> 	array('deleted' => 1),
														'where' 	=> 	array('id' => array_keys($strFinalValues)),
													);
						/* Deactivating older listing */
						$intOperationStatus = $this->_objDataOperation->setUpdateData($strDataArr);
					}
			}
			
			/* Removed used variables */
			unset($strDataArr);
			
			/* Checking for column existence */
			$this->_setWidgetTranscationSchema($strWidgetAttributeKey, $strExistingColumnArr);
				
			/* checking last insert id / updated record count */
			if($intOperationStatus > 0){
				/* Setting the widget data importing template */
				$this->_setImportDataTemplate($intWidgetCode);
				/* Creating widget object */
				$widgetObj 			= new Widget($this->_objDataOperation, $this->getCompanyCode());
				/* Getting module information */
				$moduleDetailsArr 	= $widgetObj->getWidgetDetailsByWidgetCode($intWidgetCode, 1);
				
				/* Checking for modules details found */
				if (!empty($moduleDetailsArr) && !empty($moduleDetailsArr[0]['is_backup'] = 1)) {
					/* If backup it set then do needful */
					$createTriggerStatus = $widgetObj->setBackUpTableTrigger($moduleDetailsArr[0]['widget_slug']);
				}
				/* removed used variables */
				unset($widgetObj, $moduleDetailsArr);
				
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Widget Attribute Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Widget Attribute Added successfully.'), true);
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
		$intWidgetAttributeCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;

		/* if not widget attribute code pass then do needful */
		if($intWidgetAttributeCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid widget attribute code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intWidgetAttributeCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested Widget Attribute deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Updating widget transaction table.
	/*Inputs	: $pStrColumnName :: Column name,
				: $pStrExistingColumnArr :: Already existing column details.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setWidgetTranscationSchema($pStrColumnName = '', $pStrExistingColumnArr = array()){	
		/* variable initialization */
		$strTableName	= $this->_getWidgetDetails('schema_slug');
		
		/* Checking table exists */
		if($this->_objDataOperation->isTableExists($strTableName)){
			/* loading Database Forge Module */
			$this->load->dbforge();
			/* checking column exists */
			if((empty($pStrExistingColumnArr)) && (!$this->_objDataOperation->isFiledExists($strTableName, $pStrColumnName))){
				/* Setting the add data column array */
				$strFieldArr	= array(
											$pStrColumnName =>array(
																		'type'=>'VARCHAR',
																		'constraint'=>255,
																		'null'=>true,
																)
									);
				/* Add the column in target schema */
				$this->dbforge->add_column($strTableName, $strFieldArr);
				/* Update the logger table */
				$this->_setBackupSchema($strTableName, $pStrColumnName, $strFieldArr, true);

			}else if(!empty($pStrExistingColumnArr)){
				/* Setting the existing column name */
				$strExistingColumnName = isset($pStrExistingColumnArr[0]['attri_slug_key'])?$pStrExistingColumnArr[0]['attri_slug_key']:$pStrColumnName;
				
				/* Setting the update data column array */
				$strFieldArr	= array(
											$strExistingColumnName =>array(
																		'name'=>$pStrColumnName,
																		'type'=>'VARCHAR',
																		'constraint'=>255,
																		'null'=>true,
																)
									);
				/* Update the column in target schema */
				$this->dbforge->modify_column($strTableName, $strFieldArr);
				/* Update the logger table */
				$this->_setBackupSchema($strTableName, $pStrColumnName, $strFieldArr);
			}
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Updating the backup schema structure.
	/*Inputs	: $pStrTableName :: Table name,
				: $pStrNewColumnName :: Update Column name,
				: $pStrColumnArr :: Column array,
				: $pBlnIsUpdate :: operation type (TRUE :: MODIFY // FALSE :: ADD)
	/*Returns 	: Operation Status.
	/*Created By: Vipin Kumar R. Jaiswar.
	/**********************************************************************/
	private function _setBackupSchema($pStrTableName = '', $pStrNewColumnName='', $pStrColumnArr = array(), $pBlnIsUpdate = false){
		/* Data validation */
		if(($pStrTableName == '') || (empty($pStrColumnArr)) || ($pStrNewColumnName == '')){
			/* return the operation status */
			return false;
		}
		/* loading Database Forge Module */
		$this->load->dbforge();
		
		/* Variable initialization */
		$strTableName 			= 	$pStrTableName.'_log';		 
		/* Checking table existence */
		if($this->_objDataOperation->isTableExists($strTableName)){
			/* Checking field exist or not */
			if(!$this->_objDataOperation->isFiledExists($strTableName, $pStrNewColumnName)){
				/* Checking for operation flag */
				if($pBlnIsUpdate){
					/* Adding column in log table */
					$this->dbforge->add_column($strTableName, $pStrColumnArr);
				}else{
					/* Alter the column in the log table */
					$this->dbforge->modify_column($strTableName, $pStrColumnArr);
				}
			}
		}else{
			/* Creating widget object */
			$widgetObj 			= new Widget($this->_objDataOperation, $this->getCompanyCode());
			/* creating backup schema */
			$widgetObj->setBackUpTableSchema($pStrTableName);
			/* Removed used variables */
			unset($widgetObj);
		}
		/* return the operation status */
		return true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Creating the data importing template .
	/*Inputs	: $pIntWidgetCode :: Widget code.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setImportDataTemplate($pIntWidgetCode = 0){
		/* Variable initialization */
		$strFilterArr	= array();
		/* if widget code is not passed then do needful */
		if($pIntWidgetCode == 0){
			/* do not process ahead */
			return false;
		}
		
		/* Data filter initialization */
		$strFilterArr			= array(
											'table'=>array($this->_strPrimaryTableName,'master_widget'),
											'join'=>array('',$this->_strPrimaryTableName.'.widget_code = master_widget.id'),
											'where'=>array('widget_code'=>$pIntWidgetCode),
											'column'=>array('attri_slug_key','attri_slug_name','master_widget.description')
										);
		/* get widget attributes details */
		$strWidgetDetailsArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if attributes found then do needful */
		if(!empty($strWidgetDetailsArr)){
			/* Variable initialization */
			$strColumnArr	= $strDataArr = array();
			$strWidgetName	= '';
			
			/* iterating the loop */
			foreach($strWidgetDetailsArr as $strWidgetDetailsArrKey => $strWidgetDetailsArrValue){
				/* set column index */
				$strWidgetDetailsArr[$strWidgetDetailsArrValue['attri_slug_key']]	= $strWidgetDetailsArrValue;
				/* Set widget name */
				$strWidgetName														= $strWidgetDetailsArrValue['description'];
				/* setting data array */
				$strDataArr[$strWidgetDetailsArrValue['attri_slug_key']]			= $strWidgetDetailsArrValue;
				/* removed index */
				unset($strWidgetDetailsArr[$strWidgetDetailsArrKey]);
			}
			
			/* Creating file operation object */
			$fileOperation = new Files($this->_objDataOperation, $this->getCompanyCode());
			/* Creating the template file */
			/*
			$fileOperation->exportData($strWidgetDetailsArr, $strDataArr, $strWidgetName.'-'.$this->getCompanyCode().'.xls',BASE_PATH.'uploads'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR);
			*/
			$fileOperation->exportData($strWidgetDetailsArr, $strDataArr, $strWidgetName.'-'.$this->getCompanyCode().'.csv',BASE_PATH.'uploads'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR);
			/* removed used variables */
			unset($fileOperation);
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Get widget details.
	/*Inputs	: $pColumnName :: Needed column name.
	/*Returns 	: Widget Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getWidgetDetails($pColumnName = 'description'){	
		/* variable initialization */
		$strWidgetDetailsArr	= array();
		$strWidgetName			= '';
		$intWidgetCode			= (isset($_REQUEST['wIdGetCoDe']) && ($_REQUEST['wIdGetCoDe'] != ''))? getDecyptionValue($_REQUEST['wIdGetCoDe']):0;
		
		/* if widget code is not set then do needful */
		if($intWidgetCode == 0){
			/* redirecting to parent page */
			redirect(SITE_URL.'/settings/widgets');
		}
		
		/* Data filter initialization */
		$strFilterArr			= array('table'=>'master_widget','where'=>array('id'=>$intWidgetCode));
		/* get widget details */
		$strWidgetDetailsArr	= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* if widget details are empty then do needful */
		if(!empty($strWidgetDetailsArr)){
			/* setting value */
			$strWidgetName	= (isset($strWidgetDetailsArr[0][$pColumnName])?$strWidgetDetailsArr[0][$pColumnName]:'');
		}
		
		/* Removed used variables */
		unset($strWidgetDetailsArr, $strFilterArr, $intWidgetCode);
		
		/* return the widget name */
		return $strWidgetName;
	}
}