<!DOCTYPE html>
<html>
<head>
<title>Vol Time</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

echo '<div class="container"><h3>Volunteer Roster&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>';

//echo "mcid: $mcid, sd: $sd, ed: $ed<br />";
$sql = "SELECT * from `members` 
WHERE `MemStatus` = 2
AND `Lists` NOT LIKE '%VolInactive%' 
ORDER BY `MCID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
echo "<b>Total Volunteer Count:</b> $rowcnt<br />";
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes

while ($r = $res->fetch_assoc()) {
$trows[] = "<tr><td>$r[MCID]</td><td>$r[FName]&nbsp;$r[LName]</td><td>$r[MemStatus]</td><td>$r[MCtype]</td>
<td>$r[PrimaryPhone]</td><td>$r[EmailAddress]</td><td>$r[City]</td><td>$r[Lists]</td></tr>";
	}

echo '<table class="table table-condensed">';
echo '<tr><th>MCID</th><th>Name</th><th>MemStat</th><th>MemType</th><th>Phone</th><th>Email</th><th>City</th><th>Lists</th></tr>';
foreach ($trows as $l) { echo $l; }
echo '</table>';	

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
