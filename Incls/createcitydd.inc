<?php
function createddown() {
	$locs = readdblist('Locs');
	//echo "<pre> locs: "; print_r($locs); echo "</pre>";
	$locsarray = formatdbrec($locs);
	//echo '<pre> locslist '; print_r($locsarray); echo '</pre>';
	$locslist = "['" . implode("','", $locsarray) . "'];";
	//echo "loclist: $locslist<br>";
	return($locslist);
}
?>