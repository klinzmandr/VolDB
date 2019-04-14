<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Volunteer Exemption Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</head>
<body>
<script>
$(document).ready(function() {
  $("#help").hide();
  $("#helpbtn").click(function() { $("#help").toggle(); });
  $("#res").hide();
  $("#resbtn").click(function() { $("#res").toggle(); });
  $("#inact").hide();
  $("#inactbtn").click(function() { $("#inact").toggle(); });
  $("#nolist").hide();
  $("#nolistbtn").click(function() { $("#nolist").toggle(); });
  $("#noem").hide();
  $("#noembtn").click(function() { $("#noem").toggle(); });
  $("#nonres").hide();
  $("#nonresbtn").click(function() { $("#nonres").toggle(); });
  
  });
</script>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/letter_print_css.inc.php';

$expperiod = 90;					// period in days for time reporting period
$expdate = date("Y-m-d", strtotime("today - $expperiod days"));
$expdatesecs = strtotime("today - $expperiod days");
// echo "expdate: $expdate<br />";
$expfdate = date('F d, Y', strtotime("today - $expperiod days"));
// echo "expfdate: $expfdate<br />";

echo '<div class="container">
<h3>Volunteer Exception Report&nbsp;&nbsp;
<a class="hidden-print btn btn-default" href="javascript:self.close();">CLOSE</a></h3>';
//echo '<pre>'; print_r($_REQUEST); echo '</pre>';
$sql = 'CALL rptvolexceptionsp()';
//echo "SQL: $sql<br>";

$res = $mysqli->query($sql);
$rescount = $res->num_rows;

if ($mysqli->errno != 0) {
  echo "Query Failed: (" . $mysqli->errno . ") - " . $mysqli->query_error;
  echo "<br>Failing Query string: $sql <br><br>";
  exit;
	}

if ($rescount == 0) {
	echo "<h4>No MCIDs have reported time in the last $expperiod days.</h4>";
	exit;
	}
?>

<button id="helpbtn" class="hidden-print btn btn-regular btn-xs">About this report</button><br>
<b>Active period:</b> <?=$expperiod?> days<br>
<b>Active Date:</b> <?=$expfdate?><br>
<b>Total volunteer records:</b> <?=$rescount?><br>
<div id="help">
<h4>Report Description</h4>
<p><b>Active Date:</b> - The date 90 days prior to the current date relative to when the report is run.  Volunteers are considered to be &quot;Active&quot; if they have volunteer time recorded within the last 90 days.  Any volunteer reporting time reported BEFORE this date results in the volunteer being considered as &quot;Inactive&quot;.</p>
<p>This report has the following sections which will only appear in the report if there are any MCIDs that qualify.
<ol>
<li><b>MCIDs that do not have an email address recorded on their supporter record.</b><br>
All volunteers must have a valid email address registered to allow communications with them regarding schedules, annoucements, etc.</li>
<li><b>MCIDs that do not have ANY volunteer category selected.</b><br>
The list defines the categories for time classifiction and email distrbution list(s).  Abscense of any selection should be assumed to mean that the supporter has withdrawn from being a volunteer.  These MCIDs should be reclassified by changing the Member Status and Member Type fields of the supporter record to a member or donor designation.
</li>
<li><b>Volunteer MCIDs with NO reported time within the active period.</b><br>
This section of the report should be printed out and provided to the Center Operations Committee (COPS) to review and approve reclassification of any/all of MCIDs listed to a non-volunteer status.  Reclassification steps must be performed by an administrative user and consists of adding 'Inactive' to the mailing lists of the MCID record then changing the Member Status and Member Type fields of the supporter record to a member or donor designation.  Other mailing lists selected should be left for future reference.
</li>
<li><b>NON-volunteer MCIDs that HAVE reported volunteer time within the active period.</b><br>
All MCIDs listed in this section of the report should be reclassified as a volunteer by changing the Member Status and Member Type fields of the supporter record to a volunteer designation.</li>
<li><b>MCIDs that are marked as 'Inactive'.</b><br>
The MCIDs listed need to be reviewed and an administrative user must either remove the 'Inactive' flag from the Lists of the volunteer OR change the Member Status and Member Type fields of the supporter record to a member or donor designation.</li>
</p>
<a href="./docs/VolunteerInactiveList.pdf" target="_blank">More information about inactive volunteers is available.</a>
</div> <!-- help -->

