<?php
/***********************************************************************/
/* Purpose 		: Provide REST API For Database Table with Filter.
/* Created By 	: Prashant S. Pawar
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');
/* Adding Reset response controller reference to response the requester */
require APPPATH . 'libraries/REST_Controller.php';

class Master extends REST_Controller {

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
		/* Authorized the request  */
		$this->_doAuthincation();
	}
	
	/********************************************************************/
	/* Purpose 		: Validating the APPI call
	/* Inputs		: None.
	/* Returns 		: None.
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	private function _doAuthincation(){
		/* Variable initialization */
		$status_code 				= 400;
		/* Creating logger object */
		$objLogger 				= 	new Logger();
		/* Setting the request type */
		$objLogger->strPlatform = 	'api';

		/* Setting error message */
		$response_data = array('status' => false, 'error_code' => 1, 'code' => 400, 'error' => 'Something went wrong!');
		
		/* Getting header values */
		$strHeadersArr = $this->input->request_headers();
		
		/* Checking header array empty */
		if (empty($strHeadersArr) || (!isset($strHeadersArr['authorization']))) {
			/* Setting error message */
			$response_data = array('status' => false, 'error_code' => 1, 'code' => 400, 'error' => 'Header is missing');
			/* Sending the response */
			$this->response($response_data, $status_code);
		}
		/* Verifying token validity */
		$blnStatus 	= 	$objLogger->verifyAuthentication($strHeadersArr['authorization']);

		/* if not authorized API then do needful */
		if (!$blnStatus) {
			/* Setting error message */
			$response_data = array('status' => false, 'error_code' => 1, 'code' => 400, 'error' => 'Sorry, You are not authenticate user !');
			/* Sending the response */
			$this->response($response_data, $status_code);
		}
	}

	/********************************************************************/
	/* Purpose 		: Execute Query on given table with filter query
	/* Inputs		: $table name with filter query
	/* Returns 		: JSON with Matched filtered data of given table
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	public function index_get($pStrTableRefrence){
		/* Begin, Set default response of error */
		/* Variable initialization */
		$error_msg				= array('message'=>'Something wrong!');
		$status_code 			= '400';
		$strResponseDataArr 	= array("errors" => $error_msg, 'status_code' => $status_code);
		
		/* Begin, Check table exist of not before process */
		if ($this->db->table_exists($pStrTableRefrence) ){
			/* Checking the filters provided by the requester */
			$strFilterArr 		= !empty($this->input->get('filter', TRUE)) ? $this->input->get('filter', TRUE) : array();
 
			/* Begin, Set Limit and OffSet default Limit is 10 and maximum is 500 and OffSet default is 0 */
			$intRecordPerPage 	= !empty($strFilterArr['limit']) ? (($strFilterArr['limit'] >500) ? 500 : $strFilterArr['limit']) : 10;
			$intOffset 			= !empty($strFilterArr['skip']) ? $strFilterArr['skip'] : 0;
	
			/* Setting the query / record set limit */
			$this->db->limit($intRecordPerPage, $intOffset);

			/* variable initialization */
			$strOrderByArr		= $strWhereArr	= $strRequestedColumnArr 		= 	array();
			$blnIsFilter 		= false;
			
			/* Begin, Set Orders, Fields and Where Clause Filter */
			if(!empty($strFilterArr) && is_array($strFilterArr)){
				/* Checking order by clause */
				if (!empty($strFilterArr['order']) && is_array($strFilterArr['order'])) {
					/* Setting order by clause */
					$strOrderByArr = !empty($strFilterArr['order']) ? $strFilterArr['order'] : array();
				}
				/* Checking for requested column */
				if (!empty($strFilterArr['fields']) && is_array($strFilterArr['fields'])) {
					/* Setting requested table columns in the DML query */
					$strRequestedColumnArr = !empty($strFilterArr['fields']) ? $strFilterArr['fields'] : array();
				}
			}
			/* Removed sued variables */
			unset($strFilterArr);
			
			/* Checking Requested column array is empty */
			if(!empty($strRequestedColumnArr)){
				/* iterating the loop */
				foreach ($strRequestedColumnArr as $fieldName => $fieldCheck) {
					/* checking field name is empty */
					if (!$fieldCheck) {
						/* if empty then do not add the field in the query */
						continue;
					}
					/* Checking requested field is exist in the requested table */
					$blnIsfieldExists = $this->db->field_exists($fieldName, $pStrTableRefrence);
					/* if field is not exists then do needful */
					if ($blnIsfieldExists != true){
						/* Setting error code */
						$status_code 		= '400';
						/* Setting error message */
						$error_msg			= array('message'=>'table '. $fieldName .' field name does not exist');
						/* Setting the response wrapper */
						$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
						/* Sending the response */
						$this->response($strResponseDataArr, $status_code);
						/* stop the execution process */
						return false;
					}
				}
			}
			
			/* Setting the requested filter */
			$this->_setFilter($pStrTableRefrence);
			
			/* Removed unused variables */
			unset($strWhereArr);
			
			/* Checking for ORDER BY clause */
			if(!empty($strOrderByArr)){
				/* Iterating the order by clause loop */
				foreach ($strOrderByArr as $orderField) {
					/* Field formating */
					$orderField = trim($orderField);
					/* Creating array for clause validation */
					$orderFieldArr = explode(" ", $orderField); 
					
					/* Checking for order count clause */
					if (count($orderFieldArr) != 2) {
						/* Setting error code */
						$status_code 		= '400';
						/* Setting error message */
						$error_msg			= array('message'=>'Wrong order by clause');
						/* Setting the response wrapper */
						$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
						/* Sending the response */
						$this->response($strResponseDataArr, $status_code);
						/* stop the execution process */
						return false;
					}
					/* Setting order by column name */
					$orderFieldName 		= $orderFieldArr[0];
					/* Setting order by direction ASC/DESC */
					$orderFieldOrderDir 	= $orderFieldArr[1];

					/* Checking requested order by field exists */
					$blnIsFieldExists = $this->db->field_exists($orderFieldName, $pStrTableRefrence);
					
					/* if field is not exists then do needful */
					if ($blnIsFieldExists != true){
						/* Setting error code */
						$status_code 		= '400';
						/* Setting error message */
						$error_msg			= array('message'=>'table '. $fieldName .' field name does not exist 2');
						/* Setting the response wrapper */
						$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
						/* Sending the response */
						$this->response($strResponseDataArr, $status_code);
						/* stop the execution process */
						return false;
					}
					/* Setting order by clause in DML operation */
					$this->db->order_by($orderFieldName, $orderFieldOrderDir);

				}
				/* Removed unused variables */
				unset($strOrderByArr);
			}

			/* If requested field array is not empty then do needful */
			if (!empty($strRequestedColumnArr)) {
				/* Converting column array in the string separated by comma */
				$strFields 	= implode(', ', array_keys($strRequestedColumnArr));
				/* Setting column of the requested schema */
				$this->db->select($strFields);
				/* Removed unwanted fields */
				unset($strRequestedColumnArr, $strFields);
			}else{
				/* If no field requested then show all fields */
				$this->db->select('*');
			}

			/* Setting the schema */
			$this->db->from($pStrTableRefrence);
			/* executing the query */
			$tableData 			= $this->db->get()->result_array();
			/* Getting executed query */
			$strExecutedQuery 	= $this->db->last_query();
			
			/* if request for displaying query then do needful */
			if(isset($strFilterArr['show_query'])){
				/* Setting error code */
				$status_code 		= '200';
				/* Setting error message */
				$error_msg			= array('message'=>$strExecutedQuery);
				/* Setting the response wrapper */
				$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
				/* Sending the response */
				$this->response($strResponseDataArr, $status_code);
				/* stop the execution process */
				return false;
			}
			
			/* Setting error code */
			$status_code 		= '200';
			
			/* if data found then do needful */
			if (!empty($tableData) && is_array($tableData)) {
				/* Setting error message */
				$strResponseDataArr = array("data" => $tableData, 'status_code' => $status_code);
			}else{
				/* Setting error message */
				$error_msg=array('message'=>'Data not founded');
			
				/* Setting the response wrapper */
				$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
			}
		}else{
			/* Setting error code */
			$status_code 		= '400';
			/* Setting error message */
			$error_msg			= array('message'=>'table does not exist');
			/* Setting the response wrapper */
			$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
			/* Sending the response */
			$this->response($strResponseDataArr, $status_code);
			/* stop the execution process */
			return false;
		}
		/* Sending the response */
		$this->response($strResponseDataArr, $status_code);
		/* stop the execution process */
		return true;
	}


	/********************************************************************/
	/* Purpose 		: Insert new record to given table
	/* Inputs		: $pStrTableRefrence :: table name with inserting data
	/* Returns 		: JSON with appropriate message of insertion operation
	/* Created By 	: Prashant S. Pawar
	/********************************************************************/
	public function index_post($pStrTableRefrence){
		/* Variable initialization */
		$strPostArr 		= 	$this->post();
		$error_msg 			= 	array('message'=>'Something wrong!');
		$status_code 		= 	'400';
		$strResponseDataArr = 	array("errors" => $error_msg, 'status_code' => $status_code);
		$blnIsError 		= 	false;
		$strErrorArr		= 	array();
		
		/* Checking for table exists or not */
		if ($this->db->table_exists($pStrTableRefrence) ){
			/* getting all column of the tables */
			$query 					= 	$this->db->query('SHOW COLUMNS FROM ' . $pStrTableRefrence);
			/* Converting the result set in the array format */
			$strRequestedColumnArr 	= 	$query->result_array();
			
			/* iterating the requested data */
			foreach ($strPostArr as $fieldName => $fieldValue) {
				/* Checking is field exist or not */
				$blnIsFieldExists = $this->db->field_exists($fieldName, $pStrTableRefrence);
				/* if field is not exists then do needful */
				if ($blnIsFieldExists != true){
					/* Setting error code */
					$status_code 		= '400';
					/* Setting error message */
					$error_msg			= array('message'=>'table '. $fieldName .' field name does not exist 2');
					/* Setting the response wrapper */
					$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
					/* Sending the response */
					$this->response($strResponseDataArr, $status_code);
					/* stop the execution process */
					return false;
				}
			}
			
			/* iterating the schema column array to verify mandatory data is pass by the requester */
			foreach ($strRequestedColumnArr as $field) {
				/* Data verification */
				if ($field['Null'] == 'NO' && $field['Extra'] != 'auto_increment' &&  $field['Default'] == '' && array_key_exists($field['Field'], $strPostArr) != true){
					/* Setting flag */
					$blnIsError 	= 	true;
					/* Setting error  message */
					$strErrorArr[] 	= 	$field['Field'] . ' is require field.';
				}
			}

			/* is no error found then do needful */
			if ($blnIsError == false) {
				/* Issuing insert command with requested dataset */
				$this->db->insert($pStrTableRefrence, $strPostArr);
				/* Getting last insert auto-increment is from same schema */
				$intInsertId = $this->db->insert_id();
				
				/* if insert done successfully then do needful */
				if ($intInsertId) {
					/* Setting error code */
					$status_code 		= '200';
					/* Setting error message */
					$strResponseDataArr = array("message" => 'Data Inserted Successfully.', 'status_code' => $status_code);
				}
			}else{
				/* Creating error message */
				$strErrorMessage	= array('message'=> implode(", ", $strErrorArr));
				/* Setting error message */
				$strResponseDataArr = array("errors" => $strErrorMessage, 'status_code' => $status_code);
			}
		}else{
			/* Setting error code */
			$status_code 		= '400';
			/* Setting error message */
			$error_msg			= array('message'=>'table '. $fieldName .' field name does not exist 2');
			/* Setting the response wrapper */
			$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
		}
		/* Sending the response */
		$this->response($strResponseDataArr, $status_code);
		/* stop the execution process */
		return true;
	}

	/************************************************************************************/
	/* Purpose 		: Update existing record to given table with filter query
	/* Inputs		: $pStrTableRefrence :: table name with updating data,
					: $pIntPrimaryCode	:: Primary Code.
	/* Returns 		: JSON with appropriate message of updation operation
	/* Created By 	: Prashant S. Pawar
	/************************************************************************************/
	public function index_put($pStrTableRefrence, $pIntPrimaryCode = ''){
		/* Variable initialization */
		$strPostArr 		= 	$this->put();
		$error_msg 			= 	array('message'=>'Something wrong!');
		$status_code 		= 	'400';
		$strResponseDataArr = 	array("errors" => $error_msg, 'status_code' => $status_code);
		$error 				= 	$blnIsFilter	= false;
		$errorArr 			= 	$strRequestedColumnArr	= $strWhereArr	= array();
		
		/* Checking for table exists */
		if ($this->db->table_exists($pStrTableRefrence) ){
			/* if primary code is not passed then do needful */
			if (empty($pIntPrimaryCode)) {
				/* Setting the requested filter */
				$this->_setFilter($pStrTableRefrence);
			}
			/* Update the requested record */
			$strResponseDataArr	= $this->_setUpdateRecords($pStrTableRefrence, $pIntPrimaryCode, $strPostArr );
			/* Setting error code */
			$status_code		= isset($strResponseDataArr['status_code'])?$strResponseDataArr['status_code']:'400';
		}else{
			/* Setting error code */
			$status_code 			= '400';
			/* Setting error message */
			$error_msg				= array('message'=>'table does not exist');
			/* Setting the response wrapper */
			$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
		}
		
		/* Sending the response */
		$this->response($strResponseDataArr, $status_code);
		/* stop the execution process */
		return true;
	}

	/*****************************************************************************/
	/* Purpose 		: Delete existing record from given table with filter query
	/* Inputs		: $pStrTableRefrence :: table name,
					: table name with filter for delete
	/* Returns 		: JSON with appropriate message of deletion operation
	/* Created By 	: Prashant S. Pawar
	/*****************************************************************************/
	public function index_delete($pStrTableRefrence, $pIntPrimaryId = ''){
		/* Variable initialization */
		$error_msg 			= 	array('message'=>'Something wrong!');
		$status_code 		= 	'400';
		$strResponseDataArr	= 	array("errors" => $error_msg, 'status_code' => $status_code);
		$error 				= 	$blnIsFilter	= false;
		$errorArr 			= 	$strWhereArr	= array();

		/* Checking for table exists */
		if ($this->db->table_exists($pStrTableRefrence) ){
			/* if primary code is not passed then do needful */
			if (empty($pIntPrimaryId)) {
				/* Setting the requested filter */
				$this->_setFilter($pStrTableRefrence);
			}
			
			/* Update the requested record */
			$strResponseDataArr	= $this->_setUpdateRecords($pStrTableRefrence, $pIntPrimaryId, array(), true);
			/* Setting error code */
			$status_code		= isset($strResponseDataArr['status_code'])?$strResponseDataArr['status_code']:'400';
		}else{
			/* Setting error code */
			$status_code 			= '400';
			/* Setting error message */
			$error_msg				= array('message'=>'table does not exist');
			/* Setting the response wrapper */
			$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
		}
		
		/* Sending the response */
		$this->response($strResponseDataArr, $status_code);
		/* stop the execution process */
		return true;
	}
	
	/*****************************************************************************/
	/* Purpose 		: Setting the requested filter to perform the DML operation
	/* Inputs		: $pStrTableRefrence :: Table for Reference.
	/* Returns 		: None.
	/* Created By 	: Prashant S. Pawar
	/*****************************************************************************/
	private function _setFilter($pStrTableRefrence){
		/* Variable initialization */
		$error_msg 				= array('message'=>'Something wrong!');
		$status_code 			= '400';
		$strResponseDataArr		= array("errors" => $error_msg, 'status_code' => $status_code);
		$strRequestedColumnArr	= $strWhereArr	= array();
		
		/* Setting pass filter clause */
		$strFilterArr = !empty($this->input->get('filter', TRUE)) ? $this->input->get('filter', TRUE) : array();

		/* if requester pass the filter */
		if(!empty($strFilterArr) && is_array($strFilterArr)){
			/* Checking the WHERE clause */
			if (!empty($strFilterArr['where']) && is_array($strFilterArr['where'])) {
				/* Setting the where clause */
				$strWhereArr = !empty($strFilterArr['where']) ? $strFilterArr['where'] : array();
			}
		}
		/* REmoved used variables */
		unset($strFilterArr);

		/* if where filter array pass then do needful */
		if(!empty($strWhereArr)){
			/* Iterating the filter loop */
			foreach ($strWhereArr as $fieldName => $fieldVal) {
				/* if field name is not operator then do needful */
				if ($fieldName != 'and' && $fieldName != 'or' ) {
					/* Checking field exist in the database */
					$fieldExists = $this->db->field_exists($fieldName, $pStrTableRefrence);
					/* if field is not exists then do needful */
					if ($fieldExists != true){
						/* Setting error code */
						$status_code 		= '400';
						/* Setting error message */
						$error_msg			= array('message'=>'table '. $fieldName .' field name does not exist 2');
						/* Setting the response wrapper */
						$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
						/* Sending the response */
						$this->response($strResponseDataArr, $status_code);
						/* stop the execution process */
						return false;
					}
				}
				
				/* checking is field name and field value is not empty string */
				if( !empty($fieldName) && !empty($fieldVal) && is_string($fieldVal)){
					/* Setting filter in the DML operation */
					$this->db->where($fieldName, $fieldVal);
				}
				
				/* checking is field name and field value is not value array */
				if ( !empty($fieldName) && !empty($fieldVal) && is_array($fieldVal)) {
					/* Setting column name */
					$fieldName = trim(strtolower($fieldName));

					/*******************/
					/* AND / OR CLAUSE */
					/*******************/
					if ($fieldName == 'and' || $fieldName == 'or' ) {

						/* Getting number of AND / OR grouping requested */
						$intfieldValCnt = count($fieldVal);

						/* if multiple combination then start the group */
						if ($intfieldValCnt > 1) {
							/* Start the group */
							$this->db->group_start();
						}
						/* Iterating the multiple combination */
						foreach ($fieldVal as $fieldValN) {
							/* Iterating the loop for combination */
							foreach ($fieldValN as $fieldValNKey => $fieldValNVal) {
								/* if or combination then do needful */
								if ($fieldName == 'or') {
									/* Setting the OR clause */
									$this->db->or_where($fieldValNKey, $fieldValNVal);
								}else{
									/* Setting the AND clause */
									$this->db->where($fieldValNKey, $fieldValNVal);
								}
							}
						}
						/* if multiple combination then start the group */
						if ($intfieldValCnt > 1) {
							/* Closing the group */
							$this->db->group_end();
						}
					}
					
					/************************/
					/* NOT EQUAL TO CALUSE 	*/
					/************************/
					if ( !empty($fieldVal['neq'])) {
						/* Setting the NOT Equal clause */
						$this->db->where( $fieldName . ' !=', $fieldVal['neq']);
					}
					
					/****************************/
					/* IN and NOT EQUAL CLAUSE  */
					/****************************/
					if ( !empty($fieldVal['inq']) || !empty($fieldVal['nin']) ) {
						/* if NOT Equal requested then do needful */
						if (!empty($fieldVal['nin'])) {
							/* Setting NOT Equal clause */
							$this->db->where_not_in($fieldName, $fieldVal['nin']);
						}else{
							/* Setting Equal clause */
							$this->db->where_in($fieldName, $fieldVal['inq']);
						}
					}
					
					/****************************/
					/* LIKE and NOT LIKE CLAUSE */
					/****************************/
					if (!empty($fieldVal['like']) || !empty($fieldVal['nlike'])) {
						/* Setting filter flag */
						if (!empty($fieldVal['nlike'])){
							/* if NOT Like requested then do needful */
							$this->db->not_like($fieldName, $fieldVal['nlike']);
						}else{
							/* Setting NOT Like clause */
							$this->db->like($fieldName, $fieldVal['like']);
						}
					}
					
					/****************************/
					/* >, >=, N and <=  CLAUSE  */
					/****************************/
					if (!empty($fieldVal['gt']) || !empty($fieldVal['gte']) || !empty($fieldVal['lt']) || !empty($fieldVal['lte'])) {
						/* if Greater requested then */
						if (!empty($fieldVal['gt'])) {
							/* Setting Symbol */
							$gtGtEtLtLtEt 		= '>';
							/* Setting Field value */
							$fieldNewVal 		= $fieldVal['gt'];
						}elseif (!empty($fieldVal['gte'])) {
							/* Setting Symbol */
							$gtGtEtLtLtEt 		= '>=';
							/* Setting Field value */
							$fieldNewVal 		= $fieldVal['gte'];
						}elseif (!empty($fieldVal['lte'])) {
							/* Setting Symbol */
							$gtGtEtLtLtEt 		= '<=';
							/* Setting Field value */
							$fieldNewVal 		= $fieldVal['lte'];
						}else{
							/* Setting Field value */
							$fieldNewVal 		= $fieldVal['lt'];
						}
						/* Setting the filter clause */
						$this->db->where($fieldName . ' ' . $gtGtEtLtLtEt . ' ' . $fieldNewVal);
					}
					
					/*******************/
					/* BETWEEN CLAUSE  */
					/*******************/
					if ( !empty($fieldVal['between']) && is_array($fieldVal['between']) && isset($fieldVal['between'][0]) && isset($fieldVal['between'][1]) ) {
						/* Setting the filter clause */
						$this->db->where($fieldName . ' BETWEEN "'. $fieldVal['between'][0]  . '" and "'. $fieldVal['between'][1] .'"');
					}
				}
			}
		}
	}
	
	/*****************************************************************************/
	/* Purpose 		: Updating the requested record.
	/* Inputs		: $pIntPrimaryId :: Primary Key,
					: $pStrTableRefrence :: Table for Reference,
					: $pBlnIsDeleted :: Deleted flag
	/* Returns 		: Transaction Status.
	/* Created By 	: Prashant S. Pawar
	/*****************************************************************************/
	private function _setUpdateRecords($pStrTableRefrence, $pIntPrimaryId = '', array $strPostArr = array(), $pBlnIsDeleted = false){
		/* Variable initialization */
		$error_msg 				= 	array('message'=>'Something wrong!');
		$status_code 			= 	'400';
		$strResponseDataArr		= 	array("errors" => $error_msg, 'status_code' => $status_code);
		$strRequestedColumnArr	= array();
		$strResponseText		= "Updated";
		
		/* if delete request then do needful */
		if($pBlnIsDeleted){
			/* value overwriting */
			$strResponseText		= "Deleted";
		}
		
		/* getting all column of the tables */
		$query 					= 	$this->db->query('SHOW COLUMNS FROM ' . $pStrTableRefrence);
		/* Converting the result set in the array format */
		$strRequestedColumnArr 	= 	$query->result_array();

		/* Variable initialization */
		$strPrimaryKey 			= 'id';
		/* Iterating the field requested by caller   */
		foreach ($strRequestedColumnArr as $field) {
			/* if iterated field is PRIMARY then do needful */
			if ($field['Key'] == 'PRI'){
				/* setting the primary key field name */
				$strPrimaryKey = $field['Field'];
				break;
			}
		}

		/* Checking for primary code is pass */
		if (!empty($pIntPrimaryId)) {
			/* Setting primary column filter */
			$this->db->where($strPrimaryKey, $pIntPrimaryId);
		}

		/* Begin, Check If is Delete or Update */
		if (!$pBlnIsDeleted && !empty($strPostArr) && is_array($strPostArr)) {
			/* Begin, Check If is Update the pick data from $strPostArr array and set to update */
			foreach ($strPostArr as $fieldName => $fieldValue) {

				/* Checking requested field is exist in the requested table */
				$fieldExists = $this->db->field_exists($fieldName, $pStrTableRefrence);

				if ($fieldExists != true){
					$status_code = '400';
					$error_msg=array('message'=>'table '. $fieldName .' field name does not exist 1');
					$response_data = array("errors" => $error_msg, 'status_code' => $status_code);
					$this->response($response_data, $status_code);
					return false;
				}
				$this->db->set($fieldName, $fieldValue);
			}
			/* End, Check If is Update the pick data from $strPostArr array and set to update */
		}else{
			/* Begin, Set deleted flag */
			$this->db->set('deleted', 1);
			/* End, Set deleted flag */
		}
		/* End, Check If is Delete or Update */
		
		/* executing the update DML operation */
		/**********$this->db->delete($pStrTableRefrence); ********/
		$this->db->update($pStrTableRefrence);
		/* Getting executed query */
		$strExecutedQuery 	= $this->db->last_query();
		
		/* if request for displaying query then do needful */
		if(isset($strFilterArr['show_query'])){
			/* Setting error code */
			$status_code 		= '200';
			/* Setting error message */
			$error_msg			= array('message'=>$strExecutedQuery);
			/* Setting the response wrapper */
			$strResponseDataArr = array("errors" => $error_msg, 'status_code' => $status_code);
			/* Sending the response */
			$this->response($strResponseDataArr, $status_code);
			/* stop the execution process */
			return false;
		}
		/* Checking number of rows got effected after executing the DML query */
		$updateStatus = ($this->db->affected_rows() > 0) ? TRUE : FALSE;
		
		/* If operation executed successfully then do needful */
		if ($updateStatus) {
			/* Setting error code */
			$status_code 		= '200';
			/* Setting error message */
			$strResponseDataArr = array("message" => 'Data '.$strResponseText.' Successfully.', 'status_code' => $status_code);
		}
		
		/* Return the record status */
		return $strResponseDataArr;
	}
}
