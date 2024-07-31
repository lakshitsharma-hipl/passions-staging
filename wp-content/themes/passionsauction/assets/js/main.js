jQuery(document).ready(function ($) {

    $("#togglebtn-menu").click(function(){
        $(".header-menu-mobile").toggleClass("main");
    });

    $('.dashboard-sidebar').click(function(){
        $(this).toggleClass('open');
    });

    $('.slider-galeria').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        asNavFor: '.slider-galeria-thumbs',
    });

    $('.slider-galeria-thumbs').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: false,
        asNavFor: '.slider-galeria',
        vertical: true,
        verticalSwiping: true,
        focusOnSelect: true,
        responsive: [
            {
                breakpoint: 575,
                settings: {
                    vertical: false,
                    slidesToShow: 4,
                }
            }
        ]
    });

    // SEE ALL BIDS
    // var shownDefault = myAjax.number_of_bids;

    // var numShown = shownDefault; 
    // var $table = $('.tablehis').find('tbody'); 
    // var numRows = $table.find('tr').length;
    
    // var moretext = "See All Bids";
    // var lesstext = "Show less";
      
    // $table.find('tr:gt(' + (numShown - 1) + ')').hide()                      
    /*$('.seebid').click(function() {
        if (numShown === numRows) {
            numShown = shownDefault;
          $table.find('tr:gt(' + (numShown - 1) + ')').hide()
          $('.seebid').text(moretext)                              
        } else {
            numShown = numRows;
          $('.seebid').text(lesstext)                    
        }        
        $table.find('tr:lt(' + numShown + ')').show();
    });*/

    // $('.seebid').click(function() {
    //     var bidId = $(this).data('bid');
    //     var $table = $('.tablehis[data-bid="' + bidId + '"]').find('tbody');
    //     var numRows = $table.find('tr').length;
        
    //     if ($(this).hasClass('show-all')) {
    //         $(this).removeClass('show-all').text(moretext);
    //         $table.find('tr:gt(' + (numShown - 1) + ')').hide();
    //     } else {
    //         $(this).addClass('show-all').text(lesstext);
    //         $table.find('tr').show();
    //     }
    // });   

    /* login */
    $(".passion_login_submit").on("click", function (e) {
        e.preventDefault();

        $('.error').remove();
        $('.passion_login_error').remove();
        $('.success-message').remove();       
        
        var email = $("#passion_login_email_address").val();
        var password = $("#passion_login_password").val();
        var emailRegex = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
        var err = 0;

        if (email == '') {
            $("#passion_login_email_address").after('<span class="error">Email field is required</span>');
            err++;
        } else if(email != '' && !emailRegex.test(email)){
            $("#passion_login_email_address").after('<span class="error">Invalid email address</span>');
            err++;
        }

        if (!password || password.length === 0) {
            $("#passion_login_password").after('<span class="error">Password field is required</span>');
            err++;
        }

        if ($(this).hasClass('disabled')) {
            return; 
        }

        $(this).addClass('disabled');

        if (err == 0) {
            var formData = new FormData($("#passion_login_form")[0]);
            formData.append("action", "passion_login");

            var expiryTime = 300;
            var timerInterval;

            function startTimer() {
                $('.otp_remain_time').show();
                $('.resend_otp_verify').hide();
                expiryTime = 300;
                var timerDisplay = $('.otp_remain_time'); 
                timerInterval = setInterval(function() {
                    var minutes = Math.floor(expiryTime / 60);
                    var seconds = expiryTime % 60;

                    var formattedMinutes = String(minutes).padStart(2, '0');
                    var formattedSeconds = String(seconds).padStart(2, '0');

                    timerDisplay.text('Time remaining: ' + formattedMinutes + ':' + formattedSeconds);
                    
                    if (expiryTime <= 0) {
                        clearInterval(timerInterval);
                        $('.otp_remain_time').hide();
                        $('.resend_otp_verify').show();
                    }
                    expiryTime--;
                }, 1000);
            }


            $.ajax({
                type: "POST",
                dataType: "json",
                url: myAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) { 
                    if (response.status == 'success') {                      
                        $('#passion_login_password').after('<span class="success-message">' + response.message + '</span>');
                        setTimeout(function() {
                            $('#passion_login_form').hide();
                            $('#passion_otp_verification').show(); 
                        }, 2000);
                        startTimer();
                    } else {
                        if (response.message.includes('Credentials do not match our records.')) {
                            $('#passion_login_password').after('<span class="error">' + response.message + '</span>');
                        } else if (response.message.includes('Please verify your email address before logging in.')) {
                            $('#passion_login_email_address').after('<span class="error">' + response.message + '</span>');
                        } else {
                            $('.passion_login_submit').after('<div class="passion_login_error">' + response.message + '</div>');
                        }
                    }
                    $('.passion_login_submit').removeClass('disabled');
                }
            });
        } else {
            $('.passion_login_submit').removeClass('disabled');
        }
    });

    /* OTP verify */
    $(".passion_otp_verify").on("click", function (e) {
        e.preventDefault();
        $('.error').remove();
        $('.passion_login_error').remove();
        $('.success-message').remove();
        var loginotp = $("#passion_login_otp").val(); 
        var loginemail = $("#passion_login_email_address").val(); 
        var err = 0;        
        
        if (loginotp == '') {
            $("#passion_login_otp").after('<span class="error">OTP field is required</span>');
            err++;
            return false;
        }

        var formData = new FormData();
        formData.append('loginotp', loginotp);
        formData.append('loginemail', loginemail);
        formData.append('action', 'passion_login_otp_verify');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: formData, 
            processData: false, 
            contentType: false, 
            success: function (response) {                 
                console.log(response.status);
                if (response.status == 'success') {  
                    $('.passion_otp_verify').after('<span class="success-message">' + response.message + '</span>');
                    setTimeout(function() {
                        window.location.href = myAjax.homeurl + '/dashboard';
                    }, 2000);
                } else{
                    $('.passion_otp_verify').after('<span class="passion_login_error">' + response.message + '</span>');
                }
                
            }
        });
    }); 

        /* OTP verify */
    $(".resend_otp_verify").on("click", function (e) {
        e.preventDefault();
        $('.error').remove();
        $('.passion_login_error').remove();
        $('.success-message').remove();
        var loginemail = $("#passion_login_email_address").val(); 
        var expiryTime = 300;
        var timerInterval;

        function resendTimer() {
            $('.otp_remain_time').show();
            $('.resend_otp_verify').hide();
            expiryTime = 300;
            var timerDisplay = $('.otp_remain_time'); 
            timerInterval = setInterval(function() {
                var minutes = Math.floor(expiryTime / 60);
                var seconds = expiryTime % 60;

                var formattedMinutes = String(minutes).padStart(2, '0');
                var formattedSeconds = String(seconds).padStart(2, '0');

                timerDisplay.text('Time remaining: ' + formattedMinutes + ':' + formattedSeconds);
                
                if (expiryTime <= 0) {
                    clearInterval(timerInterval);
                    $('.otp_remain_time').hide();
                    $('.resend_otp_verify').show();
                }
                expiryTime--;
            }, 1000);
        }

        $.ajax({
            type: "POST",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: {
                'action' : 'passion_resend_otp',
                'loginemail' : loginemail
            },
            success: function (response) {                 
                console.log(response.status);
                if (response.status == 'success') {  
                    $('.passion_otp_verify').after('<span class="success-message">' + response.message + '</span>');
                    resendTimer();
                } else{
                    $('.passion_otp_verify').after('<span class="passion_login_error">' + response.message + '</span>');
                }
                
            }
        });

    });   

    $('input[name="account_type"]').on('change', function(){
        var value = $(this).val();
        if(value == 'corporate') {
            $('.corporate-fields .form-control').addClass('signupvalidate');
            $('.individual-fields .form-control').removeClass('signupvalidate');
            $('.corporate-fields').show();
            $('.individual-fields').hide();
        } else {
            $('.corporate-fields .form-control').removeClass('signupvalidate');
            $('.individual-fields .form-control').addClass('signupvalidate');
            $('.corporate-fields').hide();
            $('.individual-fields').show();
        }
    });
    
    /* signup */
    $(".passion_signup_submit").on("click", function (e) {

        var phonecode = $("#passion_signup_phone_number").intlTelInput("getSelectedCountryData").dialCode;

        e.preventDefault();
        $('.error').remove();
        $('.passion_signup_success').remove();
        $('.passion_signup_error').remove();
        var err = 0;
        var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
        var password_regex = /^(?=.*[A-Z])(?=.*\d).{6,30}$/;
        var password = $("#passion_signup_password").val();

        $(".signupvalidate").each(function () {
            if (this.value == '') {
                $(this).after('<span class="error">This field is required</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signupmail") && !pattern.test(this.value)) {
                $(this).after('<span class="error">Invalid email address</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signupphone") && (this.value.length < 7 || this.value.length > 15)) {
                $(this).after('<span class="error">Phone number must be from 7 to 15 digits</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signuppassword") && !password_regex.test(this.value)) {
                $(this).after('<span class="error">Please use 1 Uppercase letter, 1 digit, password can be 6-30 characters long</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signupconfirmpassword") && this.value !== password) {
                $(this).after('<span class="error">The password confirmation and password must match</span>');
                err++;
            }
        });
        if (!$('#passion_signup_agreement').prop('checked')) {            
            $('#passion_signup_agreement').after('<span class="error">Please agree to our Terms &amp; Conditions.</span>');
            err++;
        }
        if (err == 0) {
            var formData = new FormData($("#passion_signup_form")[0]);
            formData.append("action", "passion_signup");
            formData.append("phonecode", phonecode);
            $.ajax({
                type: "POST",
                dataType: "json",
                url: myAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status == 'success') {
                        $('#passion_signup_form')[0].reset();
                        $('.corporate-fields').hide();
                        $('.have_an_account').after('<div class="passion_signup_success">' + response.message + '</div>');
                          setTimeout(function() {
                            var successMessageTop = $(".passion_signup_submit").offset().top;
                            $('html, body').animate({
                                scrollTop: successMessageTop
                            }, 1000);
                        }, 500);
                    } else {
                        if(response.err_type == 1) {
                            $('#passion_signup_email_address').after('<span class="error">' + response.message + '</span>');
                        }else {
                            $('.have_an_account').after('<div class="passion_signup_error">' + response.message + '</div>');
                        }                        
                    }
                }
            });
        }
    });

    /* forgot password */
    $(".password_reset_submit").on("click", function (e) {
        e.stopPropagation();
        $('.error').remove();
        $('.password_success').remove();
        $('.password_error').remove();
        var email = $("#password_reset_email").val();
        var emailRegex = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
        var err = 0;

        if(email.length === 0){
            $("#password_reset_email").after('<span class="error">Email address is required</span>');
            err++;
        } else if(!emailRegex.test(email)) {
            $("#password_reset_email").after('<span class="error">Invalid email address</span>');
            err++;
        }

        if(err === 0) {
            var formData = new FormData($("#password_reset_form")[0]);
            formData.append("action", "passion_forgot_password");
            $.ajax({
                type: "POST",
                dataType: "json",
                url: myAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success') {
                        $('#password_reset_form')[0].reset();
                        $('.password_reset_submit').after('<div class="password_success">'+response.message+'</div>');   
                    } else {
                        $('.password_reset_submit').after('<div class="password_error">'+response.message+'</div>');
                    }
                }
            });
        }
    });

    /* reset password */
    $(".restpassword_submit").on("click", function (e) {
        e.stopPropagation();
        $('.error').remove(); 
        $('.new_password_error').remove();
        var newpassword = $("#new_password").val();
        var confirmpassword = $("#confirm_password").val();
        var err = 0;
        var password_regex = /^(?=.*[A-Z])(?=.*\d).{6,30}$/; 

        if (newpassword == '') {
            $("#new_password").after('<span class="error">Please enter your new password</span>');
            err++;
        } else if (newpassword !== '' && !password_regex.test(newpassword)) {
            $("#new_password").after('<span class="error">Please use 1 Uppercase letter, 1 digit, password can be 6-30 characters long</span>');
            err++;
        } else {
            if (confirmpassword == '') {
                $("#confirm_password").after('<span class="error">Please confirm your new password</span>');
                err++;
            } else if (confirmpassword !== '' && !password_regex.test(confirmpassword)) {
                $("#confirm_password").after('<span class="error">Please use 1 Uppercase letter, 1 digit, password can be 6-30 characters long</span>');
                err++;
            } else if (newpassword !== confirmpassword) {
                $('#confirm_password').after('<span class="error">The new password and confirm password must be the same</span>');
                err++;
            }
        }

        if(err == 0) {
            var formData = new FormData($("#custom-reset-password-form")[0]);
            formData.append("action", "passion_reset_password");
            $.ajax({
                type: "POST",
                dataType: "json",
                url: myAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success') {
                        $('#custom-reset-password-form')[0].reset();
                        $('.restpassword_submit').after('<div class="passwords_success">'+response.message+'</div>');
                        setTimeout(function(){ 
                           window.location.href = myAjax.homeurl + '/dashboard';
                        }, 2000);       
                    } else {
                        $('.restpassword_submit').after('<div class="new_password_error">'+response.message+'</div>');
                    }
                }
            });
        }
    });

    /* verification */
    $(".user_verification_btn").on("click", function (e) {
        e.preventDefault();
        if ($(this).hasClass('disabled')) {
            return;
        }
        $('.error').remove();
        var err = 0;

        $(".verificationvalidate").each(function () {
            if (this.value == '') {
                $(this).after('<span class="error">This field is required</span>');
                err++;
            }
        });

        if (err == 0) {
            var formData = new FormData($("#passion_verification_form")[0]);
            formData.append("action", "passion_user_verification");
            $.ajax({
                type: "POST",
                dataType: "json",
                url: myAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(".user_verification_btn").addClass('disabled').attr('disabled', 'disabled');
                },
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();      
                    } else {
                        $('.user_verification_btn').after('<div class="error">'+response.message+'</div>');
                    }
                },
                complete: function() {
                    $(".user_verification_btn").removeClass('disabled').removeAttr('disabled');
                }
            });
        }
    });

    /* banner slider */
    var itemCount = $('#bannerslide .owl-item').length;
    $('#bannerslide').owlCarousel({
        loop:false,
        margin:10,
        dots:true,
        nav:true,
        autoHeight:true,
        responsiveClass:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });
    $( "#bannerslide .owl-prev").html('<svg width="5" height="18" viewBox="0 0 5 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 17L1 9L4 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>');
    $( "#bannerslide .owl-next").html('<svg width="5" height="18" viewBox="0 0 5 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L4 9L1 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>');

    $('#ongoingpro').owlCarousel({
        loop:false,
        margin:48,
        dots:true,
        nav:true,
        infinite:false,
        items:3.5,
        responsiveClass:true,
        autoplay: true,
        responsive:{
            0:{
                items:1.5,
                dots:true,
                margin:12,
                nav:true
            },
            600:{
                items:2.5,
                dots:true,
                margin:12,
                nav:true
            },
            1000:{
                items:3.5,
                dots:true,
                nav:true
            }
        }
    });
    $( "#ongoingpro .owl-prev").html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_17_55)"><path d="M5 12H19" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12L11 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12L11 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_17_55"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>');
    $( "#ongoingpro .owl-next").html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_17_50)"><path d="M5 12H19" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 18L19 12" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 6L19 12" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_17_50"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>');

    // searchbox JS
    $('.searchtype').on('click', function(){
        $('#'+$(this).data('modal')).css('display','flex');
    });

    $('.closebtn').on('click', function(){
        $('.modal').css('display','none');
    });

    $(window).on('click', function(event) {
        if (!$(event.target).closest('.modal').length) {
            $('.modal').css('display', 'none');
        }
    });

    // FILTERSIDEBAR MOBILE JS
    $('#filterwrap').click(function(){
        $('.filtersidebar').addClass('show');
    })
    $('#closepop').click(function(){
        $('.filtersidebar').removeClass('show');
    })

    $('#searchclick').click(function(){
        $('#auctionsearch').addClass('show');
    })

});

