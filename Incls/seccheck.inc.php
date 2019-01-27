<script src="js/bootstrap-session-timeout.js"></script> 
<script>
$(document).ready(function() { 
  $.sessionTimeout({
      title: 'SESSION TIMEOUT ALERT',
      message: '<h3>Your session is about to expire.</h3>',
      keepAlive: false,
      logoutUrl: 'indexsto.php',
      redirUrl: 'indexsto.php',
      warnAfter:  15*60*1000,
      redirAfter: 20*60*1000,
      countdownMessage: 'Time remaining:',
      countdownBar: true,
      countdownSmart: true,
      showButtons: false
  });
});
</script>

<?php
// check if there is an active session
if (isset($_SESSION['VolSessionActive'])) {
  return;
  }
// if url is a report exit
if (preg_match("/.*\/rpt.*\.(php|html|htm)$/i",$_SERVER['SCRIPT_NAME'])) {
  echo '<h3>SESSION NO LONGER ACTIVE</h3>';
  exit;
  }

// if not display login form
?>
<script>
function checkform(theForm) {
	var reason = "";
  reason += validateUserID(theForm.userid);	
	reason += validatePassword(theForm.password);
	if (reason != "") {
  	alert("Some fields need correction:\n" + reason);
  	return false;
		}
	return true;
	}

function validateUserID(fld) {
	var error="";
	var tfld = trim(fld.value); 		// value of field with whitespace trimmed off
  if (fld.length == 0) {
	  fld.style.background = '#F7645E';
    error = 'User ID not entered.\n';
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
  else if (fld.value.length < 6) {
   	error = "The password is the wrong length. \n";
    fld.style.background = '#F7645E';
    }
  else if (illegalChars.test(fld.value)) {
    error = "The password contains illegal characters.\n";
    fld.style.background = '#F7645E';
    } 
  else if (!((fld.value.search(/(a-z)+/)) && (fld.value.search(/(0-9)+/)))) {
    error = "The password must contain at least one number.\n";
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
	<h1>Volunteer System</h1>
	<form action="admin.php" method='POST' name="form" class="form-signin" onsubmit="return checkform(this)">
	<h2 class="form-signin-heading">Please sign in</h2>
	<input type="text" class="input-block-level" placeholder="User ID" autofocus name="userid" value="" autocomplete="off">
	<input type="text" class="input-block-level" placeholder="Password" name="password" value = "" autocomplete="off">
	<button class="btn btn-default btn-small" name='action' value='login' type="submit">Sign in</button>
	</form>
	<a href="admpasswordupd.php"><p>(Change Password)</p></a>
</div> <!-- /container -->
</body>
</html>
