<!DOCTYPE html>
<html>
<head>
<title>Send Mail List Chooser</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$seclevel = $_SESSION['VolSecLevel'];
print <<<pagePart1
<script type="application/javascript">
function checkAll(chk,fld)  {
for(var i=0; i < fld.length; i++) {
	if(chk.checked ) { fld[i].checked = true; }
	else { fld[i].checked = false ; }
	} 
}
</script>
<script>
function confirmbox(fld) {
	var counter = 0;
	for (var i=0; i<fld.length; i++) {	
	if (fld[i].checked) { 
		counter += 1; 
		} 
	}
	if (counter == 0) { 
		alert("No lists were selected."); 
		return false; 
		}
	}
</script>
<div class="container">
<h3>Available Mailing Lists</h3>
pagePart1;
$lists = readdblist('EmailLists');
$lines = explode("\n",$lists);
echo '<script>
function chkem() {
var l = EmailAddr.length;
if (l == 0) {
	alert("No FROM email address configured.\nContact the system administrator.");
	return false;
	}
return true;
}
</script>';
echo '<table class="table">';
echo "<form method=post name=\"sndform\" action=\"sendemailvols.php\" onsubmit='return confirmbox(document.sndform[\"tokey[]\"])'>";

echo '<tr>
<th width="80">
<input type="submit" name="submit" value="Submit" onclick="return chkem()"></th>
<th>List Name</th>';
if ($_SESSION['VolSecLevel'] == 'voladmin')
 echo '<td>
	<a class="btn btn-warning" href="sendemailvols.php?tokey[]=AUL">Send to Active/Unlisted</a> 
 <a class="btn btn-danger" href="sendemailvols.php?tokey[]=VolInactive">Send to Inactives</a>
 </td>';
echo '</tr>';
echo "
<tr><td><input type=\"checkbox\" name=\"chkr\" 
onchange='checkAll(document.sndform.chkr, document.sndform[\"tokey[]\"])'></td><td>&nbsp;<b>Check/Uncheck All</b><br></td></tr>";

foreach ($lines as $l) {
	$l = rtrim($l);
	if (strlen($l) <= 0) { continue; } 
	if (substr_compare($l,'//',0,2) == 0) { continue; }
	list($tla, $desc) = explode(":",$l);
	echo "<tr><td><input type=\"checkbox\" name=\"tokey[]\" value=\"$tla\" /></td><td>$desc ($tla)</td></tr>";
	}
echo '
<tr><td></td><td></td></tr>
</form></table>';
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
