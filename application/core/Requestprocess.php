<?php
/***********************************************************************/
/* Purpose 		: Request and Logger processing.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Requestprocess extends CI_Controller {
	/* variable deceleration */
	public  $_objDataOperation				= null;
	public  $_objForm						= null;
	private $_intUserCode					= 0;
	private $_intRoleCode					= 0;
	private $_intCompanyCode				= 0;
	public  $_intAdminCode 					= 0;
	private $_intDefaultStatusCode 			= 0;
	private $_intIsSetupConfigured 			= 0;
	private $_strMainModule					= '';
	private $_strChildModule				= '';
	private $_strMobileModule				= '';
	private $_strCompanyListDropDown        = '';
	private $_strAccessVerticalArr			= array();
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: $pBlnRequestFromHook :: Request from hook modules
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct($pBlnRequestFromHook = false){
		/* CI call execution */
		parent::__construct();

		 /* Creating model comment instance object */
		$this->_objDataOperation	= new Dbrequestprocess_model();

		/* Creating form helper object */
		$this->_objForm				= new Form();
		
		/* if request from hook modules then do needful */
		if($pBlnRequestFromHook){
			/* stop this class execution */
			return false;
		}
		
		/* if CRON request then do needful */
		if($this->uri->segment(1) == 'crons'){
			/* Return execution control to CRON calling controller */ 
			return;
		}

		/* Process the logger request */
		$this->_doValidateRequest();
	}

	/**********************************************************************/
	/*Purpose 	: Validating the current logger status.
	/*Inputs	: None.
	/*Returns	: Logger Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _doValidateRequest(){
		/*Variable initialization */
		$strCookiesCode	= '';

		/* Checking is valid cookie exists */
		if(isset($_COOKIE['_xAyBzCwD'])){
			/* Getting the valid logger code */
			$strCookiesCode	= $_COOKIE['_xAyBzCwD'];
		}
		
		/* If logger code is not found the do needful */
		if($strCookiesCode == ''){
			/* Destroy the all cookies */
			$this->doDistryLoginCookie();

			/* redirecting to login */
			redirect(SITE_URL.'login');
		}else{
			/* getting logger details */
			$strLoggerArr 	= $this->_getLoggerDetails($strCookiesCode);
			
			/* Logger details not found then do needful */
			if(empty($strLoggerArr)){
					/* Destroy the all cookies */
					$this->doDistryLoginCookie();
			}
			/* Processing the logger Object */
			$this->_doProcessLogger($strLoggerArr);
			
			/* Loading company custom configuration */
			$this->_setCompanyConfig();
		}
	}

	/**********************************************************************/
	/*Purpose 	: Process the logger data.
	/*Inputs	: $pStrLoggerDetailsArr	= Logger Details array.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _doProcessLogger($pStrLoggerDetailsArr = array()){
		/* if logger object is empty then do needful */
		if(empty($pStrLoggerDetailsArr)){
			/* Destroy the all cookies */
			$this->doDistryLoginCookie();

			/* redirecting to login */
			redirect(SITE_URL.'login', 'refresh');
		}
		
		/* Decoding the logger */
		$ObjStrLoggerDetails	= json_decode($pStrLoggerDetailsArr[0]['logger_data']);
		
		/* Checking looger details request */
		if(isset($_COOKIE['logger'])){
			/* Display the looger details and exit the operation */
			debugVar($ObjStrLoggerDetails, true);
		}
		
		/* Logger variable declaration */
		$strLoggerName					= $ObjStrLoggerDetails->user_info->user_name;
		$strLoggerRoleDesc				= $ObjStrLoggerDetails->user_info->role_name;
		$strLoggerRoleCode				= $ObjStrLoggerDetails->user_info->role_code;
		$this->_intUserCode				= $ObjStrLoggerDetails->user_info->id;
		$this->_intRoleCode				= $strLoggerRoleCode;
		$this->_intCompanyCode			= $ObjStrLoggerDetails->user_info->company_code;
		$this->_intAdminCode			= $ObjStrLoggerDetails->user_info->is_admin;
		$this->_intIsSetupConfigured	= $ObjStrLoggerDetails->user_info->is_setup_configured;
		$this->_strMainModule			= $ObjStrLoggerDetails->main_menu;
		$this->_strChildModule			= $ObjStrLoggerDetails->child_menu;
		$this->_strMobileModule			= $ObjStrLoggerDetails->main_mobile;
		$this->_strCompanyListDropDown  = $ObjStrLoggerDetails->companyListDropDown;
		$this->_strAccessVerticalArr	= array();//$ObjStrLoggerDetails->vertical_code;
		
		/* Global variable declaration */
		$this->load->vars(array(
									'userName'		=> $strLoggerName,
									'roleName'		=> $strLoggerRoleDesc,
									'roleCode' 		=> $strLoggerRoleCode,
									'strMainMenu'	=> $this->_strMainModule,
									'strChildMenu'	=> $this->_strChildModule,
									'strMobileMenu'	=> $this->_strMobileModule,
									'companyList'	=> $this->_strCompanyListDropDown,
									'blnDevice'		=> $this->_isMobile(),
							)
						);

		/* removed used variables */
		unset($ObjStrLoggerDetails);
		/* Setting for environment setting is up-to-date */
		//$this->_isEnvisSet();
	}

	/**********************************************************************/
	/*Purpose 	: get logger user code.
	/*Inputs	: None.
	/*Returns	: User Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getUserCode(){
		/* return user code */
		return $this->_intUserCode;
	}

	/**********************************************************************/
	/*Purpose 	: get logger user role code.
	/*Inputs	: None.
	/*Returns	: Role Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getRoleCode(){
		/* return user code */
		return $this->_intRoleCode;
	}

	/**********************************************************************/
	/*Purpose 	: get logger company code.
	/*Inputs	: None.
	/*Returns	: User Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getCompanyCode(){
		/* return company code */
		return $this->_intCompanyCode;
	}
	
	/**********************************************************************/
	/*Purpose 	: is environment setup is done.
	/*Inputs	: None.
	/*Returns	: Set up status.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getEnvSetupConfigured(){
		/* return setup configured */
		return $this->_intIsSetupConfigured;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the allowed vertical code.
	/*Inputs	: None.
	/*Returns	: Granted vertical code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getAccessVerticalCode(){
		/* return grant verticals */
		return $this->_strAccessVerticalArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: get user admin flag.
	/*Inputs	: None.
	/*Returns	: User Code.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getAdminFlag(){
		/* return company code */
		return $this->_intAdminCode;
	}

	/**********************************************************************/
	/*Purpose 	: Destroy the existing logger cookies.
	/*Inputs	: None.
	/*Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function doDistryLoginCookie(){
		/* Creating logger object */
		$objLogger	= new Logger();
		/* Logger object registration request */
		$objLogger->doDistryLoginCookie();
		/* Removed used variable */
		unset($objLogger);
	}

	/**********************************************************************/
	/*Purpose 	: Getting the current logger details.
	/*Inputs	: $pStrCookiesCode :: Logger token code.
	/*Returns	: Logger Details.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getLoggerDetails($pStrCookiesCode = ''){
		/*Variable initialization */
		$strReturnArr	= array();
		
		/* If logger code is not found the do needful */
		if($pStrCookiesCode == ''){
			/* return empty set */
			return $strReturnArr;
		}
		/* getting the logger details */
		$strloggerArr	=  $this->_objDataOperation->getDataFromTable(array('table'=>'trans_logger','column'=>array('id','token','logger_data','user_code'),'where'=>array('token'=>$pStrCookiesCode)));

		/* Return the logger details */
		return $strloggerArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: get module access main menu.
	/*Inputs	: None.
	/*Returns	: Main Menu.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getMainModule(){
		/* return main menu */
		return $this->_strMainModule;
	}
	
	/**********************************************************************/
	/*Purpose 	: get child module access main menu.
	/*Inputs	: None.
	/*Returns	: Child  Menu.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getChildModule(){
		/* return main menu */
		return $this->_strChildModule;
	}
	
	/**********************************************************************/
	/*Purpose 	: Module field array.
	/*Inputs	: $pStrModuleURL :: Module URL,
				: $pIntWidgetCode :: Widget Code,
				: $pIntAttributesCodeArr :: Attributes codes
				: $pBlnByassValidation :: module empty check by passing
	/*Returns	: Module list.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getModuleAssociatedFieldByModuleURL($pStrModuleURL = '', $pIntWidgetCode = 0, $pIntAttributesCodeArr = array(), $pBlnByassValidation = false){
		/* Variable initialization */
		$strReturnArr	= array();
		
		/* If module URL is empty then do needful */
		if(($pStrModuleURL == '') && ($pIntWidgetCode == 0) && (empty($pIntAttributesCodeArr)) && (!$pBlnByassValidation)){
			/* Return Empty Array */
			return $strReturnArr;
		} 
		
		/* Creating widget object */
		$widgetObj			= new Widget($this->_objDataOperation, $this->getCompanyCode());
		
		/* if requester needed all widget attributes then do needful  */
		if($pIntWidgetCode > 0){
			/* Get widget attributes details by widget code */
			$strReturnArr		= $widgetObj->getWidgetDetailsByWidgetCode($pIntWidgetCode);
		/* if requester needed all widget attributes option list then do needful  */
		}else if(!empty($pIntAttributesCodeArr)){
			/* Get widget attributes details by widget code */
			$strReturnArr		= $widgetObj->getWidgetOptionsDetailsByWidgetAttributesCode($pIntAttributesCodeArr);
		}else{
			/* Get module details by module slug */
			$strReturnArr		= $widgetObj->getWidgetDetailsByModuleSlug($pStrModuleURL);
		}
		/* removed used variables */
		unset($widgetObj);
		
		/* return Filed array */
		return $strReturnArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Get module column as search panel.
	/* Inputs 	: $pStrColumnArray :: Column Array,
				: $pStrAction : Search method.
	/* Returns	: Search HTML of respective panel.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getColumnAsSearchPanel($pStrColumnArray, $pStrAction = ''){
		/* Creating widget panel */
		$widgetObj	 	= new Widget($this->_objDataOperation, $this->getCompanyCode());
		/* get Search panel HTML */
		$strSearchHTML	= $widgetObj->getColumnAsSearchPanel(array_merge($pStrColumnArray,array('action'=>$pStrAction)));
		/* Removed used variables */
		unset($widgetObj);
		
		/* Return HTML */
		return $strSearchHTML;
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking is environment is set. 
	/* Inputs 	: None.
	/* Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _isEnvisSet(){
		/* Variable initialization */
		$blnIsSet	= true;
		
		/* if setup configuration is not done then then do needful */
		if($this->getEnvSetupConfigured() == 0){
			/* value overriding */
			$blnIsSet	= false;
		}
		
		/* Redirect to set-up module */
		if((!$blnIsSet) && ($this->uri->segment(1) != 'settings' && $this->uri->segment(1) != 'mod' && $this->uri->segment(1) != 'manage_widgets' && $this->uri->segment(1) != 'manage-widgets' && $this->uri->segment(1) != 'social_wall' && $this->uri->segment(1) != 'social-wall')){
			/* redirecting to login */
			redirect(SITE_URL.'settings/setup');
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Checking request is from device or desktop. 
	/* Inputs 	: None.
	/* Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _isMobile(){
		/* getting user agent reference */
		$this->load->library('user_agent');
		
		/* return the requested device type */
		return $this->agent->is_mobile();
	}
	
	/**********************************************************************/
	/*Purpose 	: Setting / loading company configuration variables. 
	/* Inputs 	: None.
	/* Returns	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _setCompanyConfig(){
		/* Loading the company configuration file */
		$this->config->load('company'.DIRECTORY_SEPARATOR.$this->getCompanyCode().DIRECTORY_SEPARATOR.'config');
	}
	
	
	/**********************************************************************/
	/*Purpose 	: get company configuration variables. 
	/* Inputs 	: $pStrServiceName :: Server Name,
				: $pStrType : :Server component name,
				: $pStrServerType :: Enviroment
	/* Returns	: Configuration Value.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function getCompanyConfig($pStrServiceName = '', $pStrType = '', $pStrServerType = ENVIRONMENT){
		/* Variable initialization */
		$strReturnArr = '';
		
		/* if server name and service type is not passed then do needful */
		if(($pStrServiceName == '') || ($pStrType == '')){
			/* return empty array */
			return $strReturnArr;
		}
		/* Getting requested service  */
		$strReturnArr	= $this->config->item($pStrServiceName);
		/* return the configuration value */
		return isset($strReturnArr[$pStrType][$pStrServerType])?$strReturnArr[$pStrType][$pStrServerType]:array();
	}
}
