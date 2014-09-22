<!DOCTYPE html>
<html>
<head>
<title>Mail Log Checker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>


<?php 

echo "<html><head><title>Mail Undelivered File</title></head>";
$lfn = isset($_REQUEST['lfn']) ? $_REQUEST['lfn'] : "";
//echo "log file name: " . $lfn . "<br>";

if ($lfn == "") {
$mllist = scandir("/var/spool/mail");
  echo "<h2>Step 1: Choose an available log file</h2>";
  echo "<br>List of available mail logs (current to oldest):<br>";
  //print_r($mllist);
  echo "<ol>";
	foreach ($mllist as $m) {
		if (($m == '.') OR ($m == "..") OR ($m == "httpd")) { continue; }
		if (substr($m,0,1) == '.') { continue; }
		echo "<a href=\"mailundeliveredchecker.php?lfn=$m\">$m</a><br>";
		//echo "filename: " . $m . "<br>";
		}
	echo "</ol>";
exit(0);
}

$fpart = $_REQUEST['lfn'];
$fn = "/var/spool/mail/" . $fpart;

if (strpos($m, "gz") !== FALSE) { 			// grab text log file
	copy($fn, "mailundeliveredfilecopy.txt");
	}
else {
	$handle = gzopen($fn, 'r');
	if ($handle === FALSE) {
		echo "Error on open of gz file: " . $from . "<br>";
		exit(0);
		}
	while (!gzeof($handle)) {									// unzip a copy to local directory
  	$buffer = gzgets($handle, 4096);
  	$farray[] = $buffer;
		}
	gzclose($handle);
	file_put_contents("mailundeliveredfilecopy.txt", $farray); 			
	}
	
$logbuffer = file("./mailundeliveredfilecopy.txt");
$logbuffersize = count($logbuffer)-1;

echo "<h2>Contents of undelivered mail log file: $fpart</h2>";
echo "<a href=\"mailundeliveredchecker.php?lfn=&indate=\">Choose another mail log</a>";
echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:self.close();\"><strong>DONE</strong></a><br>";

$hitcount = 0;
echo "<table class=\"table table-condensed\" border=\"0\" width=\"95%\"><tr><td><pre>";
foreach ($logbuffer as $l) {
	if ((stripos($l,'From MAILER-DAEMON') !== FALSE) OR (substr(ltrim($l),0,5) == 'From '))
		echo '== Start of Entry ==<br />';
		echo htmlentities(rtrim($l)) . "<br>";
		$hitcount++;
		}
// report any quarantined messages

echo "</pre></td></tr></table><br>";
echo "Record count: " . $hitcount . "<br><br>";

echo "<a href=\"mailundeliveredchecker.php?lfn=&indate=\">Choose another mail log</a>";
?>

<script src="http://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>