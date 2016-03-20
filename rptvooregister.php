<!DOCTYPE html>
<html>
<head>
<title>VOO Registration Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- new comment line to test sync -->
<?php
session_start();
//include 'Incls/vardump.inc';
//include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-01-01', strtotime("now"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime('now'));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo '<div class="container"><h3>VOO Registrations In Date Range&nbsp;&nbsp;<a class="btn btn-default" href="javascript:self.close();">CLOSE</a></h3>';

echo '<h4>VOO attendees registered within the date range specified</h4>';
print <<<pagePart1
<form action="rptvooregister.php" method="get"  class="form">
Start:<input type="text" name="sd" id="sd" value="$sd" style="width: 105px;">
End: <input type="text" name="ed" id="ed" value="$ed" style="width: 105px;">
<input type="hidden" name="action" value="list">
<input type="submit" name="submit" Value="Submit">
</form>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>

pagePart1;
if ($action == '') {
  exit;
	}

$sql = "SELECT DISTINCT `correspondence`.`MCID`, `correspondence`.`SourceofInquiry`, `members`.`MCID`, `members`.`MemDate`, `members`.* 
FROM { oj `pwcmbrdb`.`correspondence` AS `correspondence` LEFT OUTER JOIN `pwcmbrdb`.`members` AS `members` ON `correspondence`.`MCID` = `members`.`MCID` } 
WHERE `correspondence`.`SourceofInquiry` LIKE '%voo%' 
AND `members`.`MemDate` BETWEEN '$sd' AND '$ed'";
echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);

$x = array();
while ($r = $res->fetch_assoc()) {
  $x[$r[MCID]] = $r;
  }

echo 'VOO count: ' . count($x) . '<br>';
foreach ($x as $k => $v) {
  echo "k: $k, memdate: $v[MemDate]<br>";
  }
//echo '<pre> x '; print_r($x); echo '</pre>';

?>

</body>
</html>
