<!DOCTYPE html>
<html>
<head>
<title>Vol List Maint</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php 
session_start();
//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';

unset($_SESSION['TEST_MODE']);
//$_SESSION['TEST_MODE'] = 'TEST_MODE';  //force use of test data

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '' ;
$mcid = isset($_REQUEST['MCID']) ? $_REQUEST['MCID'] : '';
$email = isset($_REQUEST['EmailAddress']) ? $_REQUEST['EmailAddress'] : '';

if ($action == '') {
	echo '<form action="index.php" method="post">
	Enter your email address: <input autofocus type="text" name="EmailAddress"><br />
	Enter your PWC Id:<input type="text" name="MCID">
	<input type="hidden" name="action" value="form">
	<input type="submit" name="submit" value="Submit">
	</form>';
	exit;
	}

echo "<div align=\"center\"><img src=\"".$HomeURL."/voldb/PWC680logo.jpg\" alt=\"PWC Logo\"></div>";
echo "<html><head><title>PWC List Subscribe/Unsubcribe</title></head><body>";
echo '<div class="container">';

// create form for display
if ($action == 'form') {

//$sql = "SELECT * FROM `members` WHERE `MCID` = '$mcid' AND `EmailAddress` = '$email';";
$sql = "SELECT * FROM `members` WHERE `MCID` = '$mcid';";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

if ($rowcnt == 0) {
	echo "<h3>Your e-mail address and the PWC Id entered not found.</h3>";
	echo 'Message from select on read for form info';
	echo "email: $email, mcid: $mcid<br />";
	exit;
	}
$r = $res->fetch_assoc();

$fname = $r[FName]; $lname  = $r[LName]; $lists = $r[Lists]; $city = $r[City]; $phone = $r[PrimaryPhone];
$email = $r[EmailAddress];
print<<<outForm1
<h2>Volunteer information for $fname $lname ($r[MCID])</h2>
<h4>Not $fname $lname? <a class="btn btn-danger" href="$HomeURL">EXIT NOW</a></h4>
<p>Following are the lists available.  Those that you are subscribed to are checked.  Please update this list by checking or unchecking those lists you wish to receive notices from and click the SUBMIT button to update your profile.</p>
<p>Please use the free form notes area to list home and/or cell phone number or other contact inforamtion that might be needed.</p>
<form action="index.php" method="post">
Email Address: <input type="text" name="EmailAddress" value="$email"><br />
City: <input type="text" name="City" value="$city" >&nbsp;&nbsp;
Primary Contact Phone: <input type="text" name="PrimaryPhone" value="$phone"><br />

<table border="1">
<tr><td valign="top">LISTS:<br />
outForm1;
$AllLists = readdblist('EmailLists');
$AllListsArray = formatdbrec($AllLists);

foreach ($AllListsArray as $k => $v) {
	if (stripos($lists, $k) !== FALSE) {
		echo "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"Lists[]\" value=\"$k\" checked>$v<br>";
		}
	else {
		echo "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"Lists[]\" value=\"$k\">$v<br>";
		}
	}
print<<<outForm2
</td><td valign="top">NOTES: Please note secondary phone numbers and other pertenaint information.<br><textarea name="Notes" rows="10" cols="60">$r[Notes]</textarea></td></tr>
</td></tr></table>
<input type="hidden" name="action" value="upd">
<input type="hidden" name="MCID" value="$mcid">
<input type="submit" name="submit" value="Update Info">
</form>
<br /><br />
You may also merely 
<a class="btn btn-info btn-default" href="$HomeURL">CANCEL</a> this form and return to the PWC Home Page.<br />

outForm2;
exit(0);
	} // end: if $action="form"

// apply updates to database
if ($action == "upd") {

$mcid = $_REQUEST['MCID']; $city= $_REQUEST['City']; $phone = $_REQUEST['PrimaryPhone'];
$notes = $_REQUEST['Notes'];
$listarray = $_REQUEST['Lists'];
$Lists = implode(",",$listarray);
$updarray = array();
$updarray[EmailAddress] = $email;
$updarray[PrimaryPhone] = $phone;
$updarray[Lists] = $Lists;
$updarray[City] = $city;
$updarray[Notes] = $notes;
// echo '<pre> update array '; print_r($updarray); echo '</pre>';
sqlupdate('members', $updarray, "`MCID` = '$mcid'");

$msg = $mcid . " has updated their lists via self update";
addmaillogentry($msg);
$_SESSION['SessionUser'] = $mcid;
addlogentry($msg);
unset($_SESSION['SessionUser']);	
	}  // end if $action == "upd"

$sql = "SELECT * FROM `members` WHERE `MCID` = '$mcid' AND `EmailAddress` = '$email';";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

if ($rowcnt == 0) {
	echo "<h3>Your e-mail address and the PWC Id entered not found.</h3>";
	exit;
	}
$r = $res->fetch_assoc();

print<<<pageBody
<h3>Volunteer Update Utility</h3>

<p>This page provides access to your volunteer information used by PWC to communicate with you.  This information is accessed using your email address and the unique identifier assigned to you when your information was entered into the volunteer database.</p>
<p>Included in this information are those mailing lists that you have subscribed to.</p>
<p>You may review and update all of this information by clicking on the link below.</p>
<p>You may update any information that is provided including the mailing lists that you which to be assoicated with.</p>
<!-- <a class="btn btn-success" href="index.php?action=form&EmailAddress=$email&MCID=$mcid">Review/Update Your Volunteer Info</a><br /><br /> -->
<!-- <button class="btn btn-success" autofocus form_id="inform" type="submit">Review/Update Your Volunteer Info</button> -->
<form id="inform" action="index.php" method="post">
<input type="hidden" name="EmailAddress" value="$email" />
<input type="hidden" name="MCID" value="$mcid" />
<input type="hidden" name="action" value="form" />
<input class="btn btn-success" type="submit" name="submit" value="Review/Update Your Volunteer Info" />
</form><br />
<a class="btn btn-primary" href="$HomeURL">Cancel and go to PWC&apos;s Home Page</a>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
pageBody;
?>