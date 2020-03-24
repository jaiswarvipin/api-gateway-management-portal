<?php 
/*******************************************************************************/
/* Purpose 		: Managing the widget related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Widget{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_widget";
	private $_frameworkObj		= '';
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code
	/* Returns	: None.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
		/* CI instance reference */
		$this->_frameworkObj =& get_instance();
	}
	
	/***************************************************************************/
	/* Purpose	: get widget list.
	/* Inputs 	: None
	/* Returns	: widegt array details.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getWidgetList(){
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>$this->_strTableName,
									'where'=>array(),
									'column'=>array('id', 'description')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	/***************************************************************************/
	/* Purpose	: Get dynamic form based on attributes assigned to the same widget.
	/* Inputs 	: $pStrWidgetAttributes :: Widget attributes array ,
				: $pIsFormNeeded :: If form needed, if not hen returns only records,
				: $pStrFormName :: Contains from name,
				: $pFormPostURL :: Contains form URL,
				: $pBlnIsNeededAttributeOptionsList :: in response needed the attributes list options
	/* Returns	: Dynamic Width Form
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getWidgetAttributesWithLayout($pStrWidgetAttributes = array(), $pIsFormNeeded = true, $pStrFormName = '', $pFormPostURL = '', $pBlnIsNeededAttributeOptionsList = false){
		/* Variable initialization */
		$strReturmHTML	= '<div class="row">';
		$strAttLableArr	= $strReturnArr = $strFileArr =  array();
		
		if(empty($pStrWidgetAttributes)){
			/* Return dynamic Form HTML */
			return $strReturmHTML.'</div>';
		}
		
		/* if form not needed then return only records */
		if(!$pIsFormNeeded){
			/* Return dynamic Form HTML */
			return $strReturmHTML.'</div>';
		}
		
		/* Variable initialization */
		$strFormName	= 'frmWidgetDetails';
		$strFormPostURL	= SITE_URL.'manage-widgets/setModuleData';
		 
		/* if form name is pass then do needful */
		if($pStrFormName != ''){
			/* Value overwriting */
			$strFormName	= $pStrFormName;
		}
		
		/* if form post URL is pass then do needful */
		if($pFormPostURL != ''){
			/* Value overwriting */
			$strFormPostURL	= $pFormPostURL;
		}
		
		/* Setting the form tag */
		$strReturmHTML.=	'<form name="'.$strFormName.'" id="'.$strFormName.'" method="post" action="'.$strFormPostURL.'" enctype="multipart/form-data">';
		
		/* iterating the loop */ 
		foreach($pStrWidgetAttributes as $strLeadAttrArrKey => $strLeadAttrArrValue){
			/* Variable initialization */
			$strMandatory	= '';
			/* checking for mandatory */
			if($strLeadAttrArrValue['is_mandatory'] == 1){
				/* Value over ridding */
				$strMandatory	= '*';
			}
			/* checking for arrtibute needs to visiable */
			if(isset($strLeadAttrArrValue['attri_visiable'])){
				/* do not show */
				continue;
			}
			
			
			/* Checking the attribute type */
			switch($strLeadAttrArrValue['attri_data_type']){
				case 'textbox':
					$strReturmHTML.=	'<div class="input-field col s12">
											<input class="validate" type="text" name="widgetData['.$strLeadAttrArrValue['attri_slug_key'].']" id="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'" data-set="'.$strLeadAttrArrValue['attri_slug_key'].'" />
											<label for="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'">Enter '.$strLeadAttrArrValue['attri_slug_name'].' '.$strMandatory.'</label>
											'.getWidgetFormHelpTextHTML($strLeadAttrArrValue['attri_slug_helps']).'
										</div>';
					break;
				case 'textarea':
					$strReturmHTML.=	'<div class="input-field col s12">
											<textarea class="materialize-textarea validate" type="text" name="widgetData['.$strLeadAttrArrValue['attri_slug_key'].']" id="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'" data-set="'.$strLeadAttrArrValue['attri_slug_key'].'" ></textarea>
											<label for="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'">Enter '.$strLeadAttrArrValue['attri_slug_name'].' '.$strMandatory.'</label>
											'.getWidgetFormHelpTextHTML($strLeadAttrArrValue['attri_slug_helps']).'
										</div>';
					break;
				case 'datetime':
					$strReturmHTML.=	'<div class="input-field col s12">
											<input class="datepicker validate" type="text" name="widgetData['.$strLeadAttrArrValue['attri_slug_key'].']" id="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'" data-set="'.$strLeadAttrArrValue['attri_slug_key'].'" />
											<label for="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'">Enter '.$strLeadAttrArrValue['attri_slug_name'].' '.$strMandatory.'</label>
											'.getWidgetFormHelpTextHTML($strLeadAttrArrValue['attri_slug_helps']).'
										</div>';
					break;
				case 'checbox':
					break;
				case 'radio':
					break;
				case 'dropdown':
					$strReturmHTML.=	'<div class="input-field col s12">
											<select multiple name="widgetData['.$strLeadAttrArrValue['attri_slug_key'].'][]" id="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'" data-set="'.$strLeadAttrArrValue['attri_slug_key'].'">'.$strLeadAttrArrValue['attri_value_list'].'</select>
											<label for="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'">Select '.$strLeadAttrArrValue['attri_slug_name'].' '.$strMandatory.'</label>
											'.getWidgetFormHelpTextHTML($strLeadAttrArrValue['attri_slug_helps']).'
										</div>';
					break;
				case 'select':
					/* Getting the attributes options list */
					$strItemArr		= $this->getWidgetOptionsDetailsByWidgetAttributesCode(array($strLeadAttrArrValue['attribute_code']));
					$strOptionArr	= array();
					/* if option list found then do needful */
					if(!empty($strItemArr)){
						 
						/* Iterating the loop */
						foreach($strItemArr as $strItemArrKey => $strItemArrValue){
							/* Setting the new value a key */
							$strOptionArr[getEncyptionValue($strItemArrValue['id'])]	= $strItemArrValue['description'];	
						}
					}
					
					/* if requester needed the widget attributes label list */
					if($pBlnIsNeededAttributeOptionsList){
						/* Setting the widget attributes options list */
						$strAttLableArr[$strLeadAttrArrValue['attri_slug_key']]	= $strOptionArr;
					}
					
					/* removing the original index */
					unset($strItemArr);	
					
					/* Creating form object */
					$objForm	= new Form();
					/* Creating the dropdown index */
					$strReturmHTML.=	'<div class="input-field col s12">
											<select name="widgetData['.$strLeadAttrArrValue['attri_slug_key'].']" id="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'" data-set="'.$strLeadAttrArrValue['attri_slug_key'].'">'.$objForm->getDropDown($strOptionArr, '', true, true).'</select>
											<label for="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'">Select '.$strLeadAttrArrValue['attri_slug_name'].' '.$strMandatory.'</label>
											'.getWidgetFormHelpTextHTML($strLeadAttrArrValue['attri_slug_helps']).'
										</div>';
					/* removed used variables */
					unset($objForm);
					
					break;

					/* FILE TYPE */
				case 'file':
					$strReturmHTML.=	'
											<div class="file-field col s12 input-field">
												<div class="btn">
													<span>Please select file '.$strLeadAttrArrValue['attri_slug_name'].' '.$strMandatory.'</span>
													<input type="file" name="widgetDataFile['.$strLeadAttrArrValue['attri_slug_key'].']" id="txtWidget'.$strLeadAttrArrValue['attri_slug_key'].'" data-set="'.$strLeadAttrArrValue['attri_slug_key'].'" />
													'.getWidgetFormHelpTextHTML($strLeadAttrArrValue['attri_slug_helps']).'
												</div>
												<div class="file-path-wrapper">
													<input class="file-path validate" type="text" />
												</div>
											</div>
										';
					/* setting the file type */
					$strFileArr[$strLeadAttrArrValue['attri_slug_key']]	= $strLeadAttrArrValue['attri_slug_key'];
					break;
			}
		}
		
		$strReturmHTML.=	'<input type="hidden" name="txtOperationCode" id="txtOperationCode" value="" data-set="id" /><input type="hidden" name="txtSearch" id="txtSearch" value=""  />';
		
		/* Closing the form */
		$strReturmHTML.=	'</form>';
		
		/* removed sued variables */
		unset($strLeadAttrArr);
		
		/* Set the form container details */
		$strReturmHTML .='</div>';
		
		/* Setting the modules from */
		$strReturnArr['module_from']		= $strReturmHTML;
		$strReturnArr['file_element']		= $strFileArr;
		/* if requester needed the widget attributes label list */
		if($pBlnIsNeededAttributeOptionsList){
			/* Setting the options list for return */
			$strReturnArr['options_list']	= $strAttLableArr;
		}
		/* Removed used variables */
		unset($strAttLableArr, $strReturmHTML);
		
		/* return the form and attributes options list */
		return $strReturnArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Get module column as search panel.
	/* Inputs 	: $pStrColumnArray :: Column Array. 
	/* Returns	: Search HTML of respective panel.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getColumnAsSearchPanel($pStrColumnArray = array()){
		/* Variable initialization */
		$strDisplayClass	= '';
		$strElementPrefix	= 'txtProfile';
		
		/* if form index set then do needful */
		if(isset($pStrColumnArray['frmName'])){
			/* Set the class */
			$strDisplayClass	= 'no-add';
			$strElementPrefix	= 'txtSearch';
		}
		
		/* Variable initialization */
		$strReturmHTML	= '<div class="row '.$strDisplayClass.'">';
		
		/* if column array is empty then of needful */
		if(empty($pStrColumnArray)){
			/* Return empty HTML */
			return $strReturmHTML.'</div>';
		}
		
		/* if form object is set then do needful */
		if(isset($pStrColumnArray['frmName'])){
			$strReturmHTML.=	'<form name="'.$pStrColumnArray['frmName'].'" id="'.$pStrColumnArray['frmName'].'" method="post" action="'.$pStrColumnArray['action'].'">';
		}
		
		/* removed not required index */
		unset($pStrColumnArray['action']);
		
		
		/* Iterating the loop */
		foreach($pStrColumnArray as $pStrColumnArrayKey => $pStrColumnArrayValue){
			/* if column index is not set the do not render that element */
			if(!isset($pStrColumnArrayValue['column'])){
				/* Set the pointer to next index */
				continue;
			}
			
			/* disabled flag */
			$strDisabled	= '';
			/* checking disabled flag set */
			if(isset($pStrColumnArrayValue['disabled'])){
				/* Set the disabled flag */
				$strDisabled	= 'disabled="disabled" ';
			}
			
			/* Checking for element type */
			if(isset($pStrColumnArrayValue['is_date'])){
				/* Checking for date range element */
				if((string)$pStrColumnArrayKey == 'date_range'){
					$strReturmHTML.=	'<div class="input-field col s12">
											<label for="'.$strElementPrefix.'FromDate">From Date</label>
											<input type="text" name="'.$strElementPrefix.'FromDate" id="'.$strElementPrefix.'FromDate" class="datepicker" '.$strDisabled.'/>
										</div>
										<div class="input-field col s12">
												<label for="'.$strElementPrefix.'ToDate">To Date</label>
												<input type="text" name="'.$strElementPrefix.'ToDate" id="'.$strElementPrefix.'ToDate" class="datepicker" '.$strDisabled.'/>
										</div>';
				}
			}else if(isset($pStrColumnArrayValue['dropdown'])){
				$strReturmHTML.=	'<div class="input-field col s12">
										<select name="'.$strElementPrefix.$pStrColumnArrayValue['column'].'" id="'.$strElementPrefix.$pStrColumnArrayValue['column'].'" data-set="'.$pStrColumnArrayValue['column'].'" '.$strDisabled.'>'.$pStrColumnArrayValue['data'].'</select>
										<label for="'.$strElementPrefix.$pStrColumnArrayValue['column'].'">Select '.$pStrColumnArrayValue['label'].'</label>
									</div>';
			}else{
					$strReturmHTML.=	'<div class="input-field col s12">
											<input class="validate" type="text" name="'.$strElementPrefix.$pStrColumnArrayValue['column'].'" id="'.$strElementPrefix.$pStrColumnArrayValue['column'].'" data-set="'.$pStrColumnArrayValue['column'].'" '.$strDisabled.'/>
											<label for="'.$strElementPrefix.$pStrColumnArrayValue['column'].'">Enter '.$pStrColumnArrayValue['label'].'</label>
										</div>';
			}
		}
		
		/* if form object is set then do needful */
		if(isset($pStrColumnArray['frmName'])){
			/* Closing the form */
			$strReturmHTML.=	'<input type="hidden" name="txtSearch" id="txtSearch" value="" /></form>';
		}
		
		/* Return dynamic Form HTML */
		return $strReturmHTML.'</div>';
	}
	
	
	/***************************************************************************/
	/* Purpose	: get widget attributes list by module slug.
	/* Inputs 	: $pStrModuleSlug	:: Module slug.
	/* Returns	: Widegt array details.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getWidgetDetailsByModuleSlug($pStrModuleSlug = ''){
		/* Variable Initialization */
		$strReturnArr	= array();
		
		/* if module slug is not pass then do needful */
		if($pStrModuleSlug == ''){
			/* return empty array */
			return $strReturnArr;
		}
		
		/* Filter array */
		$strFilterArr 	= 	array(
									'table'=>array('master_modues', 'mater_module_widget_attribute', 'master_widget_attributes',$this->_strTableName),
									'column'=>array('master_modues.id as module_code', $this->_strTableName.'.id as widget_code', 'master_modues.description as module_name','master_modues.module_url as module_slug',$this->_strTableName.'.description as widget_name',$this->_strTableName.'.schema_slug as widget_slug','master_widget_attributes.attri_slug_key','master_widget_attributes.attri_slug_name','master_widget_attributes.attri_data_type','master_widget_attributes.attri_default_value','master_widget_attributes.attri_value_list, master_widget_attributes.is_mandatory, master_widget_attributes.file_driver, master_widget_attributes.attri_validation,master_widget_attributes.attri_slug_helps'),
									'join'=>array('','master_modues.id = mater_module_widget_attribute.module_code', 'mater_module_widget_attribute.attri_code = master_widget_attributes.id', 'mater_module_widget_attribute.widget_code = '.$this->_strTableName.'.id'),
									'where'=>array('master_modues.company_code'=>$this->_intCompanyCode,'master_modues.module_url'=>$pStrModuleSlug),
									'order'=>array('master_widget_attributes.view_sequence'=>'ASC')
								);
							
		/* Get Module and widget details */
		$strReturnArr	= $this->_databaseObject->getDataFromTable($strFilterArr);
		/* return widget and module details */
		return $strReturnArr;
	}

	/***************************************************************************/
	/* Purpose	: Get widget attributes list by widget code.
	/* Inputs 	: $pIntWidgetCode	:: Widget code,
				: $pIntLimit :: Limit.
	/* Returns	: Widget attribute list array details.
	/* Created By : Vipin Kumar R. Jaiswar
	/***************************************************************************/
	public function getWidgetDetailsByWidgetCode($pIntWidgetCode = 0, $pIntLimit = 0){
		/* Variable Initialization */
		$strReturnArr	= $strDataArr  = array();

		/* if widget code is not pass then do needful */
		if( $pIntWidgetCode == 0){
			/* return empty array */
			return $strReturnArr;
		}

		/* Filter array */
		$strFilterArr 	= 	array(
								'table'=>array('master_widget_attributes',$this->_strTableName),
								'column'=>array($this->_strTableName.'.id as widget_code', $this->_strTableName.'.description as widget_name',$this->_strTableName.'.schema_slug as widget_slug','master_widget_attributes.id as attribute_code','master_widget_attributes.attri_slug_key','master_widget_attributes.attri_slug_name','master_widget_attributes.attri_data_type','master_widget_attributes.attri_default_value','master_widget_attributes.attri_value_list, master_widget_attributes.is_mandatory, master_widget_attributes.file_driver, master_widget_attributes.attri_validation, master_widget_attributes.attri_slug_helps'),
								'join'=>array('', 'master_widget_attributes.widget_code = '.$this->_strTableName.'.id'),
								'where'=>array($this->_strTableName.'.company_code'=>$this->_intCompanyCode, 'master_widget_attributes.widget_code' => $pIntWidgetCode)
							);
		/* if limit is applied then do needful */
		if((int)$pIntLimit > 0){
			/* Applying the limits */
			$strFilterArr	= array_merge($strFilterArr, array('limit'=>$pIntLimit,'offset'=>0));
		}
							
		/* Get widget and attribute details */
		$strDataArr	= $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* if limit is applied then do needful */
		if((int)$pIntLimit > 0){
			/* Setting result set */
			$strReturnArr	= $strDataArr;
		}else{
			/* iterating the loop */
			foreach($strDataArr as $strDataArrKey => $strDataArrValue){
				/* Re-indexing the array */
				$strReturnArr[$strDataArrValue['attri_slug_key']]	= $strDataArrValue;
			}
		}
		/* Removed used variable */
		unset($strDataArr);
		 
		/* return widget and its attributes details */
		return $strReturnArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Get widget attributes multiple value / options list.
	/* Inputs 	: $pIntAttribuesCodeArr :: Widget attributes code array,
				: $pBlnDonNotInActiveRecords :: Default do not include deleted records.
	/* Returns	: Widget attribute options list array details.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getWidgetOptionsDetailsByWidgetAttributesCode($pIntAttribuesCodeArr = array(), $pBlnDonNotInActiveRecords = true){
		/* Variable Initialization */
		$strReturnArr	= array();

		/* if widget attributes code is not pass then do needful */
		if(empty($pIntAttribuesCodeArr)){
			/* return empty array */
			return $strReturnArr;
		}
		
		/* if request for not displaying the in-active records as well then do needful */
		if(!$pBlnDonNotInActiveRecords){
			/* Get widget attribute options details */
			$strReturnArr	= $this->_databaseObject->getDirectQueryResult('select id, attribute_code,description,default_value from master_widget_attributes_list where attribute_code in ('.implode(',',$pIntAttribuesCodeArr).') order by id');
		}else{
			/* Get widget attribute options details */
			$strReturnArr	= $this->_databaseObject->getDirectQueryResult('select id, attribute_code,description,default_value from master_widget_attributes_list where attribute_code in ('.implode(',',$pIntAttribuesCodeArr).') and deleted = 0 order by id');
		}
		 
		/* return widget and its attributes details */
		return $strReturnArr;
	}

	/***************************************************************************/
	/* Purpose	: Get validation pattern based on its elements type
	/* Inputs 	: $strAttriValidation :: Element type
	/* Returns	: Validation patterns
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getAttrValidation($strAttriValidation){
		$strPattern = "";
		
		/* check the validation value and return the pattern for that validation */
		switch($strAttriValidation){
			case 'string':
				$strPattern = "/^(\w+ ?)*$/";
				break;
			case 'notempty':
				$strPattern = "/[\s\S]/";
				break;			
			case 'numeric':
				$strPattern = "/^[0-9]*$/";
				break;
			case 'email':
				$strPattern = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix";
				break;
			case 'contact-no':
				$strPattern = "/^[0-9]*$/";
				break;
			case 'datetime':
				$strPattern = "/^[0-9\/]*$/";
				break;
			default:
				$strPattern = "/^(\w+ ?)*$/";
		}

		return $strPattern;
	}

	/***************************************************************************/
	/* Purpose	: Create BackUp Table
	/* Inputs 	: $tableNameStr :: Table Name
	/* Returns	: Boolean
	/* Created By : Vipin Kumar R. Jaiswar
	/***************************************************************************/
	public function setBackUpTableSchema($tableNameStr){
		/* Variable initialization */
		$tableNameLogStr 			= 	$tableNameStr.'_log';
		$logRecordedAtColumnName 	= 	'log_recorded_date';
		$logIdColumn 				= 	'log_id';
 		
		/* checking table exit */
		if ($this->_databaseObject->isTableExists($tableNameLogStr) ){
			/* do not do any thing */
			return false;
		}
		
		/* Drop table if exist log table */
		$blnTranscationStatus 		= 	$this->_databaseObject->getDirectQueryResult('DROP TABLE '. $tableNameLogStr);
		
		/* Creating the log table */
		$blnTranscationStatus 		= 	$this->_databaseObject->getDirectQueryResult('CREATE TABLE IF NOT EXISTS '. $tableNameLogStr .' LIKE '. $tableNameStr);
		/* if table is created then do needful */
		if ($blnTranscationStatus) {
			/* variable initialization */
			$strIndexQueryArr		= array();
			/* Getting the keys from table */
			$strIndexArr 			= 	$this->_databaseObject->getDirectQueryResult('SHOW INDEX FROM `'. $tableNameLogStr .'`',true);
			$strUniqueIndexArr		= array();
			/* if keys found then do needful */
			if (!empty($strIndexArr)) {
				/* Iterating the index loop */
				foreach ($strIndexArr as $rowShowIndex) {
					/* Setting the unique index, this helps to overcome with composite / multiple key */
					$strUniqueIndexArr[$rowShowIndex['Key_name']] = $rowShowIndex['Key_name'];
				}
				
				/* if unique array keys is not empty then do needful */
				if(!empty($strUniqueIndexArr)){
					/* iterating the loop */
					foreach($strUniqueIndexArr as $strUniqueIndexArrKey => $strUniqueIndexArrValue){
						/* checking for PRIMARY */
						if($strUniqueIndexArrKey == 'PRIMARY'){
							/* Drop the index */
							$this->_databaseObject->getDirectQueryResult('ALTER TABLE `' . $tableNameLogStr.'` DROP id');
						}else{
							/* Drop the all keys */
							$this->_databaseObject->getDirectQueryResult('DROP INDEX `' . $strUniqueIndexArrKey . '` ON ' . $tableNameLogStr);
						}
					}
				}
			}
			
			/* Creating drop index / keys */
			$changeColumnQueryStrNew = !empty($changeColumnQuery) ? implode(", ", $changeColumnQuery) : '';
			/* Droping all the keys and adding primary key in the log table */
			$this->_databaseObject->getDirectQueryResult('ALTER TABLE `' . $tableNameLogStr . '` ADD `'.$logIdColumn.'` BIGINT(20) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`'.$logIdColumn.'`)');
			/* Adding the record-date columns */
			$this->_databaseObject->getDirectQueryResult('ALTER TABLE ' . $tableNameLogStr . ' ADD ' . $logRecordedAtColumnName . "  BIGINT(20) NOT NULL DEFAULT '0', ADD INDEX `".$logRecordedAtColumnName."` (`".$logRecordedAtColumnName."`)");
			/* Adding the reference schema id columns */
			$this->_databaseObject->getDirectQueryResult("ALTER TABLE " . $tableNameLogStr . " ADD id BIGINT(20) NOT NULL DEFAULT '0' AFTER " . $logIdColumn . ", ADD INDEX `id` (`id`)");
			
			/* Return status */
			return true;
		}else{
			/* Nothing process */
			return false;
		}
	}

	/***************************************************************************/
	/* Purpose	: Create Trigger for Table
	/* Inputs 	: $tableNameStr :: Table Name
	/* Returns	: Boolean
	/* Created By : Vipin Kumar R. Jaiswar
	/***************************************************************************/
	public function setBackUpTableTrigger($tableNameStr){
		/* variable initialization */
		$tableNameLogStr 			= $tableNameStr.'_log';
		$logRecordedAtColumnName 	= 'log_recorded_date';
		$logIdColumn 				= 'log_id';
		$strColumnArr 				= $strOlderTableColumnArr	= array();
		$strTrigger  				= '';
		
		/* checking table not exit */
		if (!$this->_databaseObject->isTableExists($tableNameLogStr) ){
			/* Creating backup table */
			$this->setBackUpTableSchema($tableNameStr);
		}
		
		/* Get all columns or requested schema */
		$resultInfoArr = $this->_databaseObject->getDirectQueryResult('SHOW COLUMNS FROM ' . $tableNameLogStr);
 
		if (!empty($resultInfoArr)) {
			/* Droping the trigger if exists */
			$this->_databaseObject->getDirectQueryResult('DROP TRIGGER IF EXISTS `' . $tableNameStr . '_update_log`');
			
			/* Iterating the column array of respective schema */
			foreach ($resultInfoArr as $rowInfo) {
				$strColumnName	= $rowInfo['Field'];
				if($rowInfo['Field'] == "id"){
					$strColumnName	= 'log_id';
				}
				/* Setting the column arr */
				$strColumnArr[$rowInfo['Field']]			= '`'.$strColumnName.'`'; 
				/* Setting the column arr */
				$strOlderTableColumnArr[$rowInfo['Field']]	= 'OLD.`'.$rowInfo['Field'].'`'; 
			}
			/* Removed the primary key */
			unset($strColumnArr[$logIdColumn], $strOlderTableColumnArr[$logIdColumn]);
			
			/* Setting the record date */
			$strColumnArr['log_recorded_date']				= 'log_recorded_date';
			$strOlderTableColumnArr['log_recorded_date']	= "date_format(NOW(),'%Y%m%d%H%i%s')";
			/* Converting the column into string */
			$strColumn 			= implode(", ", $strColumnArr);
			/* Converting the insert column into string */
			$strInsertColumn	= implode(", ", $strOlderTableColumnArr);
			
			/* Creating the triggers */
			$strTrigger .= 'CREATE ';
			$strTrigger .= ' TRIGGER `' . $tableNameStr . '_update_log` BEFORE UPDATE ON `'. $tableNameStr .'` ';
			$strTrigger .= 'FOR EACH ROW BEGIN ';
			$strTrigger .= 'INSERT INTO ' . $tableNameLogStr . '( '.$strColumn. ") ";
			$strTrigger .= "VALUES( ".$strInsertColumn."); ";
			$strTrigger .= "END;";
			
			/* Creating trigger */
			$blnTriggerStatus	= $this->_databaseObject->getDirectQueryResult($strTrigger);
			
			/* removed used variables */
			unset($strColumnArr, $strOlderTableColumnArr);
			
			/* return the trigger status */
			return !empty($blnTriggerStatus) ? true : false;
		}
	}

	/***************************************************************************/
	/* Purpose	: Drop Trigger
	/* Inputs 	: $tableNameStr :: Table Name
	/* Returns	: Boolean
	/* Created By : Vipin Kumar R. Jaiswar
	/***************************************************************************/
	public function dropBackUpTableTrigger($tableNameStr){
		/* Variable initialization */
		$tableNameLogStr 			= $tableNameStr.'_log';
		/* Drop the trigger */
		$blnTriggerStatus			= $this->_databaseObject->getDirectQueryResult('DROP TRIGGER IF EXISTS `' . $tableNameStr . '_update_log`');
		/* return the trigger status */
		return (!empty($blnTriggerStatus)) ? true : false;
	}
}
