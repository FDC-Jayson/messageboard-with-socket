
$(document).ready(function() {
    $('#upload-image').on('change', function(e) {
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
        };

        reader.readAsDataURL(file);
    });

    $( "#datepicker" ).datepicker();
    $('#upload-image-btn').click(function() {
        $('#upload-image').click();
    });

    $('#edit-profile-form').on('submit', function(e) {
        e.preventDefault();

        // Create a FormData object to store the form data, including the file input
        var formData = new FormData();
        var errMsgCont = $(".error-message");

        // Append non-file input data manually
        formData.append('data[UserProfile][email]', $('#UserProfileEmail').val());
        formData.append('data[UserProfile][name]', $('#UserProfileName').val());
        formData.append('data[UserProfile][gender]', $('#UserProfileGender').val());
        formData.append('data[UserProfile][birthdate]', $('#UserProfileBirthdate').val());
        formData.append('data[UserProfile][hubby]', $('#UserProfileHubby').val());

        // Append the file input data
        formData.append('data[UserProfile][imageFile]', $('#upload-image')[0].files[0]);
        formData.append('data[UserProfile][image]', $('input[name="data[UserProfile][image]"]').val());

        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            url: '/api/update-profile', 
            data: formData,
            enctype: 'multipart/form-data',
            success: function(data) {
                errMsgCont.addClass('d-none');
                window.location.href = '/userProfiles/details'
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

                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        });
    })
})

