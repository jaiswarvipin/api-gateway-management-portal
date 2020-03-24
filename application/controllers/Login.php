<?php
/***********************************************************************/
/* Purpose 		: Authentication of user.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	/* variable deceleration */
	private $_strPrimaryTableName	= 'master_user';

	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* variable initialization */
		$dataArr	= array();

		/* Load the login */
		$dataArr['body']	= $this->load->view('auth/login', array('strSource'=>'cms'), true);
		
		/* Loading the template for browser rending */
		$this->load->view(DEFAULT_TEMPLATE, $dataArr);

		/* Removed used variable */
		unset($dataArr);
	}

	/**********************************************************************/
	/*Purpose 	: Authenticating the user.
	/*Inputs	: None.
	/*Returns 	: Authentication response.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function doAuthincation(){
		/* variable initialization */
		$strEmail		= ($this->input->post('txtEmail') != '')?$this->input->post('txtEmail'):'';
		$strPassword	= ($this->input->post('txtPassword') != '')?$this->input->post('txtPassword'):'';

		/* if email or password filed is empty then do needful */
		if(($strEmail == '') || ($strPassword == '')){
			jsonReturn(array('status'=>0,'message'=>'Email address or password value is empty.'), true);
		}
		
		/* Creating logger object */
		$objLogger				= new Logger();
		/* Setting the request type */
		$objLogger->strPlatform = 	'web';
		/* Logger Object registration request */
		$strResponseArr 		= $objLogger->doAuthincation($strEmail, $strPassword);
		/* Removed used variable */
		unset($objLogger);

		/* Creating the common DML object reference */
		$ObjdbOperation	= new Dbrequestprocess_model();
		/* Checking for requested user authentication */
		$blnStatus		= $ObjdbOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName, 'where'=>array('user_email'=>$strEmail,'password'=>md5($strPassword))));
		/* removed used variables */
		unset($ObjdbOperation);
		
		/* Checking for login status */
		if ($blnStatus) {
			/* return response */
			jsonReturn(array('status'=>1,'message'=>'Valid account .','destinationURL'=>SITE_URL.'settings/userrole'),true);
		}
		/* return the error response */
		jsonReturn(array('status'=>0,'message'=>'Invalid account.'), true);
	}
	
	/**********************************************************************/
	/*Purpose 	: Logging out existing session.
	/*Inputs	: None.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function lougout(){
		/* Creating logger object */
		$objLogger	= new Logger();
		/* removed existing all cookies */
		$objLogger->doDistryLoginCookie();
		/* removed existing system cookie */
		$objLogger->doDestroySystemCookie();
		/* Removed used variable */
		unset($objLogger);
		/* Redirect to login screen */
		redirect(SITE_URL.'login');
	}
}