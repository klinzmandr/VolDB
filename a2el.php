<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Error Log</title>
</head>
<body>
<script src="./jquery.js"></script>
<script>
$(function() {
 // alert("doc load"); 
 $("#del").click(function() {
   if (confirm("Delete error log?")) {
     // alert('yes clicked');
     return; }
   // alert("cancel clicked");
   return false;
 });
 
});
</script>
<?php

$fpath = './error_log';

$fsize = isset($_REQUEST['fsize']) ? $_REQUEST['fsize'] : 10;

if (isset($_REQUEST['delete'])) {
  // echo 'delete requested<br>';
  if (!unlink($fpath)) {
    echo "Delete of $fpath was NOT successful<br>";
    }
  }

$statcmd = "cat $fpath";
exec($statcmd, $fs, $status);

if ($status <> 0) {
  echo "<h3>No log file exist to display</h3>";
  echo "File access status: $status<br>";
  echo "File path: $fpath<br>";
  echo "<a href='a2el.php'>RETRY</a><br>";
  }

$lines = count($fs);    // line count of file
// echo "Log size: $lines<br>";
if ($fsize <=0) $fsize = 10;

$cmd = "tail -n $fsize $fpath";
// echo "$cmd<br>";
exec($cmd, $fcontents, $status);

echo "<h3>Apache Error Log (lines: $lines)</h3>";
echo "<a href='a2el.php?fsize=10'>Last 10</a>&nbsp;&nbsp;&nbsp;";
echo "<a href='a2el.php?fsize=25'>Last 25</a>&nbsp;&nbsp;&nbsp;";
echo "<a href='a2el.php'>RESET</a>&nbsp;&nbsp;&nbsp;";
echo "<a id=del href='a2el.php?delete'>Delete Log</a><br>";
foreach ($fcontents as $l) {
  echo "$l<br>";
  }
  
echo "==== End of Log ====<br>";
exit;

?>