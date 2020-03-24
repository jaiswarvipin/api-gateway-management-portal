<?php
/****************************************************************************************************/
/* Purpose      : Managing the Apilist business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Apilist extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName = "";
	private $_strDataSet 	   = array();
	private $_strWidgeSlug	   = "";
	private $_strSchemaName		= "api_list";
	
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
		$this->_strDataSet		    = $pStrDataSetArr;
		$this->_strSchemaName		= $this->_strSchemaName.'_'.$this->getCompanyCode();
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
		/* Setting the view */
		$this->_strDataSet["actionHooksArr"][] 			= $this->load->view($this->setView("index"), $this->_strDataSet, true);
		$this->_strDataSet["customWidgetHooksArr"][] 	= $this->load->view($this->setView("task_details"), $this->_strDataSet, true);
		$this->_strDataSet["skipEncryptionArr"] 		= array('devops-user-story-id','api-document');
		
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
		/* Variable initlization */
		$strVertcialListArr	= $strUserListArr	= array();
		
		/* set the vertical data */
		$strVertcialListArr	= $this->_getVerticalList();
		/* Set the user list array */
		$strUserListArr		= $this->_getUserList();
		
		/* variable initialization */
		$strConfigArr["view"]	= array(
											"system-vertical" => array(
															"attri_slug_name"=>"System/Vertical",
															"attri_slug_key"=>"system-vertical",
															"attri_data_type"=>"dropdown",
															"attribute_code"=>"",
															"attri_value_list"=>$this->_objForm->getDropDown(getArrByKeyvaluePairs($strVertcialListArr,'id','name'),''),
															"is_mandatory"=>1,
															"attri_validation"=>"notempty",
															"schema_name"=>$this->_strSchemaName,
											),
											"delivery-leader-spoc-" => array(
															"attri_slug_name"=>"Delivery Leader(SPOC)",
															"attri_slug_key"=>"delivery-leader-spoc-",
															"attri_data_type"=>"dropdown",
															"attribute_code"=>"",
															"attri_value_list"=>$this->_objForm->getDropDown(getArrByKeyvaluePairs($strUserListArr,'id','user_name'),''),
															"is_mandatory"=>1,
															"attri_validation"=>"notempty",
															"schema_name"=>$this->_strSchemaName,
											),
											"secondary-spoc" => array(
															"attri_slug_name"=>"Secondary SPOC",
															"attri_slug_key"=>"secondary-spoc",
															"attri_data_type"=>"dropdown",
															"attribute_code"=>"",
															"attri_value_list"=>$this->_objForm->getDropDown(getArrByKeyvaluePairs($strUserListArr,'id','user_name'),''),
															"is_mandatory"=>1,
															"attri_validation"=>"notempty",
															"schema_name"=>$this->_strSchemaName,
											),
										);
		
		$strConfigArr["options_list"]	= array(
													"system-vertical"		=>getArrByKeyvaluePairs($strVertcialListArr,'id','name',true),
													"delivery-leader-spoc-"	=>getArrByKeyvaluePairs($strUserListArr,'id','user_name',true),
													"secondary-spoc"		=>getArrByKeyvaluePairs($strUserListArr,'id','user_name',true)
											   );
		
		/* removed used variable */
		unset($strVertcialListArr, $strUserListArr);

		/* if parent schema is passed then do needful */
		if(isset($this->_strDataSet["table"]) && ($this->_strDataSet["table"] != "")){
			/* Custom Query */
			$strConfigArr["customQuery"]	= array(
														"table"=>array($this->_strDataSet["table"],'master_user'),
														"column"=>array($this->_strDataSet["table"].".*",'master_user.user_name'),
														"join" => array('',$this->_strDataSet["table"].'.delivery-leader-spoc- = master_user.id'),
														"where"=>array('')
												);
		}
		
		/* return configuration set */
		return $strConfigArr;
		
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the vertical list.
	/*Inputs	: None.
	/*Returns	: Get the vertical list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getVerticalList(){
		/* variable initialization */
		$strReturnArr = array();
		
		/* creating the vertical helper object */
		$objVertical = new Vertical($this->_objDataOperation, $this->getCompanyCode());
		
		/* get the vertical data array */
		return $objVertical->getVerticalDetails();
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the user list.
	/*Inputs	: None.
	/*Returns	: Get the user list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserList(){
		/* variable initialization */
		$strReturnArr =  array();
		
		/* Get user list objects */
		$strReturnArr	= $this->_objDataOperation->getDirectQueryResult("select user_name, id from master_user where deleted = 0 order by user_name");
		
		/* return the user list array */
		return $strReturnArr;
	}
}