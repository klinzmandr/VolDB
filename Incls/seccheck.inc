<?php
date_default_timezone_set('America/Los_Angeles');

$_SESSION['VolSessionLength'] = 15*60;		// session length in seconds
$sessexpiration = isset($_SESSION['VolSessionTimer']) ? $_SESSION['VolSessionTimer'] : 0;
$currenttime= time();
//echo "current: $currenttime, sessexpiration: $sessexpiration<br>";
if ($currenttime <= $sessexpiration) {			// session is live, extend it
	$_SESSION['VolSessionTimer'] = $currenttime + $_SESSION['VolSessionLength'];
	//echo "time extended<br>";	
	}
else {
print <<<loginPage
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dave Klinzman" >
    <title>Membership Login</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
  </head>

<body>
<script>
function checkform(theForm) {
	var reason = "";
  reason += validateEmail(theForm.userid);	
	reason += validatePassword(theForm.password);
	if (reason != "") {
  	alert("Some fields need correction:\n" + reason);
  	return false;
		}
	return true;
	}

function validateEmail(fld) {
	var error="";
	var tfld = trim(fld.value); 		// value of field with whitespace trimmed off
	var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
	var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ; 
  if (fld.value == "") {
	  fld.style.background = '#F7645E';
    error = 'You didn't enter an email address.\n';
    }
  else if (!emailFilter.test(tfld)) {  //test email for illegal characters
  	fld.style.background = '#F7645E';
    error = "Please enter a valid email address.\n';
    } 
  else if (fld.value.match(illegalChars)) {
    fld.style.background = '#F7645E';
    error = "The email address contains illegal characters.\n";
    } 
  else {
  	fld.style.background = 'White';
    }
  return error;
	}
}

function validatePassword(fld) {
  var error = "";
  var illegalChars = /[\W_]/; // allow only letters and numbers 
  if (fld.value == "") {
  	fld.style.background = '#F7645E';
    error = "You didn't enter a password.\n";
    	} 
  else if (fld.value.length < 7) {
   	error = "The password is the wrong length. \n";
    fld.style.background = '#F7645E';
    }
  else if (illegalChars.test(fld.value)) {
    error = "The password contains illegal characters.\n";
    fld.style.background = '#F7645E';
    } 
  else if (!((fld.value.search(/(a-z)+/)) && (fld.value.search(/(0-9)+/)))) {
    error = "The password must contain at least one numeral.\n";
    fld.style.background = '#F7645E';
    } 
  else {
    fld.style.background = 'White';
    }
  return error;
	}   

function trim(s)
	{
  return s.replace(/^\s+|\s+$/, '');
	}
</script>

<div class="container">
	<h1>Volunteer Database</h1>
	<form action="admin.php" method='POST' name="form" class="form-signin" onsubmit="return checkform(this)">
	<h2 class="form-signin-heading">Please sign in</h2>
	<input type="text" class="input-block-level" placeholder="Email address" autofocus autocomplete="off" name="userid" value="">
	<input type="text" class="input-block-level" placeholder="Password" autocomplete="off" name="password" value = "">
	     <button class="btn btn-default btn-small" name='action' value='login' type="submit">Sign in</button>
	</form>
	<a href="admpasswordupd.php"><p>(Change Password)</p></a>
</div> <!-- /container -->
</body>
</html>
loginPage;
exit;
}

?>