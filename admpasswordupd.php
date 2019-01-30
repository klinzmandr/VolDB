<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Change Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : "";
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";
$password1 = isset($_REQUEST['password1']) ? $_REQUEST['password1'] : "";
$password2 = isset($_REQUEST['password2']) ? $_REQUEST['password2'] : "";
if ($action == "update") {
	//echo "apply new password to database<br>";
	$sql = "UPDATE `adminusers` SET `UserID`='$userid', `Password`='$password1' WHERE `UserID`='$userid' AND `Password`='$password'";
	//echo "SQL: $sql<br>";
	$res = doSQLsubmitted($sql);	// do update
	
	$sql = "SELECT * FROM `adminusers` WHERE `UserID`='$userid' AND `Password`='$password1'";
	$res = doSQLsubmitted($sql);
	$nbrofrows = $res->num_rows;
	echo "<div class=\"container\">";
	$r = $res->fetch_assoc();
	//echo "number of rows: $nbrofrows<br>";
	//echo "<pre>user: "; print_r($r); echo "</pre>";
	if ($nbrofrows == 0) {
		echo "ERROR: userid and/or password provided is not valid<br>";
		}
	else {
		echo "<h4>New Password applied to userid $userid</h4>";
		echo "<a class=\"btn btn-primary\" href=\"admin.php\">RETURN</a>";
		}
	echo "</div>";
	}

print <<<pagePart1
<div class="container">
<h3>Change User Password</h3>
<p>Update existing password with the new one supplied.</p>
<script>
function checkflds(form) {
	var errcnt = 0;
	if (form.userid.value == "") errcnt += 1;
	if (form.password.value == "") errcnt += 1;
	if (form.password1.value == "") errcnt += 1;
	if (form.password2.value == "") errcnt += 1;
	if (errcnt > 0) {
		alert("A required field has not been supplied");
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
	if (form.password1.value != form.password2.value) {
		alert("New password and confirmation password do not match.");
		return false;
		}
	return true;
	}
	
function trim(s)
	{
  return s.replace(/^\s+|\s+$/, '');
	}

</script>
<form action="admpasswordupd.php" method="post"  class="form" onsubmit="return checkflds(this)">
User ID (email address): <input type="text" name="userid" autocomplete="off"><br />
Current Password: <input type="text" name="password" value="" autocomplete="off"><br />
New Password: <input type="text" name="password1" value="" autocomplete="off"><br />
Confirm New Password: <input type="text" name="password2" value="" autocomplete="off"><br />
<input type="hidden" name="action" value="update">
<input type="submit" name="submit" value="Submit">
</form>
<br /><br /><a class="btn btn-primary" href="admin.php">CANCEL</a>
</div>
pagePart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
