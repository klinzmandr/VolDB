<!DOCTYPE html>
<html>
<head>
<title>Member Search</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
include 'Incls/seccheck.inc';
include "Incls/datautils.inc";
//include "Incls/vardump.inc";
if ($_SESSION['VolSecLevel'] == 'readonly') include 'Incls/romenu.inc';
else include 'Incls/mainmenu.inc';



$filter = (isset($_REQUEST['filter'])) ? $_REQUEST['filter'] : "";

print <<<searchForm1
<div class="container">
<h1>Volunteer Search</h1>
<form action="volsearchlist.php" method="post"  name="searchform" class="form-inline">
<input type="text" name="filter" class="form-control" style="width: 200px; "  placeholder="Search String" autofocus value=$filter>
<input type="submit" class="btn btn-default" name="submit" value="Apply">
</form>
searchForm1;
if ($filter == "") {
	print <<<searchForm2
<br />
<p><b>Tips for a General VOLUNTEER Search</b></p>
<p><b>NOTE: THIS WILL ONLY SEARCH REGISTERED VOLUNTEERS.</b></p>
<p>Any character string may be entered,  The string provided is compared to the first name, last name, label name, address, city and email adress fields of every record in the database.</p><p>All records containing the string will be listed on the results page with a bullet preceeding it.  Click the specific bullet assoicated with the MCID to access that members information along with all its assoicated correspondence and funding records.</p>
<p>Try and keep the string entered to 4-6 characters even though many more may be entered.  Entering a longer string may result in no records being returned.</p>
<p>NOTE: A percent sign (%) may be used to list all member records of currently on the database.</p><br><br>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
<!-- <a class="btn btn-primary" href="volinfotabbed.php" name="filter" value="">CANCEL AND RETURN</a> -->

searchForm2;
	exit;
	}

// search db for filter value
$sql = "SELECT * FROM `members` 
WHERE ( `MCID` LIKE '%".$filter."%' 
	OR `FName` LIKE '%".$filter."%' 
	OR `LName` LIKE '%".$filter."%' 
	OR `NameLabel1stline` LIKE '%".$filter."%' 
	OR `Organization` LIKE '%".$filter."%' 
	OR `AddressLine` LIKE '%".$filter."%' 
	OR `EmailAddress` LIKE '%".$filter."%' 
	OR `City` LIKE '%".$filter."%' 
	OR `ZipCode` LIKE '%".$filter."%'
	OR `Notes` LIKE '%".$filter."%' )
 AND `MemStatus` = '2'
ORDER BY `MCID`";
$results = doSQLsubmitted($sql);
//$results = searchForMember($filter);
$nbrofrows = $results->num_rows;
//echo "Number of rows in result: $nbrofrows<br>";
// nothing returned so ask again
if ($nbrofrows == 0) {
	echo "<h2>No members found with the search string provided</h2>";
	echo "<p>Enter a string to search for in first name, last name, email address, address or city</p><br /><br />";
	echo "<a class=\"btn btn-primary\" href=\"volinfotabbed.php\">CANCEL AND RETURN</a></div>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
</body></html>';
	exit();
	}
// only 1 found so pass it to volinfotabbed page
if ($nbrofrows == 1) {
		echo "only 1 match<br />";
		$results->data_seek(0);
		$row = $results->fetch_assoc();
		$mcid = $_SESSION['VolActiveMCID'] = $row['MCID'];
print <<<oneBullet
<form action="volinfotabbed.php" name="FORM_NAME" method="post">
<input autofocus type="text" name="filter" value="$mcid" />
<input type="submit" />
</form>
<SCRIPT TYPE="text/JavaScript">document.forms["FORM_NAME"].submit();</SCRIPT>
<br /><br />
oneBullet;
	exit;
	}
// multiple rows returned, list identifying fields to select from
$results->data_seek(0);
//echo "<fieldset><legend>List containing: $filter</legend>";
echo "<fieldset><h3>List of $nbrofrows containing: $filter</h3>";
echo "<table class=\"table table-condensed\">";
echo '<tr><th>MCID</th><th>Last Name</th><th>First Name</th><th>Address</th><th>City</th><th>Email</th></tr>';
while ($row = $results->fetch_assoc()) {
	$mcid=$row['MCID'];  $fname=$row['FName']; $lname=$row['LName'];
	$org=$row['Organization']; $addr=$row['AddressLine']; $lab1line=$row['NameLabel1stline']; 
	$city = $row[City]; $eaddr=$row['EmailAddress'];  
print <<<bulletForm
<tr><td><a href="volinfotabbed.php?filter=$mcid">$mcid</a></td><td>$lname</td><td>$fname</td><td>$addr</td><td>$city</td><td>$eaddr</td></tr>

bulletForm;
}
echo "</fieldset></table>";
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
