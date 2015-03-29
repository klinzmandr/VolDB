<!DOCTYPE html>
<html>
<head>
<title>New Mailing List</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">
<?php
session_start();

//include 'Incls/vardump.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';
include 'Incls/seccheck.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
echo '<div class="container">';
if ($action == 'display') {
	if ($_SESSION['VolSecLevel'] != 'voladmin') {
		echo '<h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to maintain these lists.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded to &apos;voladmin&apos; in order to modify any lists.</p>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
		</body></html>';
		exit;
		}
	echo '<h4>Current list definitions</h4>
	<p>Modify the current list to add or delete items.</p>';
	$txt = readdblist('EmailLists');
	echo '<form action="listcreatenew.php" method="post">	
	<textarea name="CfgText" rows="20" cols="100">'.$txt.'</textarea>
	<input type="hidden" name="action" value="update"><br />
	<input type="submit" name="submit" value="Apply Update(s)">
	<form>	
	<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
</body>
</html>';
	exit;
	}

if ($action == 'update') {
	$text = $_REQUEST['CfgText'];
	//echo "update: $text<br>";
	updatedblist('EmailLists',$text);
	$action = '';
	}
	
if ($action == '') {
print <<<pagePart1
<h3>Create New Mailing List</h3>
<p>This function is provided to allow the creation or deletion of volunteer mailing lists.  These lists are used to communicate with the volunteers that have subscribed to the mailing list.  Maintenance of the volunteer&apos;s individual mailing list is done by changing the checked lists on the members records.</p>
<p>NOTE:  only volunteer records (those marked as a 'Member Status' of 2) will display the mailing lists.</p>
<p>Lists currently defined:</p>

pagePart1;
$txt = readdblist('EmailLists');
$txtarray = formatdbrec($txt);
echo "<pre>$txt</pre>";

echo '<p>Please carefully note the format and layout of the text.  The following applies:</p>
<ul><li>All lines that begin with &apos;//&apos; are considered comment lines and are ignored,</li>
<li>All blank lines are ignored,</li>
<li>Data lines are comprised of two parts,</li>
<ul><li>An acronym code used in the database, and</li>
<li>A description of the list</li></ul>
<li>A colon seperates the two</li></ul>
<p>This data line format must be maintained in order for the system to properly process the list name.</p>
<p>NOTE: DO NOT MODIFY EXISTING NAMES!  It is recommended that name lists be completely deleted.  volunteers subscribed to a list that is deleted are not modified.  The deleted list name will simply not appear in the as a choice when creating an email to a specific list.</p>';
echo '<form action="listcreatenew.php" method="post"  name="passwdform">
To modify this information click the button:
<!-- <input type="password" name="pw" value="" autocomplete="off"> -->
<input type="hidden" name="action" value="display">
<input type="submit" name="submit" value="Update Lists">
</form>';
	}
?>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
