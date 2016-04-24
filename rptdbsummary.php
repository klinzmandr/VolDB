<!DOCTYPE html>
<html>
<head>
<title>Database Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

echo '<div class="container">';
print <<<pagePart1
<h3>Database Summary*&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
Connection Info: $mysqli->host_info, 
Client Info: $mysqli->client_info, 
Server Info: $mysqli->server_info<br />
Database in use: $_SESSION[DB_InUse]<br />
pagePart1;
$sql = "SELECT * FROM `members` WHERE 1";
$res = doSQLsubmitted($sql);
$numrows = $res->num_rows;
$thisyear = date('Y',strtotime(now));
$thisyrmo = date('Y-m',strtotime(now));
$memdatemissing = 0; $memdateYTD = 0; $memdateMo = 0; 
$inactivetrue = 0; $inactivefalse = 0; $inactivemissing = 0; $inactive_expired = 0; 
$neither = 0; $state = 0; $noaddr = 0; $nocity = 0; $nostate = 0; $nozip = 0; $missingall = 0;
$nullemail = 0;
while ($r = $res->fetch_assoc()) {
	$memstatus=$r[MemStatus];
	$memstatuscount[$memstatus] += 1;
	if ($r[MemStatus]==1) $memactive += 1;
	$memdate=$r[MemDate];
	if ($memdate == "") $memdatemissing += 1;
	else {
		$memdateyr=substr($memdate,0,4); $memdateyrcount[$memdateyr]+=1;
		$memdatemo=substr($memdate,5,2); $memdateyrcount[$memdatemo]+=1;
		$memdateyrmo=substr($memdate,0,7); $memdateyrmocount[$memdateyrmo]+=1;
		}
	//echo "year: $thisyear, record date: $memdateyr<br>";
	if (($r[MemStatus] == 1) AND ($memdateyr == $thisyear)) $memdateYTD += 1;
	//echo "memdatemo: $memdateyrmo, this month: $thisyrmo<br>";
	if (($r[MemStatus] == 1) AND ($memdateyrmo == $thisyrmo)) $memdateMo += 1;
	
	// testing address fields
	if ($r[AddressLine] == '') $noaddr += 1;
	if ($r[City] == '') $nocity += 1;
	if ($r[State] == '') $nostate += 1;
	if ($r[ZipCode] == '') $nozip += 1;
	if (($r[AddressLine] == '') && ($r[City] == '') && ($r[State] == '') && ($r[ZipCode] == '')) $missingall++;
	
	// testing inactive fields	
	$inactive = $r[Inactive];
	if ($inactive == 'TRUE') $inactivetrue+=1;
	if ($inactive == 'FALSE') $inactivefalse+=1;
	if (($inactive == 'TRUE') AND ($r[Inactivedate] == '')) $inactivemissing +=1;
	if (($inactive == 'TRUE') AND ($r[Inactivedate] != '')) {
		$expired = strtotime("-90 days"); $inactive = strtotime($r[Inactivedate]);
		if ($inactive <= $expired) $inactive_expired++;
		}
	
	if ($r[EmailAddress] != "") { $email += 1; }
	if ($r[PrimaryPhone] != "") { $phone += 1; }
	if (($r[PrimaryPhone] == "") AND ($r[EmailAddress] == "")) { $neither += 1; }
	if ($r[E_Mail] == 'TRUE') { $okemail += 1; }
	if ($r[E_Mail] == 'FALSE') { $noemail += 1; }
	if ($r[E_Mail] == '') { $nullemail += 1; }
	}
print <<<formatMembers1

<h4>Member Summary:</h4>
Total number of member records: $numrows<br />
<div class="row">
<div class="col-sm-2"><b>New Members</b></div>
</div>  <!-- row -->
<div class="well">
<div class="row">
<div class="col-sm-4">Active (Status=1) Members: $memactive</div>
<div class="col-sm-4">New Members (YTD): $memdateYTD</div>
<div class="col-sm-4">New Members (This Month): $memdateMo</div>
</div>
<div class="row">
<div class="col-sm-4">Member missing Date Joined: $memdatemissing</div>
</div>
</div>  <!-- well -->
<div class="row">
<div class="col-sm-2"><b>Member Status</b></div>
</div>  <!-- row -->
<div class="well">
<div class="row">
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">Member Status: </div>
formatMembers1;
if (count($memstatuscount) > 0) {
	ksort($memstatuscount);
	foreach ($memstatuscount as $k=>$v) {
		echo "<div class=\"col-sm-1\">$k=$v</div>";
		}
	}
print <<<formatMembers2
</div>  <!-- row -->
</div>  <!-- well -->
<div class="row">
<div class="col-sm-3"><b>Members Inactive:</b></div>
</div>

formatMembers2;

print <<<formatMembers3
<div class="well">
<div class="row">
<div class="col-sm-4">Inactive False(all records): $inactivefalse</div>
<div class="col-sm-3">Inactive True (all records): $inactivetrue</div>
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">Inactive True missing date: $inactivemissing</div>
<div class="col-sm-6">Expired Inactive (Inactive > 90 days): $inactive_expired</div>
</div>

</div>  <!-- well -->
<div class="row">
<div class="col-sm-4"><b>Members Addresses:</b></div>
</div>
<div class="well">
<div class="row">
<div class="col-sm-4">Missing Address Line: $noaddr</div>
<div class="col-sm-4">Missing City: $nocity</div>
</div>
<div class="row">
<div class="col-sm-4">Missing State: $nostate</div>
<div class="col-sm-4">Missing Zip: $nozip</div>
</div>
<div class="row">
<div class="col-sm-4">Missing All Address Info: $missingall</div>
</div>
</div>  <!-- well -->
<div class="row">
<div class="col-sm-6"><b>Member phone numbers and email addresses:</b></div>
</div>
<div class="well">
<div class="row">
<div class="col-sm-4">With Email Address: $email</div>
<div class="col-sm-4">With Phone Number: $phone</div>
</div>
<div class="row">
<div class="col-sm-4">With Neither: $neither</div>
</div>
<div class="row">
<div class="col-sm-3">Email OK: $okemail</div>
<div class="col-sm-3">Email Not OK: $noemail</div>
<div class="col-sm-3">Email NULL: $nullemail</div>
</div>
</div> <!-- well -->

formatMembers3;
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
