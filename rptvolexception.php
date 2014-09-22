<!DOCTYPE html>
<html>
<head>
<title>Volunteer Exemption Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$expperiod = 60;					// period in days for time reporting period

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container">
<h3>Volunteer Exception Report&nbsp;&nbsp;
<a class="btn btn-default" href="javascript:self.close();">CLOSE</a></h3>';
if ($action == '') {
print <<<pagePart1
<p>This report is to provide an exemption listing of all those in the membership database that have reported service time.</p>
<p>This report lists: 
<ol>
<li>All volunteers that have not had any time entered within the last $expperiod days.  These members should be reviewed and the membership record mofidied to a non-volunteer status if warranted.</li>
<li>All those who are not listed as a member (member status of 0, 1, or 3) that has reported time within the last $expperiod days. These members should be reviewed and the membership updated appropraitely as well.</li>
</p>
</ol>
<br />

<a class="btn btn-success" href="rptvolexception.php?action=continue">CONTINUE</a>
pagePart1;
exit;
}

$sql = "
SELECT `voltime`.`MCID`, 
MAX( `voltime`.`VolDate` ) AS `MaxDate`, 
SUM( `voltime`.`VolTime` ) AS `TotTime`, 
`members`.`MemStatus`, `members`.`LName`, `members`.`FName` 
 FROM `voltime`, `members` 
WHERE `voltime`.`MCID` = `members`.`MCID` 
GROUP BY `voltime`.`MCID`;
";
// echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$expdate = strtotime("today -$expperiod days");
// echo "rc: $rc<br />";
$expfdate = date('F d, Y',$expdate);
//echo "expdate: $expfdate<br />";
if ($rc == 0) {
	echo "<h4>No MCIDs have failed to report time in the last $expperiod days.</h4>";
	exit;
	}
while ($r = $res->fetch_assoc()) {
	if ((strtotime($r[MaxDate]) < $expdate) AND ($r[MemStatus] != 2)) 
		continue;		// service date PRIOR to the expriation date AND mbr record is NOT a volunteer
	if ((strtotime($r[MaxDate]) > $expdate) AND ($r[MemStatus] == 2))
		continue;		// service date AFTER the expriation date AND mbr record IS a volunteer
	$results[] = $r;							
// echo '<pre>'; print_r($r); echo '</pre>';
	$expcount++;
	}

if (count($results) > 0) {
	echo "<h4>MCID Excpetion Count: $expcount</h4>";
	echo "The list volunteers that have not reported time since $expfdate and/or MCID's that are NOT identified as volunteers but HAVE reported volunteer time after $expfdate.<br />";
	echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>LastDateReported</th><th>FName</th><th>Lname</th><th>MemStatus</th></tr>";
	foreach ($results as $r) {
		echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[MaxDate]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br />';
//echo "expcount: $expcount<br />";
	}
else {
	echo "<h3>No exceptions to list</h3>";
}
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
