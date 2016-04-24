// Runs when the document is ready
// requires input fields to be id'ed as 'sd' (start date) and 'ed' (end date)
$(document).ready(function () {
	$("#sd").datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
		})
	.on('changeDate', function (selected) {
    var startDate = new Date(selected.date.valueOf());
    $('#ed').datepicker('setStartDate', startDate);
    /*  // logic to add 31 days to start date and update end date
    var d = new Date(startDate);
    d.setDate(d.getDate() +31);
		var curr_year = d.getFullYear();    
    var curr_month = d.getMonth() + 1; //Months are zero based
		if (curr_month <= 9) curr_month = "0" + curr_month;
		var curr_day = d.getDate();
		if (curr_day <= 9) curr_day = "0" + curr_day;
		var nowdate = curr_year + "-" + curr_month + "-" + curr_day
    document.getElementById("ed").value = nowdate;
    */
		})
	.on('clearDate', function (selected) {
    $('#sd').datepicker('setStartDate', null);
		});

	$("#ed").datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
		})
	.on('changeDate', function (selected) {
    var endDate = new Date(selected.date.valueOf());
    $('#sd').datepicker('setEndDate', endDate);
		})
	.on('clearDate', function (selected) {
    $('#sd').datepicker('setEndDate', null);
		});
	});
