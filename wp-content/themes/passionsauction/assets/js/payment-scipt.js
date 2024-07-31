
jQuery(document).ready(function($){
    $('#uploadinvoice').on('submit', function(e){
        e.preventDefault(); 
        $('.error-message').remove();
        $('#uploadinvoice .required').each(function() {
            var field = $(this);
        
            if (field.is(':checkbox') && !field.prop('checked')) {
                field.parent().after('<span class="error-message">' + field.data('label') + ' must be checked</span>');
                //$('#paymentform .loaderimg').hide();
            } else {
                var fieldValue = field.val().trim();
                if (fieldValue === '') {
                    field.after('<span class="error-message">' + field.data('label') + ' is required</span>');
                    //$('#paymentform .loaderimg').hide();
                }
            }
        }); 
        if ($('.error-message').length > 0) {
            return; 
        }
        $('.uploaderror').hide();
        if ($('#uploadinvoiceinput')[0].files.length > 0) {
            var formData = new FormData(this);
            $.ajax({
                url: paymentAjax.ajaxurl,
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response){
                    if(response.status == 'success'){
                        var thankYouURL = response.redirect_url;
                        jQuery('.finalmessage').html(response.message);
                        jQuery('.finalmessage').show();
                        setTimeout(function() {
                            window.location.href = thankYouURL;
                        }, 3000);
                    }
                },
                error: function(xhr, status, error){
                    console.error(xhr.responseText);
                }
            });
        } else {
            $('.uploaderror').html('Please attach a PDF or image of the invoice.');
            $('.uploaderror').show();
            return false;
        }
    });
});

jQuery(".paymentradio").change(function(){
    var selectedValue = jQuery(this).val();
    jQuery('.tab-block-content').css('display', 'none');
    jQuery('#'+selectedValue).css('display', 'block');
});

function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

var stripe_publish_key = paymentAjax.stripe_publish_key;
var stripe = Stripe(stripe_publish_key);
var elements = stripe.elements();
var style = {
    base: {
        fontSize: '16px',
        color: '#4D4D4D',
        fontFamily: 'Arial, sans-serif',
        fontSmoothing: 'antialiased',
        '::placeholder': {
            color: '#aab7c4',
        },
    },
    invalid: {
        color: '#fa755a',
    },
};
var card = elements.create('card', {
    style: style,
    hidePostalCode: true
  });
card.mount('#card-element');
card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }

    var postalCodeElement = document.getElementById('card-postal-code');
    if (event.brand !== 'amex') {
        postalCodeElement.style.display = 'none';  // Hide for non-Amex cards
    } else {
        postalCodeElement.style.display = 'inline-block';  // Show for Amex cards
    }
});
jQuery(document).ready(function($) {
    $('#paybystripe').click(function(e) {    
        e.preventDefault();        
        $('.error-message').remove();
        $('#stripe_card_error').html('');
        
        $('#paymentform .required').each(function() {
            var field = $(this);
        
            if (field.is(':checkbox') && !field.prop('checked')) {
                field.parent().after('<span class="error-message">' + field.data('label') + ' must be checked</span>');
                $('#paymentform .loaderimg').hide();
            } else {
                var fieldValue = field.val().trim();
                if (fieldValue === '') {
                    field.after('<span class="error-message">' + field.data('label') + ' is required</span>');
                    $('#paymentform .loaderimg').hide();
                }
            }
        });            
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var email_add = $('#email_address').val();
        var orderid = $('#orderid').val();

        var paymentphone = $('#paymentphone').val();
        var paymentstreet = $('#paymentstreet').val();
        var paymentcity = $('#paymentcity').val();
        var paymentstate = $('#paymentstate').val();
        var paymentcountry = $('#paymentcountry').val();
        var paymentzipcode = $('#paymentzipcode').val();
        
        // var user_name = $('#paymentform #user_name').val();
        if (!isValidEmail(email_add) && email_add != '') {
            $('#paymentform #email_address').after('<span class="error-message">Invalid email address</span>');
            $('#paymentform .loaderimg').hide();
            return;
        }        
      
        if ($('.error-message').length > 0) {
            return; 
        }
        $('#paymentform .loaderimg').show();
        var token_id = '';
        stripe.createToken(card).then(function(result) {
            if (result.error) {                
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
                $('#paymentform .loaderimg').hide();
                //$('#stripe_card_error').html('Something went wrong');
                return;
            } else {
                $('#stripe_card_error').html('');
                $(this).prop('disabled', true);
                $('#loader .loaderimg').show();
                token_id = result.token.id;
                if (token_id != '' && token_id != undefined) {
                    var formData = {
                        first_name: first_name,
                        last_name: last_name,
                        email: email_add,
                        orderid: orderid,
                        paymentphone: paymentphone,
                        paymentstreet: paymentstreet,
                        paymentcity: paymentcity,
                        paymentstate: paymentstate,
                        paymentcountry: paymentcountry,
                        paymentzipcode: paymentzipcode,
                        stripeToken: token_id,                     
                        current_url: window.location.href,
                        action: "paymentByStripe"
                    };
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: paymentAjax.ajaxurl,
                        data: formData,
                        success: function(response) {
                            $('#paymentform .loaderimg').hide();
                            if (response && response.status == 'success') {                                
                                var thankYouURL = response.redirect_url;
                                $('.finalresponse').html(response.message);
                                $('.finalresponse').css('display', 'block');
                                setTimeout(function() {
                                    window.location.href = thankYouURL;
                                }, 3000);
                            } else {
                                $('#paybystripe').prop('disabled', false);
                                $('#stripe_card_error').html(response.message);
                            }
                        }
                    });    
                } else {
                    $('#paymentform .loaderimg').hide();
                    $('#stripe_card_error').html('Something went wrong');
                }
            }
        });
    });
});
