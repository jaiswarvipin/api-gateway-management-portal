<?php
/*********************************************************************************************************/
/* Purpose: Managing the external request.
/* Created By: Jaiswar Vipin Kumar R.
/*********************************************************************************************************/

class Request{
	/* variable initialization */
	private $_strReponse		= '';
	private $_strRequestBody	= '';
	private $_strError		= '';
	private $_strFields		= '';
	
	/*****************************************************************************************************/
	/* Purpose 		: Sending request to sales-force for lead generation
	/* Inputs		: $pStrPostArr :: Post data array.
	/* Returns		: None.
	/* Created By	: Jaiswar Vipin Kumar R.
	/*****************************************************************************************************/
	function send($pStrPostArr = array()){
		/* if post data is empty then do needful */
        if (empty($pStrPostArr)){
                /* return status */
                return false;
        }

        /* set POST variables */
        $strDestiantionURL = isset($pStrPostArr['desitnationURL'])?$pStrPostArr['desitnationURL']:'';
        /* removed used index */
        unset($pStrPostArr['desitnationURL']);

        /* if destination URL is not set then do needful */
        if (empty($strDestiantionURL)){
                /* return status */
                return false;
        }

        /* variable initialization */
        $strFields	= '';
        $this->_strFields = $strFields	= http_build_query($pStrPostArr);
		
        if(isset($_COOKIE['debug'])){
            //print_r($pStrPostArr);
            echo $strFields;exit;
        }
        /* open connection */
        $ch = curl_init($strDestiantionURL);
        
		/* if header set then do needful */
		if((isset($pStrPostArr['headers'])) && (!empty($pStrPostArr['headers']))){
			/* Set the header */
			curl_setopt($ch, CURLOPT_HTTPHEADER, $pStrPostArr['headers']);
		}
		
		/* removed the used header */
		unset($pStrPostArr['headers']);
		
        /* set the url, number of POST vars, POST data */
        curl_setopt($ch,CURLOPT_URL,$strDestiantionURL);
		/* if post method requeted then do needful */
		if((isset($pStrPostArr['method'])) && (strtolower($pStrPostArr['method']) == 'post')){
			/* removed the used index */
			unset($pStrPostArr['method']);
			
			/* post the variables */
			curl_setopt($ch,CURLOPT_POST,count($pStrPostArr));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$strFields);
		}
		
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		
        
        /* execute post */
        $this->_strReponse 		= curl_exec($ch);
        /* Request Body */
        $this->_strRequestBody	= curl_getinfo($ch);
		
        if($this->_strReponse === FALSE){
            $this->_strError	= curl_error($ch);
        }
        
        /* close connection */
        curl_close($ch);
    }

	/*****************************************************************************************************/
	/* Purpose 		: Get the Response
	/* Inputs		: None.
	/* Returns		: Response.
	/* Created By	: Jaiswar Vipin Kumar R.
	/*****************************************************************************************************/
	public function getResponse(){
		/* Return the response */
		return $this->_strReponse;
	}

	/*****************************************************************************************************/
	/* Purpose 		: Get the Response Complete Response
	/* Inputs		: None.
	/* Returns		: Details Response.
	/* Created By	: Jaiswar Vipin Kumar R.
	/*****************************************************************************************************/
	public function getResponseInfo(){
		/* Return the response */
		return $this->_strRequestBody;
	}
	
	/*****************************************************************************************************/
	/* Purpose 		: Get the Response Error
	/* Inputs		: None.
	/* Returns		: Response Error.
	/* Created By	: Jaiswar Vipin Kumar R.
	/*****************************************************************************************************/
	public function getResponseError(){
		/* Return the response */
		return $this->_strError;
	}
}
?>