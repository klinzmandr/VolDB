<!DOCTYPE html>
<html>
<head>
<title>Volunteer Service Detail</title>
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
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime('now'));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container"><h3>Volunteer Service Detail&nbsp;&nbsp;<a class="btn btn-default" href="javascript:self.close();">CLOSE</a></h3>';

if ($action == '') {
	echo '<h4>Specify Date Period Required</h4>
	<p>This report produces all the detail enteries for volunteer time reported in the date range specified.</p>
	<p>This information is best suited for downloading into a spreadsheet for summarization and charting.</p>';
print <<<pagePart1
<form action="rptperioddetail.php" method="post"  class="form">
Start:<input type="text" name="sd" id="sd" value="$sd" style="width: 105px;">
End: <input type="text" name="ed" id="ed" value="$ed" style="width: 105px;">
<input type="hidden" name="action" value="generate">
<input type="submit" name="submit" Value="Submit">
</form>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>

pagePart1;
exit;
	}
//echo '<h3>Volunteer Service Analysis&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>';
$sql = "SELECT * from `voltime` 
WHERE `VolDate` BETWEEN '$sd' AND '$ed' 
ORDER BY `VTID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt > 0) {
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes
	$voldet = array(); $csv = array();
	$csv[] = "MCID;SvcDate;SvcTime;Mileage;Category;Notes\n";
	while ($r = $res->fetch_assoc()) {
		//echo '<pre>'; print_r($r); echo '</pre>';
		$voldet[] = "<tr><td>$r[MCID]</td><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td></tr>";
		$csv[] = "\"$r[MCID]\";$r[VolDate];$r[VolTime];$r[VolMileage];$r[VolCategory];\"$r[VolNotes]\"\n";
		}
	//print_r($csv);
	file_put_contents('downloads/VolServiceDetail.csv',$csv);
	//echo '<pre>mcid count '; print_r($mcidcount); echo '</pre>';
	//echo '<pre>mcid hours '; print_r($mcidhours); echo '</pre>';
	//echo '<pre>mcid milage '; print_r($mcidmileage); echo '</pre>';
	//echo '<pre>category hours '; print_r($cathours); echo '</pre>';
	echo "<h4>Period from $sd to $ed</h4>Rows extracted: $rowcnt<br>";
	echo '<table class="table-condensed" border="0">';
	echo "<a href=\"downloads/VolServiceDetail.csv\" download=\"VolServiceDetail.csv\">DOWNLOAD CSV FILE</a>";
	echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";

	echo '<tr><th>MCID</th><th>Date</th><th>Time</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>';
	foreach ($voldet as $k => $v) { 
		echo "$v";
		}
	echo	"</table>--- End of Report ---";		// ** row
	echo '<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>';
	}
?>
