<!DOCTYPE html>
<html>
<head>
<title>Add New Admin User</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$recno = isset($_REQUEST['recno']) ? $_REQUEST['recno'] : "";
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : "";
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";
$mcid = isset($_REQUEST['mcid']) ? $_REQUEST['mcid'] : "";
$role = isset($_REQUEST['role']) ? $_REQUEST['role'] : "";
$notes = isset($_REQUEST['notes']) ? $_REQUEST['notes'] : "";

if ($_SESSION['VolSecLevel'] != 'voladmin') {
		echo '<div class="container"><h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to maintain these lists.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded 			to &apos;voladmin&apos; in order to modify any lists.</p></div>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
		</body></html>';
		exit;
		}
	
if ($action == "delete") {
	//echo "Delete record number $recno<br>";
	$sql = "DELETE FROM `adminusers` WHERE `SeqNo` = '$recno'";
	$res = doSQLsubmitted($sql);
	}

if ($action == "addnew") {
	//echo "Add new record number for user: $userid, password: $password, role: $role<br>";
	$flds[UserID] = $userid;
	$flds[Password] = $password;
	$flds[MCID] = $mcid;
	$flds[Role] = $role;
	$flds[Notes] = $notes;
	$res = sqlinsert('adminusers', $flds);
	}

print <<<pagePart1
<div class="container">
<h3>Add New Administrative User</h3>
<p>Adds new admin user to the registration database.  User id is the email address of the user.  The role that is to be assigned is in the dropdown.  Both of these fields are required.  The default password provided is 'raptor' but any may be specified.  The password can be updated to a personal password by the user when they log in.</p>
<p>Please note that a new userid is needed for each role.</p>

<script>
function checkflds(form) {
	//alert("validation entered");
	var errcnt = 0;
	if (form.userid.value == "") errcnt +=1;
	if (form.password.value == "") errcnt += 1;
	if (form.mcid.value == "") errcnt += 1;
	if (form.role.value == "") errcnt += 1;
	if (errcnt > 0) {
		alert ("A required field is missing.");
		return false;
		}
	var tfld = trim(form.userid.value);  // value of field with whitespace trimmed off
	var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
	var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ; 
	if (!emailFilter.test(tfld)) {  		//test email for illegal characters
		errcnt += 1;
		} 
	if (form.userid.value.match(illegalChars)) {
		errcnt += 1;
		} 
	if (errcnt > 0) {
		alert("Userid is an illegal or badly formed email address.  Please re-enter.");  
		return false;
		}
	return true;
	}
	
function trim(s)
	{
  return s.replace(/^\s+|\s+$/, '');
	}

</script>

<form class="form" name="addform" action="voladdnewuser.php" onsubmit="return checkflds(this)">
New User ID: <input type="text" name="userid" placeholder="Email Address">
Password: <input type="text" name="password" value="raptor">
MCID: <input type="text" name="mcid" value=""><br>
Role: <select name="role">
<option value="">Select a role for the User</option>
<option value="voladmin">Volunteer Admin</option>
<option value="voluser">Volunteer User</option>
</select><br />
Notes:<br /><textarea name="notes" rows="3" cols="50"></textarea><br />
<input type="hidden" name="action" value="addnew">
<input type="submit" name="submit" value="Add New">
</form>
<br />
<!-- <a class="btn btn-primary" href="volinfotabbed.php">RETURN</a> -->
<hr width="50%"><h4>Delete Existing</h4>
pagePart1;

// list exising entries to allow delete of individual rows from DB

$sql = "SELECT * FROM adminusers ORDER BY `MCID` ASC;";
$res = doSQLsubmitted($sql);
echo "<table class=\"table-condensed\">";
echo "<tr><th>Delete</th><th>User ID</th><th>Password</th><th>MCID</th><th>Role</th><th>Notes</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>user: "; print_r($r); echo "</pre>";
	echo "<tr><td align=\"center\"><a href=\"voladdnewuser.php?action=delete&recno=$r[SeqNo]\"><img src=\"config/b_drop.png\" alt=\"DELETE\" /></a></td><td>$r[UserID]</td><td>************</td><td>$r[MCID]</td><td>$r[Role]</td><td>$r[Notes]</td></tr>";
	}
echo "</table></div>";

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
