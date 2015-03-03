<?php

print <<<menupart1
<script>
<!-- Form change variable must be global -->
var chgFlag = 0;

function chkchg() {
	if (chgFlag <= 0) { return true; }
	var r=confirm("All changes made (" + chgFlag + ") will be lost.\\n\\nConfirm by clicking OK.");	
	if (r == true) { chgFlag = 0; return true; }
		return false;
	}

// add '<body onchange="flagChange()"> to all pages needed
function flagChange() {
//	alert("change flagged");
	chgFlag += 1;
	return true;
	}

<!-- ignore any change to the filter input field -->
function ignorefilter() {
	chgFlag -= 1;
	return true;
	}

</script>
<style>
body { padding-top: 50px; }      <!-- add padding to top of each page for fixed navbar -->
</style>
<!-- ========= define main menu bar and choices ====== -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="navbar-collapse-1">
    <ul class="nav navbar-nav">
      <li><a onclick="return chkchg()" href="admin.php"><b>Home</b></a></li>
      <li><a onclick="return chkchg()" href="volinfotabbed.php">Info</a></li>
			<!-- <li><a onclick="return chkchg()" href="#">????</a></li> -->
			
menupart1;

print <<<menuPart2
<!-- ========= define Email menu item ========== -->
<li><a onclick="return chkchg()" href="sendchooser.php">Send Email</a></li>

<!-- ======== define List menu dropdown ======== -->
<!-- <li class="dropdown open">  example: to have open on load -->
<li class="dropdown">
<a id="drop2" class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">List Mgr<b class="caret"></b></a>
<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
	<li><a onclick="return chkchg()" href="listalllists.php">Display Lists Info</a></li>
	<li><a onclick="return chkchg()" href="listspecificlist.php">View Specific List</a></li>
	<li><a onclick="return chkchg()" href="listedittimerecord.php">List/Edit Time Record</a></li>
	<li><a onclick="return chkchg()" href="listcreatenew.php">Create/Update Mail Lists</a></li>
	<li><a onclick="return chkchg()" href="listvolcategories.php">Create/Update Vol Time Categories</a></li>
	<li><a onclick="return chkchg()" href="voladdition.php">Add New Volunteer</a></li>
	<li><a onclick="return chkchg()" href="voladdnewuser.php">Add New User</a></li>
</ul>
</li>  <!-- class="dropdown" -->

<!-- ========== define Time Entry menu item =========== -->
<li><a onclick="return chkchg()" href="voltimeentry.php">Time Entry</a></li>

<!-- ========== define Courses menu dropdown ======== -->
<li class="dropdown">
<a id="drop1" class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Courses<b class="caret"></b></a>
<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
	<li><a onclick="return chkchg()" href="edentry.php">Enter Course Data</a></li>
	<li><a onclick="return chkchg()" href="edcourses.php">List/Add/Update/Display Course Info</a></li>
	<li><a onclick="return chkchg()" href="edlisteditrecord.php">List/Edit Course Record Entry</a></li>	
	</ul>
</li> <!-- class="dropdown" -->

<!-- ========== define reports dropdown ======= -->
<!-- <li class="dropdown open">  example: to have open on load -->
<li class="dropdown">
<a id="drop1" class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Reports<b class="caret"></b></a>
<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
	<li><a href="rptperiodsummary.php" target="_blank">Period Service Summary</a></li>	
	<li><a href="rptperiodanalysis.php" target="_blank">Period Service Analysis</a></li>	
	<li><a href="rptperioddetail.php" target="_blank">Period Service Detail</a></li>
	<li><a href="rptcategorydetail.php" target="_blank">Category Service Detail</a></li>
	<li><a href="rptvoltimesummary.php" target="_blank">Vol Time Summary</a></li>
	<li><a href="rptmcidtimereport.php" target="_blank">List Time for MCID</a></li>
	<li><a href="rptmcidedreport.php" target="_blank">List Courses for MCID</a></li>
	<li><a href="rptvolunteerroster.php" target="_blank">Volunteer Roster</a></li>
	<li><a href="rptvolexception.php" target="_blank">Volunteer Exception Report</a></li>
	<li><a href="rptmaillogviewer.php" target="_blank">Review Mail Log</a></li>
	<li><a href="maillogchecker.php" target="_blank">Review Server Mail Log</a></li>
	<li><a href="rptdbsummary.php" target="_blank">Database Summary</a></li>
	<li class="divider"></li>
	<li><a href="rptVolUtility.html" target="_blank">Vol Utility User Info</a></li>
	<li><a href="#">Other report(s) added as needed</a></li>
	<li class="divider"></li>
	<li><a href="#myModal" data-toggle="modal" data-keyboard="true">About Volunteer DB</a></li>
</ul>
</li>  <!-- class="dropdown" -->
<script>
function setupmcid(theForm)  {
	var fld = theForm.filter.value;
	if (fld == "--none--") { theForm.filter.value = ""; return; }
	//alert("Filter value:" + fld);
	fld = theForm.filter.value = fld.toUpperCase();  // format MCID to upper case
	if (fld.length == 5)  {
		theForm.action = "volinfotabbed.php";		// assume an exact MCID entered
		return true;
		}
	if (fld == "") { theForm.filter.value = "--none--"; } // else search for it
	return true;
	}
</script>
<form name="filter" action="volfilterlist.php" method="post" class="navbar-form pull-left" onsubmit="return setupmcid(this)">&nbsp;&nbsp;&nbsp;
  <input autofocus autocomplete="off" type="text" class="form-control" style="width: 100px;" value="$filter" name="filter" onChange="ignorefilter()" placeholder="MCID">
  <input type="submit" name="submit" value="Lookup" class="btn btn-default" onClick="return chkchg()">
</form>

menuPart2;

print <<<theRest
</ul>		<!-- nav navbar-nav  *the menu bar* -->
</div>  <!--/.nav-collapse -->
</nav>  <!-- class = "navbar" -->
<!-- End mainmenu.inc -->

theRest;

print <<<theModal
<!-- ============== ABOUT Modal  ============== -->  
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">About VolDB</h4>
      </div>
    <div class="modal-body">
   <p>The Volunteer Database System is intended for the use by Pacific Wildlife Care to manage all volunteer information, collect volunteer time and effort as well as provide an email communication with them collectively.</p>
   <p>VolDB is offered under the General Public License (GPL) Version 3.  There is no license fee assoicated with the use of this system or any of the components used to develop it.  All improvements or updates made must be made available to the VolDB community.</p>
   <p><a href="docs/VolDB_Release_Info.html" target="_blank" title="VolDB Release History">Release Information Documentation</a></p>
    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end of modal -->

theModal;
?>