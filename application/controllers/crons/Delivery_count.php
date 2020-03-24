<?php
/***********************************************************************/
/* Purpose 		: Setting the delivery count by prposed planned and by DevOps.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_count extends Requestprocess {
	/* variable deceleration */
	public  $_objDataOperation		= null;
	private $_strPrimaryTableName	= 'master_user';
	private $_strSecondayTableName	= 'api_list';
	private $_strVerticalTableName	= 'system_vertical';
	private $_strWidgetAttTableName	= 'master_widget_attributes_list';
	private $_strWorkItemTableName	= 'trans_devops_workitem_status';
	private $_intMonth				= 1;
	private $_strDateRangArr		= array();
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		
		/* Set the schema */
		$this->_strSecondayTableName	= $this->_strSecondayTableName.'_'.DEFAULT_COMPANY_CODE;
		$this->_strVerticalTableName	= $this->_strVerticalTableName.'_'.DEFAULT_COMPANY_CODE;
		
		/* if from month pass then do needful */
		if((isset($_REQUEST['intMonth'])) && ($_REQUEST['intMonth'] !='')){
			/* Set the month index */
			$this->_intMonth	= $_REQUEST['intMonth'];
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Returns	: Execute the query.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* Setting the delivery count based on proposed API's at starting of the year/quaters */
		$strResponseArr = $this->_getDeliveryCountOf60DaysReport();
		/* Setting the delivery status */
		$this->_getDeliveryReport();
	}
	
	/**********************************************************************/
	/*Purpose 	: Get delivery status report of 60 days.
	/*Inputs	: none.
	/*rReturns	: Delivery stat.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getDeliveryCountOf60DaysReport(){
		/* variable initialization */
		$strWhereArr 			= $strQueryArr	=  $strFormattedArr = $strDataArr	= array();
		$intFromMonth			= (date('m')-3);
		$intToMonth				= (date('m'));
		
		$intFromDate			= getDates(-90,8).'01';
		$intToDate				= getDates(90,8).'31';
		
		$strPrevioisDateRangArr	= getDatesIntervalByQauter(getQauter($intFromMonth));
		$this->_strDateRangArr	= getDatesIntervalByQauter(getQauter());
		
		/* if previous date not found the do needful */
		if(!empty($strPrevioisDateRangArr)){
			/* Set the from date */
			$this->_strDateRangArr['from']	= $strPrevioisDateRangArr['from'];
		}
		
		
		/* setting the filter */
		$strWhereArr	= array($this->_strSecondayTableName.'.type'=>array(23),$this->_strSecondayTableName.'.`go-live-date` >='=>$this->_strDateRangArr['from'],$this->_strSecondayTableName.'.`go-live-date` <='=>$this->_strDateRangArr['to']);
		
		/* Creating query array */
		$strQueryArr	= array(
									'table'=>array($this->_strPrimaryTableName, $this->_strSecondayTableName,$this->_strVerticalTableName,$this->_strWidgetAttTableName),
									'join' => array('', $this->_strPrimaryTableName.'.id = '.$this->_strSecondayTableName.'.delivery-leader-spoc-',$this->_strSecondayTableName.'.system-vertical = '.$this->_strVerticalTableName.'.id',$this->_strSecondayTableName.'.status='.$this->_strWidgetAttTableName.'.id'),
									'column'=>array($this->_strPrimaryTableName.'.id as user_code',$this->_strVerticalTableName.'.id as vertical_code',$this->_strSecondayTableName.'.status as status_code','count('.$this->_strSecondayTableName.'.id) as totalDelivery','date_format('.$this->_strSecondayTableName.'.`go-live-date`,"%Y%m28") as deliveryMonth'),
									'where'=>$strWhereArr,
									'group'=>array($this->_strPrimaryTableName.'.id',$this->_strVerticalTableName.'.id',$this->_strSecondayTableName.'.status',$this->_strSecondayTableName.'.status','month('.$this->_strSecondayTableName.'.`go-live-date`)'),
									'order'=>array('5'=>'asc')
							   );
			
		/* Get data from dataset */
		$strFormattedArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		
		/* if data found then do needful */
		if(!empty($strFormattedArr)){
			/* Delete the data of above date range */
			$this->_objDataOperation->getDirectQueryResult('DELETE FROM trans_delivery_count WHERE delivery_month >='.$intFromDate.' AND delivery_month <='.$intToDate);
			/* iterating the loop */
			foreach($strFormattedArr as $strFormattedArrKey => $strFormattedArrValues){
				/* Set the insert array data set */
				$strDataArr[]	= array(
											'user_code'=>$strFormattedArrValues['user_code'],
											'vertical_code'=>$strFormattedArrValues['vertical_code'],
											'status_code'=>$strFormattedArrValues['status_code'],
											'delivery_count'=>$strFormattedArrValues['totalDelivery'],
											'delivery_month'=>$strFormattedArrValues['deliveryMonth'],
											'record_date'=>date('Ymd')
										);
			}
			/* Bulk insert */
			$this->_objDataOperation->setBulkInset(array('table'=>'trans_delivery_count','data'=>$strDataArr));
			/* removed used variables */
			unset($strDataArr, $strFormattedArr);
		}
		
		/* removed used variable */
		unset($strWhereArr,$strQueryArr,$strResultSetArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get delivery status report.
	/*Inputs	: none.
	/*rReturns	: Delivery stat.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getDeliveryReport(){
		/* $Widget	= new Widget($this->_objDataOperation, $this->getCompanyCode());
		$Widget->setBackUpTableTrigger('server_list_3');exit; */
		/* variable initialization */
		$strWhereArr 			= $strQueryArr	=  $strFormattedArr = $strDeliveryDateArr = array();
		$intFromMonth			= (date('m')-3);
		$intToMonth				= (date('m')+3);
		$strStatusArr			= (array)json_decode(STATUS_GROUP_ARR);
		
		$intFromDate			= getDates(-90,8).'01';
		$intToDate				= getDates(90,8).'31';
		
		/* Get the dates */
		$strPrevioisDateRangArr	= getDatesIntervalByQauter(getQauter($intFromMonth),$intFromMonth);
		$this->_strDateRangArr	= getDatesIntervalByQauter(getQauter($intToMonth),$intToMonth);
		
		/* if previous date found then do needful */
		if(isset($strPrevioisDateRangArr['from'])){
			/* set the from date */
			$this->_strDateRangArr['from']	= $strPrevioisDateRangArr['from'];
		}
		
		/* if status error is not empty then do needful */
		if(!empty($strStatusArr)){
			/* setting default values */
			$intDiffColumnName	= '';
			$intDateColumnName	= '';
			
			/* iterating the status loop */
			foreach($strStatusArr as $strStatusArrKey => $strStatusArrValues){
				/* based on the case do needful */
				switch($strStatusArrKey){
					case 'dev':
						$intDiffColumnName = 'dev_diff';
						$intDateColumnName = '`dev-closure-date`';
						break;
					case 'it_uat':
						$intDiffColumnName = 'it_uat_diff';
						$intDateColumnName = '`uat-released-date`';
						break;
					case 'biz_uat':
						$intDiffColumnName = 'biz_uat_diff';
						$intDateColumnName = '`biz-uat-release-date`';
						break;
					case 'live':
						$intDiffColumnName = 'live_diff';
						$intDateColumnName = '`go-live-date`';
						break;
					case 'Hold/Drop':
					case 'Dev Not Started':
						$intDiffColumnName = '-10000.00';
						$intDateColumnName = '`go-live-date`';
						break;
				}
				
				/* Creating query array */
				$strQueryArr	= array(
											'table'=>array($this->_strSecondayTableName,$this->_strWorkItemTableName),
											'join' =>array('', $this->_strSecondayTableName.'.`devops-user-story-id` = '.$this->_strWorkItemTableName.'.user_story_id'),
											'column'=>array($this->_strSecondayTableName.'.`delivery-leader-spoc-`',$this->_strSecondayTableName.'.`system-vertical`',$this->_strSecondayTableName.'.status','count('.$this->_strSecondayTableName.'.id) as total_delivery','AVG('.$intDiffColumnName.') as avg_diff', 'year('.$this->_strSecondayTableName.'.'.$intDateColumnName.') as delivery_year', 'month('.$this->_strSecondayTableName.'.'.$intDateColumnName.') as delivery_month','date_format('.$this->_strSecondayTableName.'.'.$intDateColumnName.',"%Y%m28") as delivery_date'),
											'where'=>array($this->_strSecondayTableName.'.type'=>array(23), $this->_strSecondayTableName.'.'.$intDateColumnName.' >='=>$this->_strDateRangArr['from'],$this->_strSecondayTableName.'.'.$intDateColumnName.' <='=>$this->_strDateRangArr['to'],'status'=>$strStatusArrValues),
											'group'=>array($this->_strSecondayTableName.'.`delivery-leader-spoc-`',$this->_strSecondayTableName.'.`system-vertical`',$this->_strSecondayTableName.'.status','year('.$this->_strSecondayTableName.'.'.$intDateColumnName.')','month('.$this->_strSecondayTableName.'.'.$intDateColumnName.')'),
											'order'=>array('month('.$this->_strSecondayTableName.'.'.$intDateColumnName.')'=>'asc')
									   );
								   
								   
				/* executing the query */
				$strDataSetArr 	= $this->_objDataOperation->getDataFromTable($strQueryArr);
				
				/* if data array found then do needful */
				if(!empty($strDataSetArr)){
					/* iterating the loop */
					foreach($strDataSetArr as $strDataSetArrKey => $strDataSetArrValue){
						/* Set the value */
						$strFormattedArr[]	= array(
														'user_code'=>$strDataSetArrValue['delivery-leader-spoc-'],
														'vertical_code'=>$strDataSetArrValue['system-vertical'],
														'status_code'=>$strDataSetArrValue['status'],
														'avarage_tat'=>$strDataSetArrValue['avg_diff'],
														'total_delivery_count'=>$strDataSetArrValue['total_delivery'],
														'delivery_year'=>$strDataSetArrValue['delivery_year'],
														'delivery_month'=>$strDataSetArrValue['delivery_month'],
														'delivery_date'=>$strDataSetArrValue['delivery_date'],
														'record_date'=>date('Ymd')
													);
						/* Set the delivery date */
						$strDeliveryDateArr[$strDataSetArrValue['delivery_date']]	= $strDataSetArrValue['delivery_date'];
					}
				}
				/* removed the used variable */
				unset($strQueryArr['where'],$strQueryArr['column'], $strDataSetArr);
			}
			
			/* if data found then do needful */
			if(!empty($strFormattedArr)){
				/* delete the older data */
				$this->_objDataOperation->getDirectQueryResult("DELETE FROM trans_quarter_delivery_count WHERE delivery_date in(".implode(",",$strDeliveryDateArr).")");
				/* Insert data */
				$this->_objDataOperation->setBulkInset(array('table'=>' trans_quarter_delivery_count','data'=>$strFormattedArr));
			}
		}
		
		/* removed used variable */
		unset($strWhereArr,$strQueryArr,$strFormattedArr);
	}
}