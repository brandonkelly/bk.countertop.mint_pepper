<?php
 if (!defined('MINT')) { header('Location:/'); } // Prevent viewing this file 


/******************************************************************************
 Countertop Database Configuration
 
 If the table you'd like to count is in a different database than the one
 containing your Mint records, specify it in the following array.
 
 Note that 'database' is the only required value.
 All others, if left blank, will default to your Mint DB settings.
 
 More info at: http://brandon-kelly.com/countertop/
 
 ******************************************************************************/

$countertop_db = array
(
	'server'   => '',
	'username' => '',
	'password' => '',
	'database' => ''
);