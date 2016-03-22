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
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>

<?php
session_start();
// include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-01-01', strtotime("now"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime('now'));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	echo '<div class="container"><h2>Vol. Education In Date Range&nbsp;&nbsp;<a  class="btn btn-default" href="javascript:self.close();">CLOSE</a></h2>';
	
if ($action == '') {
	echo '<h3>Volunteer Education within the date range specified</h3>';
print <<<pagePart1
<form action="rptvolcoursesinrange.php" method="post"  class="form">
Start:<input type="text" name="sd" id="sd" value="$sd" style="width: 105px;">
End: <input type="text" name="ed" id="ed" value="$ed" style="width: 105px;">
<input type="hidden" name="action" value="list">
<input type="submit" name="submit" Value="Submit">
</form>
</body>
</html>

pagePart1;
exit;
	}

if ($action == 'list') {
echo "Start Date: $sd, End Date: $ed&nbsp;&nbsp;<button id=\"btnHS\">Hide/Show Details</button><br>";
$sql = "SELECT `voltime`.*, `members`.`FName`, `members`.`LName` from `voltime`, `members` 
WHERE `voltime`.`MCID` = `members`.`MCID`
  AND `voltime`.`VolCategory` = 'Education' 
	AND `voltime`.`VolDate` BETWEEN '$sd' AND '$ed' 
ORDER BY `voltime`.`VTID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt > 0) {
// table volcourses: 
	$edarray = array(); $mcidarray = array();
	while ($r = $res->fetch_assoc()) {
//		echo '<pre>'; print_r($r); echo '</pre>';
		list($courseid,$notes) = explode('/',$r[VolNotes]);
		list($agency, $cid) = explode(':', $courseid);
		$mcid = $r[MCID];
		$totchours += $r[VolTime];
		$edarray[$agency][$cid][$r[VolDate]][count] += 1;
		$edarray[$agency][$cid][$r[VolDate]][hours] += $r[VolTime];
		$mcidarray[$mcid][$cid][count] += 1;
		$mcidarray[$mcid][$cid][hours] += $r[VolTime];
		$mcidtot[$mcid][count] += 1;
		$mcidtot[$mcid][tothrs] += $r[VolTime];
		$mcidnames[$mcid][name] = $r[FName] . ' ' . $r[LName];
		}
	//echo '<pre> mcidarray '; print_r($mcidarray); echo '</pre>';
  }
ksort($mcidarray);
$vcnt = count($mcidarray); 
print <<<formPart
<h4>Total volunteers: $vcnt, Total education hours: $totchours</h4>
<script>
$(function(){
   $('#btnHS').click(function() {
    $('.cr').toggle();
   });
});
</script>

formPart;

echo '<table border="0" width="50%">';
echo '<tr><th>MCID</th><th>Volunteer Name<span class="cr"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Course Name</span></th><th>Courses</th><th align="center">Hrs</th></tr>';
foreach ($mcidarray as $k => $v) {
  $vcnt = $mcidtot[$k][count]; $vhrs = $mcidtot[$k][tothrs]; $name= $mcidnames[$k][name];
  echo "<tr><td width=\"10%\">$k</td><td width=\"60%\">$name</td><td width=\"10%\">$vcnt</td><td width=\"10%\">$vhrs</td></tr>";
  foreach ($v as $kk => $vv) {
    $ccnt = $mcidarray[$k][$kk][count]; $chrs = $mcidarray[$k][$kk][hours]; 
    echo "<tr class=\"cr\"><td colspan=1></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$kk</td><td align=\"right\">$ccnt</td><td align=\"right\">$chrs</td></tr>";
    }
  }
echo '</table></div>';  
}  

?>
</body>
</html>';
