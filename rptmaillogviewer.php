<!DOCTYPE html>
<html>
<head>
<title>Mail Log Viewer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();

//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
//<a href=\"rptmaillogviewer.php?action=view&ext=LIST&fn=$delname\">(View list)</a>&nbsp;
if ($action == 'view') {
  $fname = '../MailQ/' . $_REQUEST['fn']. '.' .$_REQUEST['ext'];
  $file = file($fname);
  echo '<div class="container"><a class="btn btn-warning btn-xs" href="rptmaillogviewer.php">CONTINUE</a><br>';
  foreach ($file as $l) {
    echo $l . '<br>';
    }
  echo '</div></body></html>';
	exit;
  }

if ($action == 'delete') {
  $fname = '../MailQ/' . $_REQUEST['fn'];
  //echo "delete action detected for: $fname<br>";
  //unlink('../MailQ/' . $fname . '.LIST');
  //unlink('../MailQ/' . $fname . '.MSG');
  if (file_exists($fname . '.LIST')) unlink($fname. '.LIST');
  if (file_exists($fname . '.MSG')) unlink($fname . '.MSG');
  if (file_exists($fname . '.LOCK')) unlink($fname . '.LOCK');
  }
echo '<div class="container">
<h3>Mail Log Viewer
&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>
<p>Select the completed mail entry from the dropdown list. The lastest is at the top.</p>
<script> 
function confdel() {
  if (confirm("Delete message from queue?\\n\\nAre you sure?")) return true;;
  return false;
  }
</script>
';	

$sql = "SELECT * FROM `maillog` ORDER BY `LogID` DESC;";
$res = doSQLsubmitted($sql);
echo '
<table border=1><tr><td>
<form action="rptmaillogviewer.php" method="post"  class="form">
<select name="logentry" onchange="this.form.submit()">
<option value=""></option>
<option value="old">30 days old or more</option>';
while ($r = $res->fetch_assoc()) {
	echo "<option value=\"$r[LogID]\">$r[LogID]: $r[DateTime]</option>";	
	}
echo '<input type="hidden" name="action" value="viewdb">
</form>
</td><td>';

if ($action == 'del') {
	if ($_SESSION['VolSecLevel'] != 'voladmin') {
		echo '<h2>Invalid Security Level</h2>
		<h4>You do not have the correct authorization to maintain these lists.</h4>
		<p>Your user id is registered with the security level of &apos;voluser&apos;.  It must be upgraded 			to &apos;voladmin&apos; in order to modify any lists.</p>
		</body></html>';
		exit;
		}
	$recno = $_REQUEST['recno'];
	$sql = "DELETE FROM `maillog` WHERE `LogID` = '$recno';";
	if ($recno == 'old') {
	  $deldate = date("Y-m-d", strtotime("now - 30 days"));
	  // echo "deldate: $deldate<br>";
	  $sql = "DELETE FROM `maillog` WHERE `DateTime` < '$deldate'";
    }
  // echo "sql: $sql<br>";
	$rows = doSQLsubmitted($sql);
	echo "Records Deleted: $rows&nbsp;&nbsp;";
	echo '<a class="btn btn-success" href="rptmaillogviewer.php">CONTINUE</a></td></tr></table></div>  <!-- container -->
</body></html>';
	exit;
	}

if ($action == 'viewdb') {
	$recno = $_REQUEST['logentry'];
	$sql = "SELECT * FROM `maillog` WHERE `LogID` = '$recno';";
	if ($recno == 'old') {
	  $deldate = date("Y-m-d", strtotime("now - 30 days"));
	  // echo "deldate: $deldate<br>";
	  $sql = "Select * FROM `maillog` WHERE `DateTime` < '$deldate'";
    }
	$res = doSQLsubmitted($sql);
	$r = $res->fetch_assoc();
	$rc = $res->num_rows;
	if ($recno == 'old') {
	  echo "Delete $rc records?  <a class=\"btn btn-danger\" onclick=\"return confdel()\" href=\"rptmaillogviewer.php?action=del&recno=$recno\">DELETE</a>";
	  exit;
    }
	// echo '<pre>'; print_r($r); echo '</pre>';
	$recno = $r[LogID]; $datetime = $r[DateTime]; $user = $r[User]; 
	$seclevel = $r[SecLevel]; $mailtext =  $r[MailText];
print <<<recOut
	<a class="btn btn-danger" onclick="return confdel()" href="rptmaillogviewer.php?action=del&recno=$recno">DELETE</a></td></tr></table>
	Record Number: $recno<br />
	Date/Time: $datetime<br />
	User: $user<br />
	Security Level: $seclevel<br />
	Mail Text:<br />
	$mailtext
	</div>  <!-- container -->
</body>
</html>

recOut;
exit(0);
	}
	
// display mail sending quque from MailQ dir
$mq = scandir('../MailQ');
//echo '<pre> mail queue '; print_r($mq); echo '</pre>';
if (count($mq) > 2) {
  echo '</td><tr><td><br>
<h3>List of mail message(s) being sent or queued.</h3>
The following is a list of the subject line of messages either being sent or are in the send queue waiting for processing.<br>
<a class="btn btn-warning btn-xs" href="rptmaillogviewer.php">REFRESH</a><br>
';
  sort($mq);
//  echo '<pre> mail queue '; print_r($mq); echo '</pre>';
  foreach ($mq as $v) {
    if (substr($v,0,1) == '.') continue;
    $fpn = '../MailQ/' . $v;
    $delname = $msgname. '.' . $size;
    list($msgname, $size, $type) = explode('.', $v);
  
    if ($type == 'LIST') {
      $listcount = $size;
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
      else { $output .= "$subj ($listcount in list)&nbsp;&nbsp;
<a href=\"rptmaillogviewer.php?action=view&ext=LIST&fn=$delname\">(View list)</a>&nbsp;
<a href=\"rptmaillogviewer.php?action=view&ext=MSG&fn=$delname\">(View Msg)</a>&nbsp;      
<a onclick=\"return confdel()\" href=\"rptmaillogviewer.php?action=delete&fn=$delname\">(CANCEL)</a><br>"; }
      }
    echo $output;
    }
  echo '<br>';
  }
?>
</td></tr></table>
</div>  <!-- container -->
</body>
</html>
