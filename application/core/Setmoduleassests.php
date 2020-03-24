<?php
/***********************************************************************/
/* Purpose 		: manipulating the CI Loader.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Setmoduleassests extends Requestprocess {
	
	/* variable initialization */
	private $_strModuleName	= '';
	
	/**********************************************************************/
	/*Purpose       : Element initialization.
	/*Inputs        : $pStrModuleName :: Name.
	/*Returns		: None.
	/*Created By    : Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct($pStrModuleName = ''){
		/* calling parent construct */
		parent::__construct(false);
		
		/* Setting module Value */
		$this->_strModuleName = $pStrModuleName;
	}
	
	/**********************************************************************/
	/*Purpose       : Set the view.
	/*Inputs        : $pStrModuleName :: Name.
	/*Returns		: view path.
	/*Created By    : Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setView($pStrViewName = ''){
		/* return the view path */
		return '..'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->_strModuleName.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$pStrViewName;
	}
}