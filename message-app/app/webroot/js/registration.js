// registration.js
$(document).ready(function() {
    $('#registration-form').on('submit', function(e) {
        e.preventDefault();

        // Serialize the form data
        var formData = $(this).serialize();
        var errMsgCont = $(".error-message");

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'), 
            data: formData,
            success: function(data) {
                errMsgCont.addClass('d-none');
                window.location.href = '/registration-success-page'
            },
            error: function(res) {
                // Handle errors or validation errors from the server
                console.log("Error: ", res.responseJSON);
                if(res.responseJSON?.errors) {
                    errMsgCont.removeClass('d-none');
                    if (typeof res.responseJSON?.errors[0] === 'string' || res.responseJSON?.errors[0] instanceof String) {
                        errMsgCont.text(res.responseJSON?.errors[0]);
                    } else {
                        errMsgCont.text(Object.values(res.responseJSON?.errors)[0]);
                    }
                }
            }
        });
    })
})

