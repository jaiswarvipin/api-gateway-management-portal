<?php 
/*******************************************************************************/
/* Purpose 		: Managing the email related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Emailprocess{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_email";
	private $_frameworkObj		= '';
        private $_CompanyConfigArr	= array();
	
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code.
	/* Returns	: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0, $pBlnIsSystemGeneratedEmail = 0, $pStrCompanyConfigArr = array()){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
		/* If the Email is system generated then change the company code to 1 */
		if($pBlnIsSystemGeneratedEmail){
			/* Setting Default company code  */
			$this->_intCompanyCode	= 1;
		}
		/* CI instance reference */
		$this->_frameworkObj =& get_instance();
		/* Company Config Array for sending emails */
		$this->_CompanyConfigArr = $pStrCompanyConfigArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Sending email.
	/* Inputs 	: pEmailTemplateCode :: Email template code,
				: pIntSendingUserCode :: Receiver user code,
				: pIntLeadCode	:: Lead Code,
	/* Returns	: TRUE / FALSE.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	public function sendEmail($pEmailTemplateCode = 0, $pIntSendingUserCode = 0, $pIntLeadCode = 0, $pIntCompanyCode){
		/* Variable initialization */
		$pResponseArr 	= array('status'=>false, 'message'=>'');
		
		/* Checking for is template code passed */
		if($pEmailTemplateCode == 0){
			/* Return response array */
			return $pResponseArr['message']	= "Requested parameters are not passed. 1) Template Code : ".$pEmailTemplateCode.", 2. User Code : ".$pIntSendingUserCode;
		}
		
		/* Setting user details */
		$strUserArr	= $this->_getUserDetails($pIntSendingUserCode);
		/* Checking for is user details */
		if(empty($strUserArr)){
			/* Return response array */
			return $pResponseArr['message']	= "Requested user details is not found; Company Code : ".$this->_intCompanyCode.', user code : '.$pIntSendingUserCode;
		}
		
		/* Get email template details */
		$strEmailDetailsArr	=  $this->_getEmailTemplateDetails($pEmailTemplateCode);
		/* Checking for is email template details */
		if(empty($strEmailDetailsArr)){
			/* Return response array */
			return $pResponseArr['message']	= "Requested email template details is not found; Company Code : ".$this->_intCompanyCode.', template code : '.$pEmailTemplateCode;
		}
		
		/* Setting the email template dynamic content details */
		$strEmailBody	= $this->_setTemplateContent($pEmailTemplateCode, $strEmailDetailsArr, $strUserArr);
		/* Checking for is email body details */
		if(empty($strEmailBody)){
			/* Return response array */
			return $pResponseArr['message']	= "Requested email body rules is not found; Company Code : ".$this->_intCompanyCode.', template code : '.$pEmailTemplateCode;
		}
		
		/* Sending email based on selected service */
		if(!empty($this->_CompanyConfigArr['smtp_host'])){
			/* Sending email using SMTP */
			$pBlnEmailSentStatus 	= $this->_sendEmailSMTP($this->_CompanyConfigArr, $strEmailBody, $strUserArr, $strEmailDetailsArr);
		}else if(!empty($this->_CompanyConfigArr['MANDRILL_APIKEY'])){
			$strTemplateDetailsArr = $this->_getMandrillTemplateDetails($this->_CompanyConfigArr, $strUserArr, $strEmailDetailsArr);
			/* Sending email using MANDRILL or Mail-Chimp Service */
			$pBlnEmailSentStatus 	= $this->_sendEmailMandrill($this->_CompanyConfigArr, $strTemplateDetailsArr, $strUserArr, $strEmailDetailsArr);
        }else{
			/* Sending Email */
			$pBlnEmailSentStatus	= $this->_sendEmailDefaut($this->_CompanyConfigArr, $strEmailBody, $strUserArr, $strEmailDetailsArr);
		}
		
		/* based of email sending status do needful */
		if($pBlnEmailSentStatus){
			/* Return response array */
			$pResponseArr['message']	= $strEmailBody;
			$pResponseArr['status']		= true;
		}else{
			/* Return response array */
			$pResponseArr['message']	= 'Error occurred while sending email.';
			$pResponseArr['status']		= false;
        }
		
		/* Returns email sending status */
		return $pResponseArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Get user details by user code.
	/* Inputs 	: pIntUserCode :: Email user code.
	/* Returns	: User details array.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _getUserDetails($pIntUserCode = 0 ){
		/* Variable initialization */
		$strRerturnArr	= array();
		/* If user code is empty then do needful */
		if($pIntUserCode == 0){
			/* Return the response */
			return $strRerturnArr;
		}
		
		/* get sending use details */
		$strRerturnArr = $this->_databaseObject->getDataFromTable(
																	array(
																			'table'=>"master_user",
																			'where'=>array('company_code'=>$this->_intCompanyCode,'id'=>$pIntUserCode)
																	)
															);
		/* Return the user details */
		return $strRerturnArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Get email template details by template code.
	/* Inputs 	: pIntTemplateCode :: Email template code.
	/* Returns	: Email details array.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _getEmailTemplateDetails($pIntTemplateCode = 0 ){
		/* Variable Initialization */
		$strReturnArr	= array();
		/* if email template code is empty then do needful */
		if($pIntTemplateCode  == 0){
			/* Return template code array */
			return $strReturnArr;
		}
		
		/* get email template details */
		$strRerturnArr = $this->_databaseObject->getDataFromTable(
																	array(
																			'table'=>array($this->_strTableName,"master_email_templates"),
																			'join'=>array('',$this->_strTableName.'.id = master_email_templates.email_code'),
																			'column'=>array('master_email_templates.*'),
																			'where'=>array($this->_strTableName.'.company_code'=>$this->_intCompanyCode,$this->_strTableName.'.id'=>$pIntTemplateCode,'is_active'=>1)
																	)
															);
		
		/* return the template details */
		return $strRerturnArr;
	}
	
	/***************************************************************************/
	/* Purpose	: Email template dynamic place replacement.
	/* Inputs 	: $pIntTemplateCode :: Email template code,
				: $pStrEmailTemplateArr :: Email template details array,
				: pStrUserArr :: User detail array,
	/* Returns	: Final email body.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _setTemplateContent($pIntTemplateCode = 0, $pStrEmailTemplateArr = array(), $pStrUserArr = array()){
		/* Variable Initialization */
		$strRuleArr		= $this->_getTemplateRule($pIntTemplateCode);
		$strEmailBody	= '';
		
		/* if not rule is set the do needful */
		if(empty($strRuleArr)){
			/* Return empty email body */
			return $strEmailBody;
		}
		
		/* Value overriding */
		$strEmailBody	= $pStrEmailTemplateArr[0]['email_body'];
		
		/* Iterating the rule loop */
		foreach($strRuleArr as $strRuleArrKey => $strRuleArrValue){
			/* get value */
			$strKeyValue	= (isset($pStrEmailTemplateArr[0][$strRuleArrValue])?$pStrEmailTemplateArr[0][$strRuleArrValue]:(isset($pStrUserArr[0][$strRuleArrValue])?$pStrUserArr[0][$strRuleArrValue]:(isset($pStrLeadArr[0][$strRuleArrValue])?$pStrLeadArr[0][$strRuleArrValue]:'')));
			/* Replacing the value */
			$strEmailBody	= str_replace('{'.$strRuleArrKey.'}',$strKeyValue,$strEmailBody);
		}
		
		/* removed used variables */
		unset($strRuleArr);
		
		/* Return Email Body */
		return $strEmailBody;
	}
	
	/***************************************************************************/
	/* Purpose	: Email template dynamic place replacement rule.
	/* Inputs 	: $pIntTemplateCode :: template code,
	/* Returns	: Template decoding rule.
	/* Created By: Jaiswar Vipin Kumar R.
	/***************************************************************************/
	private function _getTemplateRule($pIntTemplateCode = 0){
		/* Variable initialization */
		$strReturnRuleArray = array();
		
		/* based on the template setting the rules */
		switch($pIntTemplateCode){
			case 1:
				/* Setting Rules */
				$strReturnRuleArray	= array('USER_NAME'=>'user_name','USER_EMAIL'=>'user_email');
				break;
		}
		
		/* Return the rules */
		return $strReturnRuleArray;
	}
    
	/***********************************************************************************************************************/
	/* Purpose	: Sending emails using hosted server email services.
	/* Inputs 	: $strCompanyConfigArr :: Company configuration array with all details for sending emails.
				: $strEmailBody :: Email body to send in the email as message.
				: $strUserArr :: Sender / Receiver User details.
	/* Returns	: Email Sending Status.
	/* Created By: Vipin Kumar R. Jaiswar.
	/***********************************************************************************************************************/
	private function _sendEmailDefaut($strCompanyConfigArr, $strEmailBody, $strUserArr, $strEmailDetailsArr){
		/* Load email library and pass Email configuration details */
		$this->_frameworkObj->load->library('email');
		/* Email address of the sender or from address */
		$this->_frameworkObj->email->to($strUserArr[0]['user_email']);
		/* Email address of the recipient or to address */
		$this->_frameworkObj->email->from($strCompanyConfigArr['smtp_user'],$strEmailDetailsArr[0]['from_name']);
		/* Email Subject line */
		$this->_frameworkObj->email->subject($strEmailDetailsArr[0]['email_subject']);
		/* Email body */
		$this->_frameworkObj->email->message($strEmailBody);

		/* Send email */
		$blnResult = $this->_frameworkObj->email->send();
		
		/* if debug request is set then do needful */
		if(isset($_COOKIE['dbug_email_smtp'])){
			/* get the SMTP debug request */
			$strDebug = $this->_frameworkObj->email->print_debugger();
			/* send on the requester screen */
			debugVar($strDebug, true);
		}
		
		/* return the email sending status */
		return ($blnResult)?true:false;
	}
	
	
    /***********************************************************************************************************************/
	/* Purpose	: Sending emails using SMTP protocol.
	/* Inputs 	: $strCompanyConfigArr :: Company configuration array with all details for sending emails.
				: $strEmailBody :: Email body to send in the email as message.
				: $strUserArr :: Sender / Receiver User details.
	/* Returns	: Email Sending Status.
	/* Created By: Vipin Kumar R. Jaiswar.
	/***********************************************************************************************************************/
	private function _sendEmailSMTP($strCompanyConfigArr, $strEmailBody, $strUserArr, $strEmailDetailsArr){
		/* Load email library and pass Email configuration details */
		$this->_frameworkObj->load->library('email', $strCompanyConfigArr);
		/* Email address of the sender or from address */
		$this->_frameworkObj->email->to($strUserArr[0]['user_email']);
		/* Email address of the recipient or to address */
		$this->_frameworkObj->email->from($strCompanyConfigArr['smtp_user'],$strEmailDetailsArr[0]['from_name']);
		/* Email Subject line */
		$this->_frameworkObj->email->subject($strEmailDetailsArr[0]['email_subject']);
		/* Email body */
		$this->_frameworkObj->email->message($strEmailBody);

		/* Send email */
		$blnResult = $this->_frameworkObj->email->send();
		
		/* if debug request is set then do needful */
		if(isset($_COOKIE['dbug_email_smtp'])){
			/* get the SMTP debug request */
			$strDebug = $this->_frameworkObj->email->print_debugger();
			/* send on the requester screen */
			debugVar($strDebug, true);
		}
		
		/* return the email sending status */
		return ($blnResult)?true:false;
	}

	    
    /***********************************************************************************************************************/
	/* Purpose	: Sending emails using ManDrill Email Solution Provider Services.
	/* Inputs 	: $strCompanyConfigArr :: Company configuration array with all details for sending emails.
				: $strEmailBody :: Email body to send in the email as message.
				: $strUserArr :: Sender / Receiver User details.
	/* Returns	: Email Sending Status.
	/* Created By: Vipin Kumar R. Jaiswar.
	/***********************************************************************************************************************/
	private function _sendEmailMandrill($strCompanyConfigArr, $strTemplateDetailsArr, $strUserArr, $strEmailDetailsArr) {
		/* If the library is not loaded, Codeigniter will return FALSE */
		if(!$this->_frameworkObj->load->is_loaded('Mailchimp_Operation')){
			/* Load the Mail-chimp Mandrill library to process the email request */
			$this->_frameworkObj->load->library('Mailchimp_Operation',array($strCompanyConfigArr['MANDRILL_APIKEY']), 'mailchimpObj');
		}
		
		/* variable initialization */
		$strUserName 			= $strUserArr[0]['user_name'];
		$strEmailTo 			= $strUserArr[0]['user_email'];
		$strTemplateName 		= isset($strTemplateDetailsArr['template_name'])?$strTemplateDetailsArr['template_name']:'';
		$strTemplateContentArr	= isset($strTemplateDetailsArr['template_content'])?$strTemplateDetailsArr['template_content']:array();
		$strIPPool 				= 'Main Pool';
		
		/* if email template name or template content array is not set then do needful */
		if(empty($strTemplateContentArr) || ($strTemplateName == '')){
			/* do not process ahead */
			return false;
		}
		
		/* Creating mandrill object */
		$mandrillObj 			= $this->_frameworkObj->mailchimpObj;
		
		/* Creating message array */
		$strMessageArr = array(
								'html' => $strEmailDetailsArr[0]['email_subject'],
								'text' => $strEmailDetailsArr[0]['email_subject'],
								'subject' => $strEmailDetailsArr[0]['email_subject'],
								'from_email' => $strEmailDetailsArr[0]['from_email'],
								'from_name' => $strEmailDetailsArr[0]['from_name'],
								'to' => array(
									array(
										'email' => $strEmailTo,
										'name' => $strUserName,
										'type' => 'to'
									)
								),
								'headers' => array('Reply-To' => $strEmailDetailsArr[0]['from_email']),
								'important' => false,
								'track_opens' => null,
								'track_clicks' => null,
								'auto_text' => null,
								'auto_html' => null,
								'inline_css' => null,
								'url_strip_qs' => null,
								'preserve_recipients' => null,
								'view_content_link' => null,
								'bcc_address' => '',
								'tracking_domain' => null,
								'signing_domain' => null,
								'return_path_domain' => null,
								'merge' => true,
								'merge_language' => 'mailchimp',
								'global_merge_vars' => (isset($strTemplateDetailsArr['global_merge_vars'])?$strTemplateDetailsArr['global_merge_vars']:array()),
								'merge_vars' => (isset($strTemplateDetailsArr['merge_vars'])?$strTemplateDetailsArr['merge_vars']:array())
							);
		 
		/* Sending the email */
		try{
			/* Send email */
			$strResultArr = $mandrillObj->messages->sendTemplate($strTemplateName, $strTemplateContentArr, $message, false, $strIPPool);
		}catch(Exception $e){
			/* Error handling */
			$errorMessage = $e->getMessage();
			/* Stop the operation */
			return FALSE;
		}
		
		/* Removed object */
		unset($mandrillObj);
		
		/* based in the */
		if((isset($strResultArr[0]['status'])) && ($strResultArr[0]['status']== "sent")){
			/* response the status */
			return true;
		}else{
			/* response the status */
			return false;
		}
	}
        
        
        /* 
        * Purpose	: To get the template details to send email using Mandrill.
	* Inputs 	: None.
	* Returns	: Template Details.
	* Created By: Vipin Kumar R. Jaiswar.
        */
        public function _getMandrillTemplateDetails($strCompanyConfigArr, $strUserArr, $strEmailDetailsArr){
            $strTemplateDetailsArr = array();
            $strTemplateDetailsArr['template_name'] = "reset-pin";
            $template_content = array(
                    array(
                            'name' => 'name',
                            'content' => 'Muzaffar'
                    ),
                    array(
                            'name' => 'code',
                            'content' => '25'
                    )
            );
            $strTemplateDetailsArr['template_content'] = $template_content;

            $global_merge_vars = array(
                            array(
                                    'name' => 'merge1',
                                    'content' => 'merge1 content'
                            )
                    );
            $strTemplateDetailsArr['global_merge_vars'] = $global_merge_vars;

            $merge_vars = array(
                            array(
                                    'rcpt' => 'recipient.email@example.com',
                                    'vars' => array(
                                            array(
                                                    'name' => 'merge2',
                                                    'content' => 'merge2 content'
                                            )
                                    )
                            )
                    );
            $strTemplateDetailsArr['merge_vars'] = $merge_vars;
            
            return $strTemplateDetailsArr;
        }
}