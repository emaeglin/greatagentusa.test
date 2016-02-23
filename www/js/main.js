$(document).ready(function(){
    $(".createuser").carousel(0);
    createusershowed  =false;
    $('#housemodal').modal('hide');
    $(".createuser").removeClass("active");
    $('#paynow').modal('hide');
    $(".modal-backdrop").addClass("hide");
  }); 
$('body').click(function() {
  if (!createusershowed ) {
    $('#paynow').modal('show'); 
    $("#ClientName").focus();createusershowed =true; 
  }
});


var typingTimer;                //timer identifier
var doneTypingInterval = 1000;  //time in ms

$('#ClientPhone').on('keyup change', function () {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(checkPhoneNumber, doneTypingInterval);
});


$('#ClientPhone').on('keydown', function () {
  clearTimeout(typingTimer);
});

$('#clientprofileform').on('submit', function () {
  if (!phoneNumberParser_e()) {
      return false;
  } else {
      return true;
  }
});


function checkPhoneNumber() {
    if (!phoneNumberParser_e()) {
        $('#ClientPhone').addClass('has-error');
    } else {
        $('#ClientPhone').removeClass('has-error');
    }
}

function phoneNumberParser_e() {
    var str = $('#ClientPhone').val();
    var res = str.split("");
    var n = 0;
    for (k in res) {
        if (!isNaN(parseInt(res[k], 10))) {
            n++;
        }
    }
    if (n >= 10 && n <= 12) {
            return true;
    }
    return false;
}