<!DOCTYPE html>
<html>
<head>
<title>Volunteer System Home Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : '';

// NOTE: (isset($var) && !empty($var)) will be equals to !empty($var)
// http://php.net/manual/en/types.comparisons.php

//echo "show login fields"; include 'Incls/vardump.inc.php'; 
if (!isset($_REQUEST['userid'])) {                // no user id
  if (!isset($_SESSION['VolSessionUser'])) {         // and no session id
    // echo "userid empty, no volsessionuser<br>"; 
  	include 'Incls/seccheck.inc.php';             // present login fields
  	exit;
  	}
  }

if (!empty($userid)) {
//  echo "check uid/pw"; include 'Incls/vardump.inc.php';  
  $_SESSION['SessionActive'] = date("Y-m-d H:i:s");
	include_once 'Incls/datautils.inc.php';
	$password = $_REQUEST['password'];
	$ok = checkcredentials($userid, $password);
	if ($ok) {
		//echo "check of user id and password passed<br>";
		addlogentry("Logged In");
		}
	else {
	  unset($_SESSION['SessionActive']);
    // addlogentry("Failed login attempt with password: $password");
    // echo '<h3 style="color: red; ">Failed login attempt</h3>';
		}
	}

echo "<div class=\"container\">";
if (!empty($_SESSION['VolSessionUser'])) {
  // echo "show logged in"; include 'Incls/vardump.inc.php';
  include_once 'Incls/datautils.inc.php';
  include 'Incls/seccheck.inc.php';         
  include_once 'Incls/mainmenu.inc.php';
	echo '<h4>Session user logged in: ' . $_SESSION['VolSessionUser'] . '</h4>
	<h5>Security level: ' . $_SESSION['VolSecLevel'] . '</h5>
	<form class="form-inline" action="indexsto.php?lo=lo" method="post"  id="xform">
  <h3>Volunteer System Home Page&nbsp  
  <button  class="btn btn-large btn-primary" name="action" value="logout" type="submit" form="xform" class="btn">Logout</button>
  </h3></form>
  </table></ul>';
	}
else {
	echo '
	<h3>Volunteer System Home Page&nbsp  
	<a class="btn btn-large btn-primary" href="admin.php">Login</a></h3>
	</h3>';
	}
// echo "show after logged in"; include 'Incls/vardump.inc.php';
?>

<!-- START OF PAGE -->
<p>Welcome to the Volunteer System (VolDB). This page will briefly describe the facilities available for administration of this system. Other information is available by clicking the main menu tabs at the top of this page.</p>
<p><b>The database contains all the information regarding the contacts, members, volunteers and donors of the organization: collectively referred to as supporters. Information contained in this database is not to be sold or
shared and is for the exclusive use of the organization.</b></p>
<p>Access to all the facilities of the system are provided on the main menu located at the top of each page. An individual supporter’s information may be obtained by entering all or the start of the supporter’s unique Member/Contact IDentifier (MCID) in the Lookup box on the far right. Leave the Lookup box empty to perform a more generalized search of name, address, city, and email information.</p>
<p>The database is organized using a unique Member/Contact IDentifier (MCID). This MCID will be used to access all information pertaining to an individual. The MCID is comprised of 3 letters (usually the first 3 letters of the supporter’s last name) and 2 digits (usually the first 2 digits of the supporter’s street address or the last 2 digits of the phone number).
When adding a new MCID a check is made to determine if it is unique. If it is not merely add 1 to the last digit or use another 2 digit string to make it unique. After an MCID has been successfully entered a data entry page is provided to allow entry of further information regarding the supporter.</p>
<p>Administrative functions, that allow maintenance of the various functions of the system and its associated database, are provided to authorized users.</p>
<p>Security levels are assigned when a new user is registered. A timed session is established when a user successfully logs in. Inactivity for longer than 20 minutes will automatically log the user out and require a new login session to be
established.</p>

<div class="well">
<h4>GPL License</h4>
<p>Volunteer System (VolDB)  Copyright (C) 2013 by Pragmatic Computing, Morro Bay, CA</p>
<p>This program comes with ABSOLUTELY NO WARRANTY.  This is free software.  It may be redistributed under certain conditions.  See &apos;Reports->About MbrDB&apos; for more information.</p>
</div>
</body>
</html>
