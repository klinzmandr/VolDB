<!DOCTYPE html>
<html>
<head>
<title>Member Filter Listing</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
session_start();
//include 'Incls/seccheck.inc.php';
unset($_SESSION['VolActiveMCID']);
//include 'Incls/vardump.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$m = "<p><b>Use of the Filer field</b></p><p>The Filter field is used to access member informaton by specifying all or the starting part of their MCID.  For example, if a members MCID is \"ABC12\" one could enter the entire string (in either upper or lower case) to access the record directly.  If one were to enter any of the beginning of the MCID (e.g. \"A\", or \"AB\", or \"ABC\" or even \"ABC1\" a list of candidate MCID's would be produced to choose your specific one from.  At any time the entry of a partial MCID produces only 1 record, it will be shown.</p>
	<p>When ever a list of MCID records is produced, click the bullet at the left of the page associated with the MCID to access the specific member records</p>
	<p>Once a single member record has been accessed, its correspondence and fund information records will be available by clicking on the main menu at the top of the page.  That MCID will remain the target until a new MCID is searched for or you click the \"Home\" menu choice.</p>";

$filter = (isset($_REQUEST['filter'])) ? $_REQUEST['filter'] : "";
echo "<div class=\"container\">";

// search db for filter value
$sql = "SELECT * FROM `members` WHERE `MCID` LIKE '".$filter."%' ORDER BY `MCID`";
$results = doSQLsubmitted($sql);
$nbrofrows = $results->num_rows;
//echo "Number of rows in result: $nbrofrows<br>";
// nothing returned so ask again
if ($nbrofrows == 0) {
	echo '<h2>No volunteers found with MCID filter provided</h2><br />
	<h4>Please try again or list the volunteers using the List Manager on the main menu.</h4>
	<script src="jquery.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	</body>
	</html>';
	exit;
	}
// only 1 found so pass it to mbr information page
if ($nbrofrows == 1) {
		echo "only 1 match<br />";
		$results->data_seek(0);
		$row = $results->fetch_assoc();
		$filter = $_SESSION['VolActiveMCID'] = $row['MCID'];
print <<<oneBullet
<form action="volinfotabbed.php" name="oneform" method="post">
<input type="text" name="filter" value="$filter" />
<-- <input type="submit" /> -->
</form>
<SCRIPT TYPE="text/JavaScript">document.forms["oneform"].submit();</SCRIPT>
oneBullet;
	exit;
	}

// multiple rows returned, list identifying fields to select from
$results->data_seek(0);
echo "<fieldset><legend>MCID list starting with: $filter</legend>";
echo '<table class="table table-condensed">
<tr><th>MCID</th><th>Last Name</th><th>First Name</th><th>Label Line 1</th><th>Email Address</th></tr>';

while ($row = $results->fetch_assoc()) {
	$mcid=$row['MCID'];  $fname=$row['FName']; $lname=$row['LName'];
	$addr1=$row['Organization']; $addr2=$row[AddressLine2]; $lab1line=$row['NameLabel1stline']; 
	$eaddr1=$row['EmailAddress1']; $eadddr2=$row['EmailAddress2']; 
print <<<bulletForm
<tr><td><a href="volinfotabbed.php?filter=$mcid">$mcid</a></td><td>$lname</td><td>$fname</td><td>$lab1line</td><td>$eaddr1</td><td>$eaddr2</td></tr>
bulletForm;
}
echo "</table>";
?>
</div>
<script src="jquery.js"></script>
<script src="../js/bootstrap.min.js"></script>

</body>
</html>
