<!DOCTYPE html>
<html>
<head>
<title>Send Mail List Sender</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php 
session_start();

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

function clickable($string) {
	// if anchors already exist - don't translate
	if (stripos($string,'<a ') !== FALSE) return($string); 
  // make sure there is an http:// on all URLs
  $string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$string);
  // make all URLs links
  $string = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A target=\"_blank\" href=\"$1\">$1</A>",$string);
  // make all emails hot links
  $string = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A HREF=\"mailto:$1\">$1</A>",$string);
  return $string;
	}

echo "<html><head><title>Send Mail Confirmation</title>";

$emarrayin = $_REQUEST['tokey'];

// delete senders email address from list
//echo '<pre>emarrayin before '; print_r($emarrayin); echo '</pre>';
//$from = $_SESSION['VolSessionUser'];
$from = $_REQUEST['sender'];
foreach ($emarrayin as $k => $v) {
	if (stripos($v, $from) !== FALSE) {
		//echo "found it at $k<br />";
		unset($emarrayin[$k]);
		}
	}
//echo '<pre>emarrayin after '; print_r($emarrayin); echo '</pre>';

foreach ($emarrayin as $v) {		// unpack the mcid and email address values into diff arrays
	//echo "v: $v<br />";
	list($mcidv, $emv) = explode(":", $v);
	$emarray[] = $emv;
	$mcidarray[] = $mcidv;
	}
//echo '<pre>emailarray '; print_r($emarray); echo '</pre>';
//echo '<pre>mcid '; print_r($mcidarray); echo '</pre>';

$subject = $_REQUEST['subject'];
$message = $_REQUEST['message'];

echo "<div class=\"container\">
<h1>Bulk Mail Confirmation&nbsp;&nbsp;<a href=\"admin.php\" class=\"btn btn-primary\"><strong>(RETURN)</strong></a></h1>";

$tce = count($emarray); $sll = strlen($subject);
$sl = isset($_REQUEST['subject'])? $sll : 0;

if (($tce == 0) || ($sl == 0)) {
print<<<errorMsg
<h3>ERROR: No addresses in the "TO" list OR the subject line is empty.</h3>Please correct and resubmit.<br><br>
<a href="rptbulkemail.php">RETURN</a>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

errorMsg;
exit;
	}
$logmsg = '';
$starttime = date('r', strtotime(now));
$logmsg .= "<br>\n<br>\n***Send Message Processing Started at $starttime***<br>\n";
$logmsg .= "Recipient Count: $tce<br>";
$logmsg .= "<br><strong>To:</strong><br>\n";
foreach ($emarray as $addr) {
	$emaddr = htmlentities($addr, ENT_COMPAT,'ISO-8859-1', true);
	$logmsg .= "&nbsp;&nbsp;$emaddr<br>\n";
	}

$logmsg .= "<br><strong>From: </strong>$from<br>\n";

$logmsg .=  "<br><b>Header:</b><br>\n";
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= "From: " . $from . "\r\n";
$headers .= "Reply-To: " . $from . "\r\n";
$headers .= "Return-Path: " . $from . "\r\n";   // these two to set reply address
$logmsg .= $headers . "<br>\n";

$foption = "-f" . $from;												// notify of undeliverable mail to sender
$logmsg .= '<br><b>f-option</b><br>' . $foption . "<br>\n";

$trmsgx = clickable($message); 							// turn url's into links

$trans = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ');
$trsub = strtr($subject, $trans);
$trmsg  = strtr($trmsgx, $trans);

$logmsg .= "<br><strong>Subject:</strong><br>\n";
$logmsg .=  $trsub . "\n";
$logmsg .=  "<br><strong>Message:</strong><br>\n";
$logmsg .=  $trmsg . "<br>\n";

// format email message
$subject = "PWCVols: " . $trsub;

$cnt = count($emarray);

$errcnt = 0;
//foreach ($em as $addr) {
for ($i = 0; $i < count($emarray); $i++) {
	$addr = $emarray[$i];
	$mcid = $mcidarray[$i];
	if ($addr == "") { continue; }
	$to = $addr;
	$finmsg = "";
	$finmsg = $trmsg;
	$tag = "<br><br><font size=1><center>
	<a href=\"$HomeURL/voldb/index.php?MCID=$mcid&action=form\">Click to manage your own lists.</a></center></font>";
	$finmsg .= $tag;
	$finmsg = wordwrap($finmsg);

	$mresp = TRUE; 
	
	$mresp = mail($to, $subject, $finmsg, $headers, $foption);
	
 if ($mresp == FALSE) {
	 	$toaddr = htmlentities($to, ENT_COMPAT,'ISO-8859-1', true);
		$logmsg .= "**ERROR: mail function failed on " . $toaddr . "<br>\n";
		echo "**ERROR: mail function failed on " . $toaddr . "<br>";
		}
	
	usleep(5000000); // wait for 5 seconds and send next
	}

echo "<br><h4>***Bulk Email Processing Complete***</h4><br>";
echo '<h5>Please refer to the mail log entry to review the results</h5>';
$logmsg .= "Last end tag used: $tag<br />\n";
$endtime = date('r', strtotime(now));
$logmsg .= "<br>\n<br>\n***Send Message Processing Complete at $endtime***<br>\n";
addmaillogentry($logmsg);

print<<<pageBody
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
pageBody;
?>