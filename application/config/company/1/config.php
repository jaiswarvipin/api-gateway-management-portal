<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/****************************************************************************************************/
/* Purpose      : Managing all the configuration variables for one company in one file.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
$config['EMAIL'] = array(
                            'SMTP'=>array(
                                            'development' => array(
                                                'protocol'  => 'smtp',
                                                'smtp_host'  => 'ssl://smtp.googlemail.com',
                                                'smtp_port'  => 465,
                                                'smtp_timeout'  => '30',
                                                'smtp_user'  => 'xxxx',
                                                'smtp_pass'  => 'xxxx',
                                                'charset'  => 'utf-8',
                                                'mailtype'  => 'html',
                                                'wordwrap'  => TRUE,
                                                'newline'  => "\r\n",
                                            ),
                                            'production' => array(
                                                'protocol'  => 'smtp',
                                                'smtp_host'  => 'ssl://smtp.googlemail.com',
                                                'smtp_port'  => 465,
                                                'smtp_timeout'  => '30',
                                                'smtp_user'  => 'xxxx',
                                                'smtp_pass'  => 'xxxx',
                                                'charset'  => 'utf-8',
                                                'mailtype'  => 'html',
                                                'wordwrap'  => TRUE,
                                                'newline'  => "\r\n",
                                            ),
                                            'testing' => array(
                                                'protocol'  => 'smtp',
                                                'smtp_host'  => 'ssl://smtp.googlemail.com',
                                                'smtp_port'  => 465,
                                                'smtp_timeout'  => '30',
                                                'smtp_user'  => 'xxxx',
                                                'smtp_pass'  => 'xxxx',
                                                'charset'  => 'utf-8',
                                                'mailtype'  => 'html',
                                                'wordwrap'  => TRUE,
                                                'newline'  => "\r\n",
                                            ),
                            ),
                            'SNS'=>array(
                                            'development' => array(
                                                'smtp_host'  => 'smtp.mailgun.org'
                                            )
                            ),
                            'MANDRILL'=>array(
                                            'development' => array(
                                                'MANDRILL_APIKEY'  => 'XXXX',
                                                'FROM_EMAIL'  => 'XXXX',
                                                'FROM_NAME'  => 'CMS Admin',
                                            ),
                                            'testing' => array(
                                                'MANDRILL_APIKEY'  => 'XXXX',
                                                'FROM_EMAIL'  => 'XXXX',
                                                'FROM_NAME'  => 'CMS Admin',
                                            ),
                            ),
                );

$config['PUSHNOTIFICATION'] = array(
                                    'UA' => array(
                                                'development' => array(
                                                    'APPKEY' => 'XXXX',
                                                    'PUSHSECRET' => 'XXXX',
                                                    'MASTERSECRET' => 'XXXX',
                                                    'VALIDATEPUSHURL' => 'https://go.urbanairship.com/api/push/validate', //this just validates if the call is correct
                                                    'PUSHURL' => 'https://go.urbanairship.com/api/push',
                                                    'SCHEDULE_PUSHURL' => 'https://go.urbanairship.com/api/schedules',
                                                ),
                                                'production' => array(
                                                    'APPKEY' => 'XXXX',
                                                    'PUSHSECRET' => 'XXXX',
                                                    'MASTERSECRET' => 'XXXX',
                                                    'VALIDATEPUSHURL' => 'https://go.urbanairship.com/api/push/validate', //this just validates if the call is correct
                                                    'PUSHURL' => 'https://go.urbanairship.com/api/push',
                                                    'SCHEDULE_PUSHURL' => 'https://go.urbanairship.com/api/schedules',
                                                ),
                                                'testing' => array(
                                                    'APPKEY' => 'XXXX',
                                                    'PUSHSECRET' => 'XXXX',
                                                    'MASTERSECRET' => 'XXXX',
                                                    'VALIDATEPUSHURL' => 'https://go.urbanairship.com/api/push/validate', //this just validates if the call is correct
                                                    'PUSHURL' => 'https://go.urbanairship.com/api/push',
                                                    'SCHEDULE_PUSHURL' => 'https://go.urbanairship.com/api/schedules',
                                                ),
                                            ),
                                    'FCM' => array(
                                            'development' => array(
                                                'API_ACCESS_KEY' => 'XXXX',
                                                'FCM_SEND_URL' => 'https://fcm.googleapis.com/fcm/send',
                                            ),
                                            'production' => array(
                                                'API_ACCESS_KEY' => 'XXXX',
                                                'FCM_SEND_URL' => 'https://fcm.googleapis.com/fcm/send',
                                            ),
                                            'testing' => array(
                                                'API_ACCESS_KEY' => 'XXXX',
                                                'FCM_SEND_URL' => 'https://fcm.googleapis.com/fcm/send',
                                            ),
                                        ),
);