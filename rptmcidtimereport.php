<!DOCTYPE html>
<html>
<head>
<title>Vol Time</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$mcid = isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : '';
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-01-01', strtotime("previous month"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime(now));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container"><h3>Volunteer Time Report for '.$mcid.'&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>';
if ($mcid == '') {
	echo '<h4>No Active MCID Selected</h4>
	<p>Use the LOOKUP function to select an MCID to use to generate this report.</p>
	<script src="jquery.js"></script>	<script src="js/bootstrap.min.js"></script>
	</body>	</html>';
	exit;
	}

print <<<pagePart1
<form action="rptmcidtimereport.php" method="post"  class="form">
Start:<input type="text" name="sd" id="sd" value="$sd" style="width: 105px;">
End: <input type="text" name="ed" id="ed" value="$ed" style="width: 105px;">
<input type="hidden" name="action" value="generate">
<input type="submit" name="submit" Value="Submit">
</form>

pagePart1;

//echo "action: $action<br />";
if ($action == '') {
echo '<h4>This report will list all reported volunteer time for the specific volunteer assoicated with the MCID currently &apos;active&apos; for the date period specified.</h4>';
echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>';
exit;
	}
//echo "mcid: $mcid, sd: $sd, ed: $ed<br />";
$sql = "SELECT * from `voltime` WHERE `MCID` = '$mcid' AND (`VolDate` >= '$sd' AND `VolDate` <= '$ed') ORDER BY `VTID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
echo "<b>Period Entry Count:</b> $rowcnt<br />";
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes

while ($r = $res->fetch_assoc()) {
$trows[] = "<tr><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td></tr>";
$vc = 'Uncategorized';
if (strlen($r[VolCategory]) > 0) $vc = $r[VolCategory];
$totalvolhrs += $r[VolTime];
$tothrs[$vc] += $r[VolTime];
$totmiles += $r[VolMileage];
	}
echo "<b>Total Miles Driven:</b> $totmiles,&nbsp;";
echo "<b>Total Volunteer Hours:</b> $totalvolhrs<br />";
echo "<b>Total Hours by Category:</b><br />";
if (count($tothrs) != 0) {
	foreach ($tothrs as $k => $v) echo "&nbsp;&nbsp;&nbsp;$k: $v<br />";
	}
echo "<b>Detail Records</b><br />";
echo '<table class="table-condensed">';
echo '<tr><th>Date</th><th>Vol Time</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>';
if (count($trows) != 0) foreach ($trows as $l) { echo $l; }
echo '</table>';	

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
