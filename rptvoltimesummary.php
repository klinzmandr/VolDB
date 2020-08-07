<?php
function prepaccum($yrkey) {
	$a = array();
	$clist = readdblist('VolCategorys');
	$clarray = formatdbrec($clist);
	$clarray['Education'] = 'Education';
//	echo '<pre> catlist '; print_r($clarray); echo '</pre>';
	ksort($clarray);
//	echo "year count: $nbr<br>";
//	for ($i = 0; $i < $nbr; $i++) {
		foreach ($clarray as $k => $v) {	
			$k = rtrim($k);
			if ($k == '') continue;
			// $yrkey = 2014 + $i;
			for ($mo = 1; $mo < 13; $mo++) {
				$a[$yrkey][$k][$mo]['count'] = 0;
				$a[$yrkey][$k][$mo]['hours'] = 0;
				$a[$yrkey][$k][$mo]['avg'] = 0;
				$a[$yrkey][$k][$mo]['mcids'] = 0;
				$a[$yrkey][$k][$mo]['mileage'] = 0;
			}
		}
//	}
	return($a);
}

//include 'Incls/vardump.inc.php';
//include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$type = isset($_REQUEST['action2']) ? $_REQUEST['action2'] : '';
$volyr = isset($_REQUEST['volyr']) ? $_REQUEST['volyr'] : '2019';
?>

<!DOCTYPE html>
<html>
<head>
<title>Category Service Detail</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<div class="container">
<h3>Volunteer Service Summary&nbsp;&nbsp;<a class="hidden-print btn btn-primary btn-xs" href="javascript:self.close();">CLOSE</a></h3>
<script>
$(function() {
  // set up select list drop downs to last selected
  $("#volyr").val('<?=$volyr?>');
  $("#selid").val('<?=$type?>');
  // set submit on either select list changing
  $("#selid, #volyr").change(function() {
    $("form").submit();
  });
});
</script>
<form class="hidden-print" action="rptvoltimesummary.php">
Year: <select id=volyr name=volyr>
<option value=2014>2014</option>
<option value=2015>2015</option>
<option value=2016>2016</option>
<option value=2017>2017</option>
<option value=2018>2018</option>
<option value=2019 selected>2019</option>
<option value=2020>2020</option>
<option value=2021>2021</option>
</select>&nbsp;&nbsp;
<select name="action2" id="selid">
<option value="" selected>Information Tables Available</option>
<option value="0">Total volunteer hours per month</option>
<option value="1">Service record count per month</option>
<option value="2">Vol&apos;s served per month</option>
<option value="3">Monthly Average Time Served per Vol</option>
<option value="4">Monthly Mileage per month</option>
</select>
<input type="hidden" name="action" value="xx">
<!-- <input type="submit" name="submit" value="submit"> -->
</form>

<?php

if ($action == '') {
  echo '<h4>Select a year and choose the table to view</h4>';
  exit;
}

$volsd = $volyr . '-01-01'; $voled = $volyr . '-12-31';
$sql = "SELECT * FROM `voltime` 
WHERE `VolDate` BETWEEN '$volsd' AND '$voled' 
ORDER BY `VolDate`;";
// echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
$resarray = array(); $yrarray = array();
while ($r = $res -> fetch_assoc()) {
	$resarray[$r['VTID']] = $r;
	if ($r['VolDate'] == '0000-00-00') continue;
	if ($r['VolDate'] == '') continue;
	$yr = date('Y', strtotime($r['VolDate']));
	$yrarray[$yr] += 1; 
}
// echo '<pre>yrarray '; print_r($yrarray); echo('</pre>');
$yrcount = count($yrarray);
$accum = prepaccum($volyr);

// echo '<pre>accum '; print_r($accum); echo '</pre>';

