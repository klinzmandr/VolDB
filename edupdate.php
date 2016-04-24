<!DOCTYPE html>
<html>
<head>
<title>Course Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body onchange="flagChange()">
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/seccheck.inc.php';
echo '<div class="hidden-print">';
include 'Incls/mainmenu.inc.php';
echo '</div>';

$action = isset($_REQUEST['action'])? $_REQUEST['action'] : "";
$seqnbr = isset($_REQUEST['CID'])? $_REQUEST['CID'] : ""; 

if ($_SESSION['VolSecLevel'] != 'voladmin') {
		echo '<div class="container"><h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to perform this function.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded to &apos;voladmin&apos; in order to perform this function.</p><br />
		</div>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
		</body></html>';
		exit;
		}

if ($action == 'upd') {
	echo "Update action for course $seqnbr completed<br>";
	$coursearray = array();
	//echo 'Update action requested.';
	$uri = $_SERVER['QUERY_STRING'];
	parse_str($uri, $coursearray);
	unset($coursearray[action]);
	unset($coursearray[submit]);
//	echo '<pre> note '; print_r($coursearray); echo '</pre>';
	$where = "`CID`='" . $seqnbr . "'";
//	echo '<pre> sql '; print_r($where); 
//	echo '<br>coursearray ';print_r($coursearray); echo '</pre>';
	sqlupdate('courses',$coursearray, $where);	
	}

if ($action == 'addnew') {
//	echo "add a new course<br>";
	$sql = "SELECT * FROM `courses` WHERE `CourseName` = '**NewRec**';";
	$res = doSQLsubmitted($sql);
	$rc = $res->num_rows;
	if (!$rc) {
//		echo 'No NewRec - Adding new record<br>';
		$newarray[CourseName] = '**NewRec**';
		sqlinsert('courses', $newarray);
		echo "A new course record has been added<br>Please complete the details or delete it.<br>";
		$sql = "SELECT * FROM `courses` WHERE `CourseName` = '**NewRec**';";
		$res = doSQLsubmitted($sql);
		}
	$r = $res->fetch_assoc();
	$seqnbr = $r[CID];
//	echo "seqnbr: $seqnbr<br>";
	}

// read note for updating
$sql = "SELECT * FROM `courses` WHERE `CID` = '$seqnbr';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$seqnbr = $r[CID];
$userid = $_SESSION['VolSessionUser'];
if ($r[CourseName] == '**NewRec**') $r[CourseName] = '';;

//echo '<pre> db '; print_r($r); echo '</pre>';
print <<<pagePart1
<script type="text/javascript" src="nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({fullPanel:true}).panelInstance('area1');
});
</script>
<script>
function chkform(form) {
	var l = form.Agency.value;
	var errmsg = "";
	if (l.length == 0) {
		errmsg += "Agency designation must be supplied\\n";
		}
	form.Agency.value = l.toUpperCase();
	var v = form.CourseId.value
	if (v.length == 0) {
		errmsg += "Course Id must be entered\\n";
		}
	if (errmsg.length > 0) {
		errmsg += "\\nPlease correct noted errors";
		alert(errmsg);
		return false;
		}
	return true;
}
</script>
<div class="container">
<h3>Update Course $seqnbr&nbsp;&nbsp;
<!-- <a class="btn btn-success"  onclick="return chkchg()" href="edcourses.php">CANCEL & RETURN</a> -->
</h3>
<form action="edupdate.php" method="get"  class="form" onsubmit="return chkform(this)">
<input type="submit" name="submit" value="Submit Update(s)">
<br><br>Agency: <input type="text" name="Agency" value="$r[Agency]" size=8 maxlength="8" placeholder="Agency">(Acronym or name: max = 8)
<br>Course Identifier:<br>
<input type="text" name="CourseId" value="$r[CourseId]" size="30" maxlength="30"  placeholder="Course Idenifier">(Unique Course Identifier: max = 30)
<br>Duration: <input type="text" name="CourseDuration" value="$r[CourseDuration]" size="8" placeholder="Duration"> Hours
<br>Course Full Name:<br>
<input type="text" name="CourseName" value="$r[CourseName]" size="80"  placeholder="Course Name">

<br>Course Description/Outline<br>
<!-- <input type="text" name="CourseDescription" value="$r[CourseDescription]" size="200"  placeholder="Course Description"> -->

<textarea name="CourseDescription" rows=10 cols=90 id=area1>
$r[CourseDescription]
</textarea>
<input type="hidden" name="CID" value="$seqnbr">
<input type="hidden" name="action" value="upd">
<br><input type="submit" name="submit" value="Submit Update(s)">
</form
<br>

</div>  <!-- container -->
pagePart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
