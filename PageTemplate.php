<!DOCTYPE html>
<html>
<head>
<title>Templatet Title</title>
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
//include 'Incls/datautils.inc';

// SELECT `MCID`, SUM(`VolTime`), SUM(`VolMileage`) FROM `voltime` GROUP BY `MCID`;
print <<<pagePart1
<h3>Template Heading</h3>
<p>Page description ...</p>


pagePart1;

?>

</body>
</html>
