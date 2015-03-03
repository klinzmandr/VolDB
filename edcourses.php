<!DOCTYPE html>
<html>
<head>
<title>Education Maintenance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';
include 'Incls/seccheck.inc';
echo '<div class="hidden-print">';
include 'Incls/mainmenu.inc';
echo '</div>
<div class="container">';

if ($_SESSION['SecLevel'] == 'voladmin') {
echo '<h3>Education Courses&nbsp;&nbsp;<a href="edupdate.php?action=addnew"><span title="Add New Course" class="glyphicon glyphicon-plus" style="color: blue; font-size: 20px"></span></a></h3>';
}
else {
	echo '<h3>Education Courses</h3>';
	}
print <<<pagePart1
<script>
function confirmContinue() {
	var r=confirm("This action cannot be reversed.\\n\\nConfirm this action by clicking OK or CANCEL"); 
	if (r==true) { return true; }
	return false;
	}
</script>

pagePart1;
$action = isset($_REQUEST['action'])? $_REQUEST['action'] : "";
$seqnbr = isset($_REQUEST['CID'])? $_REQUEST['CID'] : ""; 

if ($action == 'delete') {
//	echo "delete $seqnbr requested<br>";
	$sql = "DELETE FROM `courses` WHERE `CID` = '$seqnbr';";
	$rc = doSQLsubmitted($sql);		// returns affected_rows for delete
	if ($rc > 0) 
		echo "Deletion of course record $seqnbr successful<br>";
	else
		echo "Error on delete of note $seqnbr<br>";
	}

$sql = "SELECT * FROM `courses` WHERE '1' ORDER BY `CourseId` ASC;";
$res = doSQLsubmitted($sql);

echo '<table border=1 class="table-condensed">';
while ($r = $res->fetch_assoc()) {
//	echo '<pre> bboard '; print_r($r); echo '</pre>';
	if ($r[CourseName] == '**NewRec**') $r[CourseName] = '';;
	
//echo "<pre> course outline:->$r[CourseOutline]<-</pre><br>";
	echo "<tr><td colspan=4><b>Course ID:</b>&nbsp;<span style=\"font-size: larger;\">$r[CourseId]</span></td></tr>";
	echo "<tr><td>Course Nbr: $r[CID]</td><td>Duration: $r[CourseDuration] Hours</td><td>";

	if (($_SESSION['SessionUser'] == $r[UserID]) || ($_SESSION['SecLevel'] == 'voladmin'))
		echo "<a href=\"edupdate.php?CID=$r[CID]&action=update\"<span title=\"Update Course\" class=\"glyphicon glyphicon-pencil\" style=\"color: blue; font-size: 20px\"></span></a>&nbsp;&nbsp;&nbsp;";
	if (($_SESSION['SessionUser'] == $r[UserID]) || ($_SESSION['SecLevel'] == 'voladmin'))
		echo "<a onclick=\"return confirmContinue()\" href=\"edcourses.php?CID=$r[CID]&action=delete\"<span title=\"Delete Course\" class=\"glyphicon glyphicon-trash\" style=\"color: blue; font-size: 20px\"></span></a>&nbsp;&nbsp;&nbsp;";	
	echo "<a href=\"edprint.php?CID=$r[CID]&action=print\"<span title=\"Print Course\" class=\"glyphicon glyphicon-print\" style=\"color: blue; font-size: 20px\"></span></a>";
	echo '</td></tr>';

	echo "<tr><td><b>Agency</b>:&nbsp;$r[Agency]</td>";
	echo "<td colspan=2><b>Course Name:</b>&nbsp;$r[CourseName]</td></tr>";
	echo "<tr><td colspan=3><b>Course Description:</b>&nbsp;$r[CourseDescription]</td></tr>";

	echo "<tr><td colspan=3 align=\"center\">=================================</td><tr>";
	}

?>
</table></div>  <!-- container -->

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
