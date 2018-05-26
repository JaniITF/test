#!/usr/bin/php -q
<?php

define( '_JEXEC', 1 );

error_reporting(0);
ini_set('display_errors', 0);

define('JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../')); 
//ini_set("include_path", '/home/iphpdeco/php:' . ini_get("include_path") );

require_once ( JPATH_BASE .'/includes/defines.php' );




require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';



		// Database connector
		$db = JFactory::getDBO();
require_once('mailReader.php');

$save_directory = __DIR__.'/lab_reports'; 


$mr = new mailReader($save_directory,$db);
$mr->save_msg_to_db = TRUE;
$mr->send_email = TRUE;
$mr->readEmail();

