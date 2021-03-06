<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Volunteer Information</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<script src="jquery.js"></script>
<script src="Incls/datevalidation.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
// include "Incls/vardump.inc.php";
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/createcitydd.inc.php';
include 'Incls/mainmenu.inc.php';

$addflg = isset($_REQUEST['addflg']) ? $_REQUEST['addflg'] : '';
$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter']: "";
$filter = $filterflag = rtrim($filter);
if ($filter == "--none--") {
	$filter = "";
	unset($_SESSION['VolActiveMCID']);
	unset($_REQUEST['filter']);
	}
if ($filter != "") {
	$_SESSION['VolActiveMCID'] = $filter;
	}

echo "<div class=container>";
$mcid = $_SESSION['VolActiveMCID'];
$action = $_REQUEST['action'];

// if action is to update, get all fields supplied and write them to the database before reading
if ($action == "update") {
	$vararray = $_REQUEST['inf'];
//	echo '<pre> input inf '; print_r($vararray); echo '</pre>';

// adding saftey check to make sure MCID from input page is same as ActiveMCID
// MCIDx is from the input form of the update page.
	if ($_REQUEST['MCIDx'] != $_SESSION['VolActiveMCID']) {
	  //echo 'MCIDx: '.$_REQUEST['MCIDx']. ', VolActiveMCID: '.$_SESSION['VolActiveMCID'].'<br>'; 
	  echo "<h2 style=\"color: red; \">ERROR: MCID mismatch!!!</h2>
	  <b>If this error occurs please note the actions being taken immediately prior to
	  seeing this message and notify dave.klinzman@yahoo.com immediately. Please  
	  provide this information and any other notes along with the MCID's involved.</b><br>";
	  $log = 'XUpdate Error. MCIDx: '. $_REQUEST['MCIDx'] . ', VolActiveMCID: '. $_SESSION['VolActiveMCID'].'<br>';
	  addlogentry($log);                       // log the error
	  $log = 'SESSION ' . var_export($_SESSION, TRUE);
	  addlogentry($log);
	  $log = 'REQUEST ' . var_export($_REQUEST, TRUE);
	  addlogentry($log);	                     // log the sesssion variables
	  unset($_SESSION['VolActiveMCID']);       // force new lookup for MCID
	  exit;
    }

	$ml = isset($_REQUEST['mlist']) ? $_REQUEST['mlist'] : '';
	if ($ml != '') {
		$liststring = implode(",",$ml);		           // create list string
		$vararray['Lists'] = $liststring;						 // add the string to db record
		}
	else $vararray['Lists'] = '';									// if none are checked -----
	
	// echo '<pre> input after list upd '; echo "mcid: $mcid, "; print_r($vararray); echo '</pre>';

	$where = "`MCID`='".$mcid."'";
	sqlupdate('members',$vararray, $where);	
// add corr rec if supporter rec is new and is a voo attendee  	
	$cflds = array();
//	echo "MCType: ".$vararray[MCType].", addflg: $addflg<br>";
	if ((stripos($vararray['MCType'],'VOO') != FALSE) && ($addflg == 'newrec')) {   
//    echo '<pre> vararray '; print_r($vararray); echo '</pre>';
    $cflds['CorrespondenceType'] = 'VOORegistration';
		$cflds['DateSent'] = date('Y-m-d');
		$cflds['MCID'] = $mcid;
		$cflds['SourceofInquiry'] = 'VOO';
		$cflds['Reminders'] = '';
		$cflds['Notes'] = "auto-added on VOO registration\n" . $fields['Notes'];
		//echo "<pre>donations array"; print_r($vararray); echo "</pre>";
		//echo "<pre>correspondence array"; print_r($fields); echo "</pre>";
		sqlinsert('correspondence', $cflds);
		$addflg = '';        
    }
	}