/* toggle password */
jQuery(document).ready(function($){
    $('.toggle-password').on('click', function() {
        var inputField = $(this).closest('.form-group').find('input');
        if ($(this).hasClass('fa-eye-slash')) {
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            inputField.attr('type', 'text');
        } else {
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            inputField.attr('type', 'password');
        }
    });
});

/* dashboard change password */
jQuery(document).ready(function($){
    $(".dash_pass_change").on("click", function (e) {
        e.preventDefault();
        $('.error').remove(); 
        $('.passwords_success').remove();
        var oldpassword = $("#dash_old_password").val();
        var newpassword = $("#dash_new_password").val();
        var userid = $('#dash_userid').val();
        var err = 0;
        var password_regex = /^(?=.*[A-Z])(?=.*\d).{6,30}$/;

        if (oldpassword == '') {
            $("#dash_old_password").after('<span class="error">Please enter your old password</span>');
            err++;
        } 

        if (newpassword == '') {
            $("#dash_new_password").after('<span class="error">Please enter your new password</span>');
            err++;
        } else if (newpassword !== '' && !password_regex.test(newpassword)) {
            $("#dash_new_password").after('<span class="error">Please use 1 Uppercase letter, 1 digit, password can be 6-30 characters long</span>');
            err++;
        } else if (oldpassword === newpassword) {
            $('#dash_new_password').after('<span class="error">The new password and old password must be different</span>');
            err++;
        }

        if(err == 0) {
            $(".dash_pass_change").prop("disabled", true);
            $.ajax({
                type: "POST",
                dataType: "json",
                url: myAjax.ajaxurl,
                data: {
                    "userid" : userid,
                    "oldpassword" : oldpassword,
                    "newpassword" : newpassword,
                    "action" : 'dashboard_change_password'
                },
                success: function(response) {
                    $(".dash_pass_change").prop("disabled", false);
                    if (response.status == 'success') {
                        $('.dash_pass_change').after('<div class="passwords_success">'+response.message+'</div>');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);      
                    } else {
                        if(response.err_type == '1') {
                            $('#dash_new_password').after('<span class="error">'+response.message+'</span>');
                        } else {
                            $('.dash_pass_change').after('<div class="error">'+response.message+'</div>');
                        }
                    }
                }
            });
        }
    });
});

