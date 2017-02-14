
function ValidateDate(fld)  {
var dname = fld.id;
var stripped = fld.value.replace(/[\(\)\.\-\ \/]/g, '');
var errmsg = "";
var d = new Date();
var curr_day = d.getDate();
if (curr_day <= 9) curr_day = "0" + curr_day;
var curr_month = new String(d.getMonth() + 1); 				//Months are zero based
if (curr_month <= 9) curr_month = "0" + curr_month;
var curr_year = d.getFullYear();

// logic to clear the date field if improperly entered
if (stripped == 'xx') {
  $('#'+dname).css("background-color", "white");
  errmsg = "";
  $('#'+dname).val('');
  return true;
  }

// process text entry of now and set today's date in
var nowdate = curr_year + "-" + curr_month + "-" + curr_day;
if (stripped == 'now') {
	$('#'+dname).val(nowdate);
	$('#'+dname).css("background-color", "white")
	return true;
	}
if (stripped.length == 2) {		// assume entry of dd
	if (!stripped.match(/^(0[1-9]|[12][0-9]|3[01])/))  {
		errmsg += "Invalid day entered.\n"; 
		}
	else {
		stripped = curr_year + curr_month + stripped.substr(0,2);
		}
	}
if (stripped.length == 4) {		// assume entry of mmdd
	if(!stripped.match(/^(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])/))  { 
		errmsg += "Invalid month/day entered.\n"; 
		}
	else {
		stripped = curr_year + stripped.substr(0,2) + stripped.substr(2,2);
		}
	}
if (stripped.length != 8) {
	errmsg += "Invalid date length entered.\n";
	}
if(!stripped.match(/^[12][90][0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])/))  { 
	errmsg += "Invalid date entered.\n";
	}
var now = new Date(); 
var nowday = now.getDate(); var nowmo = now.getMonth(); var nowyr = now.getFullYear();
var nowms = Date.UTC(nowyr, nowmo, nowday);
var newyr = stripped.substr(0,4); var newmo = stripped.substr(4,2)-1; var newday = stripped.substr(6,2);
chkms = Date.UTC(newyr, newmo, newday);
if (chkms > nowms) {
	errmsg += "Date entered is in the future!\n";
	}
if (errmsg.length > 0) {
	errmsg += "\nValid formats: YYYY-MM-DD, YYYYMMDD,\nYYYY.MM.DD, YYYY/MM/DD, YYYYMMDD or 'now'";
	errmsg += "\nif MMDD entered, use current year.";
	errmsg += "\nIf DD entered use current month and year.";
	fld.focus();
	fld.style.background = 'Pink';
	alert(errmsg);
	return true;
	}
var newval = stripped.substr(0,4) + "-" + stripped.substr(4,2) + "-" + stripped.substr(6,3);
fld.value = newval;
fld.style.background = 'White';
return true;
}
