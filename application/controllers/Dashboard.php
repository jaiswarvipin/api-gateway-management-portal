<?php
/***********************************************************************/
/* Purpose 		: Application Dashboard.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Requestprocess {

	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_user';
	private $_strSecondayTableName	= 'api_list';
	private $_strVerticalTableName	= 'system_vertical';
	private $_strWidgetAttTableName	= 'master_widget_attributes_list';
	private $_strDeliveryCountTable = 'trans_delivery_count';
	private $_strDeliveryQCountTable= 'trans_quarter_delivery_count';
	private $_strGitCountTable		= 'trans_gits_repo_commit_details';
	private $_strModuleName			= "Dashboard";
	private $_strModuleForm			= "frmDashboard";
	private $_strDeliveryStatusArr	= array();
	private $_strVertcialArr		= array();
	private $_strUserArr			= array();
	private $_strDateRangArr		= array();
	private $_intVerticalCode		= 0;
	private $_intSPOCCode			= 0;
	
	/**********************************************************************/
	/*Purpose 	: Element initialization.
	/*Inputs	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
		
		/* Set the schema */
		$this->_strSecondayTableName	= $this->_strSecondayTableName.'_'.$this->getCompanyCode();
		$this->_strVerticalTableName	= $this->_strVerticalTableName.'_'.$this->getCompanyCode();
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();
		
		/* Get the delivery status */
		$this->_strDeliveryStatusArr	= $this->_getDeliveryStatusList();
		
		$this->_strDateRangArr			= getDatesIntervalByQauter(getQauter());
		$this->_intVerticalCode			= ($this->input->post('cboVertical') =='')?0:getDecyptionValue($this->input->post('cboVertical'));
		$this->_intSPOCCode				= ($this->input->post('cboSPOC') =='')?0:getDecyptionValue($this->input->post('cboSPOC'));
		$this->_strDateRangArr['from']	= ($this->input->post('txtFromDate') =='')?$this->_strDateRangArr['from']:getDateFormat($this->input->post('txtFromDate'),6);
		$this->_strDateRangArr['to']	= ($this->input->post('txtToDate') =='')?$this->_strDateRangArr['to']:getDateFormat($this->input->post('txtToDate'),6);
		$this->_strVertcialArr			= array();//getArrByKeyvaluePairs($this->_getVerticalList(),'id','name');
		$this->_strUserArr				= getArrByKeyvaluePairs($this->_getUserList(),'id','user_name');
		
		/* Getting current page number */
		$intCurrentPageNumber					= ($this->input->post('txtPageNumber') != '') ? ((($this->input->post('txtPageNumber') - 1) < 0)?0:($this->input->post('txtPageNumber') - 1)) : 0;
		
		/* Getting modules access list */
		$strDataArr['dataSet'] 				= array();
		$strDataArr['intPageNumber'] 		= ($intCurrentPageNumber * DEFAULT_RECORDS_ON_PER_PAGE) + 1;
		$strDataArr['pagination'] 			= getPagniation(array());
		$strDataArr['noAction'] 			= true;
		$strDataArr['moduleTitle']			= $this->_strModuleName;
		$strDataArr['moduleForm']			= $this->_strModuleForm;
		$strDataArr['moduleUri']			= SITE_URL.'/'.__CLASS__;
		$strDataArr['deleteUri']			= SITE_URL.'/'.__CLASS__.'/deleteRecord';
		$strDataArr['getRecordByCodeUri']	= SITE_URL.''.__CLASS__.'/getUserProfileDetailsByCode';
		$strDataArr['strDataAddEditPanel']	= 'moduleDashboardModel';
		$strDataArr['strSearchArr']			= (!empty($_REQUEST))?jsonReturn($_REQUEST):jsonReturn(array());
		$strDataArr['strDeliveryHTML']		= array();//$this->_getDeliveryReport();
		$strDataArr['strDeliveryTrackHTML']	= array();//$this->_getDeliveryReportWithTackingStatus();
		$strDataArr['strVerticalList']		= array();//$this->_objForm->getDropDown($this->_strVertcialArr);
		$strDataArr['strSPOCList']			= array();//$this->_objForm->getDropDown($this->_strUserArr);
		$strDataArr['strCodeSyncGitHTML']	= array();//$this->_getGetCommitHistory();
		
		$this->_strDateRangArr['from']		= getDateFormat($this->_strDateRangArr['from'],5);
		$this->_strDateRangArr['to']		= getDateFormat($this->_strDateRangArr['to'],5);
		$strDataArr['strFilter']			= "Filter:(From Date: ".$this->_strDateRangArr['from'].", To Date: ".$this->_strDateRangArr['to'];
		/* if vertical code selected then do needful */
		if($this->_intVerticalCode > 0){
			/* Set filter case text */
			$strDataArr['strFilter']	  .= ", Vertical: ".$this->_strVertcialArr[$this->_intVerticalCode];
		}
		/* if SPOC code selected then do needful */
		if($this->_intSPOCCode > 0){
			/* Set filter case text */
			$strDataArr['strFilter']	  .= ", Delivery Leader(SPOC): ".$this->_strUserArr[$this->_intSPOCCode];
		}
		$strDataArr['strFilter']	  .= ")";
		
		/* Load the View */
		$dataArr['body']	= $this->load->view('template/dashboard', $strDataArr, true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get delivery status report.
	/*Inputs	: none.
	/*rReturns	: Delivery stat.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getDeliveryReport(){
		/* variable initialization */
		$strWhereArr 			= $strQueryArr	=  $strFormattedArr = array();
		
		/* setting the filter */
		$strWhereArr	= array($this->_strDeliveryCountTable.'.delivery_month >='=>$this->_strDateRangArr['from'],$this->_strDeliveryCountTable.'.delivery_month <='=>$this->_strDateRangArr['to']);
		
		/* if vertical code pass then do needful */
		if($this->_intVerticalCode != 0){
			/* setting vertical filter */
			$strWhereArr	= array_merge($strWhereArr, array($this->_strVerticalTableName.'.id'=>$this->_intVerticalCode));
		}
		/* if SPOC code pass then do needful */
		if($this->_intSPOCCode != 0){
			/* setting vertical filter */
			$strWhereArr	= array_merge($strWhereArr, array($this->_strPrimaryTableName.'.id'=>$this->_intSPOCCode));
		}
		
		/* Creating query array */
		$strQueryArr	= array(
									'table'=>array($this->_strPrimaryTableName, $this->_strDeliveryCountTable,$this->_strVerticalTableName,$this->_strWidgetAttTableName),
									'join' => array('', $this->_strPrimaryTableName.'.id = '.$this->_strDeliveryCountTable.'.user_code',$this->_strDeliveryCountTable.'.vertical_code = '.$this->_strVerticalTableName.'.id',$this->_strDeliveryCountTable.'.status_code='.$this->_strWidgetAttTableName.'.id'),
									'column'=>array($this->_strPrimaryTableName.'.id',$this->_strDeliveryCountTable.'.status_code',$this->_strPrimaryTableName.'.user_name',$this->_strVerticalTableName.'.name','sum('.$this->_strDeliveryCountTable.'.delivery_count) as totalDelivery',$this->_strWidgetAttTableName.'.description'),
									'where'=>$strWhereArr,
									'group'=>array($this->_strPrimaryTableName.'.id',$this->_strVerticalTableName.'.id',$this->_strDeliveryCountTable.'.status_code'),
									'order'=>array($this->_strVerticalTableName.'.name'=>'asc')
							   );
		/* Get data from dataset */
		$strResultSetArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		
		/* If date found then do needful */
		if(!empty($strResultSetArr)){
			/* iterating the loop */
			foreach($strResultSetArr as $strResultSetArrKey => $strResultSetArrValue){
				/* Setting the values */
				if(isset($strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']])){
					$strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']] += $strResultSetArrValue['totalDelivery'];
				}else{
					$strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']] = $strResultSetArrValue['totalDelivery'];
				}
			}
		}
		
		/* removed used variable */
		unset($strWhereArr,$strQueryArr,$strResultSetArr);
		
		/* Load the View */
		return $this->load->view('dashboard/delivery', array('strDataArr'=>$strFormattedArr,'strStatusArr'=>$this->_strDeliveryStatusArr), true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get delivery status report with tracking color.
	/*Inputs	: none.
	/*rReturns	: Delivery stat.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getDeliveryReportWithTackingStatus(){
		/* variable initialization */
		$strWhereArr 			= $strQueryArr	=  $strFormattedArr = array();
		$strDeliveryHStatusArr	= (array)json_decode(STATUS_GROUP_ARR);
		
		/* setting the filter */
		$strWhereArr	= array($this->_strDeliveryQCountTable.'.delivery_date >='=>$this->_strDateRangArr['from'],$this->_strDeliveryQCountTable.'.delivery_date <='=>$this->_strDateRangArr['to']);
		
		/* if vertical code pass then do needful */
		if($this->_intVerticalCode != 0){
			/* setting vertical filter */
			$strWhereArr	= array_merge($strWhereArr, array($this->_strVerticalTableName.'.id'=>$this->_intVerticalCode));
		}
		/* if SPOC code pass then do needful */
		if($this->_intSPOCCode != 0){
			/* setting vertical filter */
			$strWhereArr	= array_merge($strWhereArr, array($this->_strPrimaryTableName.'.id'=>$this->_intSPOCCode));
		}
		
		/* Creating query array */
		$strQueryArr	= array(
									'table'=>array($this->_strPrimaryTableName, $this->_strDeliveryQCountTable,$this->_strVerticalTableName,$this->_strWidgetAttTableName),
									'join' => array('', $this->_strPrimaryTableName.'.id = '.$this->_strDeliveryQCountTable.'.user_code',$this->_strDeliveryQCountTable.'.vertical_code = '.$this->_strVerticalTableName.'.id',$this->_strDeliveryQCountTable.'.status_code='.$this->_strWidgetAttTableName.'.id'),
									'column'=>array($this->_strPrimaryTableName.'.id',$this->_strDeliveryQCountTable.'.status_code',$this->_strPrimaryTableName.'.user_name',$this->_strVerticalTableName.'.name','sum('.$this->_strDeliveryQCountTable.'.total_delivery_count) as totalDelivery','avg('.$this->_strDeliveryQCountTable.'.avarage_tat) as totalDeliveryAvg',$this->_strWidgetAttTableName.'.description'),
									'where'=>$strWhereArr,
									'group'=>array($this->_strPrimaryTableName.'.id',$this->_strVerticalTableName.'.id',$this->_strDeliveryQCountTable.'.status_code'),
									'order'=>array($this->_strVerticalTableName.'.name'=>'asc')
							   );
		/* Get data from dataset */
		$strResultSetArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		
		/* If date found then do needful */
		if(!empty($strResultSetArr)){
			/* iterating the loop */
			foreach($strResultSetArr as $strResultSetArrKey => $strResultSetArrValue){
				/* Setting the values */
				if(isset($strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']])){
					$strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']]['count'] += $strResultSetArrValue['totalDelivery'];
					$strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']]['tat'] += $strResultSetArrValue['totalDeliveryAvg'];
				}else{
					$strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']]['count'] = $strResultSetArrValue['totalDelivery'];
					$strFormattedArr[$strResultSetArrValue['name']][$strResultSetArrValue['user_name']][$strResultSetArrValue['status_code']]['tat'] = $strResultSetArrValue['totalDeliveryAvg'];
				}
			}
		}
		
		/* removed used variable */
		unset($strWhereArr,$strQueryArr,$strResultSetArr);
		
		/* Load the View */
		return $this->load->view('dashboard/delivery_status', array('strDataArr'=>$strFormattedArr,'strStatusArr'=>$strDeliveryHStatusArr), true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get Git commit details.
	/*Inputs	: none.
	/*rReturns	: Git Commit details for last one week.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getGetCommitHistory(){
		/* variable initialization */
		$strWhereArr 			= $strQueryArr	=  $strFormattedArr = array();
		$strWorkingDateArr		= getWorkingDayAsDate();
		/* setting the filter */
		$strWhereArr	= array($this->_strGitCountTable.'.record_date <='=>$strWorkingDateArr['filter_date'][0].'235959',$this->_strGitCountTable.'.record_date >='=>$strWorkingDateArr['filter_date'][count($strWorkingDateArr['filter_date'])-1].'000000');
		
		/* if vertical code pass then do needful */
		if($this->_intVerticalCode != 0){
			/* setting vertical filter */
			$strWhereArr	= array_merge($strWhereArr, array($this->_strVerticalTableName.'.id'=>$this->_intVerticalCode));
		}
		/* if SPOC code pass then do needful */
		if($this->_intSPOCCode != 0){
			/* setting vertical filter */
			$strWhereArr	= array_merge($strWhereArr, array($this->_strPrimaryTableName.'.id'=>$this->_intSPOCCode));
		}
		
		/* Creating query array */
		$strQueryArr	= array(
									'table'=>array($this->_strPrimaryTableName, $this->_strGitCountTable,'master_gits_repo', $this->_strVerticalTableName),
									'join' => array('', $this->_strPrimaryTableName.'.id = '.$this->_strGitCountTable.'.user_code',$this->_strGitCountTable.'.repo_code = master_gits_repo.id', 'master_gits_repo.vertical_cdoe = '.$this->_strVerticalTableName.'.id',),
									'column'=>array($this->_strVerticalTableName.'.id as vertical_code',$this->_strVerticalTableName.'.name','date_format('.$this->_strGitCountTable.'.record_date,"%Y%m%d") as commit_date','count('.$this->_strGitCountTable.'.id) as commit_count'),
									'where'=>$strWhereArr,
									'group'=>array($this->_strVerticalTableName.'.id','date_format('.$this->_strGitCountTable.'.record_date,"%Y%m%d")'),
									'order'=>array('commit_date'=>'desc')
							   );
		
		/* Get data from dataset */
		$strResultSetArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		$strVerticalDataArr	= $this->_getUserVertcialList();
		
		/* if result is empty then do needful */
		if(!empty($strResultSetArr)){
			/* Iterating the loop */
			foreach($strResultSetArr as $strResultSetArrKey => $strResultSetArrValue){
				/* if index exists then do needful */
				if(isset($strFormattedArr[$strResultSetArrValue['vertical_code']])){
					$strFormattedArr[$strResultSetArrValue['vertical_code']]['commit_history'][$strResultSetArrValue['commit_date']]	= $strResultSetArrValue;
				}else{
					/* Set formatting */
					$strFormattedArr[$strResultSetArrValue['vertical_code']] = $strVerticalDataArr[$strResultSetArrValue['vertical_code']];
					$strFormattedArr[$strResultSetArrValue['vertical_code']]['commit_history'][$strResultSetArrValue['commit_date']]	= $strResultSetArrValue;
				}
			}
		}
		
		
		/* removed used variable */
		unset($strWhereArr,$strQueryArr,$strResultSetArr);
		
		/* Load the View */
		return $this->load->view('dashboard/git_commit_status', array('strDataArr'=>$strFormattedArr,'strVerticalArr'=>$strVerticalDataArr, 'displayDateArr'=>$strWorkingDateArr), true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Get delivery status list.
	/*Inputs	: none.
	/*rReturns	: Delivery status list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getDeliveryStatusList(){
		/* Creating the delivery status object */
		$deliveryStatusObj = new Status($this->_objDataOperation , $this->getCompanyCode());
		/* get delivery status */
		return $deliveryStatusObj->getDeliveryStatusList();
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
		$strReturnArr	= $this->_objDataOperation->getDirectQueryResult("select user_name, id from ".$this->_strPrimaryTableName." where deleted = 0 order by user_name");
		
		/* return the user list array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the vertical user list.
	/*Inputs	: None.
	/*Returns	: Get the user with vertical list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getUserVertcialList(){
		/* variable initialization */
		$strReturnArr =  $strResultSetArr	= array();
		
		/* Creating query array */
		$strQueryArr	= array(
									'table'=>array($this->_strPrimaryTableName,'trans_user_vertical_association' , $this->_strVerticalTableName),
									'join' => array('', $this->_strPrimaryTableName.'.id = trans_user_vertical_association.user_code', $this->_strVerticalTableName.'.id = trans_user_vertical_association.vertical_code'),
									'column'=>array('DISTINCT('.$this->_strPrimaryTableName.'.id) as user_code',$this->_strVerticalTableName.'.id as vertical_code',$this->_strPrimaryTableName.'.user_name',$this->_strVerticalTableName.'.name'),
									'where'=>array('role_code'=>6),
									'group'=>array($this->_strPrimaryTableName.'.id',$this->_strVerticalTableName.'.id'),
									'order'=>array($this->_strVerticalTableName.'.name'=>'asc')
							   );
		
		/* Get data from dataset */
		$strResultSetArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
		
		/* if record found then do needful */
		if(!empty($strResultSetArr)){
			/* iterating the loop */
			foreach($strResultSetArr as $strResultSetArrKey => $strResultSetArrValue){
				/* Set the user mapping */
				$strReturnArr[$strResultSetArrValue['vertical_code']]	= $strResultSetArrValue;
			}
			
			/* re-ini variable */
			$strResultSetArr	= array();

			/* Creating query array */
			$strQueryArr	= array(
										'table'=>array($this->_strPrimaryTableName,'trans_user_vertical_association'),
										'join' => array('', $this->_strPrimaryTableName.'.id = trans_user_vertical_association.user_code'),
										'column'=>array($this->_strPrimaryTableName.'.id','trans_user_vertical_association.vertical_code','count(trans_user_vertical_association.id) as total_developer'),
										'where'=>array('role_code'=>array(8)),
										'group'=>array('trans_user_vertical_association.vertical_code'),
								   );
			
			/* Get data from dataset */
			$strResultSetArr	= $this->_objDataOperation->getDataFromTable($strQueryArr);
			
			/* if data found then do needful */
			if(!empty($strResultSetArr)){
				/* iterate the loop */
				foreach($strResultSetArr as $strResultSetArrKey => $strResultSetArrValue){
					/* Setting value */
					$strReturnArr[$strResultSetArrValue['vertical_code']]['total_developer']	=$strResultSetArrValue['total_developer'];
				}
			}
		}
		
		/* removed the used variable */
		unset($strResultSetArr);
		
		/* return the user list array */
		return $strReturnArr;
	}
}