<!DOCTYPE html>
<html>
<head>
<title>Send Mail List Sender</title>
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
include 'Incls/datautils.inc.php';

echo "<html><head><title>Send Mail Confirmation</title>";

$emarrayin = $_REQUEST['tokey'];

$fromMCID = $_SESSION['VolSessionUserMCID'];

// delete senders email address from list
//echo '<pre>emarrayin before '; print_r($emarrayin); echo '</pre>';
$from = $_REQUEST['sender'];
foreach ($emarrayin as $k => $v) {
	if (stripos($v, $from) !== FALSE) {
		//echo "found it at $k<br />";
		unset($emarrayin[$k]);
		}
	}
// echo '<pre>emarrayin after '; print_r($emarrayin); echo '</pre>';

$subject = $_REQUEST['subject'] . '  (' . $fromMCID . ')';
$message = $_REQUEST['message'];

//$timetosend = date('g:00 A', strtotime("now + 60 minutes"));
echo "<div class=\"container\">
<h1>Send Mail Confirmation&nbsp;&nbsp;<a href=\"admin.php\" class=\"btn btn-primary\"><strong>(RETURN)</strong></a></h1>
<h4>The message has been queued for sending which will is scheduled every quarter hour.</h4>
<p>The message subject and text will be sent to all of email reciepents.  A log record is produced for each batch. Progress may be reviewed by looking at &apos;Reports->Review Mail Log&apos;.  This report will tell you when a message is being sent and the number of recipients remaining or list the recipients and the message when it has been completed.</p><br>
<a class=\"btn btn-warning btn-xs\" target=\"_blank\" href=\"rptmaillogviewer.php\">Review Mail Log</a>
";

$tce = count($emarrayin); 
$sll = strlen($subject);
$sl = isset($_REQUEST['subject'])? $sll : 0;

//echo "tce: $tce, sl: $sl<br>";
if (($tce == 0) || ($sl == 0)) {
print<<<errorMsg
<h3>ERROR: No addresses in the "TO" list OR the subject line is empty.</h3>Please correct and resubmit.<br><br>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

errorMsg;
exit;
	}
// write info into MailQ for cron sender to process
// first create file names: one for list, one for message
$prefix = date('YmdHis');
$listname = "../MailQ/$prefix.$tce.LIST";
$msgname =  "../MailQ/$prefix.$tce.MSG";
//create message string for output
$msgarray[] = $from; $msgarray[] = $subject; $msgarray[] = $message;
$listval = "Original list size: $tce";
//echo "list: $listname, msg: $msgname, lock: $lockname<br>";
sort($emarrayin);
file_put_contents($listname, implode("\n", $emarrayin));
file_put_contents($msgname, implode("\n", $msgarray));

print<<<pageBody
</div>
</body>
</html>
pageBody;
?>