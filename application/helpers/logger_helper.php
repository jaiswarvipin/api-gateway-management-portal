<?php 
/***********************************************************************/
/* Purpose 		: Managing the logger request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger{
	/* Variable initialization */
	private $_objDefaultModel	= null;
	private $_strPrimaryTable	= "trans_logger";
	private $_strLoggerCode		= '';
	public $strPlatform 		= 'web';
	
	/*******************************************************************/
	/*Purpose	: Default method to be executed.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	public function __construct(){
		/* Creating the default model */
		$this->_objDefaultModel	= new Dbrequestprocess_model();
	}


	/*******************************************************************/
	/*Purpose	: Setting logger object.
	/*Inputs	: $pIntUserCode :: User code.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	/* Checking for requested user authentication */
	public function setLogger($pIntUserCode = 0){
		/* variable initialization */
		$strLoggerArr	= array();

		/* if user code is not passed then do needful */
		if($pIntUserCode == 0){
			return;
		}
		
		$strLoggerArr['platform'] 	= 	$this->strPlatform;

		/* Getting user details */
		$strResponseArr	= $this->_objDefaultModel->getDataFromTable(
																		array(
																				'table'=>array('master_user','master_role','master_company'), 
																				'join'=>array('','master_user.role_code = master_role.id','master_company.id = master_user.company_code'),
																				'column'=>array('master_user.*','master_role.description as role_name','is_setup_configured'),
																				'where'=>array('master_user.id'=>$pIntUserCode)
																			)
																	);

		/* if not response found then do needful */
		if(empty($strResponseArr)){
			/* Return error details */
			$responseArr = array('status'=>0,'message'=>'Error occurred while generating the login instance. please try after some time.');
			/* if request from API then do needful */
			if ($this->strPlatform == 'api') {
				/* return the error response */
				return $responseArr;
			}
			/* Return the response to web */
			jsonReturn($responseArr, true);
		}else{
			/* Removed the sensetive info */
			unset($strResponseArr[0]['password']);
			/* Set logger personal and logger information */
			$strLoggerArr['user_info']	= $strResponseArr[0];
		}
		/* Setting logger details */
		$this->_strLoggerCode	= $strResponseArr[0];
	 	
		/* if user object found then do needful */
		if(isset($strLoggerArr['user_info']) && (!empty($strLoggerArr['user_info'])) && $this->strPlatform == 'web' ){
			/* Setting Filter Value */
			$strWhereFilterArr	= array('role_code'=>$strLoggerArr['user_info']['role_code'], 'master_modues.company_code'=>$strLoggerArr['user_info']['company_code'], 'master_modues.is_visiable' => 1);
			
			/* Getting the Branch and Region Assign to logger user */
			if($strLoggerArr['user_info']['is_admin'] == 1){
				/* Setting value */
				$strWhereFilterArr['master_modues.company_code']	= array(1, $strLoggerArr['user_info']['company_code']);
				$strWhereFilterArr['role_code']						= array(1, $strLoggerArr['user_info']['role_code']);
			}
			
			/* Getting module access details */
			$strModuleArr	= $this->_objDefaultModel->getDataFromTable(
																			array(
																					'table'=>array('trans_module_access','master_modues'),
																					'join'=>array('','trans_module_access.module_code = master_modues.id'),
																					'column'=>array('master_modues.id', 'master_modues.description','master_modues.module_url','master_modues.parent_code',
																						'is_system',
																						),
																					'where'=>$strWhereFilterArr,
																					'order'=>array('master_modues.parent_code'=>'asc')
																				)
																		);
			
			/* if module access details found  then do needful */
			if(!empty($strModuleArr)){
				/* Variable initialization */
				$strModuleAccessArr	= array();
				$strMenuArr['main']	= '';
				$strMenuArr['child']= '';
				//debugVar($strModuleArr);
				/* Iterating the loop */
				foreach($strModuleArr as $strModuleArrKey => $strModuleArrValue){
					if($strModuleArrValue['parent_code'] == 0){
						$strModuleAccessArr[$strModuleArrValue['id']]												= $strModuleArrValue;
					}else{
						$strModuleAccessArr[$strModuleArrValue['parent_code']]['child'][$strModuleArrValue['id']]	= $strModuleArrValue;
					}					
				}
				
				unset($strModuleAccessArr[-1]);
				//debugVar($strModuleAccessArr);
				/* if module align array fund then do needful */
				if(!empty($strModuleAccessArr)){
					/* Iterating the loop */
					foreach($strModuleAccessArr as $strModuleAccessArrKey => $strModuleAccessArrValue){
						/* variable initialization */
						$blnHavingChild	= false;

						$isSystem = '';
						if (isset($strModuleAccessArrValue['is_system']) && ($strModuleAccessArrValue['is_system'] == 0)) {
							$isSystem = '/mod/';
						}
						
						/* Checking for child menu */
						if(isset($strModuleAccessArrValue['child'])){
							$blnHavingChild		= true;
							$strMenuArr['child']  .= '<ul id="'.getSlugify(strtolower($strModuleAccessArrValue['description'])).'" class="dropdown-content ">';
							/* Iterating the loop */
							foreach($strModuleAccessArrValue['child'] as $strModuleAccessArrValueKey => $strModuleAccessArrValueDetails){

								$isSystem = '';
								if (isset($strModuleAccessArrValue['is_system']) && $strModuleAccessArrValueDetails['is_system'] == 0) {
									$isSystem = 'mod/';
								}

								/* Setting inner menu */
								$strMenuArr['child'] .= '<li><a href="'.SITE_URL.$isSystem.$strModuleAccessArrValueDetails['module_url'].'">'.str_replace('[divider]','',$strModuleAccessArrValueDetails['description']).'</a></li>';
								/* if divider found then do needful */
								if(strstr($strModuleAccessArrValueDetails['description'],'[divider]')!=''){
									/* Setting inner menu */
									$strMenuArr['child'] .= '<li class="divider"></li>';
								}
							}
							$strMenuArr['child'] .= '</ul>';
						}
						/* Having child menu */
						if($blnHavingChild){
							/* Setting columns */
							$strMenuArr['main'] .= '<li><a class="dropdown-trigger" href="javascript:void(0);" data-target="'.getSlugify(strtolower($strModuleAccessArrValue['description'])).'">'.$strModuleAccessArrValue['description'].'<i class="material-icons right">arrow_drop_down</i></a></li>';
						}else{
							/* Setting columns */
							$strMenuArr['main'] .= '<li><a class="dropdown-trigger" href="'.SITE_URL.$isSystem.$strModuleAccessArrValue['module_url'].'">'.$strModuleAccessArrValue['description'].'</li>';
						}
					}
				}
				
				$strMenuArr['main']	.= '';
				
				$strLoggerArr['main_menu'] 		= '<ul id="nav-mobile" class="hide-on-med-and-down"><li class="w100">&nbsp;</li>'.$strMenuArr['main'].'</ul>';
				$strLoggerArr['main_mobile'] 	= '<ul id="mobile" class="side-nav hide-on-med-and-up"><li class="w100"><a href="javascript:void(0);"><img src="'.SITE_URL.DEFAULT_LOGO.'" class="responsive-img logo-context-container"/></a></li>'.$strMenuArr['main'].'<li><a href="'.SITE_URL.'login/lougout">Logout</a></li></ul>';
				$strLoggerArr['child_menu'] 	= $strMenuArr['child'];
				
				/* Removed used variables */
				unset($strMenuArr);
			}
			
			/************ COMPNAY LISTING FROM SUPER ADMINISTRATOR TO GET CONTROL OVER THE SUB COMPNAY DATA AND CONFIGURATION  *********/
			/* Variable initialization */
			$strCompanyListDropDownHTML = "";
			/* if logger is from PA ( SUPER ADMINISTRATOR) THEN DO NEEDFUL */
			if($strLoggerArr['user_info']['company_code'] == 1 || (isset($_COOKIE['_iSsYsTeMaDmIn']) && $_COOKIE['_iSsYsTeMaDmIn']==1)){
				/* Set the administrator / company cookies */
				$this->_setAdminCookies();
				
				/* Getting all the active company's if the User from System excluding PA account */
				$strCompanyDataResponseArr = $this->_objDefaultModel->getDataFromTable(
																						array(
																								'table' => 'master_company',
																								'column' => array('id','name'),
																								'where' => array('is_active' => 1, 'id !='=>1),
																						)
																					);
				/* if details found then do needful */
				if(!empty($strCompanyDataResponseArr) && (count($strCompanyDataResponseArr) > 1)){
					/* Iterating the loop */
					foreach($strCompanyDataResponseArr as $strCompanyDataResponseArrKey => $strCompanyDataResponseArrValue){
						/* Creating listing array */
						$strCompanyDropDownArr[$strCompanyDataResponseArrValue['id']] = $strCompanyDataResponseArrValue['name'];
					}
					
					/* Creating form object */
					$objForm	= new Form();
					/* Creating the other company listing  */
					$strCompanyListDropDownHTML = '<div class="input-field col s12 divCompanyListContainer">'
												. '<select name="cboCompanyList" id="cboCompanyList" data-set="company_code" is-change-event="yes" action="settings/setup/setCompanyEnv">'.$objForm->getDropDown($strCompanyDropDownArr, $strLoggerArr['user_info']['company_code']).'</select>'
											. '</div>';
					/* removed used variables */
					unset($objForm);
				}
			}
			
			/* Setting the company listing HTML */
			$strLoggerArr['companyListDropDown']  = $strCompanyListDropDownHTML;
                        
			
			/* Removed used object */
			unset($leadStatusObj, $strLeadStatusArr);
			
		}
		
		/* Creating logger session string */
		if ($this->strPlatform == 'api') {
			$this->_strLoggerCode	= 'rest-api_' . getRamdomeString(50) .'_'. time();
		}else {
			$this->_strLoggerCode	= getRamdomeString(50);
		}

		/* register the logger */
		$intRegisterCode 	= $this->_objDefaultModel->setDataInTable(array('table'=>$this->_strPrimaryTable,'data'=>array('user_code'=>$pIntUserCode,'token'=>$this->_strLoggerCode,'logger_data'=>json_encode($strLoggerArr),'logger_source'=>'a')));
		
		/* If logger successfully register in the database */
		if($intRegisterCode > 0){
			/* Setting the updated array */
			$strUpdatedArr	= array(
				'table' 	=> 	$this->_strPrimaryTable,
				'data' 		=> 	array(
									'deleted' 	=> 	1,
								),
				'where' 	=> 	array(
					'id != ' 		=> 	$intRegisterCode,
					'user_code' 	=> 	$pIntUserCode,
				)
			);

			/* Updating the requested record set */
			$intNunberOfRecordUpdated = $this->_objDefaultModel->setUpdateData($strUpdatedArr);

			/* If request for API then do needful */
			if ($this->strPlatform == 'api') {
				/* Setting error message */
				$responseArr 			= array('status'=>1,'message'=>'Login Successfully.');
				/* Setting the response token */
				$responseArr['token'] 	= $this->_strLoggerCode;
				/* return the token details */
				return $responseArr;
			}else{
				/* removed existing all cookies */
				$this->doDistryLoginCookie();
				/* Creating logger key */
				$this->_setLoginCookies();
				
				return true;
			}
		}else{
			/* Return error details */
			$responseArr = array('status'=>0,'message'=>'Error occurred while generating the login instance to objects. please try after some time.');
			/* If request for API then do needful */
			if ($this->strPlatform == 'api') {
				/* Setting the response token */
				$responseArr['token'] = $this->_strLoggerCode;
				/* return the error details */
				return $responseArr;
			}
			/* return error response to the WEB */
			jsonReturn($responseArr,true);
		}
	}

	/*******************************************************************/
	/*Purpose	: Setting login cookies.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	private function _setLoginCookies(){
		/* Setting logger cookie for 1 month */
		setcookie('_xAyBzCwD', $this->_strLoggerCode, time() + (2678400), "/");
	}

	/*******************************************************************/
	/*Purpose	: Setting administrator cookie while switch the company.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	private function _setAdminCookies(){
		/* Setting Cookie for System Admin or PA Administrator for 1 month this is used to show the drop down when changing the Companies */
		setcookie('_iSsYsTeMaDmIn', 1, time() + (2678400), "/");
	}
	
	/*******************************************************************/
	/*Purpose	: Removed login cookies.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	public function doDistryLoginCookie(){
		unset($_COOKIE['_xAyBzCwD']);
		setcookie('_xAyBzCwD', null, -1, '/');
	}
        
        /*******************************************************************/
	/*Purpose	: Removed System cookie.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Vipin Kumar R. Jaiswar.
	/*******************************************************************/
	public function doDestroySystemCookie(){
		if(isset($_COOKIE['_iSsYsTeMaDmIn']) && $_COOKIE['_iSsYsTeMaDmIn']==1){
			unset($_COOKIE['_iSsYsTeMaDmIn']);
			setcookie('_iSsYsTeMaDmIn', null, -1, '/');
		}
	}

	/**********************************************************************/
	/*Purpose 	: Authenticating the user from Web and REST.
	/*Inputs	: $strEmail :: Email address,
				: $strPassword :: Account password
	/*Returns 	: Error or Login response based on platform.
	/*Created By: Prashant S. Pawar
	/**********************************************************************/
	public function doAuthincation($strEmail, $strPassword){
		/* Checking for requested user authentication */
		$strResponseArr 	= 	$this->_objDefaultModel->getDataFromTable(array('table'=>'master_user', 'where' => array( 'user_email'=>$strEmail, 'password'=>md5($strPassword))));

		/* Checking user existence response */
		if(empty($strResponseArr)){
			/* if no response found then do needful */
			return array('status'=>0,'message'=>'Invalid email address or password.');
			/* if user is not active in the system then do needful */
		}else if($strResponseArr[0]['is_active'] != 1){
			/* if user is not active then do needful */
			return array('status'=>0,'message'=>'Requested login is disabled. Kindly contact to Company Administrator.');
		}else{
			/* Setting logger */
			$responseArr = $this->setLogger($strResponseArr[0]['id']);
			/* return the response */
			return $responseArr;
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Authenticating Module Hooks Request.
	/*Inputs	: $pStrToken :: API Token Header.d
	/*Returns 	: Logger details
	/*Created By: Jaisawr Vipin Kumar R.
	/**********************************************************************/
	public function verifyAuthentication($pStrToken = ''){

		/*Variable initialization */
		$strReturnArr	= array();

		/* If logger code is not found the do needful */
		if($pStrToken == ''){
			/* return empty set */
			return $strReturnArr;
		}
		/* getting the logger details */
		$strloggerArr	=  $this->_objDefaultModel->getDataFromTable(array('table'=>'trans_logger','column'=>array('id','token','logger_data','user_code'),'where'=>array('token'=>$pStrToken)));

		return !empty($strloggerArr) ? true : false;

		/* Return the logger details */
		return $strloggerArr;
	}
}
?>