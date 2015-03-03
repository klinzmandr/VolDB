<!DOCTYPE html>
<html>
<head>
<title>Add New Admin User</title>
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

/*
$catrec = readdblist('VolCategorys');	// create string for form typeahead
$catarray = formatdbrec($catrec);
$cats = '[';
foreach ($catarray as $c) {
	$c = rtrim($c);
	$cats .= "'$c',";
	}
$cats = rtrim($cats,',') . ']';
*/

$mciderr = array(); $rowcnt = 0;
// check if this is an update, string to validate mcid's is in vols string
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
if ($action == 'upd') {
	// capture input arrays and apply the values to the db
	$date = $_REQUEST['date'];
	$id = $_REQUEST['id'];
	$hrs = $_REQUEST['hrs'];
	$mileage = $_REQUEST['mileage'];
	$cat = $_REQUEST['category'];
	$note = $_REQUEST['note'];
	$rows = 0;
	for ($i = 0; $i < 10; $i++) {
		if ($date[$i] == '') break;											// first blank date is end of input rows
		list($mcid,$lname,$fname) = explode(",",$id[$i]);
		//echo "<pre>mcid after expode: "; print_r($mcid); echo '</pre>';		
		if (stripos($vols,$mcid) === FALSE) {						// test to see if input mcid is in vols string
			$mciderr[$mcid] += 1;
			echo "error mcid: $mcid";
			continue;
			}
		$flds = array();
		$flds[MCID] = $mcid;
		$flds[VolDate] = $date[$i];
		$flds[VolTime] = $hrs[$i];
		$flds[VolMileage] = $mileage[$i];
		$flds[VolCategory] = $cat[$i];
		$flds[VolNotes] = $note[$i];
		//echo "<pre>updcount $i: "; print_r($flds); echo '</pre>';
		$rows = sqlinsert('voltime',$flds);			// returns the number of rows inserted
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
		}
	}

// define the intake page
print <<<pagePart1
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Volunteer Time Entry</title>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<body onchange="flagChange()">
<div class="well">

<h2>Volunteer Time Data Entry <a class="btn btn-primary" href="admin.php" onclick="return chkchg()">RETURN</a></h2>
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
function chkform(frm) {
	var errcnt = 0;
	var id = document.getElementsByName('id[]');
	var date = document.getElementsByName('date[]');
	var hrs = document.getElementsByName('hrs[]');
	var mileage = document.getElementsByName('mileage[]');
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
			}
		if (errcnt > 0) { 
			alert("Incomplete entry. Date with no id and/or hours. Please correct");
			return false;
			}
		}
	for (i = 0; i < 10; i++) {
		if (date[i].style.backgroundColor == "Pink") errcnt += 1;
		if (hrs[i].style.backgroundColor == "Pink") { errcnt += 1; }
		if (mileage[i].style.backgroundColor == "Pink") { errcnt += 1; }
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
<form action="voltimeentry.php" method="post" id="inp" onsubmit="return chkform(this)">
<table border="0" class="table table-condensed">
<tr><th>Date</th><th>Name (MCID, Last, First)</th><th>Hours</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>
<tr>
<td><input autofocus name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search1" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input name="hrs[]" type="text" id="hrs" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input name="mileage[]" type="text" id="mileage" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat1" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart1;
echo loaddbselect('VolCategorys');
print<<<pagePart2
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
</tr>
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search2" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)>" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat2" data-provide="typeahead" data-items="5" autocomplete="off" ></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart2;
echo loaddbselect('VolCategorys');

print<<<pagePart3
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" ></td>
</tr>
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search3" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)>" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat3" data-provide="typeahead" data-items="5" autocomplete="off" ></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart3;
echo loaddbselect('VolCategorys');

print<<<pagePart4
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" ></td>
</tr>
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search4" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
	<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat4" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart4;
echo loaddbselect('VolCategorys');

print<<<pagePart5
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
</tr>
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search5" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat5" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart5;
echo loaddbselect('VolCategorys');

print<<<pagePart6
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
</tr>
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search6" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input name="hrs[]" type="text" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input name="mileage[]" type="text" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat6" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart6;
echo loaddbselect('VolCategorys');

print<<<pagePart7
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
</tr>
<tr>
<td><input name="date[]" type="text" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search7" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat7" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart7;
echo loaddbselect('VolCategorys');

print<<<pagePart8
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
</tr>
<tr>
<td><input type="text" name="date[]" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search8" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat8" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart8;
echo loaddbselect('VolCategorys');

print<<<pagePart9
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
</tr>
<tr>
<td><input type="text" name="date[]" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search9" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat9" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart9;
echo loaddbselect('VolCategorys');

print<<<pagePart10
</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
</tr>
<tr>
<td><input type="text" name="date[]" size="12" maxlength="12" style="width: 105px;" onchange="ValidateDate(this)" autocomplete="off" /></td>
<td><input name="id[]" type="text" id="search10" data-provide="typeahead" data-items="4" autocomplete="off" /></td>
<td><input type="text" name="hrs[]" value="" size="6" maxlength="6" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>
<td><input type="text" name="mileage[]" value="" size="4" maxlength="4" style="width: 50px;" onchange="isnum(this)" autocomplete="off" /></td>

<!-- <td><input name="category[]" type="text" id="cat10" data-provide="typeahead" data-items="5" autocomplete="off" /></td> -->

<td><select name="category[]" >
<option value=""></option>
pagePart10;
echo loaddbselect('VolCategorys');

print<<<pagePart11

</select></td>

<td><input name="note[]" type="text" value="" autocomplete="off" /></td>
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
<!-- <script>
 var cats = $cats; 
$('#cat1').typeahead({source: cats})
$('#cat2').typeahead({source: cats})
$('#cat3').typeahead({source: cats})
$('#cat4').typeahead({source: cats})
$('#cat5').typeahead({source: cats})
$('#cat6').typeahead({source: cats})
$('#cat7').typeahead({source: cats})
$('#cat8').typeahead({source: cats})
$('#cat9').typeahead({source: cats})
$('#cat10').typeahead({source: cats})
</script> -->

</body>
</html>
pagePart11;
?>