// get member record from ActiveMCID and display the info in update form
$sql = "SELECT * FROM `members` WHERE MCID = '$mcid'";
$res = doSQLsubmitted($sql);
//$res = readMCIDrow($mcid);
if ($res->num_rows == 0) {
  unset($_SESSION['VolActiveMCID']);    // invalid MCI
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
$e_mail=$row['E_Mail'];$mail=$row['Mail']; $notes=$row['Notes'];$lists=$row['Lists'];
$citieslist = createddown();
?>

<h3>Volunteer Information for <?=$mcid?></h3>

<script>
var reason = "";
var secLevel = "<?=$_SESSION['VolSecLevel']?>";
// validate form to ensure required fields are entered
function validateForm(theForm) {
	if (!validateLists()) return false;
	reason = "";
	// reason += validateEmpty(document.getElementById('fname'));
	reason += validateEmpty($("#fname")[0]);
	reason += validateEmpty($("#lname")[0]);
	reason += validateEmpty($('#lab1line')[0]);
	reason += validateCorrSal($('#corrsal')[0]);
	//reason += validateEmpty(theForm.AddressLine);
	reason += validateEmpty($("#CI")[0]);
	//reason += validateEmpty(theForm.State);
	//reason += validateEmpty(theForm.ZipCode);
	//reason += validateEmpty(theForm.MemStatus);
	reason += validateEmpty($("#mbrsel")[0]);    // member type select list
	//reason += validateEmpty(theForm.MemDate);
	//reason += validatePassword(theForm.pwd);
	reason += validateEmpty($("#EMA")[0]);       // emailaddress
	reason += validateEmpty($("#priphone")[0]);  // primary phone number  
	if (reason != "") {
  	alert("Some fields need attention:\n\n" + reason);
  	return false;
		}
	return true;
	}

function validateLists() {
  return true;
	var cnt = 0; var error = ""; 
	var memstatus = document.getElementsByName("inf[MCType]");
	var fld = $(".mlist:checkbox:checked").length();
	// console.log(fld);
	var fld = $(".mlist:checkbox:checked").length();
	if (fld == 0) {				
		alert("A volunteer must be registered on at least one mailing list.\n");
		return false;
		}
	return true;
  }

function validateCorrSal(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	// fld.value = document.mcform.FName.value;
  	fld.value = $("#fname").val();
  	return error;
    	}
    return error;  
	}
