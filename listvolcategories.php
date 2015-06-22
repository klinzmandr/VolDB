<!DOCTYPE html>
<html>
<head>
<title>Vol Time Categories</title>
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
		<h4>You do not have the correct authorization to maintain this list.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded to &apos;voladmin&apos; in order to modify any lists.</p>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
		</body></html>';
		exit;
		}
	echo '<h4>Current Volunteer Time Categories</h4>
	<p>Modify the currently defined volunteer time categories.</p>';
	$txt = readdblist('VolCategorys');
	echo '<form action="listvolcategories.php" method="post">	
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
	updatedblist('VolCategorys',$text);
	$action = '';
	}
	
if ($action == '') {
print <<<pagePart1
<h3>Maintain Volunteer Time Categories</h3>
<p>This function is provided to allow the creation or deletion of volunteer time categories used in the volunteer management system.  These categories are used to designate areas of volunteer support during the volunteer time entry process as well as for the volunteer self time entry program.  Maintenance of the volunteer&apos;s categories is done by selection from drop down menu selection during data entry.</p>
<p>NOTE:  these selections are merely displayed as choices during the volunteer time data entry process.  Changing or adding to this list does not effect records previously entered.</p>
<p>Volunteer categories currently defined:</p>

pagePart1;
$txt = readdblist('VolCategorys');
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
echo '<form action="listvolcategories.php" method="post"  name="passwdform">
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
