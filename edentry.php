<!DOCTYPE html>
<html>
<head>
<title>Vol Course Data Entry</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="./Incls/datevalidation.js"></script>
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';
include 'Incls/seccheck.inc';

// time entry is an admin function
if ($_SESSION['SecLevel'] != 'voladmin') {
		echo '<div class="container"><h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to perform volunteer time data entry.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded to &apos;voladmin&apos; in order to perform this function.</p><br />
		<a class="btn btn-primary" href="admin.php">RETURN</a></div>
		</body></html>';
		exit;
		}

// create the string to download with the page as an array
// we will also use this string to validate the MCID's returned as input

$sql = "SELECT `MCID`,`FName`,`LName` from `members` WHERE `MemStatus` = 2 ORDER BY `MCID`;";
$res = doSQLsubmitted($sql);
if ($res->num_rows == 0) {
	echo '<h2>No volunteers named to populate the typeahead field.</h2>
	<h3>volunteers are identified in membership database as a member status of 2.  Please check to ensure that the volunteer roster is current and complete in the database.</h3>';
	echo '<a class="btn btn-primary" href="admin.php">RETURN</a></body></html>';
	exit;
	}
// now create the string for the javascript arrays to download
$vols = '[';		// create string for form typeahead
while ($r = $res->fetch_assoc()) {
	$mcid = preg_replace("/[\(\)\.\-\ \/\&]/i", "", $r[MCID]);
	$lname = preg_replace("/[\(\)\.\-\ \/\'\&]/i", "", $r[LName]);
	$fname = preg_replace("/[\(\)\.\-\ \/\'\&]/i", "", $r[FName]);
	$vols .= "'$mcid,$lname,$fname',";
	}
$vols = rtrim($vols,',') . ']';
// echo '<pre> vols '; print_r($vols); echo '</pre>';

// create the course list for the typeahead
$sql = "SELECT * FROM `courses` WHERE 1";
$res = doSQLsubmitted($sql);
$edlist = '[';
while ($r = $res->fetch_assoc()) {
	$c = rtrim($r[Agency]) . ':' . rtrim($r[CourseId]);
	$edlist .= "'$c',";
	}
$edlist = rtrim($edlist,',') . ']';
// echo '<pre> courses '; print_r($edlist); echo '</pre>';

$mciderr = array(); $rowcnt = 0;
// check if this is an update, string to validate mcid's is in vols string
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
if ($action == 'upd') {
	// capture input arrays and apply the values to the db
	$date = $_REQUEST['date'];
	$id = $_REQUEST['id'];
	$hrs = $_REQUEST['hrs'];
	$ed = $_REQUEST['ed'];
	$note = $_REQUEST['note'];
	$rows = 0;
	for ($i = 0; $i < 10; $i++) {
		if ($date[$i] == '') break;											// first blank date is end of input rows
		list($mcid,$lname,$fname) = explode(",",$id[$i]);
//		echo "<pre>mcid after expode: "; print_r($mcid); echo '</pre>';		
		if (stripos($vols,$mcid) === FALSE) {						// test to see if input mcid is in vols string
			$mciderr[$mcid] += 1;
			echo "error mcid: $mcid<br>";
			continue;
			}
		$flds = array();
		list($agency, $courseid) = explode(':', $ed[$i]);
		$flds[MCID] = $mcid;
		$flds[CourseDate] = $date[$i];
		$flds[Agency] = $agency;
		$flds[CourseDuration] = $hrs[$i];
		$flds[CourseId] = $courseid;
		$flds[CourseNotes] = $note[$i];
//		echo "<pre>updcount $i: "; print_r($flds); echo '</pre>';
		$rows = sqlinsert('volcourses',$flds);			// returns the number of rows inserted
		$rowcnt += 1;
		}
		//echo 'vol string:' . $vols .'<br />';
		echo 'Rows applied: '.$rowcnt.'<br />';
		//echo '<pre>mciderr ->'; print_r($mciderr); echo '</pre>';
		if (count($mciderr) > 0) {
			echo 'MCID(s) entry(s) in error: ';
			foreach ($mciderr as $k => $v) {
				echo "$k($v), ";
			}
		echo '<br>';
		}
	}

// define the intake page
print <<<pagePart1
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Course Data Entry</title>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<body onchange="flagChange()">
<div class="well">

<h2>Course Data Entry <a class="btn btn-primary" href="admin.php" onclick="return chkchg()">RETURN</a></h2>
<script>
<!-- Form change variable must be global -->
var chgFlag = 0;

function chkchg() {
	if (chgFlag <= 0) { return true; }
	var r=confirm("All changes (" + chgFlag + ") made will be lost.\\nConfirm by clicking OK. ");	
	if (r == true) { chgFlag = 0; return true; }
		return false;
	}