function validateEmpty(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	fld.style.background = '#F7645E';
  	if (reason == "") {
    	error = "Required field(s) have not been filled in.\n" }
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

$(document).ready(function() {
  $("#mbrsel").val("<?=$mctype?>");   // init drop down
  $('.rbs:input:radio[value="<?=$memstatus?>"]').prop("checked", true);
	$('.EMR:input:radio[value="<?=$e_mail?>"]').prop("checked", true);
	$('.MAIL:input[value="<?=$mail?>"]').prop("checked", true);
});

function setflds(theForm) {
	//alert("entered");
	var ffld = $("#fname").val();
	var lfld = $("#lname").val();
	//alert("ffld: "+ffld+", lfld: "+lfld);
	var ll = ffld + " " + lfld;
	ll = ll.substring(0,24);
	$("#lab1line").val(ll);
	$("#corrsal").val(ffld);
	return;
	}

function checkmbr(fld) {
	var mctype = fld.value;
	var mcval = mctype.substring(0,1);
  var memstatus = $('.rbs:checked', '#mcform').val();
  
	if (mcval != memstatus) {
		fld.value = "";
		alert("Please select a Mbr Type that corresponds with the selected Mbr Status");
		return false;
		}
	return true;
	}

function chgmemstatus() {
	//alert("chg memstat entered");
	$("#mbrsel").val("");
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
var errmsg = "";
var stripped = fld.value.replace(/[\(\)\.\-\ \/]/g, "");
if ((stripped.length != 10) || (isNaN(stripped))) { 
	errmsg += "Invalid phone number.  Please include the Area Code.\n";
	}

if (errmsg.length > 0) {
	errmsg += "\nValid formats: 123-456-7890 or 123 456 7890 or (123)456-7890 or 1234567890";
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
	document.getElementById("EMR1").checked = false;
	document.getElementById("EMR2").checked = true;
	return false;
	}
fld.style.background = 'White';
document.getElementById("EMR1").checked = true;
document.getElementById("EMR2").checked = false;
return true;
}
</script>

<form method="post"  name="mcform" id="mcform" class="form-horizontal" role="form" onsubmit="return validateForm(this)">
<div style="text-align: center"><button type="submit" form='mcform' class="updb btn btn-primary">Apply Update(s)</button></div>

<!-- Tab definition header  -->
<ul id="myTab" class="nav nav-tabs">
  <li class=""><a href="#home" data-toggle="tab">Home</a></li>
  <li class=""><a href="#lists" data-toggle="tab">Lists</a></li>
  <li class=""><a href="#time" data-toggle="tab">Time</a></li>
  <li class=""><a href="#courses" data-toggle="tab">Courses</a></li>
</ul>

<!-- Tab 1 Demographic information -->
<div id="myTabContent" class="tab-content">
<div class="tab-pane fade active in" id="home">
<div class="well">
<h4>Contact Information</h4>
<div class="row">
<input type="hidden" name="MCIDx" value="<?=$mcid?>">
<div class="col-sm-4">First: <input placeholder="First Name" autofocus type="text" name="inf[FName]" value="<?=$fname?>" id=fname onchange="setflds(document.mcform)"></div>
<div class="col-sm-4">Last: <input placeholder="Last Name" type="text" name="inf[LName]" value="<?=$lname?>" id=lname onchange="setflds(document.mcform)"></div>
</div>

<div class="row">
<div class="col-sm-4">Label Line: 
<input maxlength="24" placeholder="Label Line" name="inf[NameLabel1stline]" id=lab1line value="<?=$lab1line?>"></div>
<div class="col-sm-5">Correspondence Sal:<input placeholder="Correspondence Salutation" name="inf[CorrSal]" id=corrsal value="<?=$corrsal?>"></div>
</div>
<div class="row">
<div class="col-sm-4">Org: <input placeholder="Organization" name="inf[Organization]" value="<?=$org?>"></div>
<div class="col-sm-4">Addr Line: <input placeholder="Address Line" name="inf[AddressLine]" value="<?=$addr?>"></div>
</div>
<div class="row">
<div class="col-sm-4">City: <input id="CI" placeholder="City" name="inpCity" value="<?=$city?>" autocomplete="off" onblur="loadcity()"></div>
<input type=hidden name=inf[City] id=city value="<?=$city?>">
<div class="col-sm-2">State: <input id="ST" placeholder="State	" type="text" name="inf[State]" value="<?=$state?>" style="width: 50px; " /></div>
<div class="col-sm-3">Zip: <input id="ZI" type="text" name="inf[ZipCode]" value="<?=$zip?>" size="5" maxlength="5" style="width: 100px;"  placeholder="Zip" /></div>
</div>
<script src="js/bootstrap3-typeahead.js"></script>
<script>
function loadcity() {
	//alert("loadcity");
	var cv = $("#CI").val();
	var cva = cv.split(",");
	$("#CI").val(cva[0]);
	$("#city").val(cva[0]);
	$("#ST").val(cva[1]);
	$("#ZI").val(cva[2]);
	}
</script>

<script>
var citylist = <?=$citieslist?>;
$('#CI').typeahead({source: citylist})
</script>

<div class="row">
<div class="col-sm-4">Phone: <input type="text" name="inf[PrimaryPhone]" value="<?=$priphone?>" size="12" maxlength="12" style="width: 125px;" onchange="return ValidatePhone(this)"  placeholder="Primary Phone" id=priphone /></div>
<div class="col-sm-4">Email: <input id="EMA" placeholder="Email" onchange="ValidateEmail(this)" style="width: 200px;" name="inf[EmailAddress]" value="<?=$eaddr?>"></td></tr></div>
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
<input onchange="chgmemstatus()" class=rbs type="radio" name="inf[MemStatus]" value="0" checked/>0-Contact
<input onchange="chgmemstatus()" class=rbs type="radio" name="inf[MemStatus]" value="1" />1-Member
<input onchange="chgmemstatus()" class=rbs type="radio" name="inf[MemStatus]" value="2" />2-Vol.
<input onchange="chgmemstatus()" class=rbs type="radio" name="inf[MemStatus]" value="3" />3-Donor
</div>  <!-- col-sm-7 -->
</div>	<!-- row -->
<div class="row">
<div class="col-sm-5 col-sm-offset-1">
Mbr Type:<select id=mbrsel name="inf[MCType]" size="1" onChange="checkmbr(this)">
<option value=""></option>
<?php
loaddbselect('MCTypes');
?>
</select>
</div>  <!-- col-sm-5 -->
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">
Date Joined: <?=$memdate?>
</div>  <!-- col-sm-4 -->
<script>
function chkvalidemail(fld) {
	var val = fld.value;
	if (document.getElementById("EMA").value == "") {
		document.getElementById("EMR1").checked = false;
		document.getElementById("EMR2").checked = true;
		alert("NO Email Address Available!");
		}
	return true;
	}
</script>
<script>
function confirmNO(fld) {
	if (document.getElementById("EMR2").checked === true) {
		r = confirm("Volunteers must have an ACTIVE email address.\n\nAre you sure you want to set this flag to NO?");
		if (r == false) {
			document.getElementById("EMR1").checked = true;
			document.getElementById("EMR2").checked = false;
			return false;
			}
		}
	return true;		
}
</script>

<div class="col-sm-3">Email OK?: 
<input id="EMR1" class=EMR type="radio" name="inf[E_mail]" value="TRUE" onclick="return chkvalidemail(this)" />Yes
<input id="EMR2" class=EMR type="radio" name="inf[E_mail]" value="FALSE" onclick="return confirmNO(this)" />No
</div>
<div class="col-sm-3">Mail OK?: 
<input class=MAIL type="radio" name="inf[Mail]" value="TRUE" />Yes
<input class=MAIL type="radio" name="inf[Mail]" value="FALSE" />No
</div>
</div>  <!-- row -->

<h4>Notes</h4>
<div class="col-sm-6"><textarea name="inf[Notes]" rows="3" cols="60"><?=$notes?></textarea></div>
</div>  <!-- well -->
</div>  <!-- tab pane -->

<!-- lists tab -->
<div class="tab-pane fade" id="lists">
<div class="well">
<h4>Email Lists</h4>

<?php
$text = readdblist('EmailLists');
$listkeys['AUL'] = 'Active/Unlisted';
$listkeys += formatdbrec($text);
if ($_SESSION['VolSecLevel'] == 'voladmin') $listkeys['VolInactive'] = 'Vol Inactive';
//echo '<pre> keys '; print_r($listkeys); echo '</pre>';
foreach ($listkeys as $k => $v) {
	if (strlen($k) <= 1) continue;
//	echo "key: $k, value: $v<br />";
	if (stripos($lists, $k) !== FALSE) {
		echo "
		<input class=mlist type=checkbox name='mlist[]' value='$k' checked>$v<br>";
		}
	else {
		echo "
		<input class=mlist type=checkbox name='mlist[]' value='$k'>$v<br>";
		}
	//echo "key: $k, value: $v<br>";
	}

?>
</div>  <!-- well -->
</div>  <!-- tab pane -->

<!-- time tab -->
<div class="tab-pane fade" id="time">
<div class="well">
<h4>Volunteer Time Served</h4>
<?php
$sql = "SELECT * FROM `voltime` 
WHERE `MCID` = '$mcid' 
ORDER BY `VolDate` DESC;";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

if ($rowcnt > 0) {
echo "<b>Period Entry Count:</b> $rowcnt<br />";
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes

while ($r = $res->fetch_assoc()) {
$trows[] = "<tr><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td></tr>";
$vc = 'Uncategorized';
if (strlen($r['VolCategory']) > 0) $vc = $r['VolCategory'];
$totalvolhrs += $r['VolTime'];
$tothrs[$vc] += $r['VolTime'];
$totmiles += $r['VolMileage'];
	}
echo "<b>Total Miles Driven:</b> $totmiles,&nbsp;";
echo "<b>Total Volunteer Hours:</b> $totalvolhrs<br />";
echo "<b>Total Hours by Category:</b><br />";
if (count($tothrs) != 0) {
	foreach ($tothrs as $k => $v) echo "&nbsp;&nbsp;&nbsp;$k: $v<br />";
	}
echo "<b>Detail Records</b><br />";
echo '<table class="table-condensed">';
echo '<tr><th>Date</th><th>Vol Time</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>';
if (count($trows) != 0) foreach ($trows as $l) { echo $l; }
echo '</table>---- End of Report ----<br>';	
}
else {
	echo 'NO TIME RECORDS TO REPORT<br>';
}

?>
</div>  <!-- well -->
</div>  <!-- tab pane -->

<!-- courses tab 5 -->
<div class="tab-pane fade" id="courses">
<div class="well">
<h4>Courses Attended</h4>
<?php
$sql = "SELECT * FROM `voltime` 
WHERE `MCID` = '$mcid' 
	AND `VolCategory` = 'VolEduc' 
ORDER BY `VolDate` DESC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

if ($rowcnt > 0) {
echo "<b>Entry Count:</b> $rowcnt<br />";
// table: voltime: CourseId,CourseDate,CourseDuration,CourseName,CourseNotes

while ($r = $res->fetch_assoc()) {
//	echo '<pre> ed rec '; print_r($r); echo '</pre>';
//	list($agency, $courseid) = explode(':',$r[CourseId]);
	list($courseid,$notes) = explode('/',$r['VolNotes']);
	list($agency, $cid) = explode(':', $courseid);
	$erows[] = "<tr><td>$agency<td>$cid</td><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$notes</td></tr>";
$totaledhrs += $r['VolTime'];
	}
echo "<b>Total Educ. Hours:</b> $totaledhrs<br />";
echo "<b>Detail Records</b><br />";
echo '<table class="table-condensed">';
echo '<tr><th>Agency</th><th>CourseId</th><th>CourseDate</th><th>Dur.</th><th>Notes</th></tr>';
if (count($erows) != 0) foreach ($erows as $l) { echo $l; }
echo '</table>==== END OF REPORT ====<br>';	
}
else {
	echo 'NO COURSE RECORDS TO REPORT<br>';
	}
?>

</div>  <!-- well -->
</div>  <!-- tab pane -->
</div>  <!-- tab content -->
<!-- end all tab definitions -->

<input type="hidden" name="action" value="update">
<input type="hidden" name="addflg" value="<?=$addflg?>">
</form>
</div>
<hr>
</div>
<br /><br />';
</body>
</html>
