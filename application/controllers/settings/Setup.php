<?php
/***********************************************************************/
/* Purpose 		: Application Environment Setting.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup extends Requestprocess{
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_user_config';
	private $_strModuleName			= "Environment";
	private $_strModuleForm			= "frmEnvironmentSetting";
	private $_isAdmin				= 0;
	private $_intCompanyCode		= 0;
	private $_blnSetupStatus		= false;
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* CI call execution */
		parent::__construct();
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$strRecordsetArr	= $dataArr	= array();
		
		/* getting logger details */
		$strSetupArr	= $this->_setSetupParameterAndDescription();
		$blnStatus		= true;
		
		/* Checking for instruction array */
		if(!empty($strSetupArr)){
			/* Iterating the loop */
			foreach($strSetupArr as $strSetupArrKey => $strSetupArrValue){
				/* checking for each item status */
				if(($blnStatus)){
					/* Setting the value */
					$blnStatus	= $strSetupArrValue['status'];
					/* terminate the loop */
					if(!$blnStatus){
						break;
					}
				}
			}
		}
		
		/* if setup status is TRUE then do needful */
		if($blnStatus){
			/* Updating the configuration status */
			$this->_objDataOperation->setUpdateData(array('table'=>'master_company','data'=>array('is_setup_configured'=>1),'where'=>array('id'=>$this->getCompanyCode())));
			/* Redirect to the login page */
			redirect(SITE_URL);
		}
        
		/* Getting Setup List */
		$dataArr['moduleTitle']			= $this->_strModuleName;
		$dataArr['moduleForm']			= $this->_strModuleForm;
		$dataArr['strDataAddEditPanel']	= 'widgetModel';
		$dataArr['moduleUri']			= SITE_URL.'settings/'.__CLASS__;
		$dataArr['deleteUri']			= SITE_URL.'settings/'.__CLASS__.'/deleteRecord';
		$dataArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getWidgetDetailsByCode';
		$dataArr['getRecordByCodeUri']	= SITE_URL.'settings/'.__CLASS__.'/getWidgetDetailsByCode';
		$dataArr['noSearchAdd']			= 'yes';
		
		/* Load the environment list */
		$dataArr['body']	= $this->load->view('settings/setup', array('dataArr'=>$strSetupArr), true);
		
		/* Loading the template for browser rending */
		$this->load->view(FULL_WIDTH_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}
	
	/**********************************************************************/
	/*Purpose 	: Setup the parameter description.
	/*Inputs	: None.
	/*Returns	: Logger Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setSetupParameterAndDescription(){
		/* id normal user then do needful */
		if($this->_intAdminCode == 0){
			//return array('message'=>'Working environment is not set by Company / System Administrator. Kindly get touch with them. Once suggested setup / configuration steps done by them, system will start working automatically. In this you might be needs to get login one more time.');
		}
		
		/* return the list */
		return array(
						1=>array(
									'label'=>'Roles',
									'description'=>'This will helps system to identity user access / rights classification based on the configured roles.<br/><b>How to setup:</b> Settings > User Role',
									'status'=>$this->_checkRoles()
								),
						2=>array(
									'label'=>'User',
									'description'=>'Add new user in the system, associated with configured role, system role, location and reporting structure. <br/><b>How to setup:</b> Settings > User Profiles',
									'status'=>$this->_checkUser()
								),
						3=>array(
									'label'=>'Module Access',
									'description'=>'Once role based setup, after that you can control the application feature / menu access / visibility gets controlled. <br/><b>How to setup:</b> Settings > Module Access',
									'status'=>$this->_checkModulesAccess()
								),
						4=>array(
									'label'=>'Environment',
									'description'=>'Finally we needs to setup the environment variable of system, This will helps from lead enrolment to become prospect and smooth functional of account. <br/><b>How to setup:</b> Settings > Environment',
									'status'=>$this->_checkEnvironmentSetup()
								),
					);
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking Roles.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkRoles(){
		/* getting lead location details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_role','column'=>array('id'),'where'=>array('company_code'=>$this->getCompanyCode()),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking users.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkUser(){
		/* getting lead location details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_user','column'=>array('id'),'where'=>array('company_code'=>$this->getCompanyCode()),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking module access to roles access.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkModulesAccess(){
		/* getting module access to roles details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'trans_module_access','column'=>array('id'),'where'=>array('company_code'=>$this->getCompanyCode()),'limit'=>0,'offset'=>0));
		/* Return the status */
		return (empty($strDataSet))?false:true;
	}
	
	
	/**********************************************************************/
	/*Purpose 	: Checking form environment default setting.
	/*Inputs	: None.
	/*Returns	: TRUE / FALSE.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _checkEnvironmentSetup(){
		/* Variable initialization */
		return $blnStatus	= true;
		
		/* getting module access to roles details */
		$strDataSet	=  $this->_objDataOperation->getDataFromTable(array('table'=>'master_user_config','column'=>array('value_description'),'where'=>array('company_code'=>$this->getCompanyCode())));
		
		/* if data found then do needful */
		if(!empty($strDataSet)){
			/* Iterating the loop */
			foreach($strDataSet as $strDataSetkey => $strDataSetValue){
				/* Checking for value not set */
				if((int)$strDataSetValue['value_description'] == 0){
					$blnStatus	= false;
					break;
				}
			}
		}else{
			$blnStatus	= false;
		} 
		
		/* Return the status */
		return $blnStatus;
	}
	     
	/*******************************************************************************************************/
	/*Purpose 	: Changing the Company only if the User has logged into Company 1(System).
	/*Inputs	: CompanyCode of the company that User wants to switch to.
	/*Returns 	: None.
	/*Created By: Vipin Kumar R. Jaiswar.
	/*******************************************************************************************************/
	public function setCompanyEnv(){
		/* Variable initialization */
		$intCompanyCode = ($this->input->post('txtElementCode') != '') ? getDecyptionValue($this->input->post('txtElementCode')) : 0;
		
		/* if invalid company code passed then do needful */
		if($intCompanyCode == 0){
			/* return response */
			jsonReturn(array('status'=>0,'message'=>'Invalid Company Environment Switch Request.'),true);
		}
		
		/* Creating logger object */
		$objLogger  = new Logger();
		/* Logger Object registration request */
		$intResponse = $objLogger->setLogger($intCompanyCode);
		/* Removed used variable */
		unset($objLogger);
		
		if($intResponse){
			//redirect(SITE_URL.'/settings/setup');
			/* return response */
			jsonReturn(array('status'=>1,'message'=>'Valid account.','destinationURL'=>SITE_URL.'dashboard'),true);
		}
	}
}