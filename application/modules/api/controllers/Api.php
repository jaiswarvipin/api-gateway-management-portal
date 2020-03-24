<?php
/****************************************************************************************************/
/* Purpose      : Managing the Api business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Api extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName 		= "";
	private $_strDataSet 	   		= array();
	private $_strWidgeSlug	   		= "";
	private $_strSchemaName			= "api_collection";
	private $_strPolicySchemaName	= "policy";
	
	/**********************************************************************/
	/*Purpose       : Element initialization.
	/*Inputs        : $pStrModuleSlug :: Module slug,
					: $pStrDataSetArr :: Module specific data set .
	/*Created By    : Jaiswar Vipin Kumar R.
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
		
		/* Setting the view */
		$this->_strDataSet["actionHooksArr"][] 		= $this->load->view($this->setView("index"), $this->_strDataSet, true);
		$this->_strDataSet["customWidgetHooksArr"][] = $this->load->view($this->setView("index"), $this->_strDataSet, true);
		/* $this->_strDataSet["injectView"][] 			= $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		/* $this->_strDataSet["attri_config"][] 		= array(
																	"{SCHEME COLUMN NAME}" => array(
																					"custom_field"=>1,
																					"attri_slug_name"=>"{SCHEME COLUMN NAME}",
																					"attri_data_type"=>"{VALIDATION_TYPE}",
																					"attri_default_value"=>"",
																					"attri_value_list"=>"serialize(array())",
																					"is_mandatory"=>1,
																					"attri_validation"=>"{VALIDATION_TYPE}",
																					"schema_name"=>"{SCHEME NAME}",
																	)
																);*/
		
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
		
		/* Creating form object */
		$objForm	= new Form();
					
		/* variable initialization */
		$strConfigArr["exiting_attr"]	= array("policy" => array('attri_value_list'=>$objForm->getDropDown(getArrByKeyvaluePairs($this->_setPolicyList(),"id","name"),'',false,false)));
		
		/* if parent schema is passed then do needful */
		if(isset($this->_strDataSet["table"]) && ($this->_strDataSet["table"] != "")){
			/* Custom Query */
			$strConfigArr["customQuery"]	= array(
														"table"=>$this->_strDataSet["table"],
														"column"=>array(),
														"where"=>array(),
												);
		}
		
		/* removed used variables */
		unset($objForm);
		
		/* return configuration set */
		return $strConfigArr;
		
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the policy list.
	/*Inputs	: None.
	/*Returns	: Policy list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setPolicyList(){
		/* variable list */
		$this->_strPolicySchemaName	= $this->_strPolicySchemaName."_".$this->getCompanyCode();
		
		/* get the policy list */
		$strEventFilterArr 	= array(
										'table' 	=> $this->_strPolicySchemaName,
										'column' 	=> array('id','name'),
										'order'		=> array('name'=>'asc')
									);
		
		/* getting record  */
		$strResultArr 		= $this->_objDataOperation->getDataFromTable($strEventFilterArr);
		
		/* return the policy */
		return $strResultArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the module data.
	/*Inputs	: None.
	/*Returns	: Operation status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getModuleDetailsByCode(){
		/* if data value found then do needful */
		if(!empty($this->_strDataSet[0])){
			/* creating the array */
			$strPolicyArr	= explode(",",$this->_strDataSet[0]['policy']);
			/* iterating the array */
			foreach($strPolicyArr as $strPolicyArrKey => $strPolicyArrValue){
				/* seting decoded value */
				$strPolicyArr[$strPolicyArrKey]	= getEncyptionValue($strPolicyArrValue);
			}
			/* Value overriding */
			$this->_strDataSet[0]['policy']	= $strPolicyArr;
		}
		
		/* return the value */
		return $this->_strDataSet;
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the API Collection DML operation.
	/*Inputs	: None.
	/*Returns	: Operation status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setModuleData(){
		/* variable initialization */
		$this->_strSchemaName	.= "_".$this->getCompanyCode();
		$intRespone				= "";
		
		/* Setting the Pokicy filed */
		if((isset($this->_strDataSet['policy'])) && (!empty($this->_strDataSet['policy']))){
			/* Iterating the loop */
			foreach($this->_strDataSet['policy'] as $strPlicyKey => $strPolicyValue){
				/* Decoding the value */
				$this->_strDataSet['policy'][$strPlicyKey]	= getDecyptionValue($strPolicyValue);
			}
			/* Imploding the value */
			$this->_strDataSet['policy']		= implode(",",$this->_strDataSet['policy']);
		}else{
			/* Setting the value */
			$this->_strDataSet['policy']	= "";
		}
		
		/* Set the operation filter array */
		$strEventFilterArr 	= array(
										'table' 	=> $this->_strSchemaName,
										'data' 		=> $this->_strDataSet,
										'where' 	=> array('id' => $this->_strDataSet['foreign']),
									);
		/* removed unwanted variables */
		unset($strEventFilterArr['data']['foreign']);
		
		/* Perfomaning the addition operation */
		if($this->_strDataSet['foreign'] == 0){
			/* add new records */
			$intResultStatus = $this->_objDataOperation->setDataInTable($strEventFilterArr);
		/* Perfomaning the update operation */
		}else{
			/* update the existing records */
			$intResultStatus = $this->_objDataOperation->setUpdateData($strEventFilterArr);
		}
		/* removed used variables */
		unset($strResultArr);
		
		/* return the operation status */
		return $intResultStatus;
	}
}