<?php 
/*******************************************************************************/
/* Purpose 		: Managing the Company verticals related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Vertical{
	private $_databaseObject			= null;
	private $_intCompanyCode			= 0;
	private $_strTableName				= "system_vertical";
	private $_strAssoactionTableName	= "trans_user_vertical_association";
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
		/* Setting the schema name */
		$this->_strTableName    = $this->_strTableName.'_'.$pIntCompanyCode;
	}
	
	/***************************************************************************/
	/* Purpose		: get vertical details by filer
	/* Inputs 		: pIntVerticalCodeArr	:: Vertical code array.
	/* Returns		: Vertical details.
	/* Created By	: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getVerticalDetails($pIntVerticalCodeArr = array()){
		/* Variable initialization */
		$strWhereArr	= array('company_code'=>$this->_intCompanyCode);
		
		/* if vertical code is passed then do needful */
		if(!empty($pIntVerticalCodeArr)){
			/* Setting Location id as filter details */
			$strWhereArr	= array_merge($strWhereArr , array($this->_strTableName.'.id' => $pIntVerticalCodeArr));
		}
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>$this->_strTableName,
									'where'=>$strWhereArr,
									'column'=>array('id', 'name'),
									'order'=>array('name'=>'asc')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	
	/***************************************************************************/
	/* Purpose		: get vertical user assocation details by user code
	/* Inputs 		: pIntUserCodeArr	:: User code array.
	/* Returns		: Vertical array.
	/* Created By	: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function getVerticalUserAssocationDetails($pIntUserCodeArr = array()){
		/* Variable initialization */
		$strWhereArr	= array('company_code'=>$this->_intCompanyCode);
		
		/* if user code is passed then do needful */
		if(!empty($pIntUserCodeArr)){
			/* Setting Location id as filter details */
			$strWhereArr	= array_merge($strWhereArr , array($this->_strAssoactionTableName.'.user_code' => $pIntUserCodeArr));
		}
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>$this->_strAssoactionTableName,
									'where'=>$strWhereArr,
									'column'=>array('vertical_code', 'user_code')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
}