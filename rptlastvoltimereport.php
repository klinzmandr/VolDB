<!DOCTYPE html>
<html>
<head>
<title>Last Vol Time Report</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>

<script>
// initial setup of jquery function(s) for page
$(document).ready(function () {
	$('.sd').datepicker({
    format: 'yyyy/mm/dd',
    todayBtn: true,
    todayHighlight: true,
    autoclose: true
    });

// this attaches an event to an object
	$("h3").click(function () {
    //alert("example of a click of any header 3 like the page title"); 
    });

  });  // end ready function
</script>

<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : '';

if ($action == 'rpt') {
//echo '<pre>'; print_r($_REQUEST); echo '</pre>';
$sql = 'CALL LastVolTimeQuery("'.$sd.'")';
//echo "SQL: $sql<br>";

$res = $mysqli->query($sql);
$nbr_rows = $res->num_rows;

if ($mysqli->errno != 0) {
  echo "Query Failed: (" . $mysqli->errno . ") - " . $mysqli->query_error;
  echo "<br>Failing Query string: $sql <br><br>";
  exit;
	}

echo '<h3>Last Time Reported by a Volunteer&nbsp;&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a>&nbsp;&nbsp;&nbsp;
<a class="btn btn-primary" href="rptlastvoltimereport.php">ReDo</a></h3>
Date entered: '.$sd.', Volunteer count: '.$nbr_rows.'<br />
<a href="downloads/lastvoltimereport.csv" download="lastvoltimereport.csv">DOWNLOAD CSV FILE</a>
<button type="button" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="top" title="Fields separated by semicolon(;). Text fields are quoted."><span class="glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px"></span></button>';

echo '<table class="table table-condensed">
<tr><th>MCID</th><th>LastVolDate</th><th>LastVolTime</th><th>LastVolCat</th><th>LastPayDate</th><th>LastPayType</th><th>LastPayAmt</th><th>MemType</th><th>Name</th><th>Address</th><th>City</th><th>St</th><th>Zip</th><th>Email</th></tr>';
$csv[] =  "MCID;LastVolDate;LastVolTime;LastVolCat;LastPayDate;LastPayType;LastPayAmt;MemType;Name;Address;City;St;Zip;Email;\n";
while ($r = $res->fetch_assoc()) {
  //echo "<pre> Row $rc "; print_r($r); echo '</pre>';
  $lastpaydate = $r[LastDuesDate];
  $lastpayamt = $r[LastDuesAmount];
  $lastpaytype = 'Dues';
  if (strtotime($lastpaydate) <= strtotime($r[LastDonDate])) {
    $lastpaydate = $r[LastDonDate];
    $lastpayamt = $r[LastDonAmount];
    $lastpaytype = 'Donation';
    }
  echo "<tr><td>$r[MCID]</td><td>$r[LastVolDate]</td><td>$r[VolTime]</td><td>$r[VolCategory]</td><td>$lastpaydate</td><td>$lastpaytype</td><td>$lastpayamt</td><td>$r[MCtype]</td><td>$r[NameLabel1stline]</td><td>$r[AddressLine]</td><td>$r[City]</td><td>$r[State]</td><td>$r[ZipCode]</td><td>$r[EmailAddress]</td></tr>";
  $csv[] = "$r[MCID];$r[LastVolDate];$r[VolTime];$r[VolCategory];$lastpaydate;$lastpaytype;$lastpayamt;$r[MCtype];\"$r[NameLabel1stline]\";\"$r[AddressLine]\";$r[City];$r[State];\"$r[ZipCode]\";$r[EmailAddress]\n";
  }
echo '</table>';
echo '========== END OF REPORT ============<br><br>';
file_put_contents('downloads/lastvoltimereport.csv',$csv);
}

// initialize report info
if ($action == '') {
print <<<pagePart1
<div class="container">
<h3>Last Volunteer Time Report&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary"><strong>(CLOSE)</strong></a></h3>
<p>This report details the last time a volunteer reported time, type and service provided by any active volunteer.</p>
<p>The date entered is the LAST date of these service records.  Any volunteer reporting service AFTER the date entered is ignored and ALL service records from that volunteer dropped from this report.</p>

<form action="rptlastvoltimereport.php">
<input type="hidden" name="action" value="rpt">
Select ending service date: <input class='sd' data-provide="datepicker" name='sd' value='' onchange='javascript: this.form.submit();'>
<form>

</div>
pagePart1;

}

?>

</body>
</html>
