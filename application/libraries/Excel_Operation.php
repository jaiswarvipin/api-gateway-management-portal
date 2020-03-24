<?php
/***********************************************************************/
/* Purpose 		: Managing the excel related operation.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

/** Include PHPExcel */
require_once APPPATH.'third_party'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php';


class Excel_Operation extends PHPExcel{
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: None.
	/* Returns	: None.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
	}
}