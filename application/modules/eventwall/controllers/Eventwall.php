<?php
/****************************************************************************************************/
/* Purpose      : Managing the Eventwall business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Eventwall extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName 		= "";
	private $_strDataSet 	   		= array();
	private $_strWidgeSlug	   		= "";
	private $_strSchemaName	   		= "trans_social_event_feeds_wall_grid_config";
	private $_strRoleEventAssName	= "trans_role_event_assocation";
	
	/**********************************************************************/
	/*Purpose       : Element initialization.
	/*Inputs        : $pStrModuleSlug :: Module slug,
					: $pStrDataSetArr :: Module specific data set .
	/*Created By    : Vipin Kumar R. Jaiswar.
	/**********************************************************************/
	public function __construct($pStrModuleSlug = array(), $pStrDataSetArr = array()){
		/* calling parent construct */
		parent::__construct($pStrModuleSlug[0]);
		
		/* Variable initialization */
		$this->_strClassFileName	= $pStrModuleSlug[0];
		$this->_strWidgeSlug		= $pStrModuleSlug[1];
		$this->_strDataSet			= $pStrDataSetArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the default hooked method.
	/*Inputs	: None.
	/*Returns	: Data Set.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index($pDataArr = array()){ 
	    /* if empty result set pass then do needful */
        if(empty($this->_strDataSet)){
                /* Return empty result set */
                return $this->_strDataSet;
        }		
		
		/* Start - Data manipulation logic will go here */
		/* End   - Data manipulation logic will go here */
		
		$this->_strDataSet["actionHooksArr"][] 			= $this->load->view($this->setView("index"), $this->_strDataSet, true);
		$this->_strDataSet["customWidgetHooksArr"][] 	= $this->load->view($this->setView("tags"), $this->_strDataSet, true);
		
		/* Return the dataset */
		return $this->_strDataSet;
	}
	
	/**********************************************************************/
	/*Purpose 	: Convert DB Datetime to user datetime format
	/*Inputs	: None.
	/*Returns	: Data Set.
	/*Created By: Vipin Kumar R. Jaiswar
	/**********************************************************************/
	public function getModuleDetailsByCode(){
		/* checking for data set index */
		if(isset($this->_strDataSet[0])){
			/* update from date */
			$this->_strDataSet[0]['from-date'] 	= 	( !empty($this->_strDataSet) && !empty($this->_strDataSet[0]) && !empty(!empty($this->_strDataSet[0]['from-date']))) ? getDateFormat($this->_strDataSet[0]['from-date'],5) : '0000/00/00';
			/* update to date */
			$this->_strDataSet[0]['to-date'] 	= 	( !empty($this->_strDataSet) && !empty($this->_strDataSet[0]) && !empty(!empty($this->_strDataSet[0]['to-date']))) ? getDateFormat($this->_strDataSet[0]['to-date'],5) : '0000/00/00';
		}
		/* return updated dataset */
		return $this->_strDataSet;
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the custom widget custom configuration.
	/*Inputs	: None.
	/*Returns	: Configuration dataset.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setConfiguration(){
		/* Check the attributes type supported by system */
		/* debugVar(unserialize(LEAD_ATTRIBUTE_INPUT_ELEMENT), true); */
		
		/* Check the attributes validation type supported by system */
		/* debugVar(serialize(LEAD_ATTRIBUTE_INPUT_VALIDATION), true); */
		
		/* variable initialization */
		$strConfigArr["view"]	= array(
											'event_public_code' => array(
															'custom_field'=>1,
															'attri_slug_key'=>'event_public_code',
															'attri_slug_name'=>'Public Code',
															'attri_data_type'=>'textbox',
															'attri_default_value'=>rand(10000,99999),
															'attri_value_list'=>serialize(array()),
															'is_mandatory'=>1,
															'attri_validation'=>'string',
															'schema_name'=>'events_'.$this->getCompanyCode(),
															'show_in_list'=>true,
											),
											'rows' => array(
															'custom_field'=>1,
															'attri_slug_key'=>'rows',
															'attri_slug_name'=>'rows',
															'attri_data_type'=>'textbox',
															'attri_default_value'=>'',
															'attri_value_list'=>serialize(array()),
															'is_mandatory'=>1,
															'attri_validation'=>'numeric',
															'schema_name'=>$this->_strSchemaName,
											),
											'columns' => array(
															'custom_field'=>1,
															'attri_slug_key'=>'columns',
															'attri_slug_name'=>'columns',
															'attri_data_type'=>'textbox',
															'attri_default_value'=>'',
															'attri_value_list'=>serialize(array()),
															'is_mandatory'=>1,
															'attri_validation'=>'numeric',
															'schema_name'=>$this->_strSchemaName
											),
										);
		
		/* variable initialization - alise*/
		$strConfigArr["alise"]	= array(
											'event_public_code'=>'Event Code'
										);
		
		/* if parent schema is passed then do needful */
		if(isset($this->_strDataSet["table"]) && ($this->_strDataSet["table"] != "")){
			/* If Super Administrator */
			if (!empty($this->getAdminFlag())) {
				/* Custom Query  */
				$strConfigArr["customQuery"]	= array(
														"table"=>array($this->_strDataSet["table"],$this->_strSchemaName),
														"join"=>array("", array('table' => $this->_strDataSet["table"].'.id = '.$this->_strSchemaName.'.event_code', 'type' => 'left')),
														"column"=>array($this->_strSchemaName.'.rows',$this->_strSchemaName.'.columns',$this->_strDataSet["table"].'.event_public_code'),
														"where"=>array()
													);
			}else{
				/* Custom Query  */
				$strConfigArr["customQuery"]	= array(
															"table"=>array($this->_strDataSet["table"],$this->_strSchemaName, $this->_strRoleEventAssName),
															"join"=>array("",array('table' => $this->_strDataSet["table"].'.id = '.$this->_strSchemaName.'.event_code', 'type'=>'left'), array('table' => $this->_strRoleEventAssName.'.event_code =' . $this->_strDataSet["table"].'.id', 'type'=>'left')),
															"column"=>array($this->_strSchemaName.'.rows',$this->_strSchemaName.'.columns',$this->_strDataSet["table"].'.event_public_code'),
															"where"=>array($this->_strRoleEventAssName.'.role_code' => $this->getRoleCode())
													);
			}
		}
		
		/* return configuration set */
		return $strConfigArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the event all grid matrix.
	/*Inputs	: None.
	/*Returns	: Operation status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setModuleData(){
		/* variable initialization */
		$strEventSchema	= 'events_'.$this->getCompanyCode();
		$intEventCode	= isset($this->_strDataSet['foreign'])?$this->_strDataSet['foreign']:0;
		$strDataArr		= isset($this->_strDataSet[$this->_strSchemaName])?$this->_strDataSet[$this->_strSchemaName]:array();
		$strEventDataArr= isset($this->_strDataSet[$strEventSchema])?$this->_strDataSet[$strEventSchema]:array();
		/* Removed no needed variable */
		unset($this->_strDataSet[$strEventSchema], $this->_strDataSet[$this->_strSchemaName]);
		/* added form elements */
		$strEventDataArr['data'] = array_merge($strEventDataArr['data'], $this->_strDataSet);
		/* remove the unwanted keys */
		unset($strEventDataArr['data']['foreign']);
		
		/* Get the user feed character limit */
		$userAndCustomTextFeedCharLimit 	= getUserAndCustomTextFeedCharLimit();
		$publicEventCodeChar 				= (int)$userAndCustomTextFeedCharLimit['user_feed']['char'];

		$strEventDataArr['data']['event_public_code'] = trim($strEventDataArr['data']['event_public_code']);

		if (!empty($strEventDataArr) && !empty($strEventDataArr['data']['event_public_code'])) {

			$strEventFilterArr 	= array(
				'table' 	=> $strEventSchema,
				'column' 	=> 	array( 'COUNT(id) AS event_count' ),
				'where' 	=> array('event_public_code' => $strEventDataArr['data']['event_public_code']),
			);

			if (!empty($intEventCode)) {
				$strEventFilterArr['where']['id != '] = $intEventCode;
			}

			/* getting record  */
			$strUniqueResultArr 	= $this->_objDataOperation->getDataFromTable($strEventFilterArr);
			if (!empty($strUniqueResultArr) && $strUniqueResultArr[0] && $strUniqueResultArr[0]['event_count']) {
				jsonReturn(array('status'=>0,'message'=>'Event Public Code not unique in system!'), true);
			}

			if (strlen($strEventDataArr['data']['event_public_code']) > $publicEventCodeChar) {
				jsonReturn(array('status'=>0,'message'=>'Event Public Code not more than '. $publicEventCodeChar .'!'), true);
			}
		}
		
		/* Checking for event code */
		if(($intEventCode == 0) || (empty($strDataArr)) || (!isset($strDataArr['data']))){
			/* do not process ahead */
			return false;
		}
		
		if (!empty($strDataArr['data'])) {
			$strDataArr['data']['event_code'] = $intEventCode;
		}

		/* Creating filter array */
		$strEventFilterArr 	= array(
										'table' 	=> $this->_strSchemaName,
										'data' 		=> $strDataArr['data'],
										'where' 	=> array('event_code' => $intEventCode),
									);
		/* getting record  */
		$strResultArr 		= $this->_objDataOperation->getDataFromTable($strEventFilterArr);
		
		/* checking for event wall matrix data set */
		if(!empty($strResultArr)){
			/* update the existing records */
			$intResultStatus = $this->_objDataOperation->setUpdateData($strEventFilterArr);
		}else{
			/* add new records */
			$intResultStatus = $this->_objDataOperation->setDataInTable($strEventFilterArr);
		}	
		/* removed used variables */
		unset($strResultArr);
		
		/* Creating filter array */
		$strEventFilterArr 	= array(
										'table' => $strEventSchema,
										'data' 	=> $strEventDataArr['data'],
										'where' => array('id' => $intEventCode),
									);
								
		/* getting record  */
		$intResultStatus	= $this->_objDataOperation->setUpdateData($strEventFilterArr);
		 
		/* removed used variables */
		unset($strEventDataArr);
		
		/* return the operation status */
		return $intResultStatus;
	}
}