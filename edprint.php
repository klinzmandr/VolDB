<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>BBoard Print</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/seccheck.inc.php';
echo '<div class="hidden-print">';
include 'Incls/mainmenu.inc.php';
echo '</div>';

$action = isset($_REQUEST['action'])? $_REQUEST['action'] : "";
$seqnbr = isset($_REQUEST['CID'])? $_REQUEST['CID'] : ""; 

$sql = "SELECT * FROM `courses` WHERE `CID` = '$seqnbr';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();

print <<<pagePart1
<div class="container">
<h3>Education Course $seqnbr</h3>
<table class="table">
<tr><td><h4>$r[CourseId]</h4></td></tr>
<tr><td><b>Agency:</b>&nbsp;$r[Agency]</td></td>
<tr><td><b>Course Name:</b><br>$r[CourseName]</td>
<td><b>Course Duration (Hours):</b><br>$r[CourseDuration]</td></tr>
<tr><td><b>Course Description:</b><br>$r[CourseDescription]</td></tr>
</table>
<br>

<!-- <div class="hidden-print">
<a class="btn btn-success" href="edcourses.php">RETURN</a>
</div> -->

</div>  <!-- container -->
pagePart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
