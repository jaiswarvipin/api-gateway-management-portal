<?php
/***********************************************************************/
/* Purpose 		: Application module management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Modules extends Requestprocess {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_modues';
	private $_strModuleName			= "Modules";
	private $_strModuleForm			= "frmModules";
	
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
		
		/* Getting modules list */
		$strUserRoleArr['dataSet'] 				= $this->_getModulesDetails(0,'',false,false, $intCurrentPageNumber);
		$strUserRoleArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strUserRoleArr['pagination'] 			= getPagniation($this->_getModulesDetails(0,'',false,true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strUserRoleArr['moduleTitle']			= $this->_strModuleName;
		$strUserRoleArr['moduleForm']			= $this->_strModuleForm;
		$strUserRoleArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$strUserRoleArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$strUserRoleArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getModuesDetailsByCode';
		$strUserRoleArr['strDataAddEditPanel']	= 'moduleModel';
		$strUserRoleArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strUserRoleArr['strModuleArr']			= getArrByKeyvaluePairs($this->_getModulesDetails(-1,''),'id','description');			
		$strUserRoleArr['strParentMenu']		= $this->_objForm->getDropDown($strUserRoleArr['strModuleArr'],'');
		$strUserRoleArr['strWidgetArr']			= $this->_objForm->getDropDown(getArrByKeyvaluePairs($this->_getWidgetList(),'id','description'),'');
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('settings/modules', $strUserRoleArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Get module details by code.
	/*Inputs	: None.
	/*Returns 	: Module details Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getModuesDetailsByCode(){
		/* Setting the module code */
		$intModuleCode 		= ($this->input->post('txtCode') != '') ? getDecyptionValue($this->input->post('txtCode')) : 0;
		$intModuleCodeFoAttr= ($this->input->post('txtModuleFieldCode') != '') ? getDecyptionValue($this->input->post('txtModuleFieldCode')) : 0;
		$strModulesArr		= array();

		if($intModuleCodeFoAttr > 0){
			/* getting requested module field code details */
			$strModulesArr	= $this->_getAssociatedWidgetDetails($intModuleCodeFoAttr);

			/* if record not found then do needful */
			if(empty($strModulesArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strModulesArr, true);
			}
			/* Checking the module code shared */
		}else if($intModuleCode > 0){
			/* getting requested module code details */
			$strModulesArr	= $this->_getModulesDetails($intModuleCode);

			/* if record not found then do needful */
			if(empty($strModulesArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* Return the JSON string */
				jsonReturn($strModulesArr[0], true);
			}
			
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid module code requested.'), true);
		}
	}

	/**********************************************************************/
	/*Purpose 	: Getting the module details.
	/*Inputs	: $pModuleCode :: Module code,
				: $pStrModuleName :: Module Name,
				: $isEditRequest :: Edit request,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Lead attribute details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getModulesDetails($pModuleCode = 0, $pStrModuleName = '', $isEditRequest = false, $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strUserRoleArr	= $strWhereClauseArr 	= array();
		
		/* Setting page number */
		$intCurrentPageNumber	= $pBlnPagination;
		if($intCurrentPageNumber < 0){
			$intCurrentPageNumber = 0;
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array('company_code'=>array($this->getCompanyCode()),'is_system'=>0);
		
		/* if module filter code is passed then do needful */
		if($pModuleCode < 0){
			/* Adding modules code filter */
			$strWhereClauseArr	= array('parent_code'=>0);
		/* if profile filter code is passed then do needful */
		}else if(($this->input->post('txtSearch')) && ($this->input->post('txtSearch') == '1')){
			/* if search request then do needful */
			$strModuleName			= ($this->input->post('txtModuleName') != '')?$this->input->post('txtModuleName'):'';
			$intParentModuleCode	= ($this->input->post('cboParentModuleCode') != '')?getDecyptionValue($this->input->post('cboParentModuleCode')):0;
			
			if($strModuleName != ''){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('description like'=>$strModuleName));
			}
			if($intParentModuleCode > 0){
				$strWhereClauseArr	= array_merge($strWhereClauseArr, array('parent_code'=>$intParentModuleCode));
			}
		}else{
			/* Getting module categories */
			if($pModuleCode > 0){
				/* iF edit request then do needful */
				if($isEditRequest){
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$pModuleCode));
				}else{
					/* Adding Status code filter */
					$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id'=>$pModuleCode));
				}
			}
		}
		
		/* filter by module name and parent code */
		if($pStrModuleName !=''){
			/* Adding module description and parent code as filter */
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
		$strModuleArr	=  $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* Getting status categories */
		if($pModuleCode > 0){
			$strModuleArr[0]['parent_code']	= getEncyptionValue($strModuleArr[0]['parent_code']);
		}
		
		/* Removed used variables */
		unset($strFilterArr);

		/* return status */
		return $strModuleArr;
	}

	/**********************************************************************/
	/*Purpose 	: Setting lead attribute details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setModuesDetails(){
		/* variable initialization */
		$intModuleCode		= ($this->input->post('txtModuleCode') != '')? $this->input->post('txtModuleCode'):0;
		$strModuleName		= ($this->input->post('txtModuleName') != '')?$this->input->post('txtModuleName'):'';
		$strModuleURL		= ($strModuleName != '')? getSlugify($strModuleName):'';
		$intParentCode		= ($this->input->post('cboParentModuleCode') != '')?getDecyptionValue($this->input->post('cboParentModuleCode')):0;
		$blnEditRequest		= (($intModuleCode > 0)?true:false);
		$blnSearch			= ($this->input->post('txtSearch') != '')?true:false;
		$blnBackUpData 		= ($this->input->post('rdoBackUpData') == '1') ? '1' : '0';
		$blnVisibleMenu 	= ($this->input->post('is_visiable') == '1') ? '1' : '0';

		$strWhereClauseArr	= array();
		
		if($blnSearch){
			$this->index();
			exit;
		}

		/* Checking to all valid information passed */
		if(($strModuleName == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Module description field is empty.'), true);
		}else if(($strModuleURL == '')){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Module URL field is empty.'), true);
		}
		
		/* Adding module description filter */
		$strWhereClauseArr	= array('description'=>$strModuleName);
			
		/* Checking for edit request */
		if($blnEditRequest){
			/* Adding module code filter */
			$strWhereClauseArr	= array_merge($strWhereClauseArr, array('id !='=>$intModuleCode));
		}
		
		/* Checking enter module description is already register or not */
		$strLeadAttribueDataArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>$strWhereClauseArr));
		
		/* if module already exists then do needful */
		if(!empty($strLeadAttribueDataArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Requested Module is already exists.'), true);	
		}else{
			/* Data Container */
			$strDataArr		= array(
										'table'=>$this->_strPrimaryTableName,
										'data'=>array(
													'description'=>$strModuleName,
													'module_url'=>$strModuleURL,
													'parent_code'=>$intParentCode,
													'company_code'=>$this->getCompanyCode(),
													'is_backup'=>$blnBackUpData,
													'is_visiable'=>$blnVisibleMenu,
												)
									);
			
			/* Checking for edit request */
			if($blnEditRequest){
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intModuleCode);
				/* Updating lead details in the database */
				$this->_objDataOperation->setUpdateData($strDataArr);
			}else{
				/* Adding lead details in the database */
				$intModuleCode = $this->_objDataOperation->setDataInTable($strDataArr);				
				/* Variable initialization */
				$strClassFileName	= getSlugify(ucfirst(str_replace("-","",$strModuleURL))); 
				/* Creating file helper object */
				$filesObj			= new Files($this->_objDataOperation, $this->getCompanyCode());
				/* Copying the folder structure of the module template */
				$filesObj->recursiveCopy(APPPATH.DIRECTORY_SEPARATOR.'template' , APPPATH.'modules'.DIRECTORY_SEPARATOR.$strClassFileName);
				/* Creating module controller with template */
				$filesObj->setModuleClassFile($strClassFileName, APPPATH.'modules'.DIRECTORY_SEPARATOR.$strClassFileName);
				/* Removed used variables */
				unset($filesObj);
			}
			/* Removed used variables */
			unset($strDataArr);
			
			/* checking last insert id / updated record count */
			if($intModuleCode > 0){
				/* Creating the data filter query array */
				$strFilterArr = array(
										'table'=>array('master_widget','mater_module_widget_attribute'),
										'join'=>array('','master_widget.id = mater_module_widget_attribute.widget_code'),
										'column'=>array('widget_code','schema_slug'),
										'where'=>array('module_code'=>$intModuleCode),
										'limit'=>1,
										'offset'=>0
								);

				/* Begin, Get All Table of related to this module */
				$strWidgetArr 		=  $this->_objDataOperation->getDataFromTable($strFilterArr);
				
				/* Creating widget object */
				$widgetObj 	= 	new Widget($this->_objDataOperation, $this->getCompanyCode());
				
				/* module records found then do needful */
				if((!empty($strWidgetArr)) && ($strWidgetArr[0]['schema_slug']!='')){
					/* if data backup request set then do needful */
					if($blnBackUpData == '1'){
						/* Creating the backup table */
						$createTableStatus 		= $widgetObj->setBackUpTableSchema($strWidgetArr[0]['schema_slug']);
						/* Creating the trigger */
						$createTriggerStatus 	= $widgetObj->setBackUpTableTrigger($strWidgetArr[0]['schema_slug']);
					}else{
						/* Dropping the trigger */
						$dropTriggerStatus = $widgetObj->dropBackUpTableTrigger($strWidgetArr[0]['schema_slug']);
					}
				}
				/* removed used variables */
				unset($strWidgetArr, $widgetObj);
				
				/* Checking for edit request */
				if($blnEditRequest){
					jsonReturn(array('status'=>1,'message'=>'Module details Updated successfully.'), true);
				}else{
					jsonReturn(array('status'=>1,'message'=>'Module added successfully.'), true);
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
		$intModuleCode 	= ($this->input->post('txtDeleteRecordCode') !='') ? getDecyptionValue($this->input->post('txtDeleteRecordCode')) : 0;

		/* if module code is not pass then do needful */
		if($intModuleCode == 0){
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=>"Invalid module code requested."), true);
		}
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strPrimaryTableName,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intModuleCode
											)

								);
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested module deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}
		
		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get widget List.
	/*Inputs	: None.
	/*Returns 	: Widget list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getWidgetList(){
		/* get widget details */
		return $this->_objDataOperation->getDataFromTable(array('table'=>'master_widget','where'=>array('company_code'=>$this->getCompanyCode())));
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Get associated widget List by selected module code.
	/*Inputs	: $pIntModuleCode :: module code.
	/*Returns 	: Widget Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getAssociatedWidgetDetails($pIntModuleCode = 0){
		/* Checking for module code */
		if($pIntModuleCode == 0){
			jsonReturn(array('status'=>0,'message'=>'Invalid Module code.'), true);
		}
		
		/* get associated widget details */
		$strWidgetArr =  $this->_objDataOperation->getDataFromTable(array('table'=>'mater_module_widget_attribute','column'=>array('widget_code'),'where'=>array('module_code'=>$pIntModuleCode),'limit'=>1,'offset'=>0));
		
		/* if widget code found then do needful */
		if(!empty($strWidgetArr)){
			/* set value */
			$strWidgetArr[0]['widget_code']	= array('widget_code'=>getEncyptionValue($strWidgetArr[0]['widget_code']));
		}
		
		/* return widget value */
		return $strWidgetArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get widget attributes List by widget code.
	/*Inputs	: None.
	/*Returns 	: Widget attributes code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getWidgetAttributesList(){
		/* Variable initialization */
		$intWidgetCode			= ($this->input->post('txtElementCode') != '') ? (getDecyptionValue($this->input->post('txtElementCode'))) : 0;
		$intModuleCode			= ($this->input->post('txtModuleFieldCode') != '') ? (getDecyptionValue($this->input->post('txtModuleFieldCode'))) : 0;
		$strModuleAttributesArr	= array();
		/* if widget code is not passed then do needful */
		if((int)$intWidgetCode <=0){
			jsonReturn(array('status'=>0,'message'=>'Invalid widget code.'), true);
		}
		/* if module code is not passed then do needful */
		if((int)$intModuleCode <=0){
			jsonReturn(array('status'=>0,'message'=>'Invalid module code.'), true);
		}
		
		/* get widget attributes list details */
		$strWidgetAttArr	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_widget_attributes','column'=>array('id','attri_slug_name'),'where'=>array('widget_code'=>$intWidgetCode), 'order'=>array('view_sequence'=>'ASC')));
		/* get configured  widget attributes list of selected modules details */
		$strConfAttArr		= $this->_objDataOperation->getDataFromTable(array('table'=>'mater_module_widget_attribute','column'=>array('id','attri_code'),'where'=>array('module_code'=>$intModuleCode,'widget_code'=>$intWidgetCode)));
		
		/* if configured modules found then do needful */
		if(!empty($strConfAttArr)){
			/* iterating the loop */
			foreach($strConfAttArr as $strConfAttArrKey => $strConfAttArrValue){
				/* Setting the widget module configuration array */
				$strModuleAttributesArr[$strConfAttArrValue['attri_code']]	= $strConfAttArrValue['id'];
			}
		}
		
		unset($strConfAttArr);
		
		/* Generate the widget attribute UI */
		return jsonReturn(array('status'=>1,'message'=>$this->load->view('settings/module-widget-attributes', array('strWidgetAttArr'=>$strWidgetAttArr, 'strModuleAttributesArr'=>$strModuleAttributesArr), true)),true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting module fields.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setModuesFieldsDetails(){
		/* variable initialization */
		$intModuleCode	= ($this->input->post('txtModuleFieldCode'))? getDecyptionValue($this->input->post('txtModuleFieldCode')):0;
		$strAttArr		= ($this->input->post('txtWidgetAttributesCode'))?$this->input->post('txtWidgetAttributesCode'):array();
		$intWidgetCode	= ($this->input->post('cboLeadAttributeCode'))?getDecyptionValue($this->input->post('cboLeadAttributeCode')):0;
		
		/* Validation checking */
		if($intModuleCode == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Invalid module code.'), true);
		}
		if($intWidgetCode == 0){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Invalid widget code.'), true);
		}
		if(empty($strAttArr)){
			/* Return Information */
			jsonReturn(array('status'=>0,'message'=>'Module field(s) is are not selected.'), true);
		}
		
		/* Data Container */
		$strDataArr		= array(
									'table'=>'mater_module_widget_attribute',
									'where'=>array('module_code'=>$intModuleCode),
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											)
								);
								
		/* Updating module field details in the database */
		$this->_objDataOperation->setUpdateData($strDataArr);
		
		/* Iterating the loop */		
		foreach($strAttArr as $strAttArrKey => $strAttArrValue){	
			/* Data Container */
			$strDataArr		= array(
										'table'=>'mater_module_widget_attribute',
										'data'=>array(
													'module_code'=>$intModuleCode,
													'widget_code'=>$intWidgetCode,
													'attri_code'=>getDecyptionValue($strAttArrValue),
												)
									);
                        
                        $strDataUpdateArr       = array(
                                                            'table'=>'master_widget_attributes',
                                                            'where'=>array('id'=>getDecyptionValue($strAttArrValue)),
                                                            'data'=>array(
                                                                        'view_sequence'=>(int)$strAttArrKey+1
                                                                    )
                            
                                                        );
			
			/* Adding module field in the database */
			$this->_objDataOperation->setDataInTable($strDataArr);
                        
                        /* Updating master_widget_attributes view_sequence field details in the database */
                        $this->_objDataOperation->setUpdateData($strDataUpdateArr);
		}
		
		/* Removed used variables */
		unset($strDataArr);
		/* Return information */
		jsonReturn(array('status'=>1,'message'=>'Module field associated successfully.'), true);
	}

}