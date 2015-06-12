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
echo "<b>Expiration period: $expperiod days</b><br><br>";
if ($action == '') {
print <<<pagePart1

<p>This report has the following sections which will only appear in the report if there are any MCIDs that qualify.
<ol>
<li><b>MCIDs with NO reported volunteer time in the expiration period.</b><br>
This section of the report should be printed out and provided to the Center Operations Committee (COPS) to review and approve reclassification of any/all of MCIDs listed to a non-volunteer status.  
Reclassification steps must be performed by an administrative user and consists of adding 'Inactive' to the mailing lists of the MCID record then changing the Member Status and Member Type fields of the supporter record to a member or donor designation.  Other mailing lists selected should be left for future reference.
</li>
<li><b>MCIDs that HAVE reported volunteer time during the expiration period.</b><br>
All MCIDs listed in this section of the report should be reclassified as a volunteer by changing the Member Status and Member Type fields of the supporter record to a volunteer designation.</li>
<li><b>MCIDs that are marked as 'Inactive'.</b><br>
The MCIDs listed need to be reviewed and an administrative user must either remove the 'Inactive' flag from the Lists of the volunteer OR change the Member Status and Member Type fields of the supporter record to a member or donor designation.</li>
<li><b>MCIDs that are not on ANY email list.</b><br>
Any MCID listed in this section should be assumed to have withdrawn from being a volunteer.  These MCIDs should be reclassified by changing the Member Status and Member Type fields of the supporter record to a member or donor designation.
</li>
</p>
<br />

<a class="btn btn-success" href="rptvolexception.php?action=continue">CONTINUE</a>
<br><br>
<a href="./docs/VolunteerInactiveList.pdf" target="_blank">More information about inactive volunteers is available.</a>
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
