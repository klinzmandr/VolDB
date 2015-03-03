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

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("previous month"));
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
$sql = "SELECT * from `volcourses` WHERE `CourseDate` BETWEEN '$sd' AND '$ed' ORDER BY `VCID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt > 0) {
// table volcourses: 
	$edarray = array(); 
	while ($r = $res->fetch_assoc()) {
//		echo '<pre>'; print_r($r); echo '</pre>';
		$edarray[$r[Agency]][$r[CourseId]][$r[CourseDate]][count] += 1;
		}
//	echo '<pre> edarray '; print_r($edarray); echo '</pre>';
	foreach ($edarray as $k => $v) { 
	echo "Agency: $k<ul>";
		foreach ($v as $kk => $vv) {
			foreach ($vv as $kkk => $vvv) {
				$id = $k . ':' . $kk . ':' . $kkk;
				echo "<a href=\"rptcoursesinrange.php?action=attendees&course=$id\">";
				echo "Course ID; $kk, Date: $kkk, Attendees: $vvv[count]</a><br>";
				}
			}
		echo '</ul><br>';
		}
	echo "<h4>NOTE: click course listing to get attendee list</h4>";
	}
}

if ($action == 'attendees') {
	$course = $_REQUEST['course'];
	list($agency, $courseid, $coursedate) = explode(':', $course);
//	echo "create attendee list for course $agency, $courseid, $coursedate<br>";
	$sql = "SELECT * FROM `volcourses` 
		WHERE `Agency` = '$agency' 
		AND `CourseId` = '$courseid' 
		AND `CourseDate` = '$coursedate'";
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
