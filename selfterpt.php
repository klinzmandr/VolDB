<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Time Entry Log</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$mcid = $_REQUEST['mcid'];

$sql = "SELECT * FROM `voltime` WHERE `MCID`= '$mcid' ORDER BY `VolDate` DESC LIMIT 20;";
$res = doSQLsubmitted($sql);
echo "<h4>Last 20 entries for $mcid &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"btn btn-xs btn-primary\" href=\"selfte.php?MCID=$mcid\">Return</a></h4>";
echo '<table class="table">
<tr><th>Date</th><th>Hours</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>';
while ($r = $res->fetch_assoc()) {
	//echo '<pre> Time entries '; print_r($r); echo '</pre>';
	echo "<tr><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td></tr>";
	}
echo '</table>';

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