<?php
$notavol = array(); $results = array(); $nolistarray = array(); 
$volinactarray = array(); $noemarray = array();
while ($r = $res->fetch_assoc()) {
	// service date AFTER the expriation date AND mbr record IS a volunteer
	$lastvoldate = strtotime($r['LastVolDate']);
	// echo "lastvoldate: $lastvoldate, expdatesecs: $expdatesecs<br>";
	// echo '<pre>row '; print_r($r); echo '</pre>';
	if ((strtotime($r['LastVolDate']) < $expdatesecs) AND ($r['MemStatus'] == 2)) {
		$results[] = $r;
		}
  // service date AFTER to the expriation date AND mbr record is NOT a volunteer
	if ((strtotime($r['LastVolDate']) > $expdatesecs) AND ($r['MemStatus'] != 2)) {
		$notavol[] = $r;
		}
	// vol has no registered lists						
  if ((strlen($r['Lists']) == 0) AND ($r['MemStatus'] == 2)) {
    $nolistarray[$r['MCID']] = $r;
    }
  // vol has been marked as inactive
  if (preg_match("/inactive/i", $r['Lists'])) {
    $volinactarray[$r['MCID']] = $r;
    continue;
    }
  // volunteers with NO email address on record
  if ((strlen($r['EmailAddress']) == 0) AND ($r['MemStatus'] == 2)){
    $noemarray[$r['MCID']] = $r;
    }
// echo '<pre>'; print_r($r); echo '</pre>';
	}
// echo '<pre>results '; print_r($results); echo '</pre>';
// echo '<pre>notavol '; print_r($notavol); echo '</pre>';
// echo '<pre>nolist '; print_r($nolistarray); echo '</pre>';
// echo '<pre>vol inactive '; print_r($volinactarray); echo '</pre>';
// echo '<pre>noem '; print_r($noemarray); echo '</pre>';

// vols with no email address
$rc = count($noemarray);
// echo 'Vols with no email addresses: noem<br>';
if ($rc > 0) {
	echo "<h4>There are $rc volunteer MCIDs without an email address.</h4>
<ul><button class=hidden-print id=noembtn>Show Details</button></ul>
<div id=noem>
<table class=\"table-condensed\">;
<tr><th>MCID</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	foreach ($noemarray as $k => $r) {
		echo "<tr><td>$r[MCID]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br /></div>
		<div class="page-break"></div>';
	}

// no volunteer category identified
$rc = count($nolistarray);
// echo "Vols with no vol category: nolist<br>";
if ($rc > 0) {
	echo "<h4>There are $rc volunteer MCIDs that do not have ANY volunteer category.</h4>
<ul><button class=hidden-print id=nolistbtn>Show Details</button></ul>
<div id=nolist>
<table class=\"table-condensed\">
<tr><th>MCID</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	foreach ($nolistarray as $k => $r) {
		echo "<tr><td>$r[MCID]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br /></div>
		<div class="page-break"></div>';
	}

// report expired vols, if any
$rc = count($results);
// echo "vols with no time: res - $rc<br>";
if ($rc > 0) {
	echo "<h4>There are $rc volunteer MCIDs with NO reported volunteer time in the active period.</h4>
<ul><button class=hidden-print id=resbtn>Show Details</button></ul>
<div id=res>
<table class=\"table-condensed\">
<tr><th>MCID</th><th>LastSvcDate</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	foreach ($results as $k => $r) {
		echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[LastVolDate]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br /></div>
	<div class="page-break"></div>';
	}

// non-vols reporting vol time
$rc = count($notavol);
// echo "NON-vols reporting time: nonres<br>";
if ($rc > 0) {
	echo "<h4>There are $rc NON-volunteer MCIDs that HAVE reported volunteer time during the active perod.</h4>
<ul><button class=hidden-print id=nonresbtn>Show Details</button></ul>
<div id=nonres>
<table class=\"table-condensed\">
<tr><th>MCID</th><th>LastSvcDate</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	foreach ($notavol as $k => $r) {
		echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[LastVolDate]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br /></div>
	<div class="page-break"></div>';
}

// vols marked inactive
$rc = count($volinactarray);
// echo "Inactive vols: inact, $rc<br>";
if ($rc > 0) {
	echo "<h4>There are $rc volunteer MCIDs that are marked as &apos;Inactive&apos;.</h4>
<ul><button class=hidden-print id=inactbtn>Show Details</button></ul>
<div id=inact>
<table class=\"table-condensed\">
<tr><th>MCID</th><th>FName</th><th>Lname</th><th>MemStatus</th><th>Lists</th></tr>";
	foreach ($volinactarray as $k => $r) {
		echo "<tr><td>$r[MCID]</td><td>$r[FName]</td><td>$r[LName]</td><td align=\"center\">$r[MemStatus]</td><td>$r[Lists]</td></tr>";
		}	
	echo '</table>----- END OF LIST -----<br /></div>
		<div class="page-break"></div>';
	}


?>
</body>
</html>