</script>
<script>
// add '<body onchange="flagChange()">' to all pages needed
function flagChange() {
	//alert("change flagged");
	//document.ElegetmentById("chgflg").hidden=false;
	chgFlag += 1;
	return true;
	}
</script>
<script>
function isnum(fld) {
var num = fld.value;
	if (!isNaN(num)) {
		fld.style.background = 'White';
		return true;
		}
	alert("Value entered is not a number.");
	fld.style.background = 'Pink';
	return false;
	}
</script>
<script>
function isOK(fld) {
	fld.style.background = 'White';
	return true;
	}
	
</script>
<script>
function chkform(frm) {
	var errcnt = 0;
	var id = document.getElementsByName('id[]');
	var date = document.getElementsByName('date[]');
	var hrs = document.getElementsByName('hrs[]');
	var ed = document.getElementsByName('ed[]');
	for (i = 0; i < 10; i++) {
		if (date[i].value != "") {
			if (hrs[i].value == "") {
				errcnt += 1;
				hrs[i].style.background = "Pink";
				}
			if (id[i].value == "") {
				errcnt += 1;
				id[i].style.background = "Pink";
				}
			if (ed[i].value == "") {
				errcnt += 1;
				ed[i].style.background = "Pink";
				}
			}
		if (errcnt > 0) { 
			alert("Incomplete entry. Date with no id, hours and/or course. Please correct");
			return false;
			}
		}
	for (i = 0; i < 10; i++) {
		if (date[i].style.backgroundColor == "Pink") errcnt += 1;
		if (hrs[i].style.backgroundColor == "Pink") { errcnt += 1; }
		}
	if (errcnt > 0) {
		alert("Please correct the highlighted error fields.");
		return false;
		}
	return true;
	}
</script>
<script>
function deactivatesubmit() {
	//alert("detactivate submit button");
	//document.getElementById("sub-btn").disabled=true;  // NOTE: this doesn't work in Chrome
	document.getElementById("sub-btn").style.backgroundColor="Red";	
	return true;
	}
</script>
<form action="edentry.php" method="post" id="inp" onsubmit="return chkform(this)">
<table border="0" class="table table-condensed">
<tr><th>Date</th><th>Name (MCID, Last, First)</th><th>Hours</th><th>Course</th><th>Notes</th></tr>
<tr>
<!-- row 1 -->
<td><input autofocus name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search1" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input name="hrs[]" type="text" id="hrs" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed1" data-provide="typeahead" data-items="5" autocomplete="off" size="30"/></td>
<td><input type="text" name="note[]" size="40"  autocomplete="off"></td>
</tr>

<!-- row 2 -->
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search2" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed2" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40></td>
</tr>

<!-- row 3 -->
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search3" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed3" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40></td>
</tr>

<!-- row 4 -->
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search4" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed4" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40/></td>
</tr>

<!-- row 5 -->
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search5" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed5" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40/></td>
</tr>

<!-- row 6 -->
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search6" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input name="hrs[]" type="text" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed6" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40/></td>
</tr>

<!-- row 7 -->
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search7" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed7" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40/></td>
</tr>

<!-- row 8 -->
<tr>
<td><input type="text" name="date[]" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search8" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed8" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40/></td>
</tr>

<!-- row 9 -->
<tr>
<td><input type="text" name="date[]" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search9" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed9" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40/></td>
</tr>

<!-- row 10 -->
<tr>
<td><input type="text" name="date[]" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="id[]" type="text" id="search10" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input onchange="isOK(this)" name="ed[]" type="text" id="ed10" data-provide="typeahead" data-items="5" autocomplete="off" size=30/></td>
<td><input name="note[]" type="text" value="" autocomplete="off" size=40/></td>
</tr>
</table>
<input type="hidden" name="action" value="upd">
<input id="sub-btn" name="submit" value="SUBMIT" type="submit" onclick="deactivatesubmit()">
</form>
</div>

<script src="js/bootstrap3-typeahead.js"></script>
<script>
 var vols = $vols; 
$('#search1').typeahead({source: vols})
$('#search2').typeahead({source: vols})
$('#search3').typeahead({source: vols})
$('#search4').typeahead({source: vols})
$('#search5').typeahead({source: vols})
$('#search6').typeahead({source: vols})
$('#search7').typeahead({source: vols})
$('#search8').typeahead({source: vols})
$('#search9').typeahead({source: vols})
$('#search10').typeahead({source: vols})
</script>
<script>
 var ed = $edlist; 
$('#ed1').typeahead({source: ed})
$('#ed2').typeahead({source: ed})
$('#ed3').typeahead({source: ed})
$('#ed4').typeahead({source: ed})
$('#ed5').typeahead({source: ed})
$('#ed6').typeahead({source: ed})
$('#ed7').typeahead({source: ed})
$('#ed8').typeahead({source: ed})
$('#ed9').typeahead({source: ed})
$('#ed10').typeahead({source: ed})
</script>

</body>
</html>
pagePart1;
?>