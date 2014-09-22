<!DOCTYPE html>
<html>
<head>
<title>Volunteer Information</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onLoad="initForm(this)" onChange="flagChange()">

<script src="jquery.js"></script>
<script src="Incls/datevalidation.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
//Include "Incls/vardump.inc";
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';
include 'Incls/createcitydd.inc';

$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter']: "";
$filter = $filterflag = rtrim($filter);
if ($filter == "--none--") {
	$filter = "";
	unset($_SESSION['ActiveMCID']);
	unset($_REQUEST['filter']);
	}
if ($filter != "") {
	$_SESSION['ActiveMCID'] = $filter;
	}
	
include 'Incls/mainmenu.inc';

echo "<div class=container>";
$mcid = $_SESSION['ActiveMCID'];
$action = $_REQUEST['action'];
if ($filterflag == "--none--") { 
	$m = "<p><b>Use of the MCID field</b></p><p>The MCID field is used to access and update member/contact informaton.  No MCID entered will provide access to a page to do a general search of the entire database.</p>
	<p>Click the <a href=\"mbrsearchlist.php\">general search</a> button and enter any string of characters to search the all or part of the first name, last name, label name, address, or email addresses of the entire database.  This will produce a listing of ALL records that contain the target string entered.</p>
	<p>When a target list of records is displayed, click the bullet at the left of the associated MCID to access the specific member's record./p>
	<p>Once a single member record has been accessed, its correspondence and fund information records will be available by clicking on the main menu at the top of the page.  That MCID will remain the 'active' until a new MCID is selected or you click the \"Home\" menu choice.</p>";
	echo "<h2>No MCID entered.</h2>";
	echo "<br />";
	echo "$m";
	echo "<h4><a class=\"btn btn-large btn-primary\" href=\"mbrsearchlist.php\" name=\"filter\" value=\"--none--\">General Search</a></h4></div>";
	exit;
	}

if (($action == "") AND ($mcid == "")) {
	$mcinfo = "<h3>Volunteer/Contact Informaton</h3>"; 
	$mcinfo .= "<p>This page will display all information of the volunteer's Member Id (MCID) selected by using the MCID selected via the &apos;Lookup&apos; function.  It will remain &apos;active&apos; until another is selected by either returning to the Home page or by using the &apos;Lookup&apos; to select a new one.</p>";
	//$mcinfo .= "<br /><h4><a href=\"voladdition.php\">Add New Volunteer</h4></a>Using this link to begin the process of adding a new member into the database.<br />";
	//$mcinfo .= "<br /><h4><a href=\"voladdnewuser.php\">Add New User</h4></a>Using this link to begin the process of adding a new user into the database.<br />";
	echo $mcinfo;
	exit;
	}

// if action is to update, get all fields supplied and write them to the database before reading
if ($action == "update") {
	$uri = $_SERVER['QUERY_STRING'];
	parse_str($uri, $vararray);
	if (array_key_exists('mlist',$vararray)) {
		$listarray = $vararray[mlist];						// get list array
		$liststring = implode(",",$listarray);		// create list string
		unset($vararray[mlist]);									// delete array
		$vararray[Lists] = $liststring;						// add back the string
		}
	else $vararray[Lists] = '';									// if non are checked -----
	unset($vararray[action]);										// unset page action indicator
	$where = "`MCID`='" . $mcid . "'";
	sqlupdate('members',$vararray, $where);	
	}

// get member record from ActiveMCID and display the info in update form
echo "<h3>Member Information for ".$mcid."</h3>";
$sql = "SELECT * FROM `members` WHERE MCID = '$mcid'";
$res = doSQLsubmitted($sql);
//$res = readMCIDrow($mcid);
if ($res->num_rows == 0) {
	echo "<h3>No MCID record found.  Please retry.</h3><br /><br />";
	echo "<p>Enter part or all of new MCID in the &apos;LOOKUP&apos; box and try again.</p>";
	//echo "<a class=\"btn btn-large btn-primary\" href=\"index.php\">CANCEL AND RETURN</a><br /><br />";
	exit;
	} 
