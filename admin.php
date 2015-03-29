<!DOCTYPE html>
<html>
<head>
<title>Volunteer Home Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';

unset($_SESSION['VolActiveMCID']);
unset($_SESSION['VolDB_ERROR']);
if ((($_REQUEST['action']) == 'logout')) {
	include 'Incls//datautils.inc';
	addlogentry("Logging Out");
	unset($_SESSION['VolSessionTimer']);
	unset($_SESSION['VolSessionUser']);
	unset($_SESSION['VolSecLevel']);
	include 'Incls/seccheck.inc';
	}
if ((($_REQUEST['action']) == 'login')) {
	//echo "login request received<br>";
	$userid = $_REQUEST['userid'];
	$password = $_REQUEST['password'];
	if ($userid != "") {
		include 'Incls/datautils.inc';	
		$ok = checkcredentials($userid, $password);
		if ($ok) {
			//echo "check of user id and password passed<br>";
			addlogentry("Logged In");
			}
		else {
			addlogentry("Failed login attempt with password: $password");
			echo "Failed login attempt<br>";
			}
		}
	}

//include 'Incls/vardump.inc';

if (isset($_SESSION['VolSessionUser'])) {
	include 'Incls/mainmenu.inc';
	echo "<div class=\"container\">";
	echo '<h4>Session user logged in: ' . $_SESSION['VolSessionUser'] . '</h4>';
	echo '<h5>Security level: ' . $_SESSION['VolSecLevel'] . '</h5>';
	echo "<form class=\"form-inline\" action=\"admin.php\" method=\"post\"  id=\"xform\">";
	echo "<h3>Volunteer Home Page&nbsp  <button  class=\"btn btn-large btn-primary\" name=\"action\" value=\"logout\" type=\"submit\" form=\"xform\" class=\"btn\">Logout</button></h3></form>";
	}
else {
	echo "<div class=\"container\">
	<form class=\"form-inline\" action=\"volinfotabbed.php\" method=\"post\"  id=\"yform\">";
	echo "<h2>Volunteer Database (VolDB)</h2>";
  echo "<h3>Home Page&nbsp  
  <button autofocus class=\"btn btn-large btn-primary\" name=\"action\" value=\"login\" type=\"submit\" form=\"yform\" class=\"btn\">Login</button></form></h3>";
	}

?>
<!-- START OF PAGE -->
<p><!-- START OF PAGE -->Welcome to the Volunteer Database System (VolDB).  This page will briefly describe the facilities avaiable for administration of this system.  Other informaiton is available by clicking the main menu tabs at the top of this page.</p>
<p><b>The database contains all the information regarding the contacts, members, volunteers and donors of the organization.  Information contained in this databse is not to be sold or shared and is for the exclusive use of the organization.</b></p>
<p>Access to all the facilities of the system are provided on the main menu located at the top of each page.  An individual member's information may be obtained by entering all or the start of the members unique Member/Contact IDentifier (MCID) in the filter area on the far right.  Entry of a blank filter value will provide the option to do a more generalized search of name, address, email or address information. </p>
<p>The database is organized using a unique Member/Contact IDentifier (MCID).  The MCID is comprised of 3 letters (usually the first 3 letters of the members last name) and 2 digits (usually the first 2 digits of the members street address.)  When adding a new MCID a check is made to determine if it is unique.  If it is not merely add 1 to the last digit or use another 2 digit string to make it unique.  After an MCID has been successfully entered a data entry page is proivde to allow entry of further information regarding the member or contact.  That MCID will be used to assoicate all information pertaining to it.</p>
<p>Administrative functions are provided to authorized users that will allow maintenance of the various functions of the system and its associated database.</p>
<p>Security levels are assigned when a new user is registered.  When a user successfully logs in a timed session is established.  Inactivity for longer than 15 minutes will automatically log the user out and require a new login session to be established.</p>

<div class="well">
<h4>GPL License</h4>
<p>Volunteer Database (VolDB)  Copyright (C) 2013 by Pragmatic Computing, Morro Bay, CA</p>
<p>This program comes with ABSOLUTELY NO WARRANTY.  This is free software.  It may be redistributed under certain conditions.  See &apos;Reports->About VolDB&apos; for more information.</p>
</blockquote>
<br />
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
