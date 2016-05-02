<!DOCTYPE html>
<html>
<head>
<title>Mail Log Viewer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();

//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == 'delete') {
  $fname = $_REQUEST['fn'];
  //echo "delete action detected for: $fname<br>";
  unlink('../MailQ/' . $fname . '.LIST');
  unlink('../MailQ/' . $fname . '.MSG');
  }
echo '<div class="container">
<h3>Mail Log Viewer
&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>
<p>Select the completed mail entry from the dropdown list. The lastest is at the top.</p>';

$sql = "SELECT * FROM `maillog` ORDER BY `LogID` DESC;";
$res = doSQLsubmitted($sql);
echo '
<table border=0><tr><td>
<form action="rptmaillogviewer.php" method="post"  class="form">
<select name="logentry" onchange="this.form.submit()">
<option value=""></option>';
while ($r = $res->fetch_assoc()) {
	echo "<option value=\"$r[LogID]\">$r[LogID]: $r[DateTime]</option>";	
	}
echo '<input type="hidden" name="action" value="view">
</form>
</td><td>';

if ($action == 'del') {
	if ($_SESSION['VolSecLevel'] != 'voladmin') {
		echo '<h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to maintain these lists.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded 			to &apos;voladmin&apos; in order to modify any lists.</p>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
		</body></html>';
		exit;
		}
	$recno = $_REQUEST['recno'];
	$sql = "DELETE FROM `maillog` WHERE `LogID` = '$recno';";
	$rows =doSQLsubmitted($sql);
	echo "Deleted record: $recno&nbsp;&nbsp;";
	$recno -= 1; 
	// echo "recno: $recno<br />";
	if ($recno > 0) {
		echo '<a class="btn btn-success" href="rptmaillogviewer.php?action=view&logentry='.$recno.'">View Next</a>'; 
		}
	echo '</td></tr></table></div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>';
	exit;
	}

if ($action == 'view') {
	$recno = $_REQUEST['logentry'];
	$sql = "SELECT * FROM `maillog` WHERE `LogID` = '$recno';";
	$res = doSQLsubmitted($sql);
	$r = $res->fetch_assoc();
	// echo '<pre>'; print_r($r); echo '</pre>';
	$recno = $r[LogID]; $datetime = $r[DateTime]; $user = $r[User]; 
	$seclevel = $r[VolSecLevel]; $mailtext =  $r[MailText];
print <<<recOut
	<a class="btn btn-danger" href="rptmaillogviewer.php?action=del&recno=$recno">DELETE</a></td></tr></table>
	Record Number: $recno<br />
	Date/Time: $datetime<br />
	User: $user<br />
	Security Level: $seclevel<br />
	Mail Text:<br />
	$mailtext
	</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

recOut;
exit(0);
	}
echo '
<script> 
function confdel() {
  if (confirm("Cancel message from send queue?\\n\\nAre you sure?")) return true;;
  return false;
  }
</script>
';	
// display mail sending quque from MailQ dir
$mq = scandir('../MailQ');
if (count($mq) > 2) {
  echo '</td><tr><td><br>
<h3>List of mail message(s) being sent or queued.</h3>
The following is a list of the subject line of messages either being sent or are in the send queue waiting for processing.<br>
<a class="btn btn-warning btn-xs" href="rptmaillogviewer.php">REFRESH</a><br>
';
  sort($mq);
  //echo '<pre> mail queue '; print_r($mq); echo '</pre>';
  foreach ($mq as $v) {
    if (substr($v,0,1) == '.') continue;
    $fpn = '../MailQ/' . $v;
    list($msgname, $type) = explode('.', $v);
  
    if ($type == 'LIST') {
      $listmsg = file($fpn);
      $listcount = count($listmsg);
      $output = '';
      continue;
      }
    if ($type == 'LOCK') {
      $output .= "IN PROCESS: ";
      continue;
      }
    if ($type == 'MSG') {
      $msg = file($fpn); $subj = rtrim($msg[1]);
      if (strlen($output) > 0) { $output .= "$subj ($listcount remaining)"; }
      else { $output .= "$subj ($listcount in list)&nbsp;&nbsp;<a onclick=\"return confdel()\" href=\"rptmaillogviewer.php?action=delete&fn=$msgname\">(CANCEL)</a><br>"; }
      }
    echo $output;
    }
  echo '<br>';
  }
?>
</td></tr></table>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
