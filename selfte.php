<!DOCTYPE html>
<html>
<head>
<title>Volunteer Self Time Entry</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
session_start();

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$mcid = isset($_REQUEST['MCID']) ? $_REQUEST['MCID'] : "";
//$addrow = isset($_REQUEST['AddRow']) ? $_REQUEST['AddRow'] : "";
if (isset($_REQUEST['Done'])) {
	//echo "Input Completed<br />";
	$mcid = "";
	echo '<h2>Goodbye!</h2>';
	exit;
	}

if (isset($_REQUEST['AddRow'])) {
	$uri = $_SERVER['QUERY_STRING'];
	//echo "URI: $uri<br>";
	parse_str($uri, $vararray);		// parse parameters from form into assoc array for db update
	unset($vararray[AddRow]);			// delete action parameter from assoc array
	//echo '<pre> Var array'; print_r($vararray); echo '</pre>';
	sqlinsert("voltime",$vararray);
	}

if ($mcid == "") {
print <<<pagePart1
<script>
function chkform(form) {
	var val=form.MCID.value.toUpperCase();
	var errcnt = 0;
	if (val.length > 5) errcnt += 1;
	if (!val.match(/^[A-Z]{3,3}[0-9]{2,2}/)) errcnt += 1;
	if (errcnt == 0) {
		form.MCID.value = val;		
		return true;
		}
	alert("The PWC Id is not a valid format.\\n\\nValid id is 3 letters and 2 digits.");
	return false
	}
</script>
<div class="container">
<h3>Volunteer Self Time Entry</h3>
<h4>Enter your PWC Id number:</h4>
<form action="selfte.php" method="post"  name="idform" onsubmit="return chkform(this)">
<input autofocus type="text" name="MCID" value="$mcid" style="width: 80px; " autocomplete="off" />
<input type="submit" name="submit" value="ENTER"> 
</form>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
pagePart1;
 exit;
}

// look up mcid in members table and pick up name fields for display
$sql = "SELECT `FName`,`LName` from `members` WHERE `MCID` = '$mcid';";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
$r = $res->fetch_assoc();
if ($rowcnt == 0) {
	$mcid = '';
	echo '<div class="container"><h3>Unknown MCID entered.</h3>';
	echo '<a autofocus class="btn btn-large btn-warning" href="selfte.php">TRY AGAIN</a></div>';
	exit;
	}
$mcidname = $r[FName] . "&nbsp;" . $r[LName];

// present input form with mcid and name 
print<<<pagePart2
<script>
function chkform(form) {
	//alert("checking form");
	var errmsg = "";
	form.VolDate.style.background = 'White';
	if (form.VolDate.value == "") {
		form.VolDate.style.background = '#F7645E';		
		errmsg += "Date of service is required\\n";
		}
	form.VolTime.style.background = 'White';
	var time = form.VolTime.value;
	if (isNaN(time)) {
		form.VolTime.style.background = '#F7645E';
		errmsg += "Value entered for hours is not a number.\\n";
		}
	var time = form.VolTime.value * 1;
	if ((time < 0) || (time > 12)) {
		form.VolTime.style.background = '#F7645E';
		errmsg += "Values for hours must be greater than 0, less than 12.\\n";
		}
	if (form.VolTime.value == "") {
		form.VolTime.style.background = '#F7645E';
		errmsg += "An entry for Hours is required\\n";
		}
	form.VolCategory.style.background = 'White';
	if (form.VolCategory.value == "") {
		errmsg += "A category must be selected.\\n";
		form.VolCategory.style.background = '#F7645E';
		}
	form.VolMileage.style.background = 'White';
	var mileage = form.VolMileage.value;
	if ((mileage != "") && (isNaN(mileage))) {
		errmsg += "Value entered for mileage is not a number.\\n";
		form.VolMileage.style.background = '#F7645E';
		}
	var catflag = form.VolCategory.value.search("Other");
	var noteslen = form.VolNotes.value.length;
	if ((catflag >= 0) && (noteslen == 0)) {
		errmsg += "Category selection of 'Other' requires entry of Notes field.";
		form.VolNotes.style.background = '#F7645E';
		}
	if (errmsg == "") return true;
	alert(errmsg);
	return false;
	}
</script>  
<div class="container">
<table class="table-condensed">
<form action="selfte.php" method="get"  name="dataform" onsubmit="return chkform(this)">
<fieldset>
<legend>Volunteer Self Time Entry for: $mcidname<br />PWC Id: $mcid</legend>&nbsp;&nbsp;
<a class="btn btn-xs btn-danger" href="selfte.php">Not Me!</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a class="btn btn-xs btn-primary" href="selfte.php?Done">DONE</a>
<input type="hidden" name="MCID" value="$mcid">
<tr><td><label>Date:</label></td>
<td><input type="text" name="VolDate" value="" data-provide="datepicker" id="dp1" data-date-format="yyyy-mm-dd" data-date-autoclose="true"></td></tr>
<tr><td><label>Time:</label></td> 
<td><input type="text" name="VolTime" value="" placeholder="Time in hours"></td></tr>
<tr><td><label>Category:</label></td>
<td><select name="VolCategory" onchange="chkselection(this)">
<option value=""></option>
pagePart2;
echo loaddbselect('VolCategorys');
print<<<pagePart3
</select></td></tr>
<tr><td><label>Mileage:</label></td>
<td><input type="text" name="VolMileage" value="" placeholder="Mileage (optional)"></td></tr>
<tr><td><label>Notes:</label></td>
<td><textarea rows="4" cols="25" name="VolNotes"></textarea></td></tr>
<tr><td></td><td><input type="submit" name="AddRow" value="Add">&nbsp;&nbsp;&nbsp;
</fieldset> 
<a class="btn btn-xs btn-default" href="selfterpt.php?mcid=$mcid">History</a></td></tr>
</form>
</table>
</div>
pagePart3;

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
</div>
</body>
</html>