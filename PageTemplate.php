<!DOCTYPE html>
<html>
<head>
<title>Template Title</title>
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
//include 'Incls/vardump.inc.php';
//include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
//include 'Incls/datautils.inc.php';

print <<<pagePart1
<h3>Template Heading</h3>
<p>Page description ...</p>


pagePart1;

?>

</body>
</html>
