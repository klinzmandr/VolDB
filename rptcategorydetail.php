<!DOCTYPE html>
<html>
<head>
<title>Category Service Detail</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
session_start();
// include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("previous month"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime('now'));
$cats = isset($_REQUEST['cats']) ? $_REQUEST['cats'] : '';
$details = isset($_REQUEST['details']) ? 'ON' : 'OFF';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container"><h3>Category Service Detail&nbsp;&nbsp;<a class="btn btn-default btn-xs" href="javascript:self.close();">CLOSE</a></h3>';

if ($action == '') {
	echo '
<script>
function chkcats() {
	var elems = document.getElementsByName("cats[]");
	for (i = 0; i < elems.length; i++) {
		if (elems[i].checked) return true;
		}
	alert ("There were NO categorys selected");
	return false;
	}
</script>
<script>
function chgcb() {
	var cb = document.getElementsByName("ctrlcb");
	var elems = document.getElementsByName("cats[]"); 
	if (cb[0].checked) {
		for (i = 0; i < elems.length; i++) {
			elems[i].checked = true;
		}
	}
	else {
		for (i = 0; i < elems.length; i++) {
			elems[i].checked = false;
		}
	}
	return;
}
</script>';
	echo "
	<p>This report produces all the detail entries for one or more categories selected from the following list within the date range specified.</p>
	<p>Unless details are requested, the report summarizes all volunteer time reported within the date range entered.  If requested, details are listed and are also provided to be downloaded as a CSV spreadsheet file.</p>
<h4>Specify Date Period:</h4><ul>
<form action=\"rptcategorydetail.php\" method=\"post\"  class=\"form\" onsubmit=\"return chkcats()\">
Start:<input type=\"text\" name=\"sd\" id=\"sd\" value=\"$sd\" style=\"width: 105px;\">
End: <input type=\"text\" name=\"ed\" id=\"ed\" value=\"$ed\" style=\"width: 105px;\">
</ul>";

$cats = readdblist('VolCategorys');
$catsarray = formatdbrec($cats);
asort($catsarray);
//echo '<pre> categories '; print_r($catsarray); echo '</pre>';
echo '<h4>Categories:</h4><ul>
<input type="checkbox" name="ctrlcb" checked onchange="chgcb()"><b>Check All/None</b><br>';
foreach ($catsarray as $k => $c) {
	if ($c == '') continue;
	$c = rtrim($c);
	echo "<input type=\"checkbox\" name=\"cats[]\" id=\"cats\" value=\"$k\" checked> $c<br>";
}
echo '</ul><br>
<input type="checkbox" name="details" id="details"> Show details<br>
<input type="hidden" name="action" value="generate">
<input type="submit" name="submit" Value="Submit">
</form>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
';
exit;
	}
//echo '<h3>Volunteer Service Analysis&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>';
//	echo "details flag: " . $details . "<br>";
//	echo '<pre> cats '; print_r($cats); echo '</pre>';

$sql = "SELECT * from `voltime` WHERE `VolDate` BETWEEN '$sd' AND '$ed' ORDER BY `VTID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
	
if ($rowcnt > 0) {
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes
	$voldet = array(); $csv = array(); $counts = array(); $mcidcounts = array();
	$csv[] = "MCID;SvcDate;SvcTime;Mileage;Category;Notes\n";
	while ($r = $res->fetch_assoc()) {
//	echo '<pre>'; print_r($r); echo '</pre>';
		if (in_array($r[VolCategory], $cats)) {
			$tothrs += $r[VolTime];
			$totmiles += $r[VolMileage];
			$counts[$r[VolCategory]][count] += 1;
			$counts[$r[VolCategory]][hours] += $r[VolTime];
			$mcidcounts[$r[MCID]][count] += 1;
			$mcidcounts[$r[MCID]][hours] += $r[VolTime];
			$voldet[] = "<tr><td>$r[MCID]</td><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td></tr>";
			$csv[] = "\"$r[MCID]\";$r[VolDate];$r[VolTime];$r[VolMileage];$r[VolCategory];\"$r[VolNotes]\"\n";
			}
		}
//	echo '<pre> counts '; print_r($counts); echo '</pre>';
//	echo '<pre> MCIDs '; print_r($mcidcounts); echo '</pre>';
//	echo '<pre>mcid count '; print_r($mcidcounts); echo '</pre>';
	//echo '<pre>mcid hours '; print_r($mcidhours); echo '</pre>';
	//echo '<pre>mcid milage '; print_r($mcidmileage); echo '</pre>';
	//echo '<pre>category hours '; print_r($cathours); echo '</pre>';
	echo "<h4>Period from $sd to $ed</h4>
	Rows extracted: ".count($voldet).",&nbsp;";
	echo "Categories reported: " . count($cats) . '<br>';
	echo 'Unique Vol IDs reporting: ' . count($mcidcounts) . ', Total Hrs: ' . $tothrs;
	if ($totmiles > 0) echo ", Total Mileage: $totmiles";
	echo '<br>';
//	echo '<b>Period Category service count and total hours</b><br>';
	echo '<ul><table class="table-condnesed" border=0>';
	
	echo '<tr><td align="center" width="20%"><b>Category</b></td><td width="20%" align="center"><b>SvcCount</b></td><td width="20%" align="center"><b>TotHrs</b></td></tr>';
	foreach ($counts as $k => $v) {
		echo "<tr><td>$k</td><td align=\"right\">$v[count]</td><td align=\"right\">$v[hours]</td></tr>";
		}
	echo '</table></ul>';
/*
	echo '<p><b>Period volunteer service counts and total hours</b></p>
	<table class="table-condensed">';
	echo '<th>VolID</th><th>SvcCount</th><th>TotHrs</th>';
	foreach ($mcidcounts as $k => $v) {
		echo "<tr><td>$k</td><td>$v[count]</td><td>$v[hours]</td></tr>";
		}
	echo '</table>';
*/
	if ($details == 'ON') {
		file_put_contents('downloads/CategoryServiceDetail.csv',$csv);
		echo '<table class="table-condensed" border="0">';
		echo "<a href=\"downloads/VolServiceDetail.csv\" download=\"VolServiceDetail.csv\">DOWNLOAD CSV FILE</a>";
		echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
		echo '<tr><th>MCID</th><th>Date</th><th>Time</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>';
		foreach ($voldet as $k => $v) { 
			echo "$v";
			}
		echo	"</table>";		// row
		}
	}


echo '--- End of Report ---<br>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>';
?>
