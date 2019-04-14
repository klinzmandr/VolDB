<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Send Email</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onChange="flagChange()">


<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$tokey = $_REQUEST['tokey'];

// $EmailFROM is defined as the default in the datautils include
$sender = $EmailFROM;

$sql = ''; $tostr = '(';
foreach ($tokey as $t) {
	$tostr .= "`Lists` LIKE '%$t%' OR ";
	}
$tostr = rtrim($tostr," OR ") . ')';
// echo "tostr: $tostr<br />";

$sql = "SELECT `MCID`,`FName`,`LName`,`EmailAddress` 
FROM `members` 
WHERE `MemStatus` = 2 
	AND $tostr 
	ORDER BY 'LName' ASC";
// echo "SQL: $sql<br />";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt == 0) {
	echo '<h3>There are no volunteers email addresses in any of the lists.</h3>
	<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>';
	exit;
	}
print <<<pagePart1
<div class="container">
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
	var errmsg = "";
	for (var i=0; i<fld.length; i++) {
		if (fld[i].checked) {
			counter += 1; 
			}
		}
	if (counter == 0) {
		ProgressImage = document.getElementById('progress_image');
		document.getElementById("progress").style.visibility="hidden";
		setTimeout("ProgressImage.src = ProgressImage.src",100);
		alert("No names were selected."); 
		return false; 
		}
	if (document.sndform.subject.value.length == 0) {
		errmsg += "No subject text entered.\\n"; 
		}
	var msg = document.getElementById('editarea').innerHTML
	document.sndform.message.value = msg;
	if (msg.length <= 1) {
		errmsg += "No message text entered.\\n"; 
		}
	if (errmsg.length > 0) {
		alert(errmsg);
		return false;
		}
	var r=confirm("There are " + counter + " names selected\\n" + "which will be immediately\\n queued for sending on the next hour.");
	if (r==true) {
		ProgressImage = document.getElementById('progress_image');
		document.getElementById("progress").style.visibility="visible";
		setTimeout("ProgressImage.src = ProgressImage.src",100);
		return true; 
		}
	return false;
	}
</script>

<ul id="myTab" class="nav nav-tabs">
  <li class="active"><a href="#lists" data-toggle="tab">1. Review Address(s)</a></li>
  <li class=""><a href="#compose" data-toggle="tab">2. Compose Msg</a></li>
  <li class=""><a href="#send" data-toggle="tab">3. SEND IT
  </a></li>
</ul>

<div id="myTabContent" class="tab-content">

<!--  --------------------- lists tab ------------------------- -->
<div class="tab-pane fade active in" id="lists">
<h4>Select the Email Addresses to send to:</h4>

pagePart1;

if ($rowcnt > 350) {
  echo "<h3>ERROR: Max List Size Exceeded</h3>
  <p>There are $rowcnt in the selected list(s).  This exceeds the total maximum size of 350 
  allowed to be sent at a single time. You need to split the mailing into smaller groups.</p>
  ";
  exit(0);
  }
echo "volunteers Selected: $rowcnt<br /><br />";
$mciderrs = array();
echo '<table class="table table-condensed">
<tr><th>&nbsp;</th><th>Email Address</th></tr>';
echo "<form id=\"sndform\" method=post name=\"sndform\" action=\"sendemailvolsupd.php\" onsubmit='return confirmbox(document.sndform[\"tokey[]\"])'>";
echo "<tr><td><input type=\"checkbox\" name=\"chkr\" 
onchange='checkAll(document.sndform.chkr, document.sndform[\"tokey[]\"])' checked></td><td>&nbsp;<b>Check/Uncheck All</b><br></td></tr>"; 

while ($r = $res->fetch_assoc()) {
	if (strlen($r['EmailAddress']) == 0) {
		$mciderrs[] = $r['MCID'];
		continue;
		}
	//echo '<pre>'; print_r($r); echo '</pre>';
	$mailcnt++;
	$ma = "$r[MCID]:$r[FName] $r[LName] <$r[EmailAddress]>";
	$va = "$r[FName] $r[LName] <$r[EmailAddress]>";
	$addr = htmlentities($ma, ENT_COMPAT,'ISO-8859-1', true);
	$cb = "<input type=\"checkbox\" name=\"tokey[]\" value=\"$addr\" checked />";	
	echo "<tr><td>$cb</td><td>".htmlentities($va)."</td></tr>\n";   				
	}
echo '</table><br /><br />';

if (count($mciderrs) > 0) {
	echo count($mciderrs) . " volunteers with no email addresses: ";
	foreach ($mciderrs as $e) $l .= "$e, ";
	echo "$l<br />";
	}
echo "volunteers included: $mailcnt<br />";

print <<<pagePart2
<!--------------- compose tab ------------------------>
</div>
<div class="tab-pane fade" id="compose">
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
//	var myNicEditor = new nicEditor({buttonList : ['fontSize', 'fontFormat', 'left', 'center', 'right', 	'bold','italic','underline','indent', 'outdent', 'ul', 'ol', 'hr', 'forecolor', 'bgcolor','link','unlink']});
  var myNicEditor = new nicEditor({fullPanel : true});
	myNicEditor.setPanel('myNicPanel');
	myNicEditor.addInstance('editarea');
});
</script>

<p>Have a picture or document you want to send a link for?  If so click the button to open a new window with the repository listing, upload the picture or document, copy the link using the instructions provided and paste that link into your message</p>

<a class="btn btn-info" href="https://library.pacwilica.org/repository/.upload.php" target="_blank" >Add to repository</a><br />
<h4>Compose a message:</h4>
Subject:
<input type="text" name="subject" size="80" maxlength="128" /><br />
<input type="hidden" name="message" id="message" value=""><br />
<!--  onsubmit: move text from div area into hidden text field -->
<div id="myNicPanel" style="width: 750px;"></div>
<div id="editarea" style="font-size: 12px; padding: 3px; border: 1px solid #000; width: 700px; height: 350px">
</div>  <!-- editarea -->
<br />
</div>  <!-- tab-pane -->
<!-- ------------- send tab ------------------------- -->
<div class="tab-pane fade" id="send">
<h4>Click the following button to queue the message for sending.</h4>
<input type="submit" name="submit" value="Send Message" ><br />
<p style="visibility:hidden;" id="progress"/>
<img src="Incls/progressbar.gif" width="226" height="26" alt="" /><br />DO NOT CANCEL THIS PAGE UNTIL THE MAIL SEND ROUTINE HAS COMPLETED.	A confirmation page will be displayed when that is done.</p>
<p>The message subject and text will be sent to all of the checked email reciepents.  A log record is produced and may be reviewed by looking at &apos;Reports->Review Mail Log&apos; after the mail send processing has been completed.</p>
<input type="hidden" name="sender" value="$sender">
</form>

</div>  <!-- tab-pane -->

</div>  <!-- tab-content -->

</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
pagePart2;
?>
