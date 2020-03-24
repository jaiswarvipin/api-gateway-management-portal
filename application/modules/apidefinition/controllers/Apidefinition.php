<?php
/****************************************************************************************************/
/* Purpose      : Managing the Apidefinition business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Apidefinition extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName = "";
	private $_strDataSet 	   = array();
	private $_strWidgeSlug	   = "";
	private $_strSchemaName	   = "api_definition";
	
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
		
		/* Variable initilization */
		$strQueryStringArr		= $this->input->get();
		$intApiConncetionCode	= (isset($strQueryStringArr['aPiCoLlEcTiOnCoDe']))?getDecyptionValue($strQueryStringArr['aPiCoLlEcTiOnCoDe']):0;
		
		/* if not api collection value then do needful */
		if($intApiConncetionCode == 0){
			/* redirecting to parent page */
			redirect(SITE_URL.'mod/api');
		}
		
		/* Get the API collection details */
		$strAPICollectionInformation	= $this->_getAPiCollectionDetails($intApiConncetionCode);
		
		/* if details not found then do needful */
		if(empty($strAPICollectionInformation)){
			/* redirecting to parent page */
			redirect(SITE_URL.'mod/api');
		}
		
		/* Start - Data manipulation logic will go here */
		$this->_strDataSet['moduleTitle']	= "<a href=".SITE_URL."mod/api>\"".$strAPICollectionInformation[0]['api-collection-name'].'\'s</a>" Operation List';
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
		/* Variable initilization */
		$strQueryStringArr		= $this->input->get();
		$intApiConncetionCode	= (isset($strQueryStringArr['aPiCoLlEcTiOnCoDe']))?getDecyptionValue($strQueryStringArr['aPiCoLlEcTiOnCoDe']):0;
		
		/* if not api collection value then do needful */
		if($intApiConncetionCode == 0){
			/* redirecting to parent page */
			redirect(SITE_URL.'mod/api');
		}
		
		/* variable initialization */
		$strConfigArr["view"]	= array(
											"aPiCoLlEcTiOnCoDe" => array(
															"custom_field"=>1,
															"attri_visiable"=>false,
															"attri_slug_name"=>"id",
															"alise"=>"aPiCoLlEcTiOnCoDe",
															"attri_slug_key"=>"api_collection_code",
															"attri_data_type"=>"dropdown",
															"attri_default_value"=>"",
															"attri_slug_helps"=>"",
															"attri_value_list"=>"serialize(array())",
															"is_mandatory"=>1,
															"attri_validation"=>"{VALIDATION_TYPE}",
															"schema_name"=>"{SCHEME NAME}",
											)
										);
		
		/* if parent schema is passed then do needful */
		if(isset($this->_strDataSet["table"]) && ($this->_strDataSet["table"] != "")){
			//$intAPICollectionCode
			/* Custom Query */
			$strConfigArr["customQuery"]	= array(
														"table"=>$this->_strDataSet["table"],
														"column"=>array(),
														"where"=>array('api_collection_code'=>$intApiConncetionCode)
												);
		}
		
		/* return configuration set */
		return $strConfigArr;
		
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
		$strQueryStringArr		= $this->input->get();
		$intApiConncetionCode	= (isset($strQueryStringArr['aPiCoLlEcTiOnCoDe']))?getDecyptionValue($strQueryStringArr['aPiCoLlEcTiOnCoDe']):0;
		
		/* if not api collection value then do needful */
		if($intApiConncetionCode == 0){
			jsonReturn(array('status'=>0,'message'=>'Look like some one trying th temper the request.','destinationURL'=>SITE_URL), true);
		}
		
		/* Setting the api collection code */
		$this->_strDataSet['api_collection_code']	= $intApiConncetionCode;
		
		/* Set the operation filter array */
		$strEventFilterArr 	= array(
										'table' 	=> $this->_strSchemaName,
										'data' 		=> $this->_strDataSet,
										'where' 	=> array('api_collection_code' => $intApiConncetionCode, 'id'=>$this->_strDataSet['foreign']),
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
	
	
	/**********************************************************************/
	/*Purpose 	: Get the APi Collection information.
	/*Inputs	: $pIntAPICollectionCode :: API Collection Details.
	/*Returns	: API Collection information.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getAPiCollectionDetails($pIntAPICollectionCode = 0){
		/* variable list */
		$strResultArr	= array();
		
		/* if API collection id is not passed */
		if($pIntAPICollectionCode == 0){
			/* default return */
			return $strResultArr;
		}
		
		/* get the api collcetion  list */
		$strFilterArr 		= array(
										'table' 	=> "api_collection_".$this->getCompanyCode(),
										'column' 	=> array('id','api-collection-name'),
										'where'		=> array('id'=>$pIntAPICollectionCode)
									);
		
		/* getting record  */
		$strResultArr 		= $this->_objDataOperation->getDataFromTable($strFilterArr);
		
		/* return the policy */
		return $strResultArr;
	}
}