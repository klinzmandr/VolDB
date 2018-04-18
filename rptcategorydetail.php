<?php
session_start();
// include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("previous month"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime('now'));
$cats = isset($_REQUEST['cats']) ? $_REQUEST['cats'] : '';
$details = isset($_REQUEST['details']) ? 'ON' : 'OFF';
$catsummary = isset($_REQUEST['catsummary']) ? 'ON' : 'OFF';
$volsummary = isset($_REQUEST['volsummary']) ? 'ON' : 'OFF';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
?>

<!DOCTYPE html>
<html>
<head>
<title>Category Service Detail</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

<script>
$("document").ready( function() {
  $("#help").hide();
$("#helpbtn").click(function() {
  $("#help").toggle();
  });

});
</script>

<div class="container">
<h3>Category Service Detail&nbsp;&nbsp;<a class="hidden-print btn btn-primary btn-xs" href="javascript:self.close();">CLOSE</a>&nbsp;&nbsp;<a class="hidden-print btn btn-primary btn-xs" href="rptcategorydetail.php?sd=<?=$sd?>&ed=<?=$ed?>">RE-DO</a>&nbsp;&nbsp;
<button id="helpbtn" class="hidden-print btn btn-default btn-xs">HELP</button></h3>
<div id="help">
	<p>This report produces a summary of all service records for one or more categories selected from the date range specified.</p>
	<p>The following additional summary and/or detail reports can optionally be selected as well. All listed may be downloaded to spreadsheet software by clicking the assoicated link.
	<ul><li>Volunteer Summary - lists each individual volunteer serving within the date range specified summarizing the number of time served, count of the different service categories served as well as the total number of hours served and total miles driven in all categories.</li>
	<li>Category Summary - Groups each individual volunteer with service time within the date range specified by category. Individual volunteer along with their total hours served and total miles driven is provided.</li>
	<li>Service Detail Records - all service records for all volunteers within the date range specified are listed.</li> 
	</ul></p>
</div>

<?php if ($action == '') { ?>

<script>
function chkcats() {
	var elems = document.getElementsByName("cats[]");
	for (i = 0; i < elems.length; i++) {
		if (elems[i].checked) return true;
		}
	alert ("There were NO categorys selected");
	return false;
	}
</script>
<script>
function chgcb() {
	var cb = document.getElementsByName("ctrlcb");
	var elems = document.getElementsByName("cats[]"); 
	if (cb[0].checked) {
		for (i = 0; i < elems.length; i++) {
			elems[i].checked = true;
		}
	}
	else {
		for (i = 0; i < elems.length; i++) {
			elems[i].checked = false;
		}
	}
	return;
}
</script>
<form action="rptcategorydetail.php" method="post"  class="form" onsubmit="return chkcats()">
Date range from:<input type="text" name="sd" id="sd" value="<?=$sd?>" style="width: 105px;">
to:  <input type="text" name="ed" id="ed" value="<?=$ed?>" style="width: 105px;">
<input type="submit" name="submit" Value="Submit">
</ul>

<?php
$cats = readdblist('VolCategorys');
$catsarray = formatdbrec($cats);
asort($catsarray);
//echo '<pre> categories '; print_r($catsarray); echo '</pre>';
echo '<br>
<input type="checkbox" name="volsummary" id="volsummary"> Create Volunteer Summary&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="catsummary" id="catsummary"> Create Category Summary&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="details" id="details"> List detail records<br>
<ul>
<h4>Categories:&nbsp;&nbsp;(&nbsp;<input type="checkbox" name="ctrlcb" checked onchange="chgcb()">&nbsp;Check All/None)</h4><ul>
';

foreach ($catsarray as $k => $c) {
	if ($c == '') continue;
	$c = rtrim($c);
	echo "<input type=\"checkbox\" name=\"cats[]\" id=\"cats\" value=\"$k\" checked> $c<br>";
  }
echo '</ul></ul><br>
<input type="hidden" name="action" value="generate">
</form>
</body>
</html>
';

}  // closing bracket for if ($action == '')


// SUMMARY REPORTING STARTS HERE when action <> ''
//echo '<h3>Volunteer Service Analysis&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">CLOSE</a></h3>';
//	echo "details flag: " . $details . "<br>";
//	echo '<pre> cats '; print_r($cats); echo '</pre>';

$wherelist = '("' . implode('", "', $cats) .'")';
// echo '<pre>'; print_r($wherelist); echo '</pre>';

