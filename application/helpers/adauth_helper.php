<?php 
/***********************************************************************/
/* Purpose 		: Authenticating teh use rwith Active Directive.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Adauth{
	/* Variable initialization */
	private $_objDefaultModel	= null;
	private $_strPrimaryTable	= "master_user";
	private $_strAuthType		= '';
	public $strPlatform 		= 'web';
	
	/*******************************************************************/
	/*Purpose	: Default method to be executed.
	/*Inputs	: $pStrAuthType: Authtication process, AD or SAML.
	/*Returns 	: None.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	public function __construct($pStrAuthType = 'AD'){
		/* Creating the default model */
		$this->_objDefaultModel	= new Dbrequestprocess_model();
		$this->_strAuthType		= $pStrAuthType;
	}
	
	/*******************************************************************/
	/*Purpose	: Authentication user from ADID.
	/*Inputs	: $pStrParamArr :: Auth param array.
	/*Returns 	: Auth values array.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	public function doAuth($pStrParamArr){
		
	}
	
	/*******************************************************************/
	/*Purpose	: Authentication user from ADID using SAML.
	/*Inputs	: $pStrParamArr :: Auth param array.
	/*Returns 	: Auth values array.
	/*Created By: Jaiswar Vipin Kumar R.
	/*******************************************************************/
	private function _doAuthUsingSGML($pStrParamArr	= array()){
		
	}
}
?>