// get row data from result
$res->data_seek(0);
$row = $res->fetch_assoc();
// get data values from sql query result
$mcid=$row['MCID'];  $fname=$row['FName']; $lname=$row['LName'];
$org=$row['Organization']; $addr=$row['AddressLine']; 
$lab1line=$row['NameLabel1stline']; $corrsal=$row['CorrSal']; 
$eaddr=$row['EmailAddress']; $city=$row['City']; $state=$row['State']; 
$zip=$row['ZipCode']; $priphone=$row['PrimaryPhone'];
$memstatus=$row['MemStatus'];$memdate=$row['MemDate'];
$mctype=$row['MCtype'];$inact=$row['Inactive'];$inactdate=$row['Inactivedate'];
$e_mail=$row['E_Mail'];$nomail=$row['NoMail']; $notes=$row['Notes'];$lists=$row[Lists];
$citieslist = createddown();
print <<<pagePart1
<script>
var reason = "";
var secLevel = "$_SESSION[SecLevel]";
// validate form to ensure required fields are entered
function validateForm(theForm) {
	//alert("validation entered");
	//if (secLevel != "voladmin") {
	//	alert("You do not have authority of make updates. " + secLevel + "!");
	//	return false;
	//	}
	reason = "";
	reason += validateEmpty(theForm.FName);
	reason += validateEmpty(theForm.LName);
	reason += validateEmpty(theForm.NameLabel1stline);
	reason += validateCorrSal(theForm.CorrSal);
	//reason += validateEmpty(theForm.AddressLine);
	reason += validateEmpty(theForm.City);
	//reason += validateEmpty(theForm.State);
	//reason += validateEmpty(theForm.ZipCode);
	//reason += validateEmpty(theForm.MemStatus);
	reason += validateEmpty(theForm.MCType);
	reason += validateEmpty(theForm.MemDate);
	//reason += validatePassword(theForm.pwd);
	reason += validateEmpty(theForm.EmailAddress);
	reason += validateEmpty(theForm.PrimaryPhone);
	//reason += validateEmpty(theForm.from);    
	if (reason != "") {
  	alert("Some fields need attention:\\n\\n" + reason);
  	return false;
		}
	return true;
	}
	
function validateCorrSal(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	fld.value = document.mcform.FName.value;
  	return error;
    	}
    return error;  
	}
function validateEmpty(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	fld.style.background = '#F7645E';
  	if (reason == "") {
    	error = "Required field(s) have not been filled in.\\n" }
    	} 
    else {
    	fld.style.background = 'White';
    	}
    return error;  
	}
	
<!-- Function to prevent user from using the 'Enter' key when cursor in a text field. -->
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target)?evt.target:((evt.srcElement)?evt.srcElement:null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
	}

<!-- Does not allow use of Enter key when filling out a form -->
<!-- document.onkeypress = stopRKey; -->

function initForm(theDoc) {
	clearFilter(theDoc.filter);
	initAllFields(theDoc.mcform);
	return true;
	}

function clearFilter(theForm) {
	theForm.filter.value = "";
	return true;
	}
	
function initAllFields(form) {
// Initialize all form controls
  with (form) {
//		initRadio(ttaken,"$ttaken");
		initRadio(MemStatus,"$memstatus");
		initSelect(MCType,"$mctype");
		initRadio(E_mail,"$e_mail");
		initRadio(NoMail,"$nomail");
		initRadio(Inactive,"$inact");
  	}
	}
	
function initSelect(control,value) {
// Initialize a selection list (single valued)
// alert("initSelect: control: " + control.length + ", value: " + value);
	if (value == "") return;
	for (var i = 0; i < control.length; i++) {
		if (control.options[i].value == value) {
			control.options[i].selected = true;
			break;
			}
		}
	}

function initRadio(control,value) {
//alert("initRadio");
// Initialize a radio button
	for (var i = 0; i < control.length; i++) { 
		if (control[i].value == value) {
			control[i].checked = true;
			break;
		}
	}
}

function setflds(theForm) {
	//alert("entered");
	var ffld = theForm.FName.value;
	var lfld = theForm.LName.value;
	//alert("ffld: "+ffld+", lfld: "+lfld);
	theForm.NameLabel1stline.value = ffld + " " + lfld;
	theForm.CorrSal.value = ffld;
	return;
	}

function checkmbr(fld) {
	var mctype = fld.value;
	var mcval = mctype.substring(0,1);
	//var memstatus = document.mcform.MemStatus;
	for (var i = 0; i < document.mcform.MemStatus.length; i++) {
     if (document.mcform.MemStatus[i].checked) {
    	memstatus = i;
    	break;
    	}
   	}
	if (mcval != memstatus) {
		fld.value = "";
		alert("Please select a Mbr Type that corresponds with the selected Mbr Status");
		return false;
		}
	return true;
	}

