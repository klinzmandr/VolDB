<?php
session_start();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$recno = isset($_REQUEST['SeqNo']) ? $_REQUEST['SeqNo'] : "";
$flds = isset($_REQUEST['flds']) ? $_REQUEST['flds'] : '';
if ($_SESSION['VolSecLevel'] != 'voladmin') {
		echo '
		<div class="container"><h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to maintain these lists.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded to &apos;voladmin&apos; in order to modify any lists.</p>
		<a href="admin.php"><h3>RETURN</h3></a>
		</div>
		</body></html>';
		exit;
		}
	
?>
<!DOCTYPE html>
<html>
<head>
<title>Add/Update User</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
$("document").ready(function() {
  $("#X").fadeOut(5000);
  $("#addnew").hide();
$("#addnewbtn").click (function() { 
  $("#addnew").toggle();
  });
// does case insensitive search in 'btnALL'
$.extend($.expr[":"], {
  "containsNC": function(elem, i, match, array) {
  return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
  }
  });

$("#filterbtn2").click(function() {
  $("#filter1").val("");
  $('tr').show();
  chgFlag = 0;
  });
  
$("#filter1").keyup(function() {
  var filter = $("#filter1").val();
  if (filter.length) {
    // alert("filter button clicked:" + filter);
    $('tr').hide().filter(':containsNC('+filter+')').show();
    $("#head").show();
    chgFlag = 0;
    return;
    }
  $('tr').show();
  chgFlag = 0;
  });
});
</script>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/seccheck.inc.php';

if ($action == "delete") {
	$sql = "DELETE FROM `adminusers` WHERE `SeqNo` = '$recno'";
	$res = doSQLsubmitted($sql);
	echo '<h3 style="color: red; " id="X">Delete Completed.</h3>';
	}

if ($action == "addnew") {
	//echo "Add new record number for user: $userid, password: $password, role: $role<br>";
	$res = sqlinsert('adminusers', $flds);
	echo '<h3 style="color: red; " id="X">User Record Added.</h3>';
	}
?>
<div class="container">
<h3>User Administration</h3>
<button id="addnewbtn">Add/Update User Record</button>
<b>Filter:</b><input id="filter1" value="">
<!-- <button id="filterbtn1">Apply filter</button> -->
<button id="filterbtn2">Show All</button>
<div id="addnew">
<p>Adds new admin user to the registration database.  User id is the email address of the user.  The role that is to be assigned is in the dropdown.  Both of these fields are required.  The default password provided is 'raptor' but any may be specified.  The password can be updated to a personal password by the user when they log in.</p>
<p>Please note that a new userid is needed for each role.</p>

<script>
function checkflds(form) {
	//alert("validation entered");
	var errcnt = 0;
	// if (form.userid.value == "") errcnt +=1;
	if ($("#uid").val().length == 0) errcnt +=1;
	if ($("#pwd").val().length == 0) errcnt += 1;
	if ($("#role").val().length == 0) errcnt += 1;
	if ($("#mcid").val().length == 0) errcnt += 1;
	if (errcnt > 0) {
		alert ("A required field (UserID, password, role or MCID) is missing.");
		return false;
		}
	var tfld = trim(form.userid.value);  // value of field with whitespace trimmed off
	return true;
	}
	
function trim(s)
	{
  return s.replace(/^\s+|\s+$/, '');
	}

</script>
<script>
function chkconf() {  
  if (confirm("Confirm deletion by clicking OK.")) { return true;  }
  return false;
}
</script>
<form action="voladminaddnewuser.php" method="post"  class="form" name="addform" onsubmit="return checkflds(this)">
New User ID: <input id="uid" type="text" name="flds[UserID]" placeholder="User Id">
Password: <input id="pwd" type="text" name="flds[Password]" value="raptor"><br>
Role: <select id="role" name="flds[Role]">
<option value="">Select a role for the User</option>
<!-- <option value="admin">MbrDB Admin</option>
<option value="user">MbrDB User</option>
<option value="devuser">MbrDB User w/EDI</option> -->
<option value="voladmin">Volunteer Admin</option>
<option value="voluser">Volunteer User</option>
</select>

MCID: <input id=mcid type="text" name="flds[MCID]" placeholder="MCID">
<br>Notes:<br><textarea name="flds[Notes]" rows="3" cols="50"></textarea><br />
<input type="hidden" name="action" value="addnew">
<input type="submit" name="submit" value="Add New">
</form>
<br />
<!-- <a class="btn btn-primary" href="index.php">RETURN</a> -->
<hr width="50%"><h4>Existing Users</h4>
</div>  <!-- addnew div -->

<?php
$sql = "select * from `adminusers` 
WHERE `Role` LIKE 'vol%'
ORDER BY `Role` ASC, `UserID` ASC";
$res = doSQLsubmitted($sql);
echo "<table border=1 class=\"table\">";
echo "<tr id=\"head\"><th>Action</th><th>Role</th><th>User ID</th><th>MCID</th><th>Password</th><th>Notes</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>user: "; print_r($r); echo "</pre>";
	echo "<tr>
<td>
<a href=\"voladminedituser.php?SeqNo=$r[SeqNo]\"<span title=\"EDIT\" class=\"glyphicon glyphicon-pencil\" style=\"color: blue; font-size: 15px\"></span></a>
<a href=\"voladminaddnewuser.php?SeqNo=$r[SeqNo]&action=delete\" onclick=\"return chkconf()\" <span title=\"DELETE\" class=\"glyphicon glyphicon-trash\" style=\"color: blue; font-size: 15px\"></span></a>

</td>
<td>$r[Role]</td><td>$r[UserID]</td><td>$r[MCID]</td><td>$r[Password]</td><td>$r[Notes]</td>";

	}
echo "</table><br>==== END OF LIST ===<br></div>";

?>
</div>
</body>
</html>