$mcidcatcher = array(); $motot = array(); $yrcat = array();
$grandtotmiles = 0;
foreach ($resarray as $r) {
//	echo '<pre> data '; print_r($r); echo '</pre>';
	if ($r['VolDate'] == '0000-00-00') continue;
	if ($r['VolDate'] == '') continue;
	$yr = date('Y', strtotime($r['VolDate']));
	$mo = date('n', strtotime($r['VolDate']));
	$yrmo = date('Y-m', strtotime($r['VolDate']));
	$vt = $r['VolTime'];
	$cat = $r['VolCategory'];
	$mcid = $r['MCID'];
	$miles = $r['VolMileage'];
	$grandtotmiles += $r['VolMileage'];
	if ($cat == '') continue;
//	echo "year: $yr, cateory: $cat, month: $mo, voltime: $vt<br>";
//	$accum[$yr][$mo][$cat][count] += 1;
//	$accum[$yr][$mo][$cat][hours] += $vt;
  $yrcat[$yr][$cat] += 1;
	$accum[$yr][$cat][$mo]['count'] += 1;
	$accum[$yr][$cat][$mo]['hours'] += $vt;
	$avg = $accum[$yr][$cat][$mo]['hours'] / $accum[$yr][$cat][$mo]['count'];
	$accum[$yr][$cat][$mo]['avg'] = number_format($avg, 2);
	$accum[$yr][$cat][$mo]['miles'] += $miles;
	$motot[$yr][$mo]['count'] += 1;
	$motot[$yr][$mo]['tothrs'] += $vt;
	$motot[$yr][$mo]['totmiles'] += $miles;
	$motot[$yr][$mo]['totavg'] = number_format($motot[$yr][$mo]['tothrs'] / $motot[$yr][$mo]['count'],2);
	
	$mcidkey = $yr . $cat . $mo . $mcid; 
	if (!array_key_exists($mcidkey, $mcidcatcher)) {
		$mcidcatcher[$mcidkey] += 1;
		$accum[$yr][$cat][$mo]['mcids'] += 1;
		$uniquemcids += 1;
		$motot[$yr][$mo]['mcidcount'] += 1;
		$mcidcount[$mcid] += 1; 
	}
}
$grandtotmiles = number_format($grandtotmiles);

// echo '<pre>accum '; print_r($accum); echo '</pre>';

if (count($motot) <= 0) {
  echo "<h3>No time entries for year selected.</h3>";
  exit;
  }
// echo "type: $type<br>";
if ($type == 0) {
	echo '<h4>Volunteer Hours Served</h4>'; }
elseif ($type == 1) {
	echo '<h4>Volunteer Entries</h4>'; }
elseif ($type == 2) {
	echo '<h4>Different Volunteer&apos;s That Have Served: '. count($mcidcount) .'</h4>'; }
elseif ($type == 3) {
	echo '<h4>Average Time Served</h4>'; }
elseif ($type == 4) {
  echo "<h3>Total Miles Driven: $grandtotmiles</h3>"; }
	
ksort($mcidcatcher);
// echo '<pre> mcidcatcher '; print_r($mcidcatcher); echo '</pre>';
// echo '<pre> Mo Totals '; print_r($motot); echo '</pre>';

// now unravel the accumulator array
krsort($accum);
foreach ($accum as $yr => $val) {
	echo '<table class="table-condensed" border=1>
<tr><td>YEAR: ' . $yr . '</td><td width="50" align="right"><b>Jan</b></td><td width="50" align="right"><b>Feb</b></td><td width="50" align="right"><b>Mar</b></td><td width="50" align="right"><b>Apr</b></td><td width="50" align="right"><b>May</b></td><td width="50" align="right"><b>Jun</b></td><td width="50" align="right"><b>Jul</b></td><td width="50" align="right"><b>Aug</b></td><td width="50" align="right"><b>Sep</b></td><td width="50" align="right"><b>Oct</b></td><td width="50" align="right"><b>Nov</b></td><td width="50" align="right"><b>Dec</b></td></tr>';
	foreach ($val as $k => $v) {
	  if ($yrcat[$yr][$k] == 0) continue; 
		echo "<tr><td>$k</td>";   // $k is the vol category
		ksort($v);
		foreach($v as $kk => $vv) {			// add totals 
			if ($type == 0) $fvv = number_format($vv['hours']);
			elseif ($type == 1) $fvv = number_format($vv['count']);
			elseif ($type == 2) $fvv = number_format($vv['mcids']);
			elseif ($type == 3) $fvv = number_format($vv['avg'],2);
			elseif ($type == 4) $fvv = number_format($vv['miles']);
			echo "<td align=\"right\">$fvv</td>";
		}
	}
	echo '</tr><tr><td>Mo Total:</td>';		// add monthly totals at bottom
//	echo "yr: $yr<br>";
//	echo '<pre> motot '; print_r($motot); echo '</pre>';
	foreach ($motot[$yr] as $k => $v) {
		if ($type == 0) $fv = number_format($v['tothrs']);
		elseif ($type == 1) $fv = number_format($v['count']);
		elseif ($type == 2)	$fv = number_format($v['mcidcount']);
		elseif ($type == 3) $fv = number_format($v['totavg'],2);
		elseif ($type == 4) $fv = number_format($v['totmiles']);
		echo "<td align=\"right\">$fv</td>";
	}
echo '</tr></table><br>';
echo '<div class="page-break"></div>';
}
//echo '<pre> accum '; print_r($accum); echo '</pre>';
echo '<br>==== END OF REPORT ====<br></div>';
?>
</body></html>