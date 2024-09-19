
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

 // Optional: Validate the form before submission
    // Enhanced email validation
    function validateForm() {
        var email = document.getElementById('email').value.trim();
        var email = document.getElementById('emailer').value.trim();
        console.log('Email:', email); // Debugging line
        if (email === "") {
            alert("Email must be filled out");
            return false;
        }
    
        // Optional: Add more advanced email validation if needed
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
        // More comprehensive email regex pattern
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i;
        if (!emailPattern.test(email)) {
            console.log('Validation failed: email is invalid');
            alert("Please enter a valid email address");
            return false;
        }
    
    
        console.log('Validation successful');
        return true;
    }


    // Get In touch and contact us js
    $(document).ready(function() {
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
        function handleFormSubmit(formId, url, fields) {
        if (validateFields(fields)) {
            const data = { form_type: formId };
            fields.forEach(function(field) {
                data[field.name] = $('#' + field.id).val();
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(response) {
                    console.log('Response:', response);
                    $('#' + formId)[0].reset();
                    $('#thankYouModal').modal('show');
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
        { id: 'city', name: 'city', emptyMessage: 'Please enter your city name.', regex: /^[a-zA-Z ]+$/, regexMessage: 'City name can only contain letters and spaces.' }
        ];
        
        $('#submit').click(function(e) {
        e.preventDefault();
        handleFormSubmit('contactForm', 'contact.php', contactFormFields);
        });
        
        // Form 2 (Another Contact Form)
        const contactFormerFields = [
        { id: 'fullname', name: 'fullname', emptyMessage: 'Please enter your full name.', regex: /^[a-zA-Z ]+$/, regexMessage: 'Full name can only contain letters and spaces.' },
        { id: 'mail', name: 'mail', emptyMessage: 'Please enter your email address.', regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, regexMessage: 'Please enter a valid email address.' },
        { id: 'contactNumber', name: 'contactNumber', emptyMessage: 'Please enter your contact number.', regex: /^\d{10}$/, regexMessage: 'Contact number must be 10 digits.' },
        { id: 'company', name: 'company', emptyMessage: 'Please enter your company name.', regex: /^[a-zA-Z ]+$/, regexMessage: 'Company name can only contain letters and spaces.' },
        { id: 'message', name: 'message', emptyMessage: 'Please enter your message.' }
        ];
        
        $('#send').click(function(e) {
        e.preventDefault();
        handleFormSubmit('contactFormer', 'contact.php', contactFormerFields);
        });
        });
        