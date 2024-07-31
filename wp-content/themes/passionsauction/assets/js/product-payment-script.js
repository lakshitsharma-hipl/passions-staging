
jQuery(document).ready(function($){
    $(document).on('click', '#paybyoffiline_product', function(e) {        
        e.preventDefault();        
        $('.error-message').remove();
        $('#uploadinvoice_product .required').each(function() {
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
        

        var paymentphone = $('#passion_product_phone_number').val();
        var paymentstreet = $('#paymentstreet').val();
        var paymentcity = $('#paymentcity').val();
        var paymentstate = $('#paymentstate').val();
        var paymentcountry = $('#paymentcountry').val();
        var paymentzipcode = $('#paymentzipcode').val();
        var payment_total = $('#product_payment_buc').val();
        var payment_mesg = $('#paymentmsg').val();
        var phonecode = $("#passion_product_phone_number").intlTelInput("getSelectedCountryData").dialCode;
        if (!isValidEmail(email_add) && email_add != '') {
            $('#paymentform #email_address').after('<span class="error-message">Invalid email address</span>');
            $('#paymentform .loaderimg').hide();
            return;
        }        
      
        if ($('.error-message').length > 0) {
            return; 
        }
        $('#paymentform .loaderimg').show();
       

        var formData = new FormData();
        formData.append('first_name', first_name);
        formData.append('last_name', last_name);
        formData.append('email', email_add);
        formData.append('paymentphone', paymentphone);
        formData.append('paymentstreet', paymentstreet);
        formData.append('paymentcity', paymentcity);
        formData.append('paymentstate', paymentstate);
        formData.append('paymentcountry', paymentcountry);
        formData.append('paymentzipcode', paymentzipcode);
        formData.append('phonecode', phonecode);
        formData.append('grandTotal', payment_total);
        formData.append('payment_mesg', payment_mesg);
        formData.append('current_url', window.location.href);
        formData.append('action', "sendProductPaymentInvoice");

        if ($('#uploadinvoiceinput')[0].files.length > 0) {
            formData.append('attachment', $('#uploadinvoiceinput')[0].files[0]);

            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: productPaymentAjax.ajaxurl,
                data: formData,
                processData: false, // Prevent jQuery from automatically transforming the data into a query string
                contentType: false, // Prevent jQuery from overriding the content type
                success: function(response) {
                    $('#paymentform .loaderimg').hide();
                    if (response && response.status == 'success') {                                
                        var thankYouURL = response.redirect_url;
                        $('.finalresponse').html(response.message);
                        $('.finalresponse').css('display', 'block');
                        setTimeout(function() {
                            console.log("Attachment: ", response.attach);
                            window.location.href = thankYouURL;
                        }, 3000);
                    } else {
                        $('#paybystripe').prop('disabled', false);
                        $('#stripe_card_error').html(response.message);
                    }
                }
            }); 
        } else {
            $('#uploadinvoiceinput').after('<span class="error-message">Please attach a PDF or image of the invoice.</span>');
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

var stripe_publish_key = productPaymentAjax.stripe_publish_key;
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
  if(jQuery('#card-element').length > 0) {
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
}
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
        

        var paymentphone = $('#passion_product_phone_number').val();
        var paymentstreet = $('#paymentstreet').val();
        var paymentcity = $('#paymentcity').val();
        var paymentstate = $('#paymentstate').val();
        var paymentcountry = $('#paymentcountry').val();
        var paymentzipcode = $('#paymentzipcode').val();
        var payment_total = $('#product_payment_buc').val();
        var phonecode = $("#passion_product_phone_number").intlTelInput("getSelectedCountryData").dialCode;
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
                        paymentphone: paymentphone,
                        paymentstreet: paymentstreet,
                        paymentcity: paymentcity,
                        paymentstate: paymentstate,
                        paymentcountry: paymentcountry,
                        paymentzipcode: paymentzipcode,
                        phonecode: phonecode,
                        grandTotal: payment_total,
                        stripeToken: token_id,
                        current_url: window.location.href,
                        action: "paymentByStripeForProduct"
                    };
                    console.log("HEllo",formData);
                    jQuery.ajax({
                        type: "post",
                        dataType: "json",
                        url: productPaymentAjax.ajaxurl,
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


$(document).ready(function(){
  $('.orderinvoices-img').on('click', function(e){
    e.preventDefault();

    var imgLink = $(this).children('img').attr('src');

    $('.mask').html('<div class="img-box"><img src="'+ imgLink +'"><a class="close">&times;</a>');

    $('.mask').addClass('is-visible fadein').on('animationend', function(){
      $(this).removeClass('fadein is-visible').addClass('is-visible');
    });

    $('.close').on('click', function(){
      $(this).parents('.mask').addClass('fadeout').on('animationend', function(){
        $(this).removeClass('fadeout is-visible')
      });
    });

  });
});
