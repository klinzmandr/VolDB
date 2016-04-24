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
//include 'Incls/vardump.inc.php';

unset($_SESSION['VolActiveMCID']);
unset($_SESSION['VolDB_ERROR']);
if ((($_REQUEST['action']) == 'logout')) {
	include 'Incls//datautils.inc.php';
	addlogentry("Logging Out");
	unset($_SESSION['VolSessionTimer']);
	unset($_SESSION['VolSessionUser']);
	unset($_SESSION['VolSecLevel']);
	include 'Incls/seccheck.inc.php';
	}
if ((($_REQUEST['action']) == 'login')) {
	//echo "login request received<br>";
	$userid = $_REQUEST['userid'];
	$password = $_REQUEST['password'];
	if ($userid != "") {
		include 'Incls/datautils.inc.php';	
		$ok = checkcredentials($userid, $password);
		if ($ok) {
			//echo "check of user id and password passed<br>";
			$loc = $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'];
			addlogentry("Logged In@$loc");
			}
		else {
			addlogentry("Failed login attempt with password: $password");
			echo "Failed login attempt<br>";
			}
		}
	}

//include 'Incls/vardump.inc.php';

if (isset($_SESSION['VolSessionUser'])) {
	include 'Incls/mainmenu.inc.php';
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
<p>Welcome to the Volunteer Database System (VolDB). This page will briefly describe the facilities available for
administration of this system. Other information is available by clicking the main menu tabs at the top of this page.</p>
<p><b>The database contains all the information regarding the contacts, members, volunteers and donors of the
organization: collectively referred to as supporters. Information contained in this database is not to be sold or
shared and is for the exclusive use of the organization.</b></p>
<p>Access to all the facilities of the system are provided on the main menu located at the top of each page. An individual
supporter’s information may be obtained by entering all or the start of the supporter’s unique Member/Contact IDentifier
(MCID) in the Lookup box on the far right. Leave the Lookup box empty to perform a more generalized search of name,
address, city, and email information.
</p>The database is organized using a unique Member/Contact IDentifier (MCID). This MCID will be used to access all
information pertaining to an individual. The MCID is comprised of 3 letters (usually the first 3 letters of the supporter’s last
name) and 2 digits (usually the first 2 digits of the supporter’s street address or the last 2 digits of the phone number).
When adding a new MCID a check is made to determine if it is unique. If it is not merely add 1 to the last digit or use
another 2 digit string to make it unique. After an MCID has been successfully entered a data entry page is provided to
allow entry of further information regarding the supporter.
<p>Administrative functions, that allow maintenance of the various functions of the system and its associated database, are
provided to authorized users.</p>
<p>Security levels are assigned when a new user is registered. A timed session is established when a user successfully logs
in. Inactivity for longer than 15 minutes will automatically log the user out and require a new login session to be
established.</p>

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
