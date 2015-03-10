<!DOCTYPE html>
<html>
<head>
<title>Courses Conducted</title>
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

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-01-01', strtotime("now"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime('now'));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {
	echo '<div class="container"><h3>Courses Conducted In Date Range&nbsp;&nbsp;<a class="btn btn-default" href="javascript:self.close();">CLOSE</a></h3>';

	echo '<h4>Courses conducted by all agencies within the date range specified</h4>';
print <<<pagePart1
<form action="rptcoursesinrange.php" method="post"  class="form">
Start:<input type="text" name="sd" id="sd" value="$sd" style="width: 105px;">
End: <input type="text" name="ed" id="ed" value="$ed" style="width: 105px;">
<input type="hidden" name="action" value="list">
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

if ($action == 'list') {
echo '<h3>Courses in Date Range&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>';
echo "Start Date: $sd, End Date: $ed<br>";
$sql = "SELECT * from `voltime` 
WHERE `VolCategory` = 'Education' 
	AND `VolDate` BETWEEN '$sd' AND '$ed' 
ORDER BY `VTID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt > 0) {
// table volcourses: 
	$edarray = array(); 
	while ($r = $res->fetch_assoc()) {
//		echo '<pre>'; print_r($r); echo '</pre>';
		list($courseid,$notes) = explode('/',$r[VolNotes]);
		list($agency, $cid) = explode(':', $courseid);
		$edarray[$agency][$cid][$r[VolDate]][count] += 1;
				$edarray[$agency][$cid][$r[VolDate]][hours] += $r[VolTime];
		}
//	echo '<pre> edarray '; print_r($edarray); echo '</pre>';
	foreach ($edarray as $k => $v) { 
	echo "Agency: $k<ul>";
		foreach ($v as $kk => $vv) {
			foreach ($vv as $kkk => $vvv) {
				$id = $k . ':' . $kk . ':' . $kkk;
				echo "Course ID: <a href=\"rptcoursesinrange.php?action=attendees&course=$id\">";
				echo "$kk, Date: $kkk</a>, Attendees: $vvv[count], Ed Hours: $vvv[hours]<br>";
				}
			}
		echo '</ul><br>';
		}
	echo "<h4>NOTE: click course id to get attendee list</h4>";
	}
}

if ($action == 'attendees') {
	$course = $_REQUEST['course'];
	list($agency, $courseid, $coursedate) = explode(':', $course);
//	echo "create attendee list for course $agency, $courseid, $coursedate<br>";
	$sql = "SELECT * FROM `voltime` 
		WHERE `VolNotes` LIKE '%$agency%' 
		AND `VolNotes` LIKE '%$courseid%' 
		AND `VolDate` = '$coursedate'";
	$res = doSQLsubmitted($sql);
	echo "<div class=\"container\"><h3>Attendee List&nbsp;&nbsp;<a class=\"btn btn-primary\" href=\"javascript:self.close();\">CLOSE</a></h3>";
	echo "<h4>Agency: $agency<br>Course: $courseid<br>Date: $coursedate</h4><ul>";
	while ($r = $res->fetch_assoc()) {
//		echo '<pre>'; print_r($r); echo '</pre>';
		
		$alsql = "SELECT `LName`,`FName` FROM `members` WHERE `MCID` = '$r[MCID]';";
		$alres = doSQLsubmitted($alsql);
		$memrec = $alres->fetch_assoc();
		echo $r[MCID] . '&nbsp;-&nbsp;' . $memrec[FName] . '&nbsp;' . $memrec[LName] . '<br>';
		}	
	echo '</ul>===== END OF LIST =====</div><br><br>';
	}


echo '<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>';
?>
