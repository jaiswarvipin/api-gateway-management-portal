<?php
/***********************************************************************/
/* Purpose 		: Application Widget List management.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_widgets extends Requestprocess {

	/* variable deceleration */
	private $_strPrimaryTableName				= 'master_widget';
	private $_strModuleName						= "Widgets";
	private $_strWidgetName						= "";
	private $_strModuleForm						= "frmWidget";
	private $_strModuleSlug						= "";
	private $_strWidgeSlug						= "";
	private $_strQueryString					= "";
	private $_strWidgetAttributes				= array();
	private $_strCustomQueryArr					= array();
	private $_strWidgetAllAttributes			= array();
	private $_strConfiguredWidgetAttributesArr	= array();
	private $_strWidgetCode						= array();
	private $_strGetVarArr						= array();
	private $_strCustomOptionListArr			= array();
	private $_strMultiValueContinerArr 			= array('checkbox','radio','select','dropdown');
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		
		/* checking for schema existence */
		$this->_objDataOperation->isTableExists($this->_strPrimaryTableName);
		
		/* Set module / widget environment variables */
		$this->_setWidgetOperationEnv();		 
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting the requested module environment variables
	/*Inputs	: None.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setWidgetOperationEnv(){
		/* Variable initialization */
		$strSegmentArr 			= 	$this->uri->segment_array();
		
		/* if AJAX request then do needful */
		if(isAjaxRequest()){
			/* if custom hook method request in place then do needful */
			if(count($strSegmentArr) >=4){
				/* Setting module slug on custom hook module request */
				$this->_strModuleSlug 	= 	array_values(array_slice($strSegmentArr, -3))[0];
			}else{
				/* Setting module slug on custom module request */
				$this->_strModuleSlug 	= 	array_values(array_slice($strSegmentArr, -2))[0];
			}
		}else{
			/* Setting module slug */
			$this->_strModuleSlug 	= 	array_values(array_slice($strSegmentArr, -1))[0];
		}
		
		/* checking for get variables */
		$this->_strGetVarArr	= $this->input->get();
		
		/* if get variable arrays is not empty then do needful */
		if(!empty($this->_strGetVarArr)){
			/* Creating the query string */
			$this->_strQueryString = '?'.http_build_query($this->_strGetVarArr);
		}
		
		/* Get widget attributes list by module array */
		$this->_strWidgetAttributes	= $this->getModuleAssociatedFieldByModuleURL($this->_strModuleSlug);
		
		/* if widget details are not found then do needful */
		if (empty($this->_strWidgetAttributes)) {
			/* Removed all valid cookies */
			$this->doDistryLoginCookie();
			/* Redirect to the login */
			redirect(SITE_URL, 'refresh');
			/* Stop the execution */
			return false;
		}
		
		/* iterating the configured widget column loop */
		foreach($this->_strWidgetAttributes as $strWidgetAttributesKey => $strWidgetAttributesValue){
			$this->_strWidgetCode = $strWidgetAttributesValue['widget_code'];
			/* Setting the value */
			$this->_strConfiguredWidgetAttributesArr[$strWidgetAttributesValue['widget_slug'].'.'.$strWidgetAttributesValue['attri_slug_key']]	= $strWidgetAttributesValue['attri_slug_name'];
			/* Setting the new index */
			$this->_strWidgetAttributes[$strWidgetAttributesValue['attri_slug_key']]				= $strWidgetAttributesValue;
			/* Setting widget details */
			$this->_strWidgeSlug																	= $strWidgetAttributesValue['widget_slug'];
			/* Setting the module name */
			$this->_strModuleName																	= $strWidgetAttributesValue['module_name'];
			/* Setting the widget name */
			$this->_strWidgetName																	= $strWidgetAttributesValue['widget_name'];
			/* Removed existing index */
			unset($this->_strWidgetAttributes[$strWidgetAttributesKey]);
		}
		
		/* Setting the operation index */
		$this->_strConfiguredWidgetAttributesArr[$this->_strWidgeSlug.'.id']					= $this->_strWidgeSlug.'.id';
		
		/* get all widget attributes by widget code */
		$this->_strWidgetAllAttributes 									= $this->getModuleAssociatedFieldByModuleURL('', $this->_strWidgetCode); 
		
		/* Processing the data set with hook of same module custom logic */
		$strDataArr 													= $this->_getMouleHookProcessData(array('table'=>$this->_strWidgeSlug), 'setConfiguration');
		
		/* if custom configuration found then do needful */
		if((!empty($strDataArr)) && (isset($strDataArr['view'])) && (!empty($strDataArr['view'])) ){
			/* Merging the dataset */
			$this->_strWidgetAllAttributes	= array_merge($this->_strWidgetAllAttributes, $strDataArr['view']);
			/* iterating the loop */
			foreach($strDataArr['view'] as $strDataArrKey => $strDataArrValue){
				if(isset($strDataArrValue['show_in_list'])){
					/* Setting the data set */
					$this->_strConfiguredWidgetAttributesArr[$this->_strWidgeSlug.'.'.$strDataArrValue['attri_slug_key']]	= $strDataArrValue['attri_slug_name'];
				}
			}
		}
		
		/* if exiting attributes updated values change then do needful */
		if((!empty($strDataArr)) && (isset($strDataArr['exiting_attr'])) && (!empty($strDataArr['exiting_attr'])) ){
			/* iterating the loop */
			foreach($strDataArr['exiting_attr'] as $strDataArrKey => $strDataArrValue){
				if(isset($this->_strWidgetAllAttributes[$strDataArrKey])){
					/* Iterating the exiting value update */
					foreach($strDataArrValue as $strDataArrKeyValue => $strDataArrKeyValueArr){
						/* Checking is attribute exists */
						if(isset($this->_strWidgetAllAttributes[$strDataArrKey][$strDataArrKeyValue])){
							/* attrinute found, update it */
							$this->_strWidgetAllAttributes[$strDataArrKey][$strDataArrKeyValue]	= $strDataArrKeyValueArr;
						}
					}
				}
			}
		}
		
		/* if custom option list found then do needful */
		if((!empty($strDataArr)) && (isset($strDataArr['options_list'])) && (!empty($strDataArr['options_list'])) ){
			/* set the values */
			$this->_strCustomOptionListArr	= $strDataArr['options_list'];
		}
		
		/* if custom alise configuration found then do needful */
		if((!empty($strDataArr)) && (isset($strDataArr['alise'])) && (!empty($strDataArr['alise'])) ){
			/* iterating the loop */
			foreach($strDataArr['alise'] as $strDataArrKey => $strDataArrValue){
				if(isset($this->_strConfiguredWidgetAttributesArr[$this->_strWidgeSlug.'.'.$strDataArrKey])){
					/* Setting the data set */
					$this->_strConfiguredWidgetAttributesArr[$this->_strWidgeSlug.'.'.$strDataArrKey]						= $strDataArrValue;
					$this->_strWidgetAllAttributes[$strDataArrKey]['attri_slug_name']										= $strDataArrValue;
				}
			}
		}
		
		/* if custom query configuration found then do needful */
		if((!empty($strDataArr)) && (isset($strDataArr['customQuery'])) && (!empty($strDataArr['customQuery'])) ){
			/* Merging the dataset */
			$this->_strCustomQueryArr	= $strDataArr['customQuery'];
		}
		
		/* removed user variables */
		unset($strDataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Returns	: Widget Layout with HTML.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* Variable initialization */
		$strDataArr				=  $dataArr = array();
		$blnIsCSVDownlodRequest = ($this->input->post('blnCSV') != '') ? $this->input->post('blnCSV') : 0;
		
		/* Creating widget object */
		$widgetObj			= new Widget($this->_objDataOperation, $this->getCompanyCode());
		/* Get module HTML and attributes options list */
		$strModuleHTMLArr	= $widgetObj->getWidgetAttributesWithLayout($this->_strWidgetAllAttributes, true, $this->_strModuleForm,SITE_URL.'mod/'.$this->_strModuleSlug.'/setModuleData'.$this->_strQueryString,true);
		/* removed used variables */
		unset($widgetObj);
		
		/* if custom Option list array is empty then do needful */
		if((!empty($strModuleHTMLArr)) && (!empty($this->_strCustomOptionListArr))){
			/* merge the deaful and custom option list array */
			$strModuleHTMLArr['options_list']				= array_merge($strModuleHTMLArr['options_list'],$this->_strCustomOptionListArr);
		}
		
		/* Setting the custom configured column for filter */
		$strWidgetData 						= ($this->input->post('widgetData') != '') ? $this->input->post('widgetData') : array();
		$intWidgetCode						= isset($this->_strWidgetAttributes[0]['widget_code'])?$this->_strWidgetAttributes[0]['widget_code']:0;
		$strCustomFilterArr					= $this->input->get();
		
		/* Checking for custom filter */
		if(!empty($strCustomFilterArr)){
			/* merging with widget data */
			$strWidgetData	= array_merge($strWidgetData, $strCustomFilterArr);
		}
		
		/* if data download request then do needful */
		if($blnIsCSVDownlodRequest){
			/* get data for export */
			$strReturnDataArr = $this->_getWidgetData(array(), $strWidgetData, false, -5);
			/* Creating file object */
			$fileObj	= new Files($this->_objDataOperation, $this->getCompanyCode());
			/* get exported file name */
			/*
			$strFileName= $fileObj->exportData($this->_strWidgetAllAttributes, $strReturnDataArr, $this->_strModuleName.'-'.date('YmdHis').'-'.$this->getCompanyCode().'-'.$this->getUserCode().'.xls','uploads'.DIRECTORY_SEPARATOR.'download'.DIRECTORY_SEPARATOR);
			*/
			$strFileName= $fileObj->exportData($this->_strWidgetAllAttributes, $strReturnDataArr, $this->_strModuleName.'-'.date('YmdHis').'-'.$this->getCompanyCode().'-'.$this->getUserCode().'.csv','uploads'.DIRECTORY_SEPARATOR.'download'.DIRECTORY_SEPARATOR);
			/* Removed used variable */
			unset($fileObj);

			/* if file not created the do needful */
			if($strFileName == ''){
				$strDataArr['exportStatus'] = 'No data found for export.';
			}else{
				$strDataArr['exportStatus'] = 'Data exported successfully. <a href="'.SITE_URL.$strFileName.'" target="blank">Click here</a> to download the file.';
			}
		}		
		
		/* Getting current page number */
		$intCurrentPageNumber				= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting widget access list */
		$strDataArr['dataColumnSet'] 		= $this->_strConfiguredWidgetAttributesArr;
		$strDataArr['moduleHTML'] 			= isset($strModuleHTMLArr['module_from'])?$strModuleHTMLArr['module_from']:'';
		$strDataArr['dataSet'] 				= $this->_setCustomValues($this->_getWidgetData(array_keys($this->_strConfiguredWidgetAttributesArr), $strWidgetData, false, $intCurrentPageNumber), $strModuleHTMLArr);
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation($this->_getWidgetData(array_keys($this->_strConfiguredWidgetAttributesArr), $strWidgetData, true), ($intCurrentPageNumber + 1), $this->_strModuleForm);
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['importTemplate']		= SITE_URL.'uploads'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->_strWidgetName.'-'.$this->getCompanyCode().'.xls';
		$strDataArr['moduleUri']			= SITE_URL.'mod/'.$this->_strModuleSlug.$this->_strQueryString;
		$strDataArr['deleteUri']			= SITE_URL.'mod/'.$this->_strModuleSlug.'/deleteRecord'.$this->_strQueryString;
		$strDataArr['getRecordByCodeUri']	= SITE_URL.'mod/'.$this->_strModuleSlug.'/getModuleDetailsByCode'.$this->_strQueryString;
		$strDataArr['importDataURL']		= SITE_URL.'mod/'.$this->_strModuleSlug.'/importData'.$this->_strQueryString;
		$strDataArr['strDataAddEditPanel']	= 'widgetModel';
		$strDataArr['schemaName']			= $this->_strWidgeSlug;
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strDataArr['widgetId'] 			= $intWidgetCode;
		$strDataArr['widgetAttributesArr'] 	= (!empty($this->_strWidgetAttributes)) ? $this->_strWidgetAttributes : array();
        
		/* Processing the data set with hook of same module custom logic */
		$strDataArr = $this->_getMouleHookProcessData($strDataArr, __FUNCTION__);
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('mod/index', $strDataArr, true);

		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}
        
    /**************************************************************************************************/
	/*Purpose       : Call the hook function and get the data after processing.
	/*Inputs        : $pStrDataArr :: The whole data from the calling function,
					: $strMethodName :: Name of the hook function to be called.
	/*Returns       : Widget Data after processing by the hook function.
	/*Created By    : Vipin Kumar R. Jaiswar.
	/**************************************************************************************************/
	private function _getMouleHookProcessData($pStrDataArr = array(), $strMethodName = ''){
		/* Initializing array variable to return the data processed from the Class file function */
        $strHookDataArr = $pStrDataArr;
		
		/* Variable initialization */
		$strClassFileName	= str_replace('-','',$this->_strModuleSlug);//getSlugify(str_replace("-","",$this->_strModuleSlug));
		$strClassName		= ucfirst($strClassFileName);
		
		/* check if the class file for the widget exists */
		if(file_exists(APPPATH.'modules'.DIRECTORY_SEPARATOR.$strClassFileName.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$strClassName.'.php')){
			
			/* If Class file exists, then include it and call the function of the Class */
			require_once(APPPATH.'modules'.DIRECTORY_SEPARATOR.$strClassFileName.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$strClassName.'.php');
			
			/* Initializing the Class file Object to call the function of the class file */
            $moduleHookClassObj = new $strClassName(array($strClassFileName, $this->_strWidgeSlug), $pStrDataArr);
            
			/* if method is exist then do needful  */
			if(method_exists($moduleHookClassObj, $strMethodName)){
				/* Calling the Class file function for hooking mechanism */
				$strHookDataArr = $moduleHookClassObj->$strMethodName(); 
			}
		 
			/* removed used variables */
			unset($moduleHookClassObj);
		}
		
		/* return the processed data */
        return $strHookDataArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get widget details details by code.
	/*Inputs	: None.
	/*Returns 	: Module Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getModuleDetailsByCode(){
		/* Setting the module code */
		$intPrimaryCode 			= 	($this->input->post('txtCode') != '') ? (is_numeric($this->input->post('txtCode'))?$this->input->post('txtCode'):getDecyptionValue($this->input->post('txtCode'))) : 0;
		$blnDetailsView 			= 	($this->input->post('txtDetailView') != '') ? true : false;
		$strWidgetTableDataArr		= array();
		$blnHavingMultiSelectElement= false;

		/* if primary code is empty then do needful */
		if($intPrimaryCode > 0){
			/* if any ajax request (UDPATE) the do needful */
			if(isAjaxRequest()){
				/* getting requested all widget attributes details filter by code */
				$strWidgetTableDataArr 	= 	$this->_getWidgetData(array(), array('id'=>$intPrimaryCode));
			}else{
				/* getting requested all configured widget attributes details filter by code */
				$strWidgetTableDataArr 	= 	$this->_getWidgetData(array_keys($this->_strConfiguredWidgetAttributesArr), array('id'=>$intPrimaryCode));
			}
			
			/* Iterating current module elements list */
			foreach($this->_strWidgetAllAttributes as $strWidgetAllAttributesKey => $strWidgetAllAttributesArr){
				/* Having multi select element */
				if(in_array($strWidgetAllAttributesArr['attri_data_type'],$this->_strMultiValueContinerArr)){
					/* Setting the value */
					$blnHavingMultiSelectElement	= true;
					/* Stop the iterating */
					break;
				}
			}
			
			/* Iterating the value array for setting the custom multiple values */
			foreach($strWidgetTableDataArr[0] as $strWidgetTableDataArrKey => $strWidgetTableDataArrValue){
				/* checking for date attributes */
				if((isset($this->_strWidgetAllAttributes[$strWidgetTableDataArrKey])) && ($this->_strWidgetAllAttributes[$strWidgetTableDataArrKey]['attri_data_type'] =='datetime')){
					/* set the date formatted value */
					$strWidgetTableDataArr[0][$strWidgetTableDataArrKey]	= getDateFormat($strWidgetTableDataArrValue,5);
				}
			}
			
			if($blnHavingMultiSelectElement){
				/* Creating widget object */
				$widgetObj 			= new Widget($this->_objDataOperation, $this->getCompanyCode());
				/* Get module HTML and attributes options list */
				$strModuleHTMLArr	= $widgetObj->getWidgetAttributesWithLayout($this->_strWidgetAllAttributes, true, $this->_strModuleForm,SITE_URL.'mod/'.$this->_strModuleSlug.'/setModuleData',true);
				
				/* if custom Option list array is empty then do needful */
				if((!empty($strModuleHTMLArr)) && (!empty($this->_strCustomOptionListArr))){
					/* merge the deaful and custom option list array */
					$strModuleHTMLArr['options_list']				= array_merge($strModuleHTMLArr['options_list'],$this->_strCustomOptionListArr);
				}
				
				/* if option value list is not empty then do needful */
				if((isset($strModuleHTMLArr['options_list'])) && (!empty($strModuleHTMLArr['options_list']))){
					/* Iterating the value array for setting the custom multiple values */
					foreach($strWidgetTableDataArr[0] as $strWidgetTableDataArrKey => $strWidgetTableDataArrValue){
						/* Checking is value exist */
						if((isset($strModuleHTMLArr['options_list'][$strWidgetTableDataArrKey])) && (isset($strModuleHTMLArr['options_list'][$strWidgetTableDataArrKey][getEncyptionValue($strWidgetTableDataArrValue)]))){
							/* Set option encrypted values */
							$strWidgetTableDataArr[0][$strWidgetTableDataArrKey]	= getEncyptionValue($strWidgetTableDataArrValue);
						}
					}
				}
			}
			
			/* removed used variables */
			unset($blnHavingMultiSelectElement);
		
			/* Processing the data set with hook of same module custom logic */
			$strWidgetTableDataArr	= $this->_getMouleHookProcessData($strWidgetTableDataArr, __FUNCTION__);
			
			/* if record not found then do needful */
			if(empty($strWidgetTableDataArr)){
				jsonReturn(array('status'=>0,'message'=>'Details not found.'), true);
			}else{
				/* if view request then do needful */
				if($blnDetailsView){
					/* iterating the loop */
					foreach($strWidgetTableDataArr[0] as $strWidgetTableDataArrKey => $strWidgetTableDataArrValue){
						/* if option value list is not empty then do needful */
						if(isset($strModuleHTMLArr['options_list'][$strWidgetTableDataArrKey])){
							/* set the value */
							$strWidgetTableDataArr[0][$strWidgetTableDataArrKey]	= isset($strModuleHTMLArr['options_list'][$strWidgetTableDataArrKey][$strWidgetTableDataArrValue])?$strModuleHTMLArr['options_list'][$strWidgetTableDataArrKey][$strWidgetTableDataArrValue]:'-';
						}
					}
				}
				
				/* Return the JSON string */
				jsonReturn($strWidgetTableDataArr[0], true);
			}
		}else{
			jsonReturn(array('status'=>0,'message'=>'Invalid request.'), true);
		}
	}
	
	/**************************************************************************************************************************/
	/*Purpose 	: Getting the data from requested schema.
	/*Inputs	: $pStrWidgetAttributesArr :: Configured column name to display the value,
				: $pStrFilterWidgetData :: Configured column name used for filter,
				: $pBlnCountNeeded :: Count Needed,
				: $pBlnPagination :: pagination.
	/*Returns 	: Widget Data Set Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**************************************************************************************************************************/
	private function _getWidgetData($pStrWidgetAttributesArr, $pStrFilterWidgetData = array(), $pBlnCountNeeded = false, $pBlnPagination = 0){
		/* variable initialization */
		$strWhereClauseArr 	= 	array();
		/* Setting page number */
		$intCurrentPageNumber 				= $pBlnPagination;
		
		/* if current page number is negative then do needful */
		if($intCurrentPageNumber < 0){
			/* Reset the pagination to first record set */
			$intCurrentPageNumber 			= 	0;
		}
		
		/* iterating the widget attribute filter loop */
		foreach($this->_strWidgetAllAttributes as $strAttributArrKey => $strAttributArrValue){
			/* checking for alise index */
			if(isset($strAttributArrValue['alise'])){
				/* Setting alise array with key */
				$strFilterAliseArr[$strAttributArrValue['alise']]	= $strAttributArrValue['attri_slug_key'];
			}
		}
		
		/* Setting the company filter */
		$strWhereClauseArr	= array($this->_strWidgeSlug.'.company_code'=>$this->getCompanyCode());
		
		/* if widget data is not empty then do needful */
		if(!empty($pStrFilterWidgetData)){
			/* iterating the filter column in the where clause */
			foreach ($pStrFilterWidgetData as $fieldName => $fieldValue){
				/* checking for custom filter clause */
				if(isset($strFilterAliseArr[$fieldName])){
					/* overwriting the key with attributes key */
					$fieldName	= $strFilterAliseArr[$fieldName];
					/* overwriting the key value with human readable language */
					$fieldValue	= getDecyptionValue($fieldValue);
				}
				/* if value is not empty then do needful */
				if (!empty($fieldValue)) {
					if($fieldName == 'id'){
						/* Set the filter clause */
						$strWhereClauseArr[$this->_strWidgeSlug.'.'.$fieldName] = $fieldValue;
					}else{
						/* Set Data Container */
						if((isset($this->_strWidgetAllAttributes [$fieldName])) && (in_array($this->_strWidgetAllAttributes [$fieldName]['attri_data_type'],$this->_strMultiValueContinerArr))){
							/* Set the filter clause */
							$strWhereClauseArr[$fieldName] = getDecyptionValue($fieldValue);
						}else{
							/* Set the filter clause */
							$strWhereClauseArr[$this->_strWidgeSlug.'.'.$fieldName.' like '] = $fieldValue;
						}
					}
				}
			}
		}
		
		/* removed used variables */
		unset($strFilterAliseArr);
		
		/* checking is custom query is set, if yes then do needful */
		if(!empty($this->_strCustomQueryArr)){
			/* Filter array for custom query hooked from custom modules */
			$this->_strCustomQueryArr['where']	= array_merge($this->_strCustomQueryArr['where'],$strWhereClauseArr);
			
			/* Setting the custom query */
			$strFilterArr						= $this->_strCustomQueryArr;
		}else{
			/* Filter array for default / master schema */
			$strFilterArr 						= 	array('table'=>$this->_strWidgeSlug, 'where'=>$strWhereClauseArr);
		}
		
		/* if count needed then do needful */
		if($pBlnCountNeeded ){
			$strFilterArr['column'] 		= 	array(' count('.$this->_strWidgeSlug.'.id) as recordCount ');
		}else{
			/* checking is custom query is set, if yes then do needful */
			if(!empty($this->_strCustomQueryArr)){
				/* if module not pass any column then do needful */
				if(empty($pStrWidgetAttributesArr)){
					/* over writing the value */
					$pStrWidgetAttributesArr	= array($this->_strWidgeSlug.'.*');
				}
				/* Setting the custom filter columns */
				$this->_strCustomQueryArr['column']	= array_merge($pStrWidgetAttributesArr, $this->_strCustomQueryArr['column']);
				
				/* Setting the filter columns */
				$strFilterArr['column'] 		= 	$this->_strCustomQueryArr['column'];
			}else{
				/* Setting the filter columns */
				$strFilterArr['column'] 		= 	$pStrWidgetAttributesArr;
			}
		}

		/* if requested page number is > 0 then do needful */ 
		if(($intCurrentPageNumber >= 0)){
			$strFilterArr['offset'] 		= 	($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE);
			$strFilterArr['limit'] 			= 	DEFAULT_RECORDS_ON_PER_PAGE;
		}
		
		/* if user requested from data dump in the CSV format then do needful */
		if($pBlnPagination == -5){
			/* Removed limit caluse */
			unset($strFilterArr['limit']);
		}

		/* Getting the module data set */
		return $this->_objDataOperation->getDataFromTable($strFilterArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting the module details.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setModuleData(){
		/* variable initialization */
		$strWidgetData 		= ($this->input->post('widgetData') != '') ? $this->input->post('widgetData') : array();
		$strSchemaSlug		= isset($this->_strWidgetAttributes[0]['widget_slug'])?$this->_strWidgetAttributes[0]['widget_slug']:'';
		$intDataPrimaryCode = ($this->input->post('txtOperationCode') != '') ? $this->input->post('txtOperationCode') : 0;
		$strOperationType	= 'Added';
		$strDataArr			= array('table'=>$this->_strWidgeSlug);
		$strCustomDataArr	= array();
		
		/* if request is not contains any data then do needful */
		if(empty($strWidgetData)){
			/* return response */
			jsonReturn(array('status'=>0,'message'=>'Invalid requested.'), true);
		}
		
		/* Setting the company code of requested respected code */
		$strDataArr['data']['company_code']		= $this->getCompanyCode();
		
		/* Variable initialization */
		$fileError 			= 	false;
		$firstDocumentSet 	= 	false;
		$newsDocumentsArr 	= 	array();
		$strWidgetFileName = 	'widgetDataFileName[]';
		/* Checking from File request */
		if ( isset($_FILES['widgetDataFile']) && !empty($_FILES['widgetDataFile'])) {
			/* Creating files object */
			$filesObj 	= 	new Files($this->_objDataOperation, $this->getCompanyCode());
			
			/* Iterating the file instance loop */
			foreach ($_FILES['widgetDataFile']['name'] as $strFieldName => $strFieldValue) {
				/* if file name is not empty i.e. file is selected then do needful */
				if (!empty($_FILES['widgetDataFile']['name'][$strFieldName])) {
					/* if requested column is not found in the list then do needful */
					if(!isset($this->_strWidgetAllAttributes[$strFieldName])){
						/* return the column not found */
						jsonReturn(array('status'=>0,'message'=> $strFieldName .' field name does not exist.'), true);
					}
					
					/* if mandatory field then do needful  */
					if((isset($this->_strWidgetAllAttributes[$strFieldName]['is_mandatory'])) && ($this->_strWidgetAllAttributes [$strFieldName]['is_mandatory'] == 1) && ($strFieldName == '')){
						/* return the empty field error */
						jsonReturn(array('status'=>0,'message'=> 'Requested mandatory field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') file is not selected.'), true);
					}
					/* Getting file details */
					$strFileInfoArr								= pathinfo($_FILES['widgetDataFile']['name'][$strFieldName]);
					
					/* Creating file object */
					$_FILES[$strWidgetFileName]['name'] 		= 	 $_FILES['widgetDataFile']['name'][$strFieldName];
					$_FILES[$strWidgetFileName]['type'] 		= 	 $_FILES['widgetDataFile']['type'][$strFieldName];
					$_FILES[$strWidgetFileName]['tmp_name'] 	= 	 $_FILES['widgetDataFile']['tmp_name'][$strFieldName];
					$_FILES[$strWidgetFileName]['error']		= 	 $_FILES['widgetDataFile']['error'][$strFieldName];
					$_FILES[$strWidgetFileName]['size'] 		= 	 $_FILES['widgetDataFile']['size'][$strFieldName];
					
					/* Variable initialization for CI to initialized the I/O operation */
					$configArr  								=  	array();
					$configArr['allowed_types'] 				= 	'gif|jpg|png|bmp|jpeg';
					$configArr['max_size'] 						= 	DEFAULT_FILE_UPLOAD_SIZE_IN_KB;
					$configArr['file_name'] 					= 	$strFileInfoArr['filename'].'_'.time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $strFieldName).'.'.$strFileInfoArr['extension'];

					/* Upload file with file object with data and driver */
					$fileUpload = $filesObj->uploadFile($strWidgetFileName,$_FILES[$strWidgetFileName], $configArr, $this->_strWidgetAllAttributes[$strFieldName], $this->_strWidgetAllAttributes[$strFieldName]['file_driver']);
					
					/* removed used variables */
					unset($configArr, $strFileInfoArr);
					
					/* Checking file upload status */
					if ($fileUpload['status'] != true) {
						/* if any error found then do needful */
						jsonReturn(array('status'=>0,'message'=> $fileUpload['message']), true);
					}
					
					/* Return file operation status */
					//$strDataArr['data'][$this->_strWidgetAllAttributes[$strFieldName]['attri_slug_key']] = $fileUpload['filepath'];
					
					/* checking for custom attributes */
					if(isset($this->_strWidgetAllAttributes[$strFieldName]['custom_field'])){
						/* User input text for configured attributes of custom hook widget */
						$strCustomDataArr[$this->_strWidgetAllAttributes[$strFieldName]['schema_name']]['table']																	= $this->_strWidgetAllAttributes[$strFieldName]['schema_name'];
						$strCustomDataArr[$this->_strWidgetAllAttributes[$strFieldName]['schema_name']]['data'][$this->_strWidgetAllAttributes[$strFieldName]['attri_slug_key']]	= $fileUpload['filepath'];
					}else{
						/* User input text for configured attributes of same widget */
						$strDataArr['data'][$this->_strWidgetAllAttributes[$strFieldName]['attri_slug_key']] = $fileUpload['filepath'];
					}
				}  
			}
		}
		
		/* apply validation for the fields */
		$widgetObj = new Widget($this->_objDataOperation, $this->getCompanyCode());
		
		/* Iterating the posted column  */
		foreach ($strWidgetData as $strFieldName => $strFieldValue){
			
			/* if requested column is not found in the list then do needful */
			if(!isset($this->_strWidgetAllAttributes[$strFieldName])){
				/* return the column not found */
				jsonReturn(array('status'=>0,'message'=> $strFieldValue .' field name does not exist.'), true);
			}
			/* if mandatory field then do needful  */
			if((isset($this->_strWidgetAllAttributes [$strFieldName]['is_mandatory'])) && ($this->_strWidgetAllAttributes [$strFieldName]['is_mandatory'] == 1) && ($strFieldValue == '')){
				/* return the empty field error */
				jsonReturn(array('status'=>0,'message'=> 'Requested mandatory field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') is empty.'), true);
			}
			
			/* user input value */
			$strUserInputValue	= $strFieldValue;
			/* Set Data Container */
			if(in_array($this->_strWidgetAllAttributes [$strFieldName]['attri_data_type'],$this->_strMultiValueContinerArr)){
				/* if multiple values then do needful */
				if(is_array($strFieldValue)){
					/* iterating the value array */
					foreach($strFieldValue as $strFieldValueKey => $strFieldValueDetails){
						/* Value overwriting */
						$strFieldValue[$strFieldValueKey] = getDecyptionValue($strFieldValueDetails);
					}
				}else{
					/* Value overwriting */
					$strUserInputValue	= getDecyptionValue($strFieldValue);
				}
			/* Set Data Date Time */
			}else if($this->_strWidgetAllAttributes [$strFieldName]['attri_data_type'] == "datetime"){
				/* Value overwriting */
				$strUserInputValue	= str_replace(array('/',':'),array('',''),$strFieldValue);
				/* Adding extra details */
				$strUserInputValue	= $strUserInputValue.str_repeat('0',14-strlen($strUserInputValue));
			}
			
			/* if not requried and validation set then do needful */
			if(($this->_strWidgetAllAttributes [$strFieldName]['attri_validation']!='') && ($strFieldValue != '')){
				switch($this->_strWidgetAllAttributes [$strFieldName]['attri_validation']){
					case 'string':
						/* checking for valid JSON */
						if(!ctype_alpha($strFieldValue)){
							/* return the validation failed error */
							jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') not valid letter string.'), true);
						}
						break;
					case 'notempty':
						/* checking for valid JSON */
						if(trim($strFieldValue)==''){
							/* return the validation failed error */
							jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') is empty.'), true);
						}
						break;
					case 'numeric':
						/* Validating for numeric */
						if(!is_numeric($strFieldValue)){
							/* return the validation failed error */
							jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') is not valid number.'), true);
						}
						break;
					case 'email':
						if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
							/* return the validation failed error */
							jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') is not valid email.'), true);
						}
						break;
					case 'contact-no':
						break;
					case 'datetime':
						break;
					case 'json':
						/* checking for valid JSON */
						if(!isJSON($strFieldValue)){
							/* return the validation failed error */
							jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') is not valid JSON Structure.'), true);
						}
						break;
				}
			}
			
			/* Validating the user input */
			if(
				(isset($this->_strWidgetAllAttributes [$strFieldName]['attri_validation'])) && 
				(isset($this->_strWidgetAllAttributes [$strFieldName]['is_mandatory'])) && 
				($this->_strWidgetAllAttributes [$strFieldName]['is_mandatory'] == 1) && 
				(trim($strUserInputValue != '')) && 
				(!preg_match($widgetObj->getAttrValidation($this->_strWidgetAllAttributes [$strFieldName]['attri_validation']), $strUserInputValue))
			){
				/* return the validation failed error */
				jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFieldName]['attri_slug_name'].') did not pass '.$this->_strWidgetAllAttributes [$strFieldName]['attri_validation'].' validation.'), true);
			}
		
			/* checking for custom attributes */
			if(isset($this->_strWidgetAllAttributes[$strFieldName]['custom_field'])){
				/* User input text for configured attributes of custom hook widget */
				$strCustomDataArr[$this->_strWidgetAllAttributes[$strFieldName]['schema_name']]['table']																	= $this->_strWidgetAllAttributes[$strFieldName]['schema_name'];
				$strCustomDataArr[$this->_strWidgetAllAttributes[$strFieldName]['schema_name']]['data'][$this->_strWidgetAllAttributes[$strFieldName]['attri_slug_key']]	= $strUserInputValue;
			}else{
				/* User input text for configured attributes of same widget */
				$strDataArr['data'][$this->_strWidgetAllAttributes[$strFieldName]['attri_slug_key']] = $strUserInputValue;
			}
		}
		
		/* removed used variable */
		unset($widgetObj);
		$strCustomDataArr['foreign']	= $intDataPrimaryCode;
		
		/* Processing the data set with hook of same module custom logic */
		$intTranscationStatus 	= $this->_getMouleHookProcessData(array_merge($strDataArr['data'],$strCustomDataArr), __FUNCTION__);

		if($intTranscationStatus == 1){
			/* Setting foreign key value */
			$strCustomDataArr['foreign']	= $intTranscationStatus;
			/* Value overwriting */
			$strOperationType	= 'Processed';
		}else{
			/* if data primary code is passed then do needful */
			if ($intDataPrimaryCode > 0) {
				/* Setting the key updated value */
				$strDataArr['where']	= array('id' => $intDataPrimaryCode);
				/* Updating requested module date in the database */
				$intTranscationStatus = $this->_objDataOperation->setUpdateData($strDataArr);
				/* Value overwriting */
				$strOperationType	= 'Updated';
				/* Setting foreign key value */
				$strCustomDataArr['foreign']	= $intDataPrimaryCode;
			}else{
				/* Adding requested module date in the database */
				$intTranscationStatus = $this->_objDataOperation->setDataInTable($strDataArr);
				/* Setting foreign key value */
				$strCustomDataArr['foreign']	= $intTranscationStatus;
			}
		
        	$strDataArr['operationType'] = $strOperationType;
                
			/* Processing the data set with hook of same module custom logic */
			$this->_getMouleHookProcessData($strCustomDataArr, __FUNCTION__);
		}
		
		/* removed used variables */
		unset($strDataArr);
		
		/* if DML operation done successfully then do needful */
		if ($intTranscationStatus) {
			/* return the operation status */
			jsonReturn(array('status'=>1,'message'=>'Record '.$strOperationType.' successfully.'), true);
		}else{
			/* return operation status message */
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
		/* variable initialization */
		$intDataPrimaryCode 		= 	($this->input->post('txtDeleteRecordCode') != '') ? $this->input->post('txtDeleteRecordCode') : 0;
		
		/* if request is not contains any data then do needful */
		if(empty($intDataPrimaryCode)){
			/* return response */
			jsonReturn(array('status'=>0,'message'=>'Invalid requested.'), true);
		}
		
		/* Setting the updated array */
		$strUpdatedArr	= array(
									'table'=>$this->_strWidgeSlug,
									'data'=>array(
												'deleted'=>1,
												'updated_by'=>$this->getUserCode(),
											),
									'where'=>array(
												'id'=>$intDataPrimaryCode
											)

								);
								
		/* Updating the requested record set */
		$intNunberOfRecordUpdated = $this->_objDataOperation->setUpdateData($strUpdatedArr);
		
		/* Processing the data set with hook of same module custom logic */
		$this->_getMouleHookProcessData($strUpdatedArr, __FUNCTION__);

		if($intNunberOfRecordUpdated > 0){
			jsonReturn(array('status'=>1,'message'=>'Requested '.$this->_strModuleName.' deleted successfully.'), true);
		}else{
			jsonReturn(array('status'=>0,'message'=>DML_ERROR), true);
		}

		/* removed variables */
		unset($strUpdatedArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Processing the custom hook modules request.
	/*Inputs	: $pStrMethodName :: Method name.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function processHookRequest($pStrMethodName = ''){
		/* if method name is not passed then do needful */
		if($pStrMethodName == ''){
			jsonReturn(array('status'=>1,'message'=>''), true);
		}
		
		/* Processing the data set with hook of same module custom logic */
		$strDataArr = $this->_getMouleHookProcessData($_REQUEST, $pStrMethodName);
		
		jsonReturn($strDataArr, true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Importing data in the respective modules.
	/*Inputs	: None.
	/*Returns 	: Transaction Status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function importData(){
		
		/* if data file is not selected then do needful */
		if((!isset($_FILES['widgetDataImportFile']['name'])) || ($_FILES['widgetDataImportFile']['name'] == '')){
			/* return the error */
			jsonReturn(array('status'=>0,'message'=>'Data file is not selected'), true);
		}
		/* Getting file name and extension array */
		$pStrFileArr	= explode('.',$_FILES['widgetDataImportFile']['name']);
		
		/* if invalid file format then do needful */
		if(!in_array(end($pStrFileArr), array('xls'))){
			/* return the error */
			jsonReturn(array('status'=>0,'message'=>'Invalid file type is selected. Only xls format is allowed.'), true);
		}
		
		/* Reading the file content */
		$fileReaderObject 	= new Files($this->_objDataOperation, $this->getCompanyCode());
		/* get file contact as array */
		$strFileContentArr 	= $fileReaderObject->readExcelFile($_FILES['widgetDataImportFile']['tmp_name']);
		/* removed used variables */
		unset($fileReaderObject);
		
		if(empty($strFileContentArr)){
			/* return the error */
			jsonReturn(array('status'=>0,'message'=>'Empty file uploaded.'), true);
		}
		
		/* iterating the excel header data loop */
		foreach($strFileContentArr[0] as $strFileContentArrKey => $strFileContentArrValue){
			/* converting the data in the data column text */
			$strFileContentArr[0][$strFileContentArrKey]	= getSlugify($strFileContentArrValue);
		}
		
		/************ Checking for data column header and EXCEL column header ************/
		/* Variable initialization */
		$blnIsDataColumnOkay = true;
		/* Iterating the widget column array loop */
		foreach($this->_strWidgetAllAttributes  as $strWidgetAllAttributesKey => $strWidgetAllAttributesValue){
			/* checking requested value in the file column array set */
			if(!in_array($strWidgetAllAttributesKey, $strFileContentArr[0])){
				/* Value overwriting */
				$blnIsDataColumnOkay	= false;
				/* terminating the loop */
				break;
			}
		}
		
		/* if column name is not matching or having any having issue then do needful */
		if(!$blnIsDataColumnOkay){
			/* return the error */
			jsonReturn(array('status'=>0,'message'=>'Invalid data file headers. Kindly download the suggested data template and re-upload.'), true);
		}
		
		/************ Data Verification ************/
		/* Creating widget object */
		$widgetObj = new Widget($this->_objDataOperation, $this->getCompanyCode());
		/* Get module HTML and attributes options list */
		$strModuleHTMLArr	= $widgetObj->getWidgetAttributesWithLayout($this->_strWidgetAllAttributes, true, $this->_strModuleForm,SITE_URL.'mod/'.$this->_strModuleSlug.'/setModuleData',true);
		
		/* iterating the loop */
		foreach($strFileContentArr as $strFileContentArrKey => $strFileContentArrContainer){
			/* do not process the header index */
			if($strFileContentArrKey == 0){
				continue;
			}
			
			/* iterating the value array loop */
			foreach($strFileContentArrContainer as $strFileContentArrContainerKey => $strFileContentArrContainerValue){
				/* Checking validation type and do validation */
				if(
					(isset($this->_strWidgetAllAttributes [$strFileContentArr[0][$strFileContentArrContainerKey]]['is_mandatory'])) && 
					($this->_strWidgetAllAttributes [$strFileContentArr[0][$strFileContentArrContainerKey]]['is_mandatory'] == 1) && 
					(isset($this->_strWidgetAllAttributes [$strFileContentArr[0][$strFileContentArrContainerKey]]['attri_validation'])) && 
					((trim($strFileContentArrContainerValue) == '') || (!preg_match($widgetObj->getAttrValidation($this->_strWidgetAllAttributes [$strFileContentArr[0][$strFileContentArrContainerKey]]['attri_validation']), $strFileContentArrContainerValue)))
				){
					/* Return error message */
					jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFileContentArr[0][$strFileContentArrContainerKey]]['attri_slug_name'].') did not pass '.$this->_strWidgetAllAttributes [$strFileContentArr[0][$strFileContentArrContainerKey]]['attri_validation'].' validation.'), true);
				}
				
				/* Checking for options validation */
				if(
					(isset($strModuleHTMLArr['options_list'])) && 
					(!empty($strModuleHTMLArr['options_list'])) && 
					(isset($strModuleHTMLArr['options_list'][$strFileContentArr[0][$strFileContentArrContainerKey]]))
				){
					if(!in_array($strFileContentArrContainerValue, $strModuleHTMLArr['options_list'][$strFileContentArr[0][$strFileContentArrContainerKey]])){
						/* Return error message */
						jsonReturn(array('status'=>0,'message'=> 'Requested field ('.$this->_strWidgetAllAttributes [$strFileContentArr[0][$strFileContentArrContainerKey]]['attri_slug_name'].') value is did not match with list value.'), true);
					}else{
						/* Value overwriting */
						$strFileContentArr[$strFileContentArrKey][$strFileContentArrContainerKey]	= getDecyptionValue(array_search($strFileContentArrContainerValue, $strModuleHTMLArr['options_list'][$strFileContentArr[0][$strFileContentArrContainerKey]], true));
					}
				}
				
				/* Setting-up Column index and other necessary values */
				$strFileContentArr[$strFileContentArrKey][$strFileContentArr[0][$strFileContentArrContainerKey]]	= $strFileContentArr[$strFileContentArrKey][$strFileContentArrContainerKey];
				$strFileContentArr[$strFileContentArrKey]['record_date']											= date('YmdHis');
				$strFileContentArr[$strFileContentArrKey]['updated_by']												= $this->getUserCode();
				$strFileContentArr[$strFileContentArrKey]['company_code']											= $this->getCompanyCode();
				/* Removing non-used index */
				unset($strFileContentArr[$strFileContentArrKey][$strFileContentArrContainerKey]);
			}
		}
		/* Removed used variables */
		unset($widgetObj, $strFileContentArr[0]);
		
		/************ Data Insertion ************/
		$intNumberOfRecordInset 	= $this->_objDataOperation->setBulkInset(array('table'=>$this->_strWidgeSlug,'data'=>$strFileContentArr));
		
		/* if bulk import done successful then do needful */
		if($intNumberOfRecordInset > 0){
			/* Return success message */
			jsonReturn(array('status'=>1,'message'=> 'Records ('.$intNumberOfRecordInset.') imported successfully.'), true);
		}else{
			/* Return error message */
			jsonReturn(array('status'=>0,'message'=> DML_ERROR), true);
		}
	}
	
	/******************************************************************************************************/
	/*Purpose 	: Set the result set widget attribute options list value in the human readable format.
	/*Inputs	: $pStrWidgetDataSetArr :: Data set,
				: $pStrWidgetAttributesArr :: Attributes list array.
	/*Returns 	: Data in the human readable format.
	/*Created By: Jaiswar Vipin Kumar R.
	/******************************************************************************************************/
	private function _setCustomValues($pStrWidgetDataSetArr = array(), $pStrWidgetAttributesArr = array()){
		/* if empty data set pass then do needful */
		if(empty($pStrWidgetDataSetArr)){
			/* return the empty array */
			return $pStrWidgetDataSetArr;
		}
		
		/* Iterating the data value set */
		foreach($pStrWidgetDataSetArr as $pStrWidgetDataSetArrKey => $pStrWidgetDataSetAttributeArr){
			/* Iterating the widget attribute */
			foreach($pStrWidgetDataSetAttributeArr as $pStrWidgetDataSetAttributeArrKey => $pStrWidgetDataSetAttributeArrValue){
				/* Encrypting the value for the text */
				$strWidgetDataSetAttributeArrValue	= getEncyptionValue($pStrWidgetDataSetAttributeArrValue);
				/* if attribute is type of multiple option value holder then do needful */
				if(isset($pStrWidgetAttributesArr['options_list'][$pStrWidgetDataSetAttributeArrKey])){
					/* Setting human readable value */
					$pStrWidgetDataSetArr[$pStrWidgetDataSetArrKey][$pStrWidgetDataSetAttributeArrKey]	=  isset($pStrWidgetAttributesArr['options_list'][$pStrWidgetDataSetAttributeArrKey][$strWidgetDataSetAttributeArrValue])?$pStrWidgetAttributesArr['options_list'][$pStrWidgetDataSetAttributeArrKey][$strWidgetDataSetAttributeArrValue]:'-';
				/* if file type then do needful */
				}else if(isset($pStrWidgetAttributesArr['file_element'][$pStrWidgetDataSetAttributeArrKey])){
					/* Setting file display */
					$pStrWidgetDataSetArr[$pStrWidgetDataSetArrKey][$pStrWidgetDataSetAttributeArrKey]	=  ($pStrWidgetDataSetAttributeArrValue!='')?'<img src="'.SITE_URL.$pStrWidgetDataSetAttributeArrValue.'" class="materialboxed" width="50">':'-';
				/* if field is empty then replace the the blank value to - */
				}else if($pStrWidgetDataSetAttributeArrValue == ''){
					$pStrWidgetDataSetArr[$pStrWidgetDataSetArrKey][$pStrWidgetDataSetAttributeArrKey]	= '-';
				/* Checking for date and time field */
				}else if((isset($this->_strWidgetAttributes[$pStrWidgetDataSetAttributeArrKey])) && ($this->_strWidgetAttributes[$pStrWidgetDataSetAttributeArrKey]['attri_data_type'] == 'datetime')){
					$pStrWidgetDataSetArr[$pStrWidgetDataSetArrKey][$pStrWidgetDataSetAttributeArrKey]	= getDateFormat($pStrWidgetDataSetAttributeArrValue,5);
				}
			}
		}
		
		/* return the empty array */
		return $pStrWidgetDataSetArr;	
	}
}