/* dashboard change password */
jQuery(document).ready(function($){
    $(".dash_user_save").on("click", function (e) {
        e.preventDefault();
        $('.error').remove(); 
        $('.passwords_success').remove();
        var userid = $(this).attr('data-id');
        // var username = $('#dash_user_name').val();
        var err = 0;

        $(".dashboard_validate").each(function () {
            if (this.value == '') {
                $(this).after('<span class="error">This field is required</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signupmail") && !pattern.test(this.value)) {
                $(this).after('<span class="error">Invalid email address</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signupphone") && (this.value.length < 7 || this.value.length > 15)) {
                $(this).after('<span class="error">Phone number must be from 7 to 15 digits</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signuppassword") && !password_regex.test(this.value)) {
                $(this).after('<span class="error">Please use 1 Uppercase letter, 1 digit, password can be 6-30 characters long</span>');
                err++;
            } else if (this.value != '' && $(this).hasClass("signupconfirmpassword") && this.value !== password) {
                $(this).after('<span class="error">The password confirmation and password must match</span>');
                err++;
            }
        });


        // var formData = new FormData($("#passion_signup_form")[0]);
        // formData.append("action", "passion_signup");
        // formData.append("phonecode", phonecode);
        // $.ajax({
        //     type: "POST",
        //     dataType: "json",
        //     url: myAjax.ajaxurl,
        //     data: formData,
        //     processData: false,
        //     contentType: false,
        // })

        if(err == 0) {
            $(".dash_user_save").prop("disabled", true);
            var formData = new FormData($("#dashboard_edit_form")[0]);
            var phonecode = $("#passion_edit_phone_number").intlTelInput("getSelectedCountryData").dialCode;
            formData.append("action", "dashboard_general_change");            
            
            formData.append("phonecode", phonecode);

            $.ajax({
                type: "POST",
                dataType: "json",
                url: myAjax.ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success') {
                        $('.dash_user_save').after('<div class="passwords_success">'+response.message+'</div>');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);      
                    } else {
                        $('#dash_user_save').after('<span class="error">'+response.message+'</span>');
                    }
                    $(".dash_user_save").prop("disabled", false);
                }
            });
        }
    });
});

function getCookie(cookieName) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(';');
    for(var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) === 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return "";
}

function validateInput(input) {
    var inputValue = input.value;
    inputValue = inputValue.replace(/\D/g, '');
    inputValue = inputValue.slice(0, 6);
    input.value = inputValue;
}