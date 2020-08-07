<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();
// include 'Incls/vardump.inc.php';

?>
<!DOCTYPE html>
<html>
<head>
<title>Vol Time Entry</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/jsutils.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="./Incls/datevalidation.js"></script>
<script src="js/bootstrap3-typeahead.js"></script>

<?php
include 'Incls/datautils.inc.php';
// include 'Incls/mainmenu.inc.php';
// include 'Incls/seccheck.inc.php';

$sql = "SELECT `MCID`,`FName`,`LName` from `members` 
WHERE `MemStatus` = 2
	AND	`Lists` NOT LIKE '%VolInactive%'  
ORDER BY `MCID`;";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
// create the string for the javascript arrays to download
$vols = '[';		// create string for form typeahead
while ($r = $res->fetch_assoc()) {
	$mcid = preg_replace("/[\(\)\.\-\ \/\&]/i", "", $r['MCID']);
	$lname = preg_replace("/[\(\)\.\-\ \/\'\&]/i", "", $r['LName']);
	$fname = preg_replace("/[\(\)\.\-\ \/\'\&]/i", "", $r['FName']);
	$vols .= " '$mcid,$lname,$fname',";
	}
$vols = rtrim($vols,',') . ']';
// $vols = $vols . "]";
// echo "vols: $vols<br>";
?>

<script>
// initial setup of jquery function(s) for page
$(function() {
	// alert("on document load");
	
$('form input, button').keydown(function (e) {
  var inp = this.id;
  console.log("on keydown event: "+inp);
  if (inp == 'Btn') return;
  if (e.keyCode == 13) {
    var inputs = $(this).parents("form").eq(0).find(":input");
    if (inputs[inputs.index(this) + 1] != null) {
      inputs[inputs.index(this) + 1].focus();
      }
    e.preventDefault();
    return false; 
  }
  });
});  // end ready function
</script>
<script>
$(function() { 
$("#Btn").click(function(evt) {
  // alert('add new rec');
  evt.preventDefault();
  var kid = this.id;
  console.log("keyid: "+kid);
  console.log("date: "+$("#date").val());
  // console.log("id: "+$("#mcid").val());
  $("#date").val(''); $("#date").focus();
  $(".mcid").val('');
  return;
  });
});
</script>
<div class="container">
<h3>Volunteer Time Entry (Prototype)
<span id="helpbtn" title="Help" class="glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span>
&nbsp;&nbsp;   <a href="javascript:self.close();" class="hidden-print btn btn-primary"><b>CLOSE</b></a>
</h3>
<div id=help>
Explaination of page.
</div>

<h4>Header 4 title to click</h4>
<!-- page contents -->
<br><br>
<form>
<input type=text id=date value='' autofocus onchange="ValidateDate(this)" autocomplete="off"><br>
<input type="text" class="mcid" data-provide="typeahead" autocomplete="off" /><br>
<input type="text" class="mcid" data-provide="typeahead" autocomplete="off" /><br><br>
<input type=text id=DC value=''><br>
<input type=text id=DD value=''><br>
<button id=Btn>Enter</button>
</form>
</div>  <!-- container -->

<script>
var vols = <?=$vols?>; 
$('.mcid').typeahead({
  source: vols,
  items: 4
  })

</script>

</body>
</html>
