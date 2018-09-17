var err      = [];
var err1     = [];
var ErrorMsg = [];
$(document).ready(function() {
    $("#support_submit").click(function() {
        $("#support_form").validate({
            rules: {
                support_name: {
                    required: true
                },
                support_email: {
                    required: true,
                    email: true
                },
                support_phone: {
                    required: true
                },
                support_msg: {
                    required: true
                }
            },
            messages: {
                support_name: {
                    required: "Please enter your name"
                },
                support_email: {
                    required: "Please enter your email address",
                    email: "Please enter your valid email address"
                },
                support_phone: {
                    required: "Please enter your phone number"
                },
                support_msg: {
                    required: "Please enter your message"
                }
            },
            errorElement: "span",
            wrapper: "span",
            errorPlacement: function(error, element) {
                var ErrorMsg = error.text();
                err1.push(ErrorMsg+'<br>');
            },
        });

        if ($("#support_form").valid()) {
          save_info();
        } else {
          var seprate = unique(err1);
          var error = seprate.join('');
          document.body.scrollTop = document.documentElement.scrollTop = 0;
          $("#ErrMsg").html('');
          $("#ErrMsg").html('<span style="color:red;font-weight:bold;margin-top:10px;">'+error+'</span>');
          err1 = [];
          $("#support_msg").removeClass('error');
          return false;
        }



    });
});

function save_info(){
    $.ajax({
        url: base_url + "/ajax/support_ajax.php",
        type: "POST",
        dataType: "json",
        data: "type=support_query&support_name=" + $("#support_name").val() + "&support_email=" + $("#support_email").val() + "&support_phone=" + $("#support_phone").val() + "&support_msg=" + $("#support_msg").val(),
        beforeSend: function() {
            $('#support-info').loader({
                'loader': 'on',
                'img_url': base_url + "/images/ajax-loader.gif"
            });
        },
        success: function(data) {
            $('#support-info').loader({
                'loader': 'off'
            });
            if (data['error'] == 'false') {
                $('#support_form')[0].reset();
                $('#formSubmitSuccess').click();
            }
        }
    });
}
function unique(list) {
    var result = [];
    $.each(list, function(i, e) {
      if ($.inArray(e, result) == -1) result.push(e);
    });
    return result;
}






