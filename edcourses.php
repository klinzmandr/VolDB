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
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/seccheck.inc.php';
echo '<div class="hidden-print">';
include 'Incls/mainmenu.inc.php';
echo '</div>
<div class="container">';

if ($_SESSION['VolSecLevel'] == 'voladmin') {
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

// delete course record
if ($action == 'delete') {
//	echo "delete $seqnbr requested<br>";
	$sql = "DELETE FROM `courses` WHERE `CID` = '$seqnbr';";
	$rc = doSQLsubmitted($sql);		// returns affected_rows for delete
	if ($rc > 0) 
		echo "Deletion of course record $seqnbr successful<br>";
	else
		echo "Error on delete of note $seqnbr<br>";
	}

// produce course listing
$sql = "SELECT * FROM `courses` WHERE '1' ORDER BY `Agency` ASC, `CourseId` ASC;";
$res = doSQLsubmitted($sql);

echo '<table border=0 class="table-condensed">';
if (($_SESSION['VolSessionUser'] == $r[UserID]) || ($_SESSION['VolSecLevel'] == 'voladmin'))
	echo '<tr><th>Edit</th><th>Delete</th><th>Print</th><th>Agency</th><th>CourseId</th><th>Course Full Name</th></tr>';
else	echo '<tr><th>Print</th><th>Agency</th><th>CourseId</th></tr>'; 		
while ($r = $res->fetch_assoc()) {
//	echo '<pre> bboard '; print_r($r); echo '</pre>';
	if ($r[CourseName] == '**NewRec**') $r[CourseName] = '';
//	$cid = $r[Agency] . ':' . $r[CourseId];
	echo '<tr>';
	if (($_SESSION['VolSessionUser'] == $r[UserID]) || ($_SESSION['VolSecLevel'] == 'voladmin'))
		echo "<td><a href=\"edupdate.php?CID=$r[CID]&action=update\"<span title=\"Edit Course\" class=\"glyphicon glyphicon-pencil\" style=\"color: blue; font-size: 20px\"></span></a>&nbsp;&nbsp;&nbsp;</td>";
	if (($_SESSION['VolSessionUser'] == $r[UserID]) || ($_SESSION['VolSecLevel'] == 'voladmin'))
		echo "<td><a onclick=\"return confirmContinue()\" href=\"edcourses.php?CID=$r[CID]&action=delete\"<span title=\"Delete Course\" class=\"glyphicon glyphicon-trash\" style=\"color: blue; font-size: 20px\"></span></a>&nbsp;&nbsp;&nbsp;</td>";	
	echo "<td><a href=\"edprint.php?CID=$r[CID]&action=print\"<span title=\"Print Course\" class=\"glyphicon glyphicon-print\" style=\"color: blue; font-size: 20px\"></span></a></td>";
	echo "<td>$r[Agency]</td><td>$r[CourseId]</td><td>$r[CourseName]";
//	echo "<td>Course Nbr: $r[CID]</td><td>";
//	echo "<tr><td>Course Nbr: $r[CID]</td><td>Duration: $r[CourseDuration] Hours</td><td>";


	echo '</td></tr>';

//	echo "<tr><td><b>Agency</b>:&nbsp;$r[Agency]</td>";
//	echo "<td colspan=2><b>Course Name:</b>&nbsp;$r[CourseName]</td></tr>";
//	echo "<tr><td colspan=3><b>Course Description:</b>&nbsp;$r[CourseDescription]</td></tr>";

//	echo "<tr><td colspan=3 align=\"center\">=================================</td><tr>";
	}

?>
</table></div>  <!-- container -->

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
