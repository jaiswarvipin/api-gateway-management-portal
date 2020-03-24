<?php 
/***********************************************************************/
/* Purpose 		: Managing the file I/O operation.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Files{

	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_frameworkObj		= '';
	private $_fileDriverSet 	= array();

	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code
	/* Returns	: None.
	/* Created By : Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
		/* CI instance reference */
		$this->_frameworkObj =& get_instance();
	}

	/***************************************************************************************/
	/*Purpose 	: Recursive complete folder copying.
	/*Inputs	: $pStrSource	:: Source folder path,
				: $pStrDestination :: Destination folder path.
	/*Returns 	: Operation status.
	/*Created By: Vipin Kumar R. Jaiswar.
	/***************************************************************************************/
	function recursiveCopy($pStrSource = '', $pStrDestination = ''){
		/* if source and destination files name are empty then do needful */
		if(($pStrSource == '') || ($pStrDestination == '')){
			/* Returns operation errors */
			return false;
		}
		
		/* Check for symlinks */
		if (is_link($pStrSource)) {
			return symlink(readlink($pStrSource), $pStrDestination);
		}
    
		/* Simple copy for a file */
		if (is_file($pStrSource)) {
			return copy($pStrSource, $pStrDestination);
		}

		/* Make destination directory */
		if (!is_dir($pStrDestination)) {
			mkdir($pStrDestination);
		}

		/* Loop through the folder */
		$dirObj = dir($pStrSource);
		/* Iterating the loop */
		while (false !== $entry = $dirObj->read()) {
			/* Skip pointers */
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			/* Deep copy directories */
			$this->recursiveCopy("$pStrSource/$entry", "$pStrDestination/$entry");
		}

		/* Clean up */
		$dirObj->close();
		
		/* return the operation status */
		return true;
	}

	/***************************************************************************************/
	/*Purpose 	: Recursive complete folder copying.
	/*Inputs	: $pStrSource	:: Source folder path,
				: $pStrDestination :: Destination folder path.
	/*Returns 	: Operation status.
	/*Created By: Vipin Kumar R. Jaiswar.
	/***************************************************************************************/
	function setModuleClassFile($widgetName, $path){
		/* Defining the file path */
		$strFileName = $path.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.ucfirst($widgetName).'.php';
		/* Creating the controller file  */
		try{
			/* Creating the file */
			$fileObj 		= fopen($strFileName, 'w');
			$strFileData 	= 
'<?php
/****************************************************************************************************/
/* Purpose      : Managing the '.ucfirst($widgetName).' business logic in this hooked class.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class '.ucfirst($widgetName).' extends Setmoduleassests {
	/* variable initialization */
	private $_strClassFileName = "";
	private $_strDataSet 	   = array();
	private $_strWidgeSlug	   = "";
	private $_strSchemaName		= "{SCHEMA NAME}";
	
	/**********************************************************************/
	/*Purpose       : Element initialization.
	/*Inputs        : $pStrModuleSlug :: Module slug,
					: $pStrDataSetArr :: Module specific data set .
	/*Created By    : Jaiswar Vipin Kumar R.
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
		/* $this->_strDataSet["actionHooksArr"][] 		= $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		/* $this->_strDataSet["customWidgetHooksArr"][] = $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		/* $this->_strDataSet["injectView"][] 			= $this->load->view($this->setView("index"), $this->_strDataSet, true); */
		/* $this->_strDataSet["attri_config"][] 			= array(
																	"{SCHEME COLUMN NAME}" => array(
																					"custom_field"=>1,
																					"attri_slug_name"=>"{SCHEME COLUMN NAME}",
																					"attri_data_type"=>"{VALIDATION_TYPE}",
																					"attri_default_value"=>"",
																					"attri_value_list"=>"serialize(array())",
																					"is_mandatory"=>1,
																					"attri_validation"=>"{VALIDATION_TYPE}",
																					"schema_name"=>"{SCHEME NAME}",
																	)
																);
		*/
		/* Return the dataset */
		return $this->_strDataSet;
	}
	
	/**********************************************************************/
	/*Purpose 	: Managing the custom widget custom configuration.
	/*Inputs	: None.
	/*Returns	: Configuration dataset.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function setConfiguration(){
		/* Check the attributes type supported by system */
		/* debugVar(unserialize(LEAD_ATTRIBUTE_INPUT_ELEMENT), true); */
		
		/* Check the attributes validation type supported by system */
		/* debugVar(unserialize(LEAD_ATTRIBUTE_INPUT_VALIDATION), true); */
		
		/* variable initialization */
		$strConfigArr["view"]	= array(
											"{SCHEME COLUMN NAME}" => array(
															"custom_field"=>1,
															"attri_slug_name"=>"{SCHEME COLUMN NAME}",
															"attri_data_type"=>"{VALIDATION_TYPE}",
															"attri_default_value"=>"",
															"attri_value_list"=>"serialize(array())",
															"is_mandatory"=>1,
															"attri_validation"=>"{VALIDATION_TYPE}",
															"schema_name"=>"{SCHEME NAME}",
											)
										);
										
		/* if parent schema is passed then do needful */
		if(isset($this->_strDataSet["table"]) && ($this->_strDataSet["table"] != "")){
			/* Custom Query */
			$strConfigArr["customQuery"]	= array(
														"table"=>$this->_strDataSet["table"],
														"column"=>array(),
														"where"=>array(),
												);
		}
		
		/* return configuration set */
		return $strConfigArr;
		
	}
}';
			fwrite($fileObj, $strFileData);
		}catch(Exception $e){
			return $e;
		}
	}	

	/***************************************************************************************/
	/*Purpose 	: Upload Files on with given File Driver.
	/*Inputs	: $pStrWidgetDataFileName :: File reference object name,
				: $pStrFileObject :: File Object Refrence,
				: $pStrConfigArr :: CI I/O configuration array,
				: $strWidgetAttribute :: Widget attributes array,
				: $pStrFileDriver :: file upload drivers,
				: $pBlnRetrunABSPath :: return relative or absolute file path. 
	/*Returns 	: Operation status.
	/*Created By: Vipin Kumar R. Jaiswar
	/***************************************************************************************/
	public function uploadFile($pStrWidgetDataFileName = '', $pStrFileObject = array(), $pStrConfigArr = array(), $strWidgetAttribute, $pStrFileDriver = 'local', $pBlnRetrunABSPath = false){
		/* if CI I/O configuration details not set then do needful  */
		if(empty($pStrFileObject)){
			/* return error message */
			return array('status' => false, 'message' => "Select file information looks corrupted.");
		}
		
		/* if CI I/O configuration details not set then do needful  */
		if(empty($pStrConfigArr)){
			/* return error message */
			return array('status' => false, 'message' => "CI I/O configuration details are not set.");
		}
		
		/*Get File Driver Details and It's Credential by Driver Name*/
		$strRerturnArr = $this->_getFileDriverCredentialByDriverName($pStrFileDriver);
		
		/* Validate File Driver Details set or not */
		if (empty($strRerturnArr)) {
			/* return error message */
			return array('status' => false, 'message' => "$pStrFileDriver - file uploading configuration details not found.");
		}

		/*Validate File Driver Credential set or not*/
		if (empty($strRerturnArr[0]['value_description'])) {
			/* return error message */
			return array('status' => false, 'message' => "$pStrFileDriver - file uploading Credentials is not set.");
		}
		
		/* File Driver Credential JSON Decode */
		$valueDescription = json_decode($strRerturnArr[0]['value_description'], true);

		switch (strtolower($pStrFileDriver)){
			/************ Local File Upload *****************/
			case "local":
				/* check file driver already set or not if not then set */
				$strDirPath = !empty($valueDescription['path']) ? $valueDescription['path'].DIRECTORY_SEPARATOR : '';
				/* if directory path is not set then do needful */
				
				if($strDirPath == ''){
					/* return error message */
					return array('status' => false, 'message' => "File uploading path is not set.");
				}

				/*Set Upload Path*/
				$pStrConfigArr['upload_path'] 		= 	'uploads'.DIRECTORY_SEPARATOR.str_replace('\\',DIRECTORY_SEPARATOR,str_replace('/',DIRECTORY_SEPARATOR, $strDirPath));
				
				/* Upload file On Local Directory of Server */
				$strUploadDataArr = $this->_uploadFileLocally($pStrWidgetDataFileName, $pStrFileObject, $pStrConfigArr);
				
				/* if file uploaded successfully then do needful */
				if (!empty($strUploadDataArr) && $strUploadDataArr['status']) {
					/* Return ABS path of the uploaded file */
					if($pBlnRetrunABSPath){
						/* Setting the return file name */
						$strReturnFileName 	= 	BASE_PATH.DIRECTORY_SEPARATOR.$pStrConfigArr['upload_path'] .DIRECTORY_SEPARATOR . $strUploadDataArr['upload']['file_name'];
					}else{
						/* Setting the return file name */
						$strReturnFileName 	= 	$pStrConfigArr['upload_path'] .DIRECTORY_SEPARATOR . $strUploadDataArr['upload']['file_name'];
					}
					
					/* return the working details */
					return array('status' => true, 'message' => 'Successfully', 'filepath' => $strReturnFileName);
				} else {
					/* Return permission path */
					return array('status' => false, 'message' => $strUploadDataArr['message']);
				}
				break;
				
			/************ For S3 Bucket Upload *****************/
			case "s3":
				/* Validate File Driver Details set or not */
				if ((!isset($valueDescription['key'])) || (!isset($valueDescription['secret'])) || (!isset($valueDescription['bucket'])) || ($valueDescription['key'] == '') || ($valueDescription['secret'] == '') || ($valueDescription['bucket'] == '')) {
					/* return error message */
					return array('status' => false, 'message' => "S3 Credentials is not set");
				}
				
				/* If the library is not loaded, Codeigniter will return FALSE */
				if(!$this->_frameworkObj->load->is_loaded('s3')){
					/* Load the S3 library to process the S3 request */
					$this->_frameworkObj->load->library('s3');
				}
				
				/* Setting authorization details */
				S3::setAuth ( $valueDescription['key'], $valueDescription['secret'] ) ;
				
				/* Setting the temp directory path */
				$strDirPath = !empty($strWidgetAttribute['attri_default_value']) ? $strWidgetAttribute['attri_default_value'].DIRECTORY_SEPARATOR : '';

				/*Set Upload Path*/
				$config['upload_path'] 		= 	'uploads'.DIRECTORY_SEPARATOR.$valueDescription['bucket'].DIRECTORY_SEPARATOR.$strDirPath;

				/* Upload file On Local Directory of Server */
				$strUploadDatArr = $this->_uploadFileLocally($pStrWidgetDataFileName, $pStrFileObject, $config);
				/* if file locally uploaded successfully then do needful */
				if (!empty($uploadData1) && $uploadData1['status']) {
					/* Variable initialization */
					$strFileFullPath 	= $strUploadDatArr['upload']['full_path'];
					$strFileName 		= $strUploadDatArr['upload']['file_name'];
					$strURI 			= $strDirPath.$strFileName;
					
					/* Setting the file reference */
					$AWSS3InputObj = S3::inputFile ( $strFileFullPath ) ;
					/* Uploading the file on S3 */
					if ( S3::putObject ( $AWSS3InputObj , $valueDescription['bucket'] , $strURI , S3::ACL_PUBLIC_READ ) ) {
						/* Setting the file path */
						$strFilePath = $strFileFullPath . DIRECTORY_SEPARATOR . $strFileName;
						
						/* Return the file uploading details */
						return array('status' => true, 'message' => 'Successfully', 'filepath' => $strFilePath);
					} else {
						/* Return error message */
						return array('status' => false, 'message' => 'Failed to upload file.');
					}
				} else {
					/* Return file local uploading error message */
					return array('status' => false, 'message' => $strUploadDatArr['message']);
				}
				break;
		}
	}

	/***************************************************************************************/
	/*Purpose 	: Get File Driver Details and Credential by Driver Name
	/*Inputs	: $pStrFileDriver ::File Driver Name
	/*Returns 	: Credential of requested file driver type.
	/*Created By: Vipin Kumar R. Jaiswar
	/***************************************************************************************/
	private function _getFileDriverCredentialByDriverName($pStrFileDriver = 'local'){
		/* checking is driver authorization information exist in the class variable, if not then do needful */
		if (empty($this->_fileDriverSet[$pStrFileDriver])) {
			/* Get requested file drive information */
			$strRerturnArr = $this->_databaseObject->getDataFromTable(array(
																		'table' 	=> 	"master_company_config",
																		'where' 	=> 	array('company_code' => $this->_intCompanyCode, 'key_description' => $pStrFileDriver)
																	)
													);
			/* Setting details in class object */
			$this->_fileDriverSet[$pStrFileDriver] = !empty($strRerturnArr) ? $strRerturnArr : array();
		}
		/* return requested file driver details */
		return $this->_fileDriverSet[$pStrFileDriver];
	}

	/***************************************************************************************/
	/*Purpose 	: Upload file on server
	/*Inputs	: $pStrFileName ::  File name,
				: $pStrFileObject :: File reference object,
				: $pStrConfigArr :: CI I/O configuration array
	/*Returns 	: File I/O status
	/*Created By: Vipin Kumar R. Jaiswar
	/***************************************************************************************/
	private function _uploadFileLocally($pStrFileName = '',$pStrFileObject = array(), $pStrConfigArr = array()){
		/* checking for file name */
		if($pStrFileName != ''){
			/* Setting the file name as server current time stamp */
			$pStrFileName	= date('YmdHis');
		}
		/* checking requested file path exist on server, if not then create it with appropriate permission */
		if(!file_exists($pStrConfigArr['upload_path'])) {
			/* Creating the directory */
			mkdir($pStrConfigArr['upload_path'], 0777, true);
		}
		/* Setting the file reference */
		$_FILES[$pStrFileName]	= $pStrFileObject;
	 
		/* Load upload library and initialize configuration */
		$this->_frameworkObj->load->library('upload', $pStrConfigArr);
		$this->_frameworkObj->upload->initialize($pStrConfigArr);
		
		/* uploading files on the server */
		if ($this->_frameworkObj->upload->do_upload($pStrFileName)) {
			/* get file upload operation details */
			$uploadDataArr = $this->_frameworkObj->upload->data();
			/* Return the file I/O details */
			return array('status' => true, 'message' => 'Uploaded Successfully', 'upload' => $uploadDataArr );
		} else {
			/* return the file uploading error details */
			return array('status' => false, 'message' => $this->_frameworkObj->upload->display_errors());
		}
	}
	
	/***************************************************************************************/
	/*Purpose 	: Exporting data in the CSV.
	/*Inputs	: $pStrColumnHeaderArr :: Columns header array,
				: $pStrDataArr ::  Data Set,
				: $pStrFileName :: exported file name,
				: $pStrPath :: File location
	/*Returns 	: File path
	/*Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************************/
	public function exportData($pStrColumnHeaderArr = array(), $pStrDataArr = array(), $pStrFileName = '', $pStrPath = ''){
		/* if data set is empty then do needful */
		if(empty($pStrDataArr) || empty($pStrColumnHeaderArr)){
			return '';
		}
		/* Setting file location */
		$pStrPath = ($pStrPath == '')?BASE_PATH.'uploads'.DIRECTORY_SEPARATOR.'download'.DIRECTORY_SEPARATOR:$pStrPath;
		
		/* variable initialization */
		$strColumnTitleArr 	= array();
		
		/* iterating the loop */
		foreach($pStrColumnHeaderArr as $pStrColumnHeaderArrKey => $pStrColumnHeaderArrValue){
			/* Setting the column title */
			$strColumnTitleArr[$pStrColumnHeaderArrKey]	= $pStrColumnHeaderArrValue['attri_slug_name'];
		}
		
		/* divide the data array with the RECORD_PER_CSV so as to get the number of files that we have to create, using round function so as to get a proper integer value */
		$timeToLoop 	= ceil(count($pStrDataArr)/RECORD_PER_CSV);

		/* if more then 1 data set found then do needful **/
		if((int)$timeToLoop > 1){
			/* Creating file name */
			$strFileArr	= explode('.',$pStrFileName);
			
			/* Loop over to create the files with the proper data */
			for($intFileCounter = 0; $intFileCounter <= $timeToLoop; $intFileCounter++){
				$strDataArr 	= array_slice($pStrDataArr, ($intFileCounter * RECORD_PER_CSV), RECORD_PER_CSV);
				/* Creating Excel file using the proper data */
				$fileToZip[]	= $this->_createExcelFile($strDataArr, $strColumnTitleArr, $pStrPath.$strFileArr[0]."_".$intFileCounter.'.'.$strFileArr[1]);
			}
			/* Creating a zip file of the xls files created by using the $fileToZip array and providing the destination path for the zip file */
			/* if true, good; if false, zip creation failed */
			$result = $this->_createZipFile($fileToZip, $pStrPath.$strFileArr[0].'.zip');
		}else{
			/* Creating files */
			$result	= $this->_createExcelFile($pStrDataArr, $strColumnTitleArr, $pStrPath.$pStrFileName);
		}
		
		/* return the created file name */
		return $result;
	}
        
    /***************************************************************************************/
	/*Purpose 	: Creating the Excel file.
	/*Inputs	: $strColumnTitleArr :: Columns header array,
						: $pStrDataArr ::  Data Set,
						: $pStrFileName :: creating file name
	/*Returns 	: TRUE
	/*Created By: Vipin Kumar R. Jaiswar.
	/***************************************************************************************/
	private function _createExcelFile($pStrDataArr = array(), $strColumnTitleArr = array(), $pStrFileName = '', $boolCoreLib = true){
		/* if data set is empty then do needful */
		if(empty($pStrDataArr) || empty($strColumnTitleArr)){
			return '';
		}
		
		/* variable initialization */
		$intCounterForLoop	= 0;
		$intColumnIndex		= 65;
		
		$removeKeys = array_diff_key($pStrDataArr[0], $strColumnTitleArr);

		if ($boolCoreLib) {

			$fp = fopen($pStrFileName, 'w');

			fputcsv($fp, $strColumnTitleArr);

			foreach ($pStrDataArr as $row)
			{
				// place row of data
				$row = array_diff($row, $removeKeys);
				fputcsv($fp, array_values($row));
			}

			fclose($fp);

		}else{

		/* If the library is not loaded, Codeigniter will return FALSE */
		if(!$this->_frameworkObj->load->is_loaded('Excel_Operation')){
			/* Load the Excel_Operation library to process the Excel_Operation request */
			$this->_frameworkObj->load->library('Excel_Operation');
		}
	
		/* Creating excel operation */
		$excelOperationObj = new Excel_Operation();
		
		/* Set document properties */
		$excelOperationObj->getProperties()->setCreator($this->_frameworkObj->load->get_var('userName'))
												 ->setLastModifiedBy($this->_frameworkObj->load->get_var('userName'))
												 ->setTitle($pStrFileName)
												 ->setSubject($pStrFileName)
												 ->setDescription($pStrFileName.' - Generated by CMS Bajaj Finsrve.')
												 ->setKeywords($pStrFileName)
												 ->setCategory($pStrFileName);

		/* Create a first sheet */
		$excelOperationObj->setActiveSheetIndex(0);

		/* iterating the column header loop */
		foreach($strColumnTitleArr as $strColumnTitleArrKey => $strColumnTitleArrValue){
			/* Setting the column title in excel */
			$excelOperationObj->getActiveSheet()->setCellValue(chr($intColumnIndex + $intCounterForLoop).'1', $strColumnTitleArrValue);
			/* incrementing the counter */
			$intCounterForLoop++;
		}

		/* iterating loop for setting data */
		foreach($pStrDataArr as $pStrDataArrKey => $pStrDataArrValue){
			/* Value over writing  */
			$intCounterForLoop = 0;
			/* iterating the loop as per column */
			foreach($strColumnTitleArr as $strColumnTitleArrKey => $strColumnTitleArrValue){
				/* Setting the data in cell index */
				$excelOperationObj->getActiveSheet()->setCellValue(chr($intColumnIndex + $intCounterForLoop).($pStrDataArrKey+2), isset($pStrDataArrValue[$strColumnTitleArrKey])?$pStrDataArrValue[$strColumnTitleArrKey]:'-');
				/* incrementing the counter */
				$intCounterForLoop++;
			}
		}
		
		/* Creating excel I/O operation object */
		$objWriter = PHPExcel_IOFactory::createWriter($excelOperationObj, 'Excel2007');
		/* Creating data file */
		$objWriter->save($pStrFileName);
		/* Setting the active sheet */
		$excelOperationObj->setActiveSheetIndex(0);

		}
		
		/* return created file name */
		return $pStrFileName;
	}
	
	
	/***************************************************************************************/
	/*Purpose 	: Creating a compressed zip file of all the excel files created.
	/*Inputs	: $files :: Array of files to be archived zip file,
				: $destination ::  Destination path to create the archived zip file,
				: $overwrite :: overwriting the existing zip file flag
	/*Returns 	: File name
	/*Created By: Vipin Kumar R. Jaiswar.
	/***************************************************************************************/
	private function _createZipFile($files = array(),$destination = '',$overwrite = false) {
		/* if the zip file already exists and overwrite is false, return false */
		if(file_exists($destination) && !$overwrite) { return false; }
		
		/* variable initialization */
		$valid_files = array();
		
		/* if files were passed in... */
		if(is_array($files)) {
			/* cycle through each file */
			foreach($files as $file) {
				/* make sure the file exists */
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		
		/* if we have good files... */
		if(count($valid_files)) {
			/* create the archive */
			$zip = new ZipArchive();
			/* Creating zip file I/O index */
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			/* add the files */
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			/* close the zip -- done! */
			$zip->close();

			/* check to make sure the file exists */
			return $destination;
		}else{
			return '';
		}
	}
	
	/***************************************************************************************/
	/*Purpose 	: Reading Imported Excel File Content.
	/*Inputs	: $pStrFileObject :: File reference
	/*Returns 	: Data from file
	/*Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************************/
	public function readExcelFile($pStrFileObject = ''){
		/* variable initialization */
		$strDataArr = array();
		
		/* if file object is empty then do needful */
		if(empty($pStrFileObject)){
			/* return empty data array */
			return $strDataArr;
		}
		
		/* If the library is not loaded, Codeigniter will return FALSE */
		if(!$this->_frameworkObj->load->is_loaded('Excel_Operation')){
			/* Load the S3 library to process the S3 request */
			$this->_frameworkObj->load->library('Excel_Operation');
		}
		/* Creating EXCEL object */
		$excelOperationObj = new Excel_Operation();
		/* Creating EXCEL reader object */
		$objReader			= PHPExcel_IOFactory::createReader('Excel2007');
		/* Loading the data from EXCEL reader object */
		$strFileContentObj 	= $objReader->load($pStrFileObject);
		
		/* Get worksheet dimensions */
		$objSheet 			= $strFileContentObj->getSheet(0); 
		$highestRow 		= $objSheet->getHighestRow(); 
		$highestColumn 		= $objSheet->getHighestColumn();

		/* Loop through each row of the worksheet in turn */
		for ($intRowCounter = 1; $intRowCounter <= $highestRow; $intRowCounter++){ 
			/* Read a row of data into an array */
			$strDataSet = $objSheet->rangeToArray('A' . $intRowCounter . ':' . $highestColumn . $intRowCounter,NULL,TRUE,FALSE);
			/* Setting in the array */
			$strDataArr[]	= isset($strDataSet[0])?$strDataSet[0]:array();
		}
		/* Removed used variables */
		unset($excelOperationObj, $strFileContentObj, $objReader);
		
		/* return the data set */
		return $strDataArr;
	}
}