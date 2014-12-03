<!DOCTYPE html>
<html>
<head>
<title>Service Analysis</title>
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

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("previous month"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime("previous month"));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container"><h3>Volunteer Service Analysis&nbsp;&nbsp;<a class="btn btn-default" href="javascript:self.close();">CLOSE</a></h3>';

//if ($sd == '') $sd = date('Y-m-d', strtotime("first day of previous month"));
//if ($ed == '') $ed = date('Y-m-d', strtotime("last day of previous month"));
print <<<pagePart1
<form action="rptperiodanalysis.php" method="post"  class="form">
Start:<input type="text" name="sd" id="sd" value="$sd" style="width: 105px;">
End: <input type="text" name="ed" id="ed" value="$ed" style="width: 105px;">
<input type="hidden" name="action" value="generate">
<input type="submit" name="submit" Value="Submit">
</form>

pagePart1;

if ($action == '') {
print <<<firstPage
<h4>Specify Date Period Required</h4>
<p>This analysis reports the number of volunteers that served as well as the total number of hours for each.</p>
<p>Specify the time period for which the analysis is to be done.</p>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>

firstPage;
exit;
}


$sql = "SELECT `voltime`.*, `members`.`FName`, `members`.`LName` 
FROM `voltime`, `members` 
WHERE `voltime`.`MCID` = `members`.`MCID`
	AND `voltime`.`VolDate` BETWEEN '$sd' AND '$ed' 
ORDER BY `VTID` ASC;";

//echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

if ($rowcnt > 0) {
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes
	$mcidcount = array(); $mcidhours = array(); $mcidmileage = array();
	while ($r = $res->fetch_assoc()) {
		//echo '<pre>'; print_r($r); echo '</pre>';
		$vc = $r[VolCategory];
		$mcid = strtoupper($r[MCID]);
		if (strlen($vc) > 0) $cathours[$vc] += $r[VolTime];
		else $cathours[Uncategorized] += $r[VolTime];
		$totalvolhrs += $r[VolTime];
		$mcidcount[$mcid] += 1;
		$mcidhours[$mcid] += $r[VolTime];
		$mcidmileage[$mcid] += $r[VolMileage];
		$mcidname[$mcid] = $r[FName] . ' ' . $r[LName];
		$totalmiles += $r[VolMileage];
		$totalhours += $r[VolTime];
		}
	$totalhours = number_format($totalhours); 
	$totalmiles = number_format($totalmiles);
	ksort($mcidcount);
	ksort($mcidhours);
	ksort($mcidmileage);
	ksort($cathours);
	//echo '<pre>mcid count '; print_r($mcidcount); echo '</pre>';
	//echo '<pre>mcid hours '; print_r($mcidhours); echo '</pre>';
	//echo '<pre>mcid milage '; print_r($mcidmileage); echo '</pre>';
	//echo '<pre>category hours '; print_r($cathours); echo '</pre>';
	echo "<b>Volunteers Served: </b>" . count($mcidcount) . ",&nbsp;";
	echo "<b>Total Volunteer Hours:</b> $totalhours,&nbsp;";
	echo "<b>Total Miles Driven:</b> $totalmiles<br />";	
	echo "<b>Total Hours by Category:</b><br />";
	echo '<div class="row">';
	foreach ($cathours as $k => $v) { 
		$v = number_format($v);
		echo "<div class=\"col-sm-2\">$k: $v</div>";
		}
	echo	"</div><br />";		// ** row
	echo '<table class="table table-condensed">';
	echo '<tr><th>MCID</th><th>Vol Name</th><th>Serv.Cnt<th>Time</th><th>Mileage</th></tr>';

	foreach ($mcidcount as $m=>$v) { 
		echo "<tr><td>$m</td><td>$mcidname[$m]</td><td>$v</td><td>$mcidhours[$m]</td><td>$mcidmileage[$m]</td></tr>"; 
		}
	echo '</table>';	
	}
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
