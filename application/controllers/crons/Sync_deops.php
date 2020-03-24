<?php
/***********************************************************************/
/* Purpose 		: Synch the DevOps work item based on the UserStory code.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_deops extends Requestprocess {
	/* variable deceleration */
	public  $_objDataOperation		= null;
	private $_strPrimaryTableName 	= 'trans_devops_workitem_status';
	private $_strSecondaryTableName = 'api_list_3';
	private $_strDataArr			= array();
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function __construct(){
		/* calling parent construct */
		parent::__construct();
	}
	
	/**********************************************************************/
	/*Purpose 	: Default method to be executed.
	/*Inputs	: none
	/*Returns	: Execute the query.
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	public function index(){
		/* Getting the json object */
		$strResponseArr = $this->_getDevOpsDetails();

		/* if json obejct is empty then do needful */
		if(empty($strResponseArr)){
		}else{
			/* checking the value array */
			if(!isset($strResponseArr['value'])){
				/* do not process ahead */
				return false;
			}
			
			/* get delivery status */
			$strDeliveryStatusArr	= $this->_getDeliveryStatusList();
			$strDeliveryStatusArr	= array_flip($strDeliveryStatusArr['keyvalue']);
			
			/* iterating the response */
			foreach($strResponseArr['value'] as $strResponseArrKey => $strResponseObj){
				/* variable initialization */
				$intDevDateDiff							= $intITUATDateDiff	= $intGoLiveDateDiff	=	0;
				$intUserStoryID							= $strResponseObj->id;
				/* Converting the JSOE obejct into Array */
				$strResponseObj 						= (array)$strResponseObj->fields;
				
				/* Setting values */
				$intDevelopmentPlannedStartDate			= isset($strResponseObj['Custom.DevelopmentPlannedStartDate'])?getDateFormat($strResponseObj['Custom.DevelopmentPlannedStartDate'],6):0;
				$intDevelopmentActualStartDate 			= isset($strResponseObj['Custom.DevelopmentActualStartDate'])?getDateFormat($strResponseObj['Custom.DevelopmentActualStartDate'],6):0;;
				$intDevelopmentPlannedEndDate			= isset($strResponseObj['Custom.DevelopmentPlannedEndDate'])?getDateFormat($strResponseObj['Custom.DevelopmentPlannedEndDate'],6):0;
				$intDevelopmentActualEndDate			= isset($strResponseObj['Custom.DevelopmentActualEndDate'])?getDateFormat($strResponseObj['Custom.DevelopmentActualEndDate'],6):0;
				$intITUATPlannedStartDate				= isset($strResponseObj['Custom.ITUATPlannedStartDate'])?getDateFormat($strResponseObj['Custom.ITUATPlannedStartDate'],6):0;
				$intITUATPlannedEndDate					= isset($strResponseObj['Custom.ITUATPlannedEndDate'])?getDateFormat($strResponseObj['Custom.ITUATPlannedEndDate'],6):0;
				$ITUATActualStartDate					= isset($strResponseObj['Custom.ITUATActualEndDate'])?getDateFormat($strResponseObj['Custom.ITUATActualEndDate'],6):0;
				$ITUATActualEndDate						= isset($strResponseObj['Custom.ITUATActualStartDate'])?getDateFormat($strResponseObj['Custom.ITUATActualStartDate'],6):0;
				$intBizUATPlannedStartDate				= isset($strResponseObj['Custom.BizUATActualStartDate'])?getDateFormat($strResponseObj['Custom.BizUATActualStartDate'],6):0;
				$intBizUATPlannedEndDate				= isset($strResponseObj['Custom.BizUATActualEndDate'])?getDateFormat($strResponseObj['Custom.BizUATActualEndDate'],6):0;
				$intBizUATActualStartDate				= isset($strResponseObj['Custom.BizUATPlannedStartDate'])?getDateFormat($strResponseObj['Custom.BizUATPlannedStartDate'],6):0;
				$intBizUATActualEndDate					= isset($strResponseObj['Custom.BizUATPlannedEndDate'])?getDateFormat($strResponseObj['Custom.BizUATPlannedEndDate'],6):0;
				$intN2PPlannedStartDate					= isset($strResponseObj['Custom.N2PPlannedStartDate'])?getDateFormat($strResponseObj['Custom.N2PPlannedStartDate'],6):0;
				$intN2PPlannedEndDate					= isset($strResponseObj['Custom.N2PPlannedEndDate'])?getDateFormat($strResponseObj['Custom.N2PPlannedEndDate'],6):0;
				$intN2PActualStartDate					= isset($strResponseObj['Custom.N2PActualStartDate'])?getDateFormat($strResponseObj['Custom.N2PActualStartDate'],6):0;
				$intN2PActualEndDate					= isset($strResponseObj['Custom.N2PActualEndDate'])?getDateFormat($strResponseObj['Custom.N2PActualEndDate'],6):0;
				$intGoLivePlannedDate					= isset($strResponseObj['Custom.GoLivePlannedDate'])?getDateFormat($strResponseObj['Custom.GoLivePlannedDate'],6):0;
				$GoLiveActualDate						= isset($strResponseObj['Custom.GoLiveActualDate'])?getDateFormat($strResponseObj['Custom.GoLiveActualDate'],6):0;
				$strTitle								= isset($strResponseObj['System.Title'])?$strResponseObj['System.Title']:'';
				$strAssignedTo							= isset($strResponseObj['System.AssignedTo'])?$strResponseObj['System.AssignedTo']->displayName:'';
				$strStatus								= isset($strResponseObj['System.State'])?$strResponseObj['System.State']:0;
				$intStatusCode							= isset($strDeliveryStatusArr[$strStatus])?$strDeliveryStatusArr[$strStatus]:72;
				/* Checking for user story exists */
				if(isset($this->_strDataArr[$intUserStoryID])){
					/* set the dev diff */
					foreach($this->_strDataArr[$intUserStoryID] as $strDateKey  => $strDateValue){
						/* setting the date diff */
						$intDevDateDiff		= getDateDiff($intDevelopmentActualEndDate, $strDateValue['dev-closure-date']);
						$intITUATDateDiff	= getDateDiff($ITUATActualEndDate, $strDateValue['uat-released-date']);
						$intGoLiveDateDiff	= getDateDiff($GoLiveActualDate, $strDateValue['go-live-date']);
					}
				}
				
				/* Setting the array for insert / update array */
				$strDataArr	= array(
											'development_planned_start_date'=>$intDevelopmentPlannedStartDate,
											'development_planned_end_date'	=>$intDevelopmentPlannedEndDate,
											'development_actual_start_date'	=>$intDevelopmentActualStartDate,
											'development_actual_end_date'	=>$intDevelopmentActualEndDate,
											'it_uat_planned_start_date'		=>$intITUATPlannedStartDate,
											'it_uat_planned_end_date'		=>$intITUATPlannedEndDate,
											'it_uat_actual_start_date'		=>$ITUATActualStartDate,
											'it_uat_actual_end_date'		=>$ITUATActualEndDate,
											'biz_uat_planned_start_date'	=>$intBizUATPlannedStartDate,
											'biz_uat_planned_end_date'		=>$intBizUATPlannedEndDate,
											'biz_uat_actual_start_date'		=>$intBizUATActualStartDate,
											'biz_uat_actual_end_date'		=>$intBizUATActualEndDate,
											'n2p_planned_start_date'		=>$intN2PPlannedStartDate,
											'n2p_planned_end_date'			=>$intN2PPlannedEndDate,
											'n2p_actual_start_date'			=>$intN2PActualStartDate,
											'n2p_actual_end_date'			=>$intN2PActualEndDate,
											'go_live_planned_date'			=>$intGoLivePlannedDate,
											'go_live_actual_date'			=>$GoLiveActualDate,
											'story_title'					=>$strTitle,
											'assigned_to'					=>$strAssignedTo,
											'state'							=>$strStatus,
											'dev_diff'						=>$intDevDateDiff,
											'it_uat_diff'					=>$intITUATDateDiff,
											'live_diff'						=>$intGoLiveDateDiff
									   );
				
				/* chceking if user story Id exists */
				$strUSerStoryDetalsArr	= $this->_objDataOperation->getDataFromTable(array('table'=>$this->_strPrimaryTableName,'where'=>array('user_story_id'=>$intUserStoryID)));
				
				/* if record found then do needful */
				if(!empty($strUSerStoryDetalsArr)){
					/* Insert the data */
					$intOperationStatus	= $this->_objDataOperation->setUpdateData(array('table'=>$this->_strPrimaryTableName,'data'=>$strDataArr,'where'=>array('user_story_id'=>$intUserStoryID)));
				}else{
					/* Updating the data */
					$intOperationStatus	= $this->_objDataOperation->setDataInTable(
																					array(
																							'table'=>$this->_strPrimaryTableName,
																							'data'	=> array_merge($strDataArr,array('user_story_id'=>$intUserStoryID))
																						)
																			);
				}
				
				/* Updating the data */
				$intOperationStatus	= $this->_objDataOperation->setUpdateData(
																				array(
																						'table'=>$this->_strSecondaryTableName,
																						'data'=>array('`biz-uat-release-date`'=>$intBizUATActualEndDate,'status'=>$intStatusCode),
																						'where'	=> array('`devops-user-story-id`'=>$intUserStoryID)
																					)
																		);
																		
				/* Getthe system status */
				$intStatusCode	= isset($strDeliveryStatusArr[$strStatus])?$strDeliveryStatusArr[$strStatus]:0;
				
				/* if system found then do needful */
				if($intStatusCode!=0){
					/* Updating the original data status */
					$intOperationStatus	= $this->_objDataOperation->setDataInTable(
																						array(
																								'table'=>$this->_strSecondaryTableName,
																								'data'=>array('status'=>$intStatusCode),
																								'where'	=> array('user_story_id'=>$intUserStoryID)
																							)
																				);
				}
			}
		}
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the DevOps Details.
	/*Inputs	: $strUserStoryIDArr = User Story ID array.
	/*Returns	: Return the .
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getDeliveryStatusList($strUserStoryIDArr = array()){
		/* Creating the delivery status object */
		$deliveryStatusObj = new Status($this->_objDataOperation , $this->getCompanyCode());
		/* get delivery status */
		return $deliveryStatusObj->getDeliveryStatusList();
	}
	
	/**********************************************************************/
	/*Purpose 	: Get the DevOps Details.
	/*Inputs	: $strUserStoryIDArr = User Story ID array.
	/*Returns	: Return the .
	/*Created By: Jaiswar Vipin Kumar R.
	/**********************************************************************/
	private function _getDevOpsDetails($strUserStoryIDArr = array()){
		/* variables initlization */
		$strReturnArr	= array();
		/* Execute the query */
		$strResultArr	= $this->_objDataOperation->getDataFromTable(
																		array(
																				'table'=>$this->_strSecondaryTableName,
																				'column'=>array('id','`devops-user-story-id`','`dev-closure-date`','`uat-released-date`','`go-live-date`'),
																				'where'=>array('`devops-user-story-id` >'=>0)
																			)
																	);
		/* if devops user id found then do needful */
		if(!empty($strResultArr)){
			/* iterating the loop */
			foreach($strResultArr as $strResultArrKey => $strResultArrValue){
				/* Set Values */
				$strReturnArr[$strResultArrValue['devops-user-story-id']][$strResultArrValue['id']]	= $strResultArrValue;
				$strReturnArr['story'][$strResultArrValue['devops-user-story-id']]					= $strResultArrValue['devops-user-story-id'];
			}
		}
		/* removed used variables */
		unset($strResultArr);
		/* set data set */
		$this->_strDataArr	= $strReturnArr;
		
		/* if user story found then do needful */
		if(!empty($strReturnArr)){
			/* Setting header */
			$strHeaderArr	= array('Content-Type:application/json','Authorization: Basic '. base64_encode(DEVOPS_USERNAME.":".DEVOPS_KEY));
			
			/* Return the DevOps Story Details */
			$requestObj	= new Request();
			/* Sending the request */
			$requestObj->send(array('desitnationURL'=>'https://dev.azure.com/BFLDevOpsOrg/_apis/wit/workitems?ids='.implode(',',array_keys($strReturnArr['story'])),'headers'=>$strHeaderArr,'method'=>"get"));
			
			/* if any error occured then do needful */
			if($requestObj->getResponseError() != ''){
				/* Return the empty array */
				return array();
			}else{
				/* return the response */
				return (array)json_decode($requestObj->getResponse());
			}
		}else{
			return array();
		}
	}
}