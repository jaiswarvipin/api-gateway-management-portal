<?php
/****************************************************************************************************/
/* Purpose      : Managing the Policy business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Policy extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName = "";
	private $_strDataSet 	   = array();
	private $_strWidgeSlug	   = "";
	private $_strSchemaName		= "{SCHEMA NAME}";
	
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
		
		/* Checking for data value */
		if((isset($this->_strDataSet['dataSet'])) && (!empty($this->_strDataSet['dataSet']))){
			/* iterating the value */
			foreach($this->_strDataSet['dataSet'] as $strIndex => $strValueArr){
				/* chceking for auth config value set */
				if(($strValueArr['auth-type-json-config'] !="") &&(isJSON($strValueArr['auth-type-json-config']))){
					/* Setting the value */
					$this->_strDataSet['dataSet'][$strIndex]['auth-type-json-config']	= '<i class="material-icons Tiny">check</i>';
				}
				/* chceking for request header config value set */
				if(($strValueArr['request-header'] !="") && (isJSON($strValueArr['request-header']))){
					/* Setting the value */
					$this->_strDataSet['dataSet'][$strIndex]['request-header']	= '<i class="material-icons Tiny">check</i>';
				}
				/* chceking for request header config value set */
				if(($strValueArr['request-body-'] !="") && (isJSON($strValueArr['request-body-']))){
					/* Setting the value */
					$this->_strDataSet['dataSet'][$strIndex]['request-body-']	= '<i class="material-icons Tiny">check</i>';
				}
				/* chceking for response config value set */
				if(($strValueArr['response'] !="") && (isJSON($strValueArr['response']))){
					/* Setting the value */
					$this->_strDataSet['dataSet'][$strIndex]['response']	= '<i class="material-icons Tiny">check</i>';
				}
				/* chceking for moc-response config value set */
				if(($strValueArr['moc-response'] !="") && (isJSON($strValueArr['moc-response']))){
					/* Setting the value */
					$this->_strDataSet['dataSet'][$strIndex]['moc-response']	= '<i class="material-icons Tiny">check</i>';
				}
			}
		}
		
		/* Start - Data manipulation logic will go here */
		/* End   - Data manipulation logic will go here */
		
		/* Setting the view */
		/* $this->_strDataSet["actionHooksArr"][] 		= $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		/* $this->_strDataSet["customWidgetHooksArr"][] = $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		/* $this->_strDataSet["injectView"][] 			= $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		/* $this->_strDataSet["attri_config"][] 			= array(
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
																);
		*/
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
		$strConfigArr["view"]	= array(
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
										);
										
		/* if parent schema is passed then do needful */
		if(isset($this->_strDataSet["table"]) && ($this->_strDataSet["table"] != "")){
			/* Custom Query */
			$strConfigArr["customQuery"]	= array(
														"table"=>$this->_strDataSet["table"],
														"column"=>array(),
														"where"=>array(),
												);
		}
		
		/* return configuration set */
		return $strConfigArr;
		
	}
}