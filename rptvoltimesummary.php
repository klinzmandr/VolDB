<?php
function prepaccum($nbr) {
	$a = array();
	$clist = readdblist('VolCategorys');
	$clarray = formatdbrec($clist);
	$clarray[Education] = 'Education';
//	echo '<pre> catlist '; print_r($clarray); echo '</pre>';
	ksort($clarray);
//	echo "year count: $nbr<br>";
	for ($i = 0; $i < $nbr; $i++) {
		foreach ($clarray as $k => $v) {	
			$k = rtrim($k);
			if ($k == '') continue;
			$yrkey = 2014 + $i;
			for ($mo = 1; $mo < 13; $mo++) {
				$a[$yrkey][$k][$mo][count] = 0;
				$a[$yrkey][$k][$mo][hours] = 0;
				$a[$yrkey][$k][$mo][avg] = 0;
				$a[$yrkey][$k][$mo][mcids] = 0;
			}
		}
	}
	return($a);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Volunteer Time Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
//include 'Incls/vardump.inc.php';
//include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/letter_print_css.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

print <<<formPart
<div class="container">
<h3>Volunteer Service Summary&nbsp;&nbsp;<a class="hidden-print btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>
<script>
function chksel() {
//	var v = document.getElementById("selid");
	var v = $("#selid").val();
	if (v == "") {
		alert("No report type has been selected.");
		return false;
	}
return true;
}
</script>
<form class="hidden-print" action="rptvoltimesummary.php" onsubmit="return chksel()">
<select name="type" id="selid" onchange="this.form.submit()">
<option value=""></option>
<option value="0">Total volunteer hours in month</option>
<option value="1">Service record count in month</option>
<option value="2">Vol&apos;s served in month</option>
<option value="3">Average Time Served</option>
<option value=""></option>
</select>&nbsp;&nbsp;
<input type="hidden" name="action" value="go">
<!-- <input type="submit" name="submit" value="submit"> -->
</form>

formPart;
if ($action == '') {
print <<<pagePart1
<p>This report agregates all volunteer time records entries recorded on the database. The report counts and totals grouping by year, volunteer category and month</p>
<p>Select from the following list to create the appropriate summary report.</p>

<h4>Report Types</h4>
<p>In general, reports utilize all time records available on the volunteer database.  The reports produced will always reflect information for the entire history of the time data entry system up to the last entries made.  Also, please note that the yearly tables are listed in reverse chronological order.</p>
<p><b>Total Volunteer Hours</b></p>
<p>This report summarizes all the time entries recorded on the volunteer database and totals all entries by adding up the total hours grouping them by month within a specific year.</p>
<p><b>Service Record Count</b></p>
<p>This report counts all the time entries recorded on the volunteer database and reports these counts by month within a specific year.</p>
<p><b>Volunteers Served</b></p>
<p>This report determines the number of different volunteers that reported service time and summaries these counts by month within a specific year.</p>
<p><b>Average Time Served</b></p>
<p>This report calculates an average length of service time for all time entries by totaling the hours in each month and dividing by number of service entries.  The average of these averages is provided for each month as well.</p>
</div>

pagePart1;
exit;
}

$sql = "SELECT * FROM `voltime` WHERE 1 ORDER BY `VolDate`;";
$res = doSQLsubmitted($sql);
$resarray = array(); $yrarray = array();
while ($r = $res -> fetch_assoc()) {
	$resarray[$r[VTID]] = $r;
	if ($r[VolDate] == '0000-00-00') continue;
	if ($r[VolDate] == '') continue;
	$yr = date('Y', strtotime($r[VolDate]));
	$yrarray[$yr] += 1; 
}
echo '<pre> yrarray '; print_r($yrarray); echo('</pre>');
$yrcount = count($yrarray);
$accum = prepaccum($yrcount);

//echo '<pre> accum '; print_r($accum); echo '</pre>';

$mcidcatcher = array(); $motot = array(); $mcidmoarray = array();
foreach ($resarray as $r) {
//	echo '<pre> data '; print_r($r); echo '</pre>';
	if ($r[VolDate] == '0000-00-00') continue;
	if ($r[VolDate] == '') continue;
	$yr = date('Y', strtotime($r[VolDate]));
	$mo = date('n', strtotime($r[VolDate]));
	$yrmo = date('Y-m', strtotime($r[VolDate]));
	$vt = $r[VolTime];
	$cat = $r[VolCategory];
	$mcid = $r[MCID];
	if ($cat == '') continue;
//	echo "year: $yr, cateory: $cat, month: $mo, voltime: $vt<br>";
//	$accum[$yr][$mo][$cat][count] += 1;
//	$accum[$yr][$mo][$cat][hours] += $vt;
	$accum[$yr][$cat][$mo][count] += 1;
	$accum[$yr][$cat][$mo][hours] += $vt;
	$avg = $accum[$yr][$cat][$mo][hours] / $accum[$yr][$cat][$mo][count];
	$accum[$yr][$cat][$mo][avg] = number_format($avg, 2);
	$motot[$yr][$mo][count] += 1;
	$motot[$yr][$mo][tothrs] += $vt;
	$motot[$yr][$mo][totavg] = 
    number_format($motot[$yr][$mo][tothrs] / $motot[$yr][$mo][count],2);

	$mcidmokey = $yr . $mo . $mcid;    // unique mcid per month
	if (!in_array($mcidmokey, $mcidmoarray)) {
	   $motot[$yr][$mo][mcidcount] += 1;
	   $mcidmoarray[] = $mcidmokey;
	   }
	
	$mcidkey = $yr . $cat . $mo . $mcid; 
	if (!array_key_exists($mcidkey, $mcidcatcher)) {
		$mcidcatcher[$mcidkey] += 1;
		$accum[$yr][$cat][$mo][mcids] += 1;
		$uniquemcids += 1;
//		$motot[$yr][$mo][mcidcount] += 1;
		$mcidcount[$mcid] += 1; 
	}
}

//echo '<pre> mcidcatcher '; print_r($mcidcatcher); echo '</pre>';
//echo '<pre> motot-2017 '; print_r($motot[2017]); echo '</pre>';
//echo '<pre> accum-2017-CtrVol '; print_r($accum[2017][CtrVol]); echo '</pre>';
// echo "type: $type<br>";
if ($type == 0) {
	echo '<h4>Volunteer Hours Served</h4>'; }
elseif ($type == 1) {
	echo '<h4>Volunteer Entries</h4>'; }
elseif ($type == 2) {
	echo '<h4>Different Volunteer&apos;s That Have Served: '. count($mcidcount) .'</h4>'; }
elseif ($type == 3) {
	echo '<h4>Average Time Served</h4>'; }
	
ksort($mcidcatcher);
// echo '<pre> mcidcatcher '; print_r($mcidcatcher); echo '</pre>';
echo '<pre> Mo Totals '; print_r($motot); echo '</pre>';

// now unravel the accumulator array
krsort($accum);
foreach ($accum as $yr => $val) {
	echo '<table class="table-condensed" border=1>
<tr><td>YEAR: ' . $yr . '</td><td width="50" align="right"><b>Jan</b></td><td width="50" align="right"><b>Feb</b></td><td width="50" align="right"><b>Mar</b></td><td width="50" align="right"><b>Apr</b></td><td width="50" align="right"><b>May</b></td><td width="50" align="right"><b>Jun</b></td><td width="50" align="right"><b>Jul</b></td><td width="50" align="right"><b>Aug</b></td><td width="50" align="right"><b>Sep</b></td><td width="50" align="right"><b>Oct</b></td><td width="50" align="right"><b>Nov</b></td><td width="50" align="right"><b>Dec</b></td></tr>';
//echo "year: $yr<br>";
//echo '<pre> val '; print_r($val); echo '</pre>';
	foreach ($val as $k => $v) {
		echo "<tr><td>$k</td>";  // $k is the vol category
		ksort($v);
		foreach($v as $kk => $vv) {			// add  mo totals for each category
			if ($type == 0) $fvv = number_format($vv[hours]);
			elseif ($type == 1) $fvv = number_format($vv[count]);
			elseif ($type == 2) $fvv = number_format($vv[mcids]);
			elseif ($type == 3) $fvv = number_format($vv[avg],2);
			echo "<td align=\"right\">$fvv</td>";
		}
	}
	echo '</tr><tr><td>Mo Total:</td>';		// add monthly totals at bottom
//	echo "yr: $yr<br>";
//	echo '<pre> motot '; print_r($motot); echo '</pre>';
	foreach ($motot[$yr] as $k => $v) {
		if ($type == 0) $fv = number_format($v[tothrs]);
		elseif ($type == 1) $fv = number_format($v[count]);
		elseif ($type == 2)	$fv = number_format($v[mcidcount]);
		elseif ($type == 3) $fv = number_format($v[totavg],2);
		echo "<td align=\"right\">$fv</td>";
	}
echo '</tr></table><br>';
echo '<div class="page-break"></div>';
}
//echo '<pre> accum '; print_r($accum); echo '</pre>';
echo '<br>==== END OF REPORT ====<br></div>';
?>

</body>
</html>
