<!DOCTYPE html>
<html>
<head>
<title>Edit Time Record</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onLoad="initForm(this)" onchange="flagChange()">
<?php
session_start();
// include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

// time entry is an admin function
if ($_SESSION['VolSecLevel'] != 'voladmin') {
		echo '<div class="container"><h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to perform this function.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded to &apos;voladmin&apos; in order to perform this function.</p><br />
		</div>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
		</body></html>';
		exit;
		}

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-d', strtotime('-1 month'));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime(now));
$vcat = isset($_REQUEST['vcat']) ? $_REQUEST['vcat'] : '%';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$recno = isset($_REQUEST['recno']) ? $_REQUEST['recno'] : 0;

//if ($sd == '') $sd = date('Y-m-d', strtotime('-30 days'));
//if ($ed == '') $ed = date('Y-m-d', strtotime(now));

if (($action == 'delete') AND ($recno > 0)) {
	$fsql = "DELETE FROM `voltime` WHERE `VTID` = '".$recno."';";
	$fdelcount = doSQLsubmitted($fsql);
	echo "Volunteer time entry record $recno deleted<br>";
	$action = '';
	}

if ($action == 'update') {
	//echo 'update request received<br />';
	$uri = $_SERVER['QUERY_STRING'];
	parse_str($uri, $vararray);
	$where = "`VTID`='$vararray[VTID]'";
	//echo '<pre> Update parameters '; print_r($vararray); echo '</pre>';
	
	// unset array val's that we are not updating
	unset($vararray[VTID]);
	unset($vararray[MCID]);
	unset($vararray[action]);
	unset($vararray[submit]);
	
	sqlupdate('voltime',$vararray, $where);	  // now apply changes

	$action = '';
	}


echo '<div class="container"><h3>Edit Time Record</h3>';
if (($action == '') OR ($recno == 0)) {
	
	echo '<p>Specify the date range and volunteer category for the time records to list.  Select a specific record to edit by clicking its corresponding record number.</p>';

print <<<pagePart1a
<form action="listedittimerecord.php" method="post" class="form">
Start:<input autofocus type="text" name="sd" value="$sd" onchange="ValidateDate(this)" placeholder="YYYY-MM-DD" size="12" maxlength="12" style="width: 105px;">
End: <input type="text" name="ed" value="$ed" onchange="ValidateDate(this)" placeholder="YYYY-MM-DD" size="12" maxlength="12" style="width: 105px;">
VolCategory: <select name=vcat>
<option value='%'></option>
pagePart1a;
loaddbselect('VolCategorys');
print <<<pagePart1b
<option value='Education'>Education</option>
</select>
<input type="hidden" name="action" value="updform">
<input type="submit" name="submit" Value="Submit">
</form>

pagePart1b;

	$sql = "SELECT * FROM `voltime` 
		WHERE (`VolDate` >= '$sd' AND `VolDate` <= '$ed')
			AND `VolCategory` LIKE '$vcat' 
		ORDER BY `VolDate` DESC;";
//	echo "sql: $sql<br>";
	$res = doSQLsubmitted($sql);
	echo '<table class="table-condensed">
	<tr><th>RecNbr</th><th>MCID</th><th>ServiceDate</th><th>VolTime</th><th>Milage</th><th>Category</th><th>Notes</th></tr>';
	$rowcnt = $res->num_rows;
	echo "Records found in date range: $rowcnt<br />";
	while ($r = $res->fetch_assoc()) {
		//echo '<pre>'; print_r($r); echo '</pre>';
		$rcdlink = "<a href=\"listedittimerecord.php?action=updform&recno=$r[VTID]\">$r[VTID]</a>";
		echo "<tr><td>$rcdlink</td><td>$r[MCID]</td><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td>";
		$tothrs += $r[VolTime];
		}
	echo "</table>Total Hours Recorded: $tothrs<br>";
	echo '----- END OF REPORT -----
<script src="Incls/datevalidation.js"></script>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>';

exit;
}

if ($action == 'updform') {
	if ($recno > 0) {
		$sql = "SELECT * FROM `voltime` where `VTID` = '$recno'";
		$res = doSQLsubmitted($sql);
		$r = $res->fetch_assoc();
//		echo '<pre> time record '; print_r($r); echo '</pre>';		
		print <<<inForm1
		<script>
function initForm(theDoc) {
	initAllFields(theDoc.VTForm);
	return true;
	}
function initAllFields(form) {
// Initialize all form controls
  with (form) {
		initSelect(VolCategory,"$r[VolCategory]");
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
<form name="VTForm" id="VTForm" action="listedittimerecord.php" method="get">
<h4>Record Number: $r[VTID]</h4> 
<b>MCID: $r[MCID]</b><br />
<input type="hidden" name="VTID" value="$r[VTID]">
<input type="hidden" name="MCID" value="$r[MCID]">
Date: <input onchange="ValidateDate(this)" type="text" name="VolDate" value="$r[VolDate]" style="width: 105px;">
Vol Time: <input type="text" name="VolTime" value="$r[VolTime]" style="width: 105px;">
Mileage: <input type="text" name="VolMileage" value="$r[VolMileage]" style="width: 105px;">
		
<!-- Category: <input type="text" name="VolCategory" value="$r[VolCategory]"> -->
Category: <select name="VolCategory" >
<option value=""></option>
inForm1;
loaddbselect('VolCategorys');
print<<<inForm2
<option value='Education'>Education</option>  
</select></td>
Notes: <input type="text" name="VolNotes" value="$r[VolNotes]"><br /><br />
<input type="hidden" name="action" value="update">
<input type="submit" name="submit" value="Submit">
</form><br />
<a class="btn btn-primary" href="listedittimerecord.php?action">CANCEL UPDATE</a><br /><br />
<a class="btn btn-danger" href="listedittimerecord.php?action=delete&recno=$recno" onclick="return confirmdelete()">DELETE RECORD</a>
inForm2;
		}
	}


?>
</div>  <!-- container -->
<script src="Incls/datevalidation.js"></script>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
