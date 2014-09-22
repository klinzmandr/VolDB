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
echo "<html><head><title>Mail Log File</title></head>";
$lfn = isset($_REQUEST['lfn']) ? $_REQUEST['lfn'] : "";
//echo "log file name: " . $lfn . "<br>";

$mllist = scandir("/var/log");
if ($mllist === FALSE) {
	echo '<h3>Mail Log ERROR</h3>
	<p>The directory for the mail log does not exist.</p>
	<a class="btn btn-warning" href="javascript:self.close();"><strong>CLOSE</strong></a><br>';
	exit;
	}

if ($lfn == "") {
  echo "<h2>Step 1: Choose an available log file</h2>";
  echo "<br>List of available mail logs (current to oldest):<br>";
  //print_r($mllist);
  echo "<ol>";
	foreach ($mllist as $m) {
		if (($m == '.') OR ($m == "..") OR ($m == "httpd")) { continue; }
		if (substr($m,0,4) != 'mail') { continue; }
		echo "<a href=\"maillogchecker.php?lfn=$m\">$m</a><br>";
		//echo "filename: " . $m . "<br>";
		}
	echo "</ol>";
exit(0);
}

// if lfn contains a file name then
// copy indicated mail log to maillogfilecopy.txt in this dir
$fn = "/var/log/" . $_REQUEST['lfn'];
//echo "filename: " . $fn . "<br>";
echo "<h2>Step 2: Enter a date/time range.</h2>";
if (strpos($m, "gz") !== FALSE) { 			// grab text log file
	copy($fn, "maillogfilecopy.txt");
	}
else {
	$handle = gzopen($fn, 'r');
	if ($handle === FALSE) {
		echo "Error on open of gz file.<br>";
		echo '<h3>Error on open of of mail log file.</h3>
		<p>This is probably because there are no mail logs to display.</p>
		<a class="btn btn-warning" href="javascript:self.close();"><strong>CLOSE</strong></a><br>';
		exit(0);
		}
	//echo "to here<br>";
	while (!gzeof($handle)) {									// unzip a copy to local directory
  	$buffer = gzgets($handle, 4096);
  	$farray[] = $buffer;
		}
	gzclose($handle);
	file_put_contents("maillogfilecopy.txt", $farray); 			
	}

$logbuffer = file("./maillogfilecopy.txt");
$indate = substr($logbuffer[0],0,15);
$logbuffersize = count($logbuffer)-1;
$enddate = substr($logbuffer[$logbuffersize],0,15);

$sd = substr($logbuffer[0],0,15);
$ed = substr($logbuffer[$logbuffersize],0,15);
$indate = (isset($_REQUEST['indate'])) ? $_REQUEST['indate'] : $sd;
$enddate = (isset($_REQUEST['enddate'])) ? $_REQUEST['enddate'] : $ed;

print<<<inputForm
<form action="maillogchecker.php">
<input type="text" name="indate" value="$indate"  placeholder="Enter start date (and optional time) ..."/>

<input type="text" name="enddate" value="$enddate" />
<input type="hidden" name="lfn" value=$lfn />
<input type="submit" name="Submit" value="Submit" class="btn btn-primary" />

&nbsp;&nbsp;&nbsp;<a class="btn btn-warning" href="javascript:self.close();"><strong>DONE</strong></a><br>
First log entry: $sd&nbsp;&nbsp;Last log entry:$ed<br>
And all or part of an optional email address:&nbsp;
<input  class="input-medium search-query" type="text" name="ema" value=>
</form>
<a href="maillogchecker.php?lfn=&indate=">Choose another mail log</a>
inputForm;

//$firstentry = substr($logbuffer[0],0,15);
//echo "<strong>Date and time of first log entry: " . $firstentry . "</strong><br>";
if (strlen($indate) == 0) {
	echo "<br><br>Enter a valid date (and optionally a time) from which to list log entries.<br>";
	echo "Examples:<br>";
	echo "&nbsp;&nbsp;March 1 - from March 1 of the current year<br>";
	echo "&nbsp;&nbsp;March 1, 2013 - from March 1 of the current year<br>";
	echo "&nbsp;&nbsp;Mar 1 - from March 1 of the current year<br>";
	echo "&nbsp;&nbsp;3/1/13 - from March 1 of the current year<br>";
	echo "&nbsp;&nbsp;3/1/13 18:00 - from March 1 of the current year on or after 6:00 PM<br>";

	echo "<br><a href=\"maillogchecker.php?lfn=\"\"\"\">Choose another mail log file.</a>";
	exit(0);
	}

echo "<h2>List of mail log entries entries on or after: $indate</h2>";

$hitcount = 0;
$from = strtotime($indate); $to = strtotime($enddate);
$ema = isset($_REQUEST['ema']) ? $_REQUEST['ema'] : "";		// optional email address, if entered
echo "<table class=\"table table-condensed\" border=\"0\" width=\"95%\">";
// report all email addresses sent to
foreach ($logbuffer as $l) {
		$la = explode(",", $l);											// line array of all elements
		//echo "la7:$la[7]:<br />";
		if (substr($la[7],1,3) == 'dsn') $la[7] = $la[8];
		if ($la[7] == '') $la[7] = 'Incoming message';
		$dt = substr($l,0,15);
	  $msg = $dt . "&nbsp;";
	  if ((strtotime($dt) >= $from) AND (strtotime($dt) <= $to)) {	
	    $apos = $atpos = $strlen = 0;
	    $str = $la[0];
	    $retval = (($atpos = stripos($la[0], '@')) && ($apos = stripos($la[0],": to=",0))); 
	    if ($ema != "") { $emlval = ($emlpos = stripos($la[0], $ema, $apos+5)); } 
// if in range and search string entered and string found
			if (($retval) AND ($emlval)) {
				// col 1 = time   col 2 = email address   col 3 status
				echo "<tr><td width=\"15%\">" . substr($la[0],0,15) . "</td><td>" . htmlentities(substr($la[0],$apos+5)) . "</td><td>" . substr($la[7],0) . "</td></tr>";
				$hitcount++;
				continue;
				}
// if in range and search string NOT entered				
			if (($retval) AND ($ema == "")) {
				echo "<tr><td width=\"15%\">" . substr($la[0],0,15) . "</td><td>" . htmlentities(substr($la[0],$apos+5)) . "</td><td>" . substr($la[7],0) . "</td></tr>";
				$hitcount++;
				continue;
				}		
			}
		}
// report any quarantined messages
$qcount = 0;
foreach ($logbuffer as $l) {
	if (stripos($l,"quar")) {
		$la = explode(" ", $l);
		$dt = substr($l,0,15);
	  $msg = $dt . "&nbsp;";
		foreach ($la as $q) {
			if (stripos($q, 'to=') !== FALSE) {
				if (strtotime($dt) > $from) {
					$namepart = substr($q,3);
					echo $msg . "&nbsp;" . $namepart . "<br>";
					$qcount++;
					$qlist[$namepart] += 1;
					}
				} 
			}
		}
	}

echo "</table><br>";
echo "Record count: " . $hitcount . "<br><br>";
if ($qcount == 0) {
	echo "No messages reported as quarantined<br>";
	echo "<a href=\"maillogchecker.php?lfn=&indate=\">Choose another mail log</a>";
	exit(0);
	}
echo "Quarantined Messages:<br>";
echo "<table border=\"1\"><tr><td>" . implode(" ",array_keys($hitlist)) . "</td></tr></table><br>";
echo "Quarantined message count: $qcount<br>";
echo "<a href=\"maillogchecker.php?lfn=&indate=\">Choose another mail log</a>";
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>