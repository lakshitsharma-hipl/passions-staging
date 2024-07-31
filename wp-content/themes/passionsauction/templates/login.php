<?php
/* Template Name: Login */
get_header();
$verified = isset($_GET['verified']) && $_GET['verified'] == 'true'; ?>

<section class="login-section">
    <div class="customcontainer">
        <div class="login-inner">
            <div class="login-head">
                <h2>Login</h2>
            </div>
             <?php if ($verified): ?>
                <div class="success-message">
                    <p>User account verified! You can now sign in and access your account.</p>
                </div>
            <?php endif; ?>
            <form method="post" id="passion_login_form" class="login-form">
                <div class="form-group passion_email">
                    <label>Email Address<span class="required-form-field">*</span></label>
                    <input type="email" name="passion_login_email_address" class="form-control" id="passion_login_email_address" placeholder="Enter Your Email Address">
                </div>
                <div class="form-group passion_pass">
                    <label>Password<span class="required-form-field">*</span></label>
                    <input type="password" name="passion_login_password" class="form-control" id="passion_login_password" placeholder="Enter Your Password">
                   <span id="login-password-toggle" class="fa fa-fw field-icon toggle-password fa-eye-slash"></span>                     
                </div>                                
                <div class="form-group text-end">
                    <a href="<?php echo site_url(); ?>/forgot-password/" class="forgetpass">Forgot password?</a>
                </div>
                <div class="form-group text-center">
                    <input type="button" name="passion_login_submit" class="passion_login_submit custom-btn fill-btn btn btn-black" value="LOGIN">
                </div>
                <div class="login_footer">
                    <span>Don't have an Account?<a href="<?php echo site_url(); ?>/signup/"> Create Account</a></span> 
                </div>
            </form>
            <form method="post" id="passion_otp_verification" class="login-form" style="display:none;">
                <div class="form-group">
                    <div class="otp-flex">
                        <label>OTP<span class="required-form-field">*</span></label>
                        <div class="otp_resend_remain">
                            <span class="otp_remain_time"></span>
                            <span class="resend_otp_verify" style="display:none;">Resend OTP</span>
                        </div>
                    </div>
                    <input type="text" name="passion_login_otp" value="" class="form-control" id="passion_login_otp" oninput="validateInput(this)" maxlength="6" placeholder="Enter Your OTP">  
                </div>
                <div class="form-group text-center">
                    <input type="button" name="passion_otp_verify" class="passion_otp_verify custom-btn fill-btn btn btn-black" value="VERIFY">
                </div>
            </form> 
        </div>
    </div>
</section>

<?php get_footer(); ?>