function chgmemstatus() {
	//alert("chg memstat entered");
	document.mcform.MCType.value = "";
	return true;
	}
function setInactiveDate(fld) {
	//alert("set inactive date entered");
	var d = new Date();
  var curr_date = d.getDate();
  var curr_month = d.getMonth() + 1; 
  var curr_year = d.getFullYear();
	document.mcform.Inactivedate.value = curr_year + "-" + curr_month + "-" + curr_date;
	return true;
	}

function clearInactiveDate(fld) {
	document.mcform.Inactivedate.value = '';
	}
</script>

<script>
function ValidatePhone(fld)  {
//alert("validation entered");
var errmsg = "";
var stripped = fld.value.replace(/[\(\)\.\-\ \/]/g, '');
if (stripped.length != 10) { 
	errmsg += "Invalid phone number.  Please include the Area Code.\\n";
	}
if(!stripped.match(/^[0-9]{10}/))  { 
	errmsg += "Invalid phone number entered.\\n";
	}
if (errmsg.length > 0) {
	errmsg += "\\nValid formats: 123-456-7890 or 123 456 7890 or (123)456-7890 or 1234567890";
	fld.style.background = '#F7645E';
	alert(errmsg);
	return false;
	}
var newval = stripped.substr(0,3) + "-" + stripped.substr(3,3) + "-" + stripped.substr(6,4);
fld.value = newval;
fld.style.background = 'White';
return true;
}
</script>
<script>
function ValidateEmail(fld)  {
//alert("validation entered");
var errmsg = "";
var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
var illegalChars= /[\(\)\<\>\,\?\;\:\\\"\[\]]/ ; 
if ((!emailFilter.test(fld.value)) || (fld.value.match(illegalChars))) { 
	fld.style.background = 'Pink';
	alert("Invalid email address entered.");
	return false;
	}
fld.style.background = 'White';
return true;
}
</script>

<form name="mcform" id="mcform" class="form-horizontal" role="form" onsubmit="return validateForm(this)">
<div style="text-align: center"><button type="submit" form='mcform' class="btn btn-primary">Update Member</button></div>

<!-- Tab definition header  -->
<ul id="myTab" class="nav nav-tabs">
  <li class=""><a href="#home" data-toggle="tab">Home</a></li>
  <!-- <li class=""><a href="#detail" data-toggle="tab">Detail</a></li> -->
  <li class=""><a href="#notes" data-toggle="tab">Notes</a></li>
  
pagePart1;

if ($memstatus == 2) echo '<li class=""><a href="#lists" data-toggle="tab">Lists</a></li>';

print <<<pagePart2
</ul>
<!-- Tab 1 Demographic information -->
<div id="myTabContent" class="tab-content">
<div class="tab-pane fade active in" id="home">
<div class="well">
<h4>Contact Information</h4>
<div class="row">
<div class="col-sm-4">First: <input placeholder="First Name" autofocus type="text" name="FName" value="$fname" onchange="setflds(document.mcform)"></div>
<div class="col-sm-4">Last: <input placeholder="Last Name" type="text" name="LName" value="$lname" onchange="setflds(document.mcform)"></div>
</div>

<div class="row">
<div class="col-sm-4">Label Line: <input placeholder="Label Line" name="NameLabel1stline" value="$lab1line"></div>
<div class="col-sm-5">Correspondence Sal:<input placeholder="Correspondence Salutation" name="CorrSal" value="$corrsal"></div>
</div>
<div class="row">
<div class="col-sm-4">Org: <input placeholder="Organization" name="Organization" value="$org"></div>
<div class="col-sm-4">Addr Line: <input placeholder="Address Line" name="AddressLine" value="$addr"></div>
</div>
<div class="row">
<div class="col-sm-4">City: <input id="CI" placeholder="City" name="City" value="$city" autocomplete="off" onblur="loadcity()"></div>
<div class="col-sm-2">State: <input id="ST" placeholder="State	" type="text" name="State" value="$state" style="width: 50px; " /></div>
<div class="col-sm-3">Zip: <input id="ZI" type="text" name="ZipCode" value="$zip" size="5" maxlength="5" style="width: 100px;"  placeholder="Zip" /></div>
</div>
<script src="js/bootstrap3-typeahead.js"></script>
<script>
function loadcity() {
	//alert("loadcity");
	var cv = $("#CI").val();
	var cva = cv.split(",");
	$("#CI").val(cva[0]);
	$("#ST").val(cva[1]);
	$("#ZI").val(cva[2]);
	}
</script>

<script>
var citylist = $citieslist;
$('#CI').typeahead({source: citylist})
</script>

<div class="row">
<div class="col-sm-4">Phone: <input type="text" name="PrimaryPhone" value="$priphone" size="12" maxlength="12" style="width: 125px;" onchange="return ValidatePhone(this)"  placeholder="Primary Phone" /></div>
<div class="col-sm-4">Email: <input placeholder="Email" onchange="ValidateEmail(this)" style="width: 200px;" name="EmailAddress" value="$eaddr"></td></tr></div>
</div>
<!-- </div>  well -->
<!-- </div>  tab pane -->

<!-- Tab 2 membership information -->
<!-- <div class="tab-pane fade" id="detail"> -->
<!-- <div class="well"> -->
<h4>Volunteer Detail</h4>
<div class="row">
<div class="col-sm-7">
Mbr Status:&nbsp;
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="0" checked/>0-Contact
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="1" />1-Member
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="2" />2-Vol.
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="3" />3-Donor
</div>  <!-- col-sm-7 -->
</div>	<!-- row -->
<div class="row">
<div class="col-sm-5 col-sm-offset-1">
Mbr Type:<select name="MCType" size="1" onChange="checkmbr(this)">
<option value=""></option>
pagePart2;
loaddbselect('MCTypes');
print <<<pagePart3
</select>
</div>  <!-- col-sm-5 -->
</div>  <!-- row -->
<div class="row">
<div class="col-sm-3">
Date Joined:<input onchange="ValidateDate(this)" placeholder="YYYY-MM-DD" name="MemDate" value="$memdate" style="width: 100px;">
</div>  <!-- col-sm-4 -->

<div class="col-sm-3">Email OK?: 
<input type="radio" name="E_mail" value="TRUE" />Yes
<input type="radio" name="E_mail" value="FALSE" />No
</div>
<div class="col-sm-3">Mail OK?: 
<input type="radio" name="NoMail" value="TRUE" />Yes
<input type="radio" name="NoMail" value="FALSE" />No
</div>
</div>  <!-- row -->
<div class="row">
<div class="col-sm-3">Mbr Inactive?: 
<input onclick="setInactiveDate()" type="radio" name="Inactive" value="TRUE" />Yes
<input onclick="clearInactiveDate()" type="radio" name="Inactive" value="FALSE" />No
</div>
<div class="col-sm-4">Date Inactive: <input placeholder="Date Inactive" name="Inactivedate"  onchange="ValidateDate(this)" value="$inactdate"></div>
</div>
</div>  <!-- well -->
</div>  <!-- tab pane -->


<!-- Tab 3 member notes -->
<div class="tab-pane fade" id="notes">
<div class="well">
<h4>Notes</h4>
<div class="row">
<div class="col-sm-6"><textarea name="Notes" rows="3" cols="60">$notes</textarea></div>
</div>  <!-- row -->
</div>  <!-- well -->
</div>	<!-- tab pane -->

pagePart3;
// Tab 4 email lists - displayed if member status = 2 -->

echo '<div class="tab-pane fade" id="lists">
<div class="well">
<h4>Email Lists</h4>';

$text = readdblist('EmailLists');
$listkeys = formatdbrec($text);
foreach ($listkeys as $k => $v) {
	//echo "key: $k, value: $v<br />";
	if (stripos($lists, $k) !== FALSE) {
		echo "<input type=\"checkbox\" name=\"mlist[]\" value=\"$k\" checked>$v<br>";
		}
	else {
		echo "<input type=\"checkbox\" name=\"mlist[]\" value=\"$k\">$v<br>";
		}
	//echo "key: $k, value: $v<br>";
	}

echo '</div>  <!-- well -->
</div>  <!-- tab pane -->
<!-- end all tab definitions -->
</div>  <!-- tab content -->';

?>
<input type="hidden" name="action" value="update">
</form>
</div>
<hr></div><br /><br />
</body>
</html>
