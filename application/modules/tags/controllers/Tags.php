<?php
/****************************************************************************************************/
/* Purpose      : Managing the Tags business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Tags extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName = "";
	private $_strDataSet 	   = array();
	private $_strWidgeSlug	   = "";
	private $_strSchemaName	   = "trans_social_event_feeds_wall_grid_config";
	
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
				//return $this->_strDataSet;
		}
		
		/* checking for event details is not empty */
		if (!empty($this->_strDataSet) && (isset($this->_strDataSet['dataSet'])) && !empty($this->_strDataSet['dataSet'][0]['name'])) {
			/* Setting event details */
			$this->_strDataSet['moduleTitle'] = '<a href="'.SITE_URL.'mod/event-wall">'.$this->_strDataSet['dataSet'][0]['name'] . '</a> > ' .$this->_strDataSet['moduleTitle'];
		}
		
		/* Return the dataset */
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
		/* debugVar(unserialize(LEAD_ATTRIBUTE_INPUT_VALIDATION), true); */
		
		/* variable initialization */
		$strConfigArr['view']	= array(
									'event_code' => array(
													'custom_field'=>1,
													'attri_slug_key'=>'event_code',
													'attri_slug_name'=>'event_code',
													'attri_data_type'=>'',
													'attri_default_value'=>'',
													'attri_value_list'=>serialize(array()),
													'is_mandatory'=>0,
													'attri_validation'=>'numeric',
													'schema_name'=>$this->_strDataSet['table'],
													'alise'=>'event-code',
									),
								);
		
		/* if parent schema is passed then do needful */
		if(isset($this->_strDataSet["table"]) && ($this->_strDataSet["table"] != "")){
			/* Custom Query */
			$strConfigArr["customQuery"]	= array(
														"table"=>array($this->_strDataSet["table"],'events_1'),
														"join"=>array("",$this->_strDataSet["table"].'.event_code = events_1.id'),
														"column"=>array('events_1.id as event_code','events_1.name'),
														"where"=>array()
												);
		}
		
		/* return configuration set */
		return $strConfigArr;
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Set tag with event code.
	/*Inputs	: None.
	/*Returns	: Operation status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setModuleData(){
		/* variable initialization */
		$strEventSchema	= 'tags_'.$this->getCompanyCode();
		$intTagCode		= isset($this->_strDataSet['foreign'])?$this->_strDataSet['foreign']:0;
		$intEventCode	= ($this->input->get('event-code'))? getDecyptionValue($this->input->get('event-code')) : 0 ;
		$strDataArr		= array();
		
		if($intEventCode > 0){
			$strDataArr		= array('data'=>array('event_code'=>$intEventCode));
		}
		
		/* Checking for event code */
		if(($intEventCode == 0) || (empty($strDataArr)) || (!isset($strDataArr['data']))){
			/* do not process ahead */
			return false;
		}
		
		/* Creating filter array */
		$strEventFilterArr 	= array(
										'table' 	=> $strEventSchema,
										'data' 		=> $strDataArr['data'],
										'where' 	=> array('id' => $intTagCode),
									);
		/* update the existing records */
		$intResultStatus	= $this->_objDataOperation->setUpdateData($strEventFilterArr);
		
		/* removed used variables */
		unset($strEventDataArr);
		
		/* return the operation status */
		return $intResultStatus;
	}
}