$sql = "SELECT `voltime`.*, `members`.`FName`, `members`.`LName` from `voltime`, `members`
WHERE `voltime`.`MCID` = `members`.`MCID`
  AND `volcategory` IN $wherelist
	AND `voltime`.`VolDate` BETWEEN '$sd' AND '$ed'
ORDER BY `voltime`.`VTID` ASC";
$res = doSQLsubmitted($sql);
// echo "sql: $sql<br>";
$rowcnt = $res->num_rows;
//echo "rowcnt: $rowcnt<br>";
if ($rowcnt > 0) {
	$voldet = array(); $csv = array(); $counts = array(); $mcidcounts = array();
	$categories = array(); $catscsv = array(); $names = array(); $ind = array();
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes	
	$catscsv[] = "VolCategory;MCID;FName;LName;TotHrs;TotMiles";
	$csv[] = "MCID;FName,LName,SvcDate;SvcTime;Mileage;Category;Notes\n";
	while ($r = $res->fetch_assoc()) {
//		echo '<pre>'; print_r($r); echo '</pre>';
			if (rtrim($r[VolCategory]) == '') continue;
			$tothrs += $r[VolTime];
			$totmiles += $r[VolMileage];
			$counts[$r[VolCategory]][count] += 1;
			$counts[$r[VolCategory]][hours] += $r[VolTime];
			$mcidcounts[$r[MCID]][count] += 1;
			$mcidcounts[$r[MCID]][hours] += $r[VolTime];
			$categories[$r[VolCategory]][$r[MCID]][VolTime] += $r[VolTime];
			$categories[$r[VolCategory]][$r[MCID]]['VolMileage'] += $r[VolMileage];
			$names[$r[MCID]] = "$r[FName];$r[LName]";
			$ind[$r[MCID]][Count] += 1;
			$ind[$r[MCID]][Cats][$r[VolCategory]] += 1;
			$ind[$r[MCID]][Hours] += $r[VolTime];
			$ind[$r[MCID]][Mileage] += $r[VolMileage];
			
			$voldet[] = "<tr><td>$r[MCID]</td><td>$r[FName]</td><td>$r[LName]</td><td width=\"100\">$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td></tr>";
			$csv[] = "\"$r[MCID]\";\"$r[FName]|\";\"$r[LName]\";$r[VolDate];$r[VolTime];$r[VolMileage];$r[VolCategory];\"$r[VolNotes]\"\n";
		}

	echo "<h4>Period from $sd to $ed</h4>";
	echo '<b>Unique Volunteers reporting:</b> ' . count($mcidcounts) . '<br>
	<b>Total Hrs:</b> ' . $tothrs . '<br>';
	if ($totmiles > 0) echo "<b>Total Mileage:</b> $totmiles";
	echo '<br>';
//	echo '<b>Period Category service count and total hours</b><br>';
	echo '<ul><table class="table-condnesed" border=0>';
	
	echo '<tr><td align="center" width="20%"><b>Category</b></td><td width="20%" align="center"><b>SvcCount</b></td><td width="20%" align="center"><b>TotHrs</b></td></tr>';
	ksort($counts);
	foreach ($counts as $k => $v) {
		echo "<tr><td>$k</td><td align=\"right\">$v[count]</td><td align=\"right\">$v[hours]</td></tr>";
		}
	echo '</table></ul>';
  ksort($counts);
  $str = '';
  if (count($counts) > 0) {
    foreach ($counts as $k => $v) {
      $str1 .= "['$k'," . $v[count] . "],";
      $str2 .= "['$k'," . $v[hours] . "],";
      }
  $piechartdata1 = rtrim($str1, ','); 
  $piechartdata2 = rtrim($str2, ','); 
  }
//echo "piechartdata: $piechartdata1<br>";
// chart1 output here
?>

<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

// Load the Visualization API and the corechart package.
google.charts.load('current', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart1);

// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and
// draws it.
function drawChart1() {

  // Create the data table.
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Count');
  data.addColumn('number', 'Total');
  data.addRows(
  [<?=$piechartdata1?>]);
  // [['one',1],['two',2],['three',3]]);

  // Set chart options
  var options = {'title':'Service Distribution (Count)',
                 'width':400,
                 'height':300};

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.PieChart(document.getElementById('chart1'));
  chart.draw(data, options);
}

<!-- chart2 output here-->
// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart2);

// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and
// draws it.
function drawChart2() {

  // Create the data table.
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Hours');
  data.addColumn('number', 'Total');
  data.addRows(
  [<?=$piechartdata2?>]);
  // [['one',1],['two',2],['three',3]]);

  // Set chart options
  var options = {'title':'Service Distribution (Hours)',
                 'width':600,
                 'height':300};

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.BarChart(document.getElementById('chart2'));
  chart.draw(data, options);
}

</script>

<!--Table that will hold the pie charts-->
<table>
<tr><td id="chart1"></td></tr><tr><td id="chart2"></td></tr></table>

<?php
// output volunteer summary if requested
	if ($volsummary == 'ON') {
		echo '<h4>Volunteer Individual Summary&nbsp;&nbsp;
		<a class="btn btn-success btn-xs" href="rptcategorydetail.php?sd=<?=$sd?>&ed=<?=$ed?>">Start Over</a></h4>';
//		echo '<pre> ind '; print_r($ind); echo '</pre>';
		echo "<a href=\"downloads/VolSummary.csv\" download=\"VolSummary.csv\">DOWNLOAD CSV FILE</a>";
		echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
		echo '<table border=0 class="table-condensed">
		<tr><th>MCID</th><th>Name</th><th>SvcCnt</th><th>CatCnt</th><th>TotHrs</th><th>TotMiles</th></tr>';
		$vscsv[] = "MCID;Name;SvcCnt;CatCnt;TotHrs;TotMiles\n";
		ksort($ind);
		foreach ($ind as $k => $v) {
			$t = $v[Hours]; $m = $v[Mileage]; $cc = count($v[Cats]); $c = $v[Count];
			list($fn,$ln) = explode(';',$names[$k]);
			echo "<tr><td>$k</td><td>$fn&nbsp;$ln</td><td align=right>$c</td><td align=right>$cc</td><td align=right>$t</td><td align=right>$m</td></tr>";
			$vscsv[] = "$k;\"$fn $ln\";$c;$cc;$t;$m\n";
//			echo "Key: $k, cats count: " . count($v[Cats]); 
//			echo "<pre> v "; print_r($v); echo '</pre>';
			}
		echo '</table>';
		file_put_contents('downloads/VolSummary.csv',$vscsv);
		}

// output category summary if requested
	if ($catsummary == 'ON') {
//		echo '<pre> catsarray '; print_r($cats); echo '</pre>';
		echo '<h4>Volunteer Category Summary&nbsp;&nbsp;
		<a class="btn btn-success btn-xs" href="rptcategorydetail.php?sd=<?=$sd?>&ed=<?=$ed?>">Start Over</a></h4>';
		echo "<a href=\"downloads/VolCategorySummary.csv\" download=\"VolCategorySummary.csv\">DOWNLOAD CSV FILE</a>";
		echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
		echo '<table border=0 class="table-condensed">
		<tr><th>Category</th><th>MCID</th><th>Name</th><th>TotalHrs</th><th>TotMiles</th></tr>';
		$cscsv[] = "Category;MCID;Name;TotalHrs;TotMiles\n";
// $k = cat, $kk = mcid, $vv[VolTime] = total hrs, $vv[VolMileage] = total miles
		foreach ($categories as $k => $v) { 
			//echo "<tr><td>$k</td><td>$v[Name]</td>";
			foreach ($v as $kk => $vv) {
				$t = $vv[VolTime]; $m = $vv[VolMileage]; list($fn,$ln) = explode(';',$names[$kk]);
				echo "<tr><td>$k</td><td>$kk</td><td>$fn&nbsp;$ln</td><td>$t</td><td>$m</td></tr>";
				$cscsv[] = "$k;$kk;\"$fn $ln\";$t;$m\n"; 
				}
			}
		echo '</table>';
		file_put_contents('downloads/VolCategorySummary.csv',$cscsv);
		}

// output service details if requested
	if ($details == 'ON') {
		file_put_contents('downloads/VolServiceDetail.csv',$csv);
		echo '<h4>Volunteer Service Detail Records&nbsp;&nbsp;
		<a class="btn btn-success btn-xs" href="rptcategorydetail.php?sd=<?=$sd?>&ed=<?=$ed?>">Start Over</a></h4>';
		echo "<a href=\"downloads/VolServiceDetail.csv\" download=\"VolServiceDetail.csv\">DOWNLOAD CSV FILE</a>";
		echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
		echo '<table class="table-condensed" border="0">
		<tr><th>MCID</th><th>FName</th><th>LName</th><th>Date</th><th>Time</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>';
		foreach ($voldet as $k => $v) { 
			echo "$v";
			}
		echo	"</table>";		// row
		}
	}

?>
<br>--- End of Report ---<br>
</body>
</html>

