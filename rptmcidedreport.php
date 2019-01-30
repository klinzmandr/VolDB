<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Vol Courses</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$mcid = isset($_SESSION['VolActiveMCID']) ? $_SESSION['VolActiveMCID'] : '';
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-01-01', strtotime("previous month"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime(now));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container"><h3>Volunteer Education Report for '.$mcid.'&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>';
if ($mcid == '') {
	echo '<h4>No Active MCID Selected</h4>
	<p>Use the LOOKUP function to select an MCID to use to generate this report.</p>
	<script src="jquery.js"></script>	<script src="js/bootstrap.min.js"></script>
	</body>	</html>';
	exit;
	}

print <<<pagePart1
<form action="rptmcidedreport.php" method="post"  class="form">
Start:<input type="text" name="sd" id="sd" value="$sd" style="width: 105px;">
End: <input type="text" name="ed" id="ed" value="$ed" style="width: 105px;">
<input type="hidden" name="action" value="generate">
<input type="submit" name="submit" Value="Submit">
</form>

pagePart1;

//echo "action: $action<br />";
if ($action == '') {
echo '<h4>This report will list all courses reported for the volunteer for the date range specified.</h4>';
echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body>
</html>';
exit;
	}

//echo "mcid: $mcid, sd: $sd, ed: $ed<br />";
$sql = "SELECT * FROM `voltime` 
WHERE `MCID` = '$mcid' 
	AND `VolCategory` = 'Education' 
	AND `VolDate` BETWEEN '$sd' AND '$ed'
ORDER BY `VolDate` DESC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

if ($rowcnt > 0) {
echo "<b>Period Entry Count:</b> $rowcnt<br />";
// table: voltime: CourseId,CourseDate,CourseDuration,CourseName,CourseNotes

while ($r = $res->fetch_assoc()) {
//	echo '<pre> ed rec '; print_r($r); echo '</pre>';
//	list($agency, $courseid) = explode(':',$r[CourseId]);
	list($courseid,$notes) = explode('/',$r[VolNotes]);
	list($agency, $cid) = explode(':', $courseid);
	$trows[] = "<tr><td>$agency<td>$cid</td><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$notes</td></tr>";
$totaledhrs += $r[CourseDuration];
	}
echo "<b>Total Educ. Hours:</b> $totaledhrs<br />";
echo "<b>Detail Records</b><br />";
echo '<table class="table-condensed">';
echo '<tr><th>Agency</th><th>CourseId</th><th>CourseDate</th><th>Dur.</th><th>Notes</th></tr>';
if (count($trows) != 0) foreach ($trows as $l) { echo $l; }
echo '</table>';	
}

echo '<br>==== END OF REPORT ====<br>';
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body>
</html>
