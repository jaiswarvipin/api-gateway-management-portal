<?php 
/*******************************************************************************/
/* Purpose 		: Managing the modules related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Status{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_widget_attributes_list";
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code
	/* Returns	: None.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Get delivery status list.
	/*Inputs	: none.
	/*rReturns	: Delivery status list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getDeliveryStatusList(){
		/* variable initialization */
		$strReturnArr	= $strQueryArr	=  array();
		
		/* Creating query array */
		$strQueryArr	= array(
									'table'=>$this->_strTableName,
									'column'=>array('id','description'),
									'where'=>array('attribute_code'=>DELIVERY_STATUS_WIDGET_ATTR_CODE),
							   );
							   
		/* Get data from dataset */
		$strResultSetArr	= $this->_databaseObject->getDataFromTable($strQueryArr);
		/* removed used variable */
		unset($strQueryArr);
		
		/* if status found teh do needful */
		if(!empty($strResultSetArr)){
			/* Iterting the loop */
			foreach($strResultSetArr as $strResultSetArrKey => $strResultSetArrValue){
				/* setting the key value paris */
				$strReturnArr['keyvalue'][$strResultSetArrValue['id']]	= $strResultSetArrValue['description'];
			}
		}
		/* set the default result set */
		$strReturnArr['defaultvalue']	= $strResultSetArr;
		/* removed used variable */
		unset($strResultSetArr);
		
		/* return the status result set */
		return $strReturnArr;
	}
}