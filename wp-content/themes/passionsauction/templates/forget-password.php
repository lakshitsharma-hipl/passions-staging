<?php
/* Template Name: Forget Password */

get_header(); 

if (isset($_GET['action']) && $_GET['action'] === 'rp' && isset($_GET['user_id']) && isset($_GET['key'])) { 
    ?>
    <section class="login-section">
        <div class="customcontainer">
            <div class="login-inner">
                <div class="login-head">
                    <h2>Reset Password</h2>
                </div>
                <form method="post" id="custom-reset-password-form" class="custom-reset-password-form">
                    <div class="form-group">
                        <label>New Password<span class="required-form-field">*</span></label>
                        <input type="password" name="new_password" class="form-control" id="new_password" placeholder="New Password" oninput="this.value = this.value.replace(/\s/g, '');">
                         <span id="reset-password-toggle" class="fa fa-fw field-icon toggle-password fa-eye-slash"></span>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password<span class="required-form-field">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password" oninput="this.value = this.value.replace(/\s/g, '');">
                         <span id="con-reset-password-toggle" class="fa fa-fw field-icon toggle-password fa-eye-slash"></span>
                    </div>
                    <div class="restpassword-form-section text-center">
                        <input type="hidden" id="userid"  name="user_id" value="<?php echo $_GET['user_id'];?>">
                        <input type="hidden" id="userkey" name="key" value="<?php echo $_GET['key']; ?>">
                        <input type="button" name="restpassword_submit" class="restpassword_submit custom-btn fill-btn btn btn-black"  value="SUBMIT">
                    </div>
                </form>
            </div>
        </div>
    </section><?php
    } else { ?>
    <section class="login-section">
        <div class="customcontainer">
            <div class="login-inner">
                <div class="login-head">
                    <h2>Forgot Password</h2>
                </div>
                <form method="post" id="password_reset_form" class="password-reset-form">
                    <div class="form-group">
                        <label>Email Address<span class="required-form-field">*</span></label>
                        <input type="email" name="password_reset_email" class="form-control" id="password_reset_email" placeholder="Enter Your Email Address">
                    </div>
                    <div class="form-group text-center">
                        <input type="button" name="password_reset_submit" class="password_reset_submit custom-btn fill-btn btn btn-black" value="SEND">
                    </div>
                    <div class="form-group text-center login_footer">
                        Remember your account? <a href="<?php echo site_url(); ?>/login/" id="back_to_login_link" class="back-login">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </section><?php
    } ?>

<?php get_footer(); ?>