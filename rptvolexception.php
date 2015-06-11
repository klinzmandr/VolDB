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
include 'Incls/letter_print_css.inc';

$expperiod = 60;					// period in days for time reporting period

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container">
<h3>Volunteer Exception Report&nbsp;&nbsp;
<a class="btn btn-default" href="javascript:self.close();">CLOSE</a></h3>';
echo "Expiration period: $expperiod days<br>";
if ($action == '') {
print <<<pagePart1
<p>This report is to provide an exemption listing of all those in the membership database that have reported service time.</p>
<p>This report has the following sections: 
<ol>
<li>List of all volunteers that have not had any time entered within the last $expperiod days.  These members should be reviewed and the membership record mofidied to an inactive status if warranted.</li>
<li>List of all those who are not listed as a volunteer but has reported time within the last $expperiod days. These MCIDs should be reviewed and updated appropraitely.</li>
<li>List of all volunteer records that are marked as &apos;VolInactive&apos;. These MCIDs should be reviewed and reclassified as either members or donors.</li>
</p>
</ol>
<p>More information regarding <a href="./docs/VolunteerInactiveList.pdf" target="_blank">inactive volunteer identification and corresponding procedures</a> are available.</p>
<br />

<a class="btn btn-success" href="rptvolexception.php?action=continue">CONTINUE</a>
pagePart1;
exit;
}

$sql = "
SELECT `voltime`.`MCID`, 
MAX( `voltime`.`VolDate` ) AS `MaxDate`, 
SUM( `voltime`.`VolTime` ) AS `TotTime`, 
`members`.`MemStatus`, `members`.`LName`, `members`.`FName`, `members`.`Lists` 
 FROM `voltime`, `members` 
WHERE `voltime`.`MCID` = `members`.`MCID` 
AND `Lists` NOT LIKE '%VolInactive%' 
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
	echo "<h4>No MCIDs have reported time in the last $expperiod days.</h4>";
	exit;
	}
$notavol = array(); $results = array(); $notavolcnt = 0; $expcount = 0;
while ($r = $res->fetch_assoc()) {
	if ((strtotime($r[MaxDate]) > $expdate) AND ($r[MemStatus] != 2)) {
		$notavol[] = $r;
		$notavolcnt += 1;
		continue;		// service date PRIOR to the expriation date AND mbr record is NOT a volunteer
		}
	if ((strtotime($r[MaxDate]) < $expdate) AND ($r[MemStatus] == 2)) {
		$results[] = $r;
		$expcount += 1;
		continue;		// service date AFTER the expriation date AND mbr record IS a volunteer
		}						
// echo '<pre>'; print_r($r); echo '</pre>';
	
	}
if ($expcount > 0) {
	echo "<h4>There are $expcount volunteer MCIDs witih NO reported volunteer time in the expiration period.</h4>";
	echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>LastSvcDate</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	foreach ($results as $r) {
		echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[MaxDate]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br />';
//echo "expcount: $expcount<br />";
	echo '
		<div class="page-break"></div>
		';
	}
if ($notavolcnt > 0) {
	echo "<h4>There are $notavolcnt non-volunteer MCIDs that HAVE reported volunteer time during the expiration perod.</h4>";
	echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>LastSvcDate</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	foreach ($notavol as $r) {
		echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[MaxDate]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br />';
	echo '
	<div class="page-break"></div>
	';
//	echo '<pre> not vols'; print_r($notavol); echo '</pre>';
}

$sql = "SELECT `MCID`,`MemStatus`, `LName`, `FName`, `Lists`
FROM `members`
WHERE `MemStatus` = 2 
	AND `Lists` LIKE '%volinactive%'
ORDER BY `MCID` ASC;";
// echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc > 0) {
	echo "<h4>There are $rc volunteer MCIDs that are marked as &apos;Inactive&apos;.</h4>";
	echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	while ($r = $res->fetch_assoc()) {
		echo "<tr><td>$r[MCID]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br />';
//	echo '<pre> not vols'; print_r($notavol); echo '</pre>';
	echo '
		<div class="page-break"></div>
		';
	}

$sql = "SELECT `MCID`,`MemStatus`, `LName`, `FName`, `Lists`
FROM `members`
WHERE `MemStatus` = 2 
	AND `Lists` IS NULL
ORDER BY `MCID` ASC;";
// echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc > 0) {
	echo "<h4>There are $rc volunteer MCIDs that are not on ANY email list.</h4>";
	echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	while ($r = $res->fetch_assoc()) {
		echo "<tr><td>$r[MCID]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br />';
//	echo '<pre> not vols'; print_r($notavol); echo '</pre>';
	echo '
		<div class="page-break"></div>
		';
	}
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
