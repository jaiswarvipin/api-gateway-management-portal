<?php
/***********************************************************************/
/* Purpose 		: Managing the excel related operation.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

/** Include PHPExcel */
require_once APPPATH.'third_party'.DIRECTORY_SEPARATOR.'mailchimp-mandrill'.DIRECTORY_SEPARATOR.'Mandrill.php';


class Mailchimp_Operation extends Mandrill{
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: None.
	/* Returns	: None.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct($apikey){
		/* calling parent construct */
		parent::__construct($apikey[0]);
	}
}