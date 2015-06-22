<!DOCTYPE html>
<html>
<head>
<title>Available Lists</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

print <<<pagePart1
<div class="container">

pagePart1;
$lists = readdblist('EmailLists');
$lines = explode("\n",$lists);
echo '<table border="1" align="center" width="80%">
<tr><td valign="top">';  // first row, 1st col

echo '<h4>Currently Defined Mailing Lists</h4>';
echo '<table>
<tr><th>DB Acronym</th><th>List Description</th></tr>';
foreach ($lines as $l) {
	if (strlen($l) <= 0) { continue; } 
	if (substr_compare($l,'//',0,2) == 0) { continue; }
	list($tla, $desc) = explode(":",$l);
	echo "<tr><td>$tla</td><td>$desc</td></tr>";
	}
echo '</div></table>';

// 1st row, 2nd column
echo '</td><td valign="top">';
// second report in right col
echo '<h4>Volunteer List Statistics</h4>';

$sql = "SELECT `MCID`,`Lists` FROM `members` WHERE `MemStatus` = 2;";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

$systemlists = readdblist('EmailLists');
$syslistsarray = formatdbrec($systemlists);
//echo '<pre> syslistsarray '; print_r($syslistsarray); echo '</pre>';

$mcidcount = array(); $listarray = array(); $listcounter = array(); 
while ($r = $res->fetch_assoc()) {
	$listarray = explode(",",$r[Lists]);	// array of member's lists
	$c = count($listarray);								// number of lists for MCID
	$mcidcount[$c] += 1;
//	echo '<pre> listarray '; print_r($listarray); echo '</pre>';
	foreach ($listarray as $v) {
			$listcounter[$v] += 1;
//			if ($v == 'VolInactive') echo "mcid: $r[MCID]<br>";
		}
	}

//echo "<pre> listcounter "; print_r($listcounter); echo "</pre>";
echo "Number of volunteers: " . $rowcnt; echo "<br><br>";
echo "By List: <br><ul>";
$nolists = 0;
foreach ($listcounter as $a => $b) {
	if ($a == "") {
		$nolists = $b;
		}
	else {
		//echo "$a: $b<br />";
		if ($a == 'VolInactive') {
			echo 'VolInactive: ' . $b . '<br>';
			continue;
			}
		if ($a == 'AUL') {
			echo 'Active/Unlisted: '. $b . '<br>';
			continue;
			}
		echo $syslistsarray[$a] . ": ". $b . "<br>";
		}
	}
echo "MCID count with no email lists: $nolists<br />";
echo "</ul><br>";
ksort($mcidcount);
echo "Multi-list counts: <br><ul>";
foreach ($mcidcount as $k => $v) {
	echo "Vols on " . $k . " list(s): " . $v . "<br>";
	}
echo "</ul><br>";

echo '</td></tr>';  // end of 1st row
echo '<tr><td colspan="2" align="center">'; 		// start 2nd row

// now included on the Member Exceptions report
// vols with no mailing lists
/*
echo '
<h4>List of volunteers with NO Mailing Lists</h4>';

$sql = "SELECT * FROM `members` WHERE `MemStatus` = 2 AND `Lists` IS NULL;";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt == 0) {
	echo '<h3>NONE</h3>
	<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>';
	exit;
	}
	
echo '<table align="center" width="80%">
<tr><th>MCID</th><th>Last Name</th><th>First Name</th><th>MemStatus</th><th>MemType</th></tr>';
while($r = $res->fetch_assoc()) {
	//echo '<pre>List members '; print_r($r); echo '</pre>';
	echo "<tr><td>$r[MCID]</td><td>$r[LName]</td><td>$r[FName]</td><td>$r[MemStatus]</td><td>$r[MCtype]</td></tr>";	
	}
echo '</td></tr>';
echo '</div></table>';
*/


?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
