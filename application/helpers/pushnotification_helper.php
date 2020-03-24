<?php
/**
 * @author  Vipin Kumar R. Jaiswar
 * Purpose  For sending Push Notifications to Users
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Pushnotification{
    private $_CompanyConfigArr	= array();
    
    /***************************************************************************/
    /* Purpose	: Initialization
    /* Inputs 	: None.
    /* Returns	: None.
    /* Created By : Vipin Kumar R. Jaiswar.
    /***************************************************************************/
    public function __construct($pStrCompanyConfigArr = array()){
        /* Company Config Array for sending emails */
        $this->_CompanyConfigArr = $pStrCompanyConfigArr;
    }

    /***************************************************************************************/
    /*Purpose 	: To format the data to make the Payload to send to Urbanairship(UA).
    /*Inputs	: $data	:: contails the User data like device token, type etc.,
                    : $message :: contains the message that needs to be sent in Push notification.
    /*Returns 	: formatted payload.
    /*Created By    : Vipin Kumar R. Jaiswar.
    /***************************************************************************************/
    public function getPushNotificationPayload($data, $message) {

        $getMsg = $audi_arr = $tokArray = $typArray = $returnArray = array();
        $flagAndoid = $flagIOS = '';

        if (count($data) > 0) {
            foreach ($data as $userDevice) {
                /* Begin, Code Modify for Urbanairship Payload Changes for channel_id */
                if ($userDevice['device_type'] == 'ios') {
                    $flagIOS = "ios";

                    if ($userDevice['is_channel'] == 1) {
                        $tokArray[][$flagIOS."_channel"] = $userDevice['device_token'];
                    }else{
                        $tokArray[]["device_token"] = $userDevice['device_token'];
                    }

                } else if ($userDevice['device_type'] == 'android') {
                    $flagAndoid = "android";

                    if ($userDevice['is_channel'] == 1) {
                        $tokArray[][$flagAndoid."_channel"] = $userDevice['device_token'];
                    }else{
                        $tokArray[]["apid"] = $userDevice['device_token'];
                    }

                } else {
                    $tokArray[]["apid"] = $userDevice['device_token'];
                    $flagAndoid = "";
                }
                /* End, Code Modify for Urbanairship Payload Changes for channel_id */
            }
            $audi_arr['or'] = $tokArray;
            if ($flagIOS)
                $typArray[] = $flagIOS;
            if ($flagAndoid)
                $typArray[] = $flagAndoid;
        }

        if (is_array($message)) {
            if ($flagIOS) {

                if ($message['type'] == 'disable') {
                    $getMsg['message'][$flagIOS]['content-available'] = 1;
                } else {
                    $getMsg['message'][$flagIOS]['alert'] = $message['title'];
                }
                $getMsg['message'][$flagIOS]['extra'] = array('type' => $message['type'], 'detail' => $message['description'], 'id' => $message['id']);
            }
            if ($flagAndoid) {

                if ($message['type'] == 'disable') {
                    $getMsg['message'][$flagAndoid]['content-available'] = 1;
                } else {
                    $getMsg['message'][$flagAndoid]['alert'] = $message['title'];
                }
                $getMsg['message'][$flagAndoid]['extra'] = array('type' => $message['type'], 'detail' => $message['description'], 'id' => $message['id']);
            }
        } else {
            $getMsg['message']['alert'] = $message;
        }

        $getMsg['audi_arr'] = $audi_arr;
        $getMsg['device_types'] = $typArray;

        return array($getMsg);
    }

    /***************************************************************************************/
    /*Purpose 	: To send the push notifation immediately to the users.
    /*Inputs	: $data	:: contails the whole formatted payload to send to UA,
    /*Returns 	: push notificaton sent status.
    /*Created By    : Vipin Kumar R. Jaiswar.
    /***************************************************************************************/
    public function nowPushNotification($data) {
        foreach ($data as $row) {

            $channel = $row['audi_arr'];
            $push = array("audience" => $channel, "notification" => $row['message'], "device_types" =>
                $row['device_types']);

            $json = json_encode($push);

            $PUSHURL = $this->_CompanyConfigArr['PUSHURL'];
            /* send Push notification function start */
            $this->sendPushNotification($PUSHURL, $json);
            /* send Push notification function end */

        }
    }

    /***************************************************************************************/
    /*Purpose 	: To schedule the push notifation to send to the users at a given time.
    /*Inputs	: $data	:: contails the whole formatted payload to send to UA,
    /*Returns 	: push notificaton sent status.
    /*Created By    : Vipin Kumar R. Jaiswar.
    /***************************************************************************************/
    public function schedulePushNotification($row) {
        $strSchedulePushAt = $row['published_at'];
        $row1 = json_decode($row['payload_data'],TRUE);
        $row = $row1[0];

        $channel = $row['audi_arr'];
        $push_array = array("audience" => $channel, "notification" => $row['message'], "device_types" =>
            $row['device_types']);
        $schedule_array = array( "scheduled_time" => str_replace(' ','T',$strSchedulePushAt)); 

        $push = array('name' => 'Mizuho Staff App', 'schedule'=>$schedule_array, 'push'=>$push_array);

        $json = json_encode($push);

        $SCHEDULE_PUSHURL = $this->_CompanyConfigArr['SCHEDULE_PUSHURL'];
        /* Call library function start */
        $this->sendPushNotification($SCHEDULE_PUSHURL, $json);
        /* Call library function end */

        $content = json_decode($content,TRUE);
        $is_sent = 0;
        if($content['ok'] == true){
            $is_sent = 1;
        }
        $content = json_encode($content);
        if($is_sent){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    /***************************************************************************************/
    /*Purpose 	: To delete the push notifation which was schedule to be delivered. Only future notifications can be deleted
    /*Inputs	: $pushId :: contails the Push ID which needs to be deleted, Push ID we get after scehduling a Push notification,
    /*Returns 	: push notificaton deleted status.
    /*Created By    : Vipin Kumar R. Jaiswar.
    /***************************************************************************************/
    public function deletePushNotification($pushId){
        $jsonPushData = "";
        $SCHEDULE_PUSHURL = $this->_CompanyConfigArr['SCHEDULE_PUSHURL'];
        /* Call library function start */
        $this->sendPushNotification($SCHEDULE_PUSHURL, $jsonPushData, $pushId);
        /* Call library function end */

        $content = json_decode($content,TRUE);
        return TRUE;
    }

    /***************************************************************************************/
    /*Purpose 	: To send the push notifation using the CURL call.
    /*Inputs	: $PUSHURL :: the URL for the UA API to be used.
     *                $jsonPushData :: the data or payload to send to UA for push notification.
     *                $pushId :: this param is used only to delete the scheduled push notification.
    /*Returns 	: push notificaton sent status.
    /*Created By    : Vipin Kumar R. Jaiswar.
    /***************************************************************************************/
    public function sendPushNotification($PUSHURL, $jsonPushData, $pushId = NULL){
        $APPKEY = $this->_CompanyConfigArr['APPKEY'];
        $MASTERSECRET = $this->_CompanyConfigArr['MASTERSECRET'];
        /* pass pushId if want to delete the Push Notification */
        if($pushId != NULL){
            /* different URL for deleting Schedule Push Notification API */
            $session = curl_init($PUSHURL."/".$pushId);
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, "DELETE");
        }else{
            /* URL for sending NOW and Schedule Push Notification API */
            $session = curl_init($PUSHURL);
            curl_setopt($session, CURLOPT_POST, True);
        }

        curl_setopt($session, CURLOPT_USERPWD, $APPKEY . ':' . $MASTERSECRET);
        curl_setopt($session, CURLOPT_POSTFIELDS, $jsonPushData);
        curl_setopt($session, CURLOPT_HEADER, False);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($session, CURLOPT_VERBOSE, True);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept: application/vnd.urbanairship+json; version=3;'));
        $content = curl_exec($session);
        curl_close($session);
        debugVar($content,true);
    }
    
    /***************************************************************************************/
    /*Purpose 	: To send the push notifation using the CURL call.
    /*Inputs	: $PUSHURL :: the URL for the UA API to be used.
     *                $jsonPushData :: the data or payload to send to UA for push notification.
     *                $pushId :: this param is used only to delete the scheduled push notification.
    /*Returns 	: push notificaton sent status.
    /*Created By    : Vipin Kumar R. Jaiswar.
    /***************************************************************************************/
    public function sendFCMNotification($pStrDeviceTokenIDs){
        //API access key from Google API's Console
        $API_ACCESS_KEY = $this->_CompanyConfigArr['API_ACCESS_KEY'];
        // FCM send URL from config file
        $FCM_SEND_URL = $this->_CompanyConfigArr['FCM_SEND_URL'];

        //If only one device token then use $to and key as to
        //$to = 'fjDG43BqmQg:APA91bEZ5Tka1KUH-8G9p1FWnKeT_J3hprtt8ILEmvjM61L9p0HPNjULPduYR3zKiiT8UwTDq-EbzYsMPOHwrcz3A30GCB1Urp6HZMhbovUAChYLNE2iF6-kutHLhSbcAFZaByqwBmBr';

        //If multiple device tokens then use key "registration_ids"
        $registrationIds = $pStrDeviceTokenIDs;

        //prep the bundle
        $msg = array
                (
                    'body' 	=> 'Body  Of Notification',
                    'title'	=> 'Title Of Notification',
                    'icon'	=> 'myicon',/*Default Icon*/
                    'sound' => 'mySound'/*Default sound*/
                );
        //extra data that needs to be passed usually used for deep linking
        $extra = array(
                    'newsid' => 25,
                );
        $fields = array
                (
                    //'to'              => $to, //If single device token then use key "to"
                    'registration_ids'  => $registrationIds, //If multiple device tokens then use key "registration_ids",
                    'data'              => $extra,
                    'notification'      => $msg
                );


        $headers = array
                    (
                        'Authorization: key=' . $API_ACCESS_KEY,
                        'Content-Type: application/json'
                    );
        //Send Reponse To FireBase Server	
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $FCM_SEND_URL );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        //Echo Result Of FireBase Server
        //echo $result;
        $result = json_decode($result, true);
        if($result['success']){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
}