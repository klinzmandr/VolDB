<!-- Form change variable must be global -->
var chgFlag = 0;

$(document).ready(function() {
  $('.updb').prop('disabled', true);
  $("#Xmsg").fadeOut(2000);
  $("#help").hide();
  $('.updb').prop('disabled', true);

// to detect and change on form
var $form = $('form');
// var formValues = $('form').getFormValues();  // save form in case of reset
var origForm = $form.serialize();   // to save field values on initial load
 
$('form :input').on('keyup input', function() {
  if ($form.serialize() !== origForm) {         // check for any changes
    chgFlag++;
    $('.updb').prop('disabled', false);    
    $(".updb").css({"background-color": "red", "color":"white"});
    // console.log("chgFlag: "+chgFlag);
    return;
    }
  });

$("#helpbtn").click(function() {
  $("#help").toggle();
  });

$("form").change(function(){
  if (this.id == "filter") return;  // ignore filter input
  chgFlag += 1; 
  $(".updb").css({"background-color": "red", "color":"black"});
  $('.updb').prop('disabled', false);    
  // setInterval(blink_text, 1000);
  });
  
$("#reset").click(function() {
  chgFlag = 0;
  $(".updb").css({"background-color": "grey", "color":"black"});
  $('.updb').prop('disabled', true);    
  });  

$(".dropdown").click(function(event) {
	if (chgFlag <= 0) { return true; }
	var r=confirm("All changes made will be lost.\n\nConfirm abandoning changes and leaving page by clicking OK.");	
	if (r == true) { 
    chgFlag = 0; 
    return true; 
	  }
  event.preventDefault();
  return false;
  });

// add of bootstrap modal for error messages
$('body').append('<div class="hidden-print modal fade" id="msgdialog"> \
  <div class="modal-dialog"> \
    <div class="modal-content"> \
      <div class="modal-header"> \
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> \
        <h4 id="msgdialogtitle" class="modal-title"></div> \
      <div id="msgdialogcontent" class="modal-body"></div> \
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div> \
    </div> \
  </div> \
 </div>');
 
// does case insensitive search in 'filterbtn1'
$.extend($.expr[":"], {
  "containsNC": function(elem, i, match, array) {
  return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
  }
  });

$("#filterbtn2").click(function() {
  $("#filter").val("");
  $("#filter").focus();
  $('tr').show();
  chgFlag = 0;
  });
  
$("#filter").keyup(function() {
  var filter = $("#filter").val();
  if (filter.length) {
    // alert("filter button clicked:" + filter);
    $('tr').hide().filter(':containsNC('+filter+')').show();
    $("#head").show();
    chgFlag = 0;
    return;
    }
  $('tr').show();
  chgFlag = 0;
  });

});

