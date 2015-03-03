<!DOCTYPE html>
<html>
<head>
<title>List/Edit Course Record</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onLoad="initForm(this)" onchange="flagChange()">
<?php
session_start();
// include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

// time entry is an admin function
if ($_SESSION['SecLevel'] != 'voladmin') {
		echo '<div class="container"><h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to perform this function.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded to &apos;voladmin&apos; in order to perform this function.</p><br />
		</div>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
		</body></html>';
		exit;
		}

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime('-1 month'));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime(now));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$recno = isset($_REQUEST['VCID']) ? $_REQUEST['VCID'] : 0;

//if ($sd == '') $sd = date('Y-m-d', strtotime('-30 days'));
//if ($ed == '') $ed = date('Y-m-d', strtotime(now));

if (($action == 'delete') AND ($recno > 0)) {
	$fsql = "DELETE FROM `volcourses` WHERE `VCID` = '".$recno."';";
	$fdelcount = doSQLsubmitted($fsql);
	echo "Volunteer course entry record $recno deleted<br>";
	$action = '';
	}

if ($action == 'update') {
	//echo 'update request received<br />';
	$uri = $_SERVER['QUERY_STRING'];
	parse_str($uri, $vararray);
	$where = "`VCID`='$vararray[VCID]'";
//	echo '<pre> Update parameters '; print_r($vararray); echo '</pre>';

// unset array val's that we are not updating
	$courseid = $vararray[Agency] . ':' . $vararray[CourseId];
	unset($vararray[Agency]);
	$vararray[CourseId] = $courseid;
	unset($vararray[VCID]);
	unset($vararray[MCID]);
	unset($vararray[action]);
	unset($vararray[submit]);
//	echo '<pre> Update parameters '; print_r($vararray); echo '</pre>';

	sqlupdate('volcourses',$vararray, $where);	  // now apply changes

	$action = '';
	}
	
echo '<div class="container"><h3>Edit Course Record</h3>';
if (($action == '') OR ($recno == 0)) {
echo '<p>Select a specific record to edit by clicking its corresponding record number.</p>';

print <<<pagePart1
<form action="edlisteditrecord.php" method="post" class="form">
Start:<input autofocus type="text" name="sd" value="$sd" onchange="ValidateDate(this)" placeholder="YYYY-MM-DD" size="12" maxlength="12" style="width: 105px;">
End: <input type="text" name="ed" value="$ed" onchange="ValidateDate(this)" placeholder="YYYY-MM-DD" size="12" maxlength="12" style="width: 105px;">
<!-- Rec Nbr:&nbsp;<input type="text" style="width: 50px;" name=recno value=''> -->
<input type="hidden" name="action" value="updform">
<input type="submit" name="submit" Value="Submit">
</form>

pagePart1;

$sql = "SELECT * FROM `volcourses` where (`CourseDate` >= '$sd' AND `CourseDate` <= '$ed')
ORDER BY `CourseDate` DESC, `CourseId` ASC;";
	$res = doSQLsubmitted($sql);
	echo '<table class="table-condensed">
	<tr><th>RecNbr</th><th>MCID</th><th>CourseDate</th><th>Agency</th><th>CourseId</th><th>Duration</th><th>Notes</th></tr>';
	$rowcnt = $res->num_rows;
	echo "Records found in date range: $rowcnt<br />";
	while ($r = $res->fetch_assoc()) {
		list($agency, $courseid) = explode(':',$r[CourseId]);
//		echo '<pre>course '; print_r($r); echo '</pre>';
		$rcdlink = "<a href=\"edlisteditrecord.php?action=updform&VCID=$r[VCID]\">$r[VCID]</a>";
		echo "<tr><td>$rcdlink</td>
		<td>$r[MCID]</td>
		<td>$r[CourseDate]</td>
		<td>$agency</td>
		<td>$courseid</td>
		<td>$r[CourseDuration]</td>
		<td>$r[CourseNotes]</td></tr>";
		}
	echo '</table>----- END OF REPORT -----
<script src="Incls/datevalidation.js"></script>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>';

exit;
}

if ($action == 'updform') {
	if ($recno > 0) {
		$sql = "SELECT * FROM `volcourses` where `VCID` = '$recno'";
		$res = doSQLsubmitted($sql);
		$r = $res->fetch_assoc();
		list($agency, $courseid) = explode(':',$r[CourseId]);
		print <<<inForm1
		<script>
function initForm(theDoc) {
	initAllFields(theDoc.VTForm);
	return true;
	}
function initAllFields(form) {
// Initialize all form controls
  with (form) {
		initSelect(VolCategory,"$r[CourseDescription]");
  	}
	}
function initSelect(control,value) {
// Initialize a selection list (single valued)
	if (value == "") return;
	for (var i = 0; i < control.length; i++) {
		if (control.options[i].value == value) {
			control.options[i].selected = true;
			break;
			}
		}
	}
</script>
<script>
function confirmdelete() {
	var r=confirm("This record will be premanently deleted.\\n\\nConfirm this action by clicking OK.");	
	if (r == true) { return true; }
	return false;
	}
</script>

<h5>Enter new values and submit</h5>
<form name="VCForm" id="VTForm" action="edlisteditrecord.php" method="get">
<h4>Record Number: $r[VCID]</h4> 
<b>MCID: $r[MCID]</b><br />
<input type="hidden" name="VCID" value="$r[VCID]">
<input type="hidden" name="MCID" value="$r[MCID]">
Date: <input onchange="ValidateDate(this)" type="text" name="CourseDate" value="$r[CourseDate]" style="width: 105px;">
<br>Agency: <input type="text" name="Agency" maxsize=8 value="$agency">
<br>Course Id: <input type="text" name="CourseId" maxsize=30 value="$courseid">
<br>Duration: <input type="text" name="CourseDuration" value="$r[CourseDuration]" style="width: 105px;">
<br>Notes: <input type="text" name="CourseNotes" value="$r[CourseNotes]"><br /><br />
<input type="hidden" name="action" value="update">
<br><input type="submit" name="submit" value="Submit">
</form><br />
<a class="btn btn-primary btn-xs" href="edlisteditrecord.php">CANCEL UPDATE</a><br /><br />
<a class="btn btn-danger btn-xs" href="edlisteditrecord.php?action=delete&VCID=$recno" onclick="return confirmdelete()">DELETE RECORD</a>
inForm1;
		}
	}


?>
</div>  <!-- container -->
<script src="Incls/datevalidation.js"></script>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
