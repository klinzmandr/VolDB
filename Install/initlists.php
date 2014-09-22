<!DOCTYPE html>
<html>
<head>
<title>Vol DB Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
//include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'datautils.inc';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';



if ($action == 'continue') {
	// set headers for output file
	$filearray[] = "MCID:FName:LName:MemStatus:EmailAddress:PrimaryPhone:AddressLine:City:State:ZipCode:Lists\n";
	
	$inputarray = file('Listdb.csv');
	$inputarraycount = count($inputarray);
	//echo "Input List Array: $inputarraycount<br />";
	foreach ($inputarray as $l) {
		list($fname,$lname,$pw,$email,$phone,$addr,$city,$st,$zip,$lists) = explode(":",$l);
		$lists = rtrim($lists);
		//echo "Email: $email, Lists: $lists<br />";
		if (strlen($email) == 0) {
			$noemailarray[] = $fname . " " . $lname;
			continue;
			}
		$sql = "SELECT * from `members` where `EmailAddress` = '$email';";
		$res = doSQLsubmitted($sql);
		$rowcnt = $res->num_rows;
		
		if ($rowcnt > 1) {
			//echo "***mulitple emails on database for email address $email<br />";
			$multiemailarray[] = $email;
			continue;
			}
		$outarray = array();
		if ($rowcnt == 0) {
			//echo "No member record for email address: $email<br />";
			$norecsarray[] = $email;
			$mcid = '';
			}
		if ($rowcnt == 1) {
			$r = $res->fetch_assoc();
			$mcid = $r[MCID];
			}
		$outarray[] = $mcid;
		$outarray[] = $fname;
		$outarray[] = $lname;
		$outarray[] = 2;							// MemStatus for volunteer
		$outarray[] = $email;
		$outarray[] = $phone;
		$outarray[] = $addr;
		$outarray[] = $city;
		$outarray[] = $st;
		$outarray[] = $zip;
		$outarray[] = $lists . "\n";  // line feed needed as record separator
		$filearray[] = implode(':',$outarray);
		}
	if (count($noemailarray) > 0) {
		echo '<pre>Missing Email Addresses - Ignored '; print_r($noemailarray); echo '</pre>';
		}
	if (count($multiemailarray) > 0) {
		echo '<pre>Multiple Emails - Ignored '; print_r($multiemailarray); echo '</pre>';
		}
	if (count($norecsarray) > 0) {
		echo '<pre>No Membership Records - included '; print_r($norecsarray); echo '</pre>';
		}
	echo 'Matched email addresses producing an MCID for record.<br />
	NOTE:  missing MCID values means that a member record is MISSING from the member database and must be supplied before importing this file into the vols database!<br />';
	echo "filearray: ".count($filearray)."<br />";
	echo '<pre> Input List Array '; print_r($filearray); echo '</pre>';
	file_put_contents('Listdb.withMCID.csv',$filearray);
	exit;
	}
	
	
if ($action == '') {
print <<<pagePart1
<h3>Volunteer List DB Update</h3>
<p>This script is to initialize the volunteer names and distro lists of the database from the original vol list system.  It matches the original vol list entry by using the email address provided, captures the MCID from that match then makes sure that the database entry is set to a status of 2 and moves the lists for that entry into the lists db column.  An attempt is also made to identify the type of volunteer (MCtype) field based on the first list code identified (if there is more than 1.)</p>
<p>This script is a one time deal and should only be used to initialize the database ONE time.  Its probably a good idea to back the database us prior to running this one.</p>
<p>The program parses the CSV input file from the vols/Lists.csv application, looks each entry up in the member database using the unique email address and creates a report and accompanying output report summarizing the actions. </p>
<p>The resulting CSV file is formated for input directly in to the vols database.</p>
<a class="btn btn-primary" href="initlists.php?action=continue">CONTINUE</a>
pagePart1;
exit;
}
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
