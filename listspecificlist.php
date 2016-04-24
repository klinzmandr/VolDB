<!DOCTYPE html>
<html>
<head>
<title>Specific Lists</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
// include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$listname = $_REQUEST['listname'] ? $_REQUEST['listname'] : '';
echo '
<div class="container">
<h3>List Members of Available Mailing List';
if ($action != "") echo ": $listname";
echo '</h3>
<form action="listspecificlist.php" method="post">
<table class="table"><td>
<select onchange="this.form.submit()" name="listname" size="1">
<option value="">Select List Name</option>';
loaddbselect('EmailLists');
echo '</select>
<input type="hidden" name="action" value="display">
</td>';
if ($_SESSION['VolSecLevel'] == 'voladmin')
 echo '<td>
 <a class="btn btn-warning" href="listspecificlist.php?listname=AUL&action=display">List Active/Unlisted</a>
 <a class="btn btn-danger" href="listspecificlist.php?listname=VolInactive&action=display">List Inactives</a>
</td>';

echo '</table></form>';

if ($action == 'display') {	
	$sql = "SELECT * FROM `members` 
	WHERE `Lists` LIKE '%$listname%'
	AND `Lists` NOT LIKE '%VolInactive%';";
	if ($listname == 'VolInactive') 
		$sql = "SELECT * FROM `members`	WHERE `Lists` LIKE '%$listname%';";
	if ($listname == 'AUL') 
		$sql = "SELECT * FROM `members`	WHERE `Lists` LIKE '%$listname%';";
	$res = doSQLsubmitted($sql);
	$rowcnt = $res->num_rows;
	if ($rowcnt == 0) {
		echo '<h3>There are no volunteers in the list '.$listname.'</h3>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>';
		exit;
		}
	
	echo '<table class="table table-condensed">
<tr><th>MCID</th><th>Last Name</th><th>First Name</th><th>City</th><th>Email</th><th>PhoneNumber</th><th>Notes/Additional Phone Numbers</th></tr>';
	$emarray = array();
	while($r = $res->fetch_assoc()) {
//		echo '<pre>List members '; print_r($r); echo '</pre>';
		$emarray[] = $r[EmailAddress];
		echo "<tr><td><a href=\"volinfotabbed.php?filter=$r[MCID]\">$r[MCID]</a></td>
		<td>$r[LName]</td><td>$r[FName]</td><td>$r[City]</td><td>$r[EmailAddress]</td><td>$r[PrimaryPhone]</td><td>$r[Notes]</td></tr>";	
		}
	echo '</table>';
	
echo '<h3>Comma seperated list of email addresses</h3>';
$emlist = implode($emarray, ', ');
echo $emlist;
echo '<br /><br />';
echo '</div>';
}

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
