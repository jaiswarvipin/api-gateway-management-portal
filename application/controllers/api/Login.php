<?php
/***********************************************************************/
/* Purpose 		: Provide REST API For Database Table with Filter.
/* Created By 	: Prashant S. Pawar
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');
/* Adding Reset response controller reference to response the requester */
require APPPATH . 'libraries/REST_Controller.php';

class Login extends REST_Controller {

	/********************************************************************/
	/* Purpose 		: Initiating the Default CI Model properties and methods
	/* Inputs		: None.
	/* Returns 		: None.
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	public function __construct(){
		/* Calling RESET construct */
		parent::__construct();
		/* Creating file helper instance */
		$this->load->helper('file');
		/* Creating database helper instance */
		$this->load->database();
	}

	/********************************************************************/
	/* Purpose 		: Checking for login
	/* Inputs		: None.
	/* Returns 		: Authentication Token 
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	public function index_post(){
		/* Variable initialization */
		$status_code 	= 	'400';
		$error_msg 		= 	array('message'=>'Something wrong!');
		$response_data 	= 	array("errors" => $error_msg, 'status_code' => $status_code);
		$strEmail		= 	($this->post('eMaIl') != '') ? $this->post('eMaIl'):'';
		$strPassword	= 	($this->post('pAsSwOrD') != '') ?$this->post('pAsSwOrD'):'';
		
		/* if email or password filed is empty then do needful */
		if(($strEmail == '') || ($strPassword == '')){
			/* Setting error message */
			$error_msg   =  array( 'message' => 'Email address or password value is empty.' );
			/* Setting the response wrapper */
			$response_data  =  array("errors" => $error_msg, 'status_code' => $status_code);
		}

		/* Creating logger object */
		$objLogger 				= 	new Logger();
		/* Setting the request type */
		$objLogger->strPlatform 	= 	'api';
		/* Authenticating the requested credential */
		$response_data 			= 	$objLogger->doAuthincation($strEmail, $strPassword);
		/* Remove the used variables */
		unset($objLogger);
		
		/* Checking for authentication response status */
		if ($response_data['status'] == 1) {
			/* Overwriting the status code */
			$status_code = '200';
		}
		
		/* RESET response */
		$this->response($response_data, $status_code);
		/* stop the execution process */
		return true;
	}
}
