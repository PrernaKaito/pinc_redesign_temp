
function reveal() {
    var reveals = document.querySelectorAll(".reveal");

    for (var i = 0; i < reveals.length; i++) {
        var windowHeight = window.innerHeight;
        var elementTop = reveals[i].getBoundingClientRect().top;
        var elementVisible = 50;

        if (elementTop < windowHeight - elementVisible) {
            reveals[i].classList.add("active");
        } else {
            reveals[i].classList.remove("active");
        }
    }
}

window.addEventListener("scroll", reveal);
reveal();


function scrollToForm() {
    document.getElementById("get-in-touch-form-section").scrollIntoView({
        behavior: 'smooth' // Enables smooth scrolling
    });
}


// news letter JS

 
    $(document).ready(function() {
        let   recaptchaindexWidgetId;
        let   divRecaptchaWidgetId;
    
        // Render the reCAPTCHA widgets
        function renderReCAPTCHAs() {
            recaptchaindexWidgetId = grecaptcha.render(' recaptchaindex', {
                'sitekey': '6LcXmoUqAAAAAFk-ZYLjAPspAXmRFxxGchnSxkvx', // Replace with your reCAPTCHA site key
            });
    
        divRecaptchaWidgetId = grecaptcha.render('divRecaptcha', {
                'sitekey': '6LcXmoUqAAAAAFk-ZYLjAPspAXmRFxxGchnSxkvx', // Replace with your reCAPTCHA site key
            });
        }
    
        // Function to validate form fields
        function validateFields(fields) {
            let isValid = true;
            fields.forEach(function(field) {
                const value = $('#' + field.id).val();
                const errorElement = $('#' + field.id + '_error');
                errorElement.text('');
                if (value === '') {
                    errorElement.text(field.emptyMessage);
                    isValid = false;
                } else if (field.regex && !field.regex.test(value)) {
                    errorElement.text(field.regexMessage);
                    isValid = false;
                }
            });
            return isValid;
        }
    
        // Function to handle form submission via AJAX
        function handleFormSubmit(formId, url, fields, widgetId) {
            if (validateFields(fields)) {
                const data = { form_type: formId };
                fields.forEach(function(field) {
                    data[field.name] = $('#' + field.id).val();
                });
    
                // Get the specific reCAPTCHA response
                const recaptchaResponse = grecaptcha.getResponse(widgetId);
    
                if (recaptchaResponse.length === 0) {
                    alert("Please complete the reCAPTCHA verification.");
                    return; // Prevent form submission if reCAPTCHA is not completed
                }
    
                // Append the reCAPTCHA response to the form data
                data['recaptcha_response'] = recaptchaResponse;
    
                // Perform the AJAX request
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function(response) {
                        console.log('Response:', response);
                        $('#' + formId)[0].reset(); // Reset the form after successful submission
                        grecaptcha.reset(widgetId); // Reset the reCAPTCHA widget
                        $('#thankYouModal').modal('show'); // Show a thank you modal
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', xhr.responseText);
                        $('#result').addClass('alert alert-danger').removeClass('d-none').text('Error: ' + xhr.responseText);
                    }
                });
            }
        }
    
        // Form 1 (Contact Form)
        const contactFormFields = [
            { id: 'name', name: 'name', emptyMessage: 'Please enter your name.', regex: /^[a-zA-Z ]+$/, regexMessage: 'Name can only contain letters and spaces.' },
            { id: 'email', name: 'email', emptyMessage: 'Please enter your email address.', regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, regexMessage: 'Please enter a valid email address.' },
            { id: 'mobile', name: 'mobile', emptyMessage: 'Please enter your mobile number.', regex: /^\d{10}$/, regexMessage: 'Mobile number must be 10 digits.' },
            { id: 'city', name: 'city', emptyMessage: 'Please enter your city name.', regex: /^[a-zA-Z ]+$/, regexMessage: 'City name can only contain letters and spaces.' },
            { id: 'inquiryType', name: 'inquiryType', emptyMessage: 'Please Select' },
            { id: 'textmessage', name: 'textmessage', emptyMessage: 'Please enter your message.' }
        ];
    
        // Handle submit for Contact Form
        $('#submit').click(function(e) {
            e.preventDefault();
            handleFormSubmit('contactForm', 'contact.php', contactFormFields, recaptchaindexWidgetId);
        });
    
        // Form 2 (Another Contact Form)
        const contactFormerFields = [
            { id: 'fullname', name: 'fullname', emptyMessage: 'Please enter your full name.', regex: /^[a-zA-Z ]+$/, regexMessage: 'Full name can only contain letters and spaces.' },
            { id: 'mail', name: 'mail', emptyMessage: 'Please enter your email address.', regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, regexMessage: 'Please enter a valid email address.' },
            { id: 'contactNumber', name: 'contactNumber', emptyMessage: 'Please enter your contact number.', regex: /^\d{10}$/, regexMessage: 'Contact number must be 10 digits.' },
            { id: 'company', name: 'company', emptyMessage: 'Please enter your company name.', regex: /^[a-zA-Z ]+$/, regexMessage: 'Company name can only contain letters and spaces.' },
            { id: 'message', name: 'message', emptyMessage: 'Please enter your message.' },
            { id: 'inquiryType2', name: 'inquiryType2', emptyMessage: 'Please Select' }
        ];
    
        // Handle submit for Contact Former Form
        $('#send').click(function(e) {
            e.preventDefault();
            handleFormSubmit('contactFormer', 'contact.php', contactFormerFields, divRecaptchaWidgetId);
        });
    
        // Call render function on page load
        renderReCAPTCHAs();
    });
            
