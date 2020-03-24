<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/****************************************************************************************************/
/* Purpose      : Managing all the configuration variables for one company in one file.
/* Created By   : Vipin Kumar R. Jaiswar.
/****************************************************************************************************/
$config['EMAIL'] = array(
                            'SMTP'=>array(
                                            'development' => array(
                                                'smtp_host'  => 'localhost',
                                                'smtp_port'  => '25',
                                                'smtp_timeout'  => '30',
                                                'smtp_user'  => '',
                                                'smtp_pass'  => '',
                                                'charset'  => 'utf-8',
                                                'mailtype'  => 'html',
                                                'wordwrap'  => TRUE,
                                                'newline'  => "\r\n",
                                            ),
                                            'production' => array(
                                                'smtp_host'  => 'smtp.mailgun.org',
                                                'smtp_port'  => '587',
                                                'smtp_timeout'  => '30',
                                                'smtp_user'  => '',
                                                'smtp_pass'  => '',
                                                'charset'  => 'utf-8',
                                                'mailtype'  => 'html',
                                                'wordwrap'  => TRUE,
                                                'newline'  => "\r\n",
                                            ),
                                            'testing' => array(
                                                'smtp_host'  => 'smtp.mailgun.org',
                                                'smtp_port'  => '587',
                                                'smtp_timeout'  => '30',
                                                'smtp_user'  => '',
                                                'smtp_pass'  => '',
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
                );