<?php
/****************************************************************************************************/
/* Purpose      : Managing the Socialfeeds business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Socialfeeds extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName = "";
	private $_strDataSet 	   = array();
	private $_strWidgeSlug	   = "";
	
	/**********************************************************************/
	/*Purpose       : Element initialization.
	/*Inputs        : $pStrModuleSlug :: Module slug,
					: $pStrDataSetArr :: Module specific data set .
	/*Created By    : Vipin Kumar R. Jaiswar.
	/**********************************************************************/
	public function __construct($pStrModuleSlug = array(), $pStrDataSetArr = array()){
		/* calling parent construct */
		parent::__construct($pStrModuleSlug[0]);
		
		/* Variable initialization */
		$this->_strClassFileName	= $pStrModuleSlug[0];
		$this->_strWidgeSlug		= $pStrModuleSlug[1];
		$this->_strDataSet		= $pStrDataSetArr;
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the default hooked method.
	/*Inputs	: None.
	/*Returns	: Data Set.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index($pDataArr = array()){ 
            /* if empty result set pass then do needful */
            if(empty($this->_strDataSet)){
                    /* Return empty result set */
                    return $this->_strDataSet;
            }		
		/* Start - Data manipulation logic will go here */
		/* End   - Data manipulation logic will go here */
		
		/* Setting the view */
		/* $data["actionSection"] = $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		
		/* Return the dataset */
		return $this->_strDataSet;
	}
}