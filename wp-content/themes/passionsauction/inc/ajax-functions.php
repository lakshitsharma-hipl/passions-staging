<?php
/* otp 6 digit */
function generate_otp() {
    return mt_rand(100000, 999999);
}

/* login */
add_action('wp_ajax_passion_login', 'passion_login');
add_action('wp_ajax_nopriv_passion_login', 'passion_login');
function passion_login() {
    $response = array();
    $email = sanitize_email($_POST['passion_login_email_address']);
    $password = sanitize_textarea_field($_POST['passion_login_password']);
    $remember_me = true;
    $user = wp_authenticate($email, $password);    

    if (is_wp_error($user)) {
        $response = array('status' => 'failed', 'message' => 'Credentials do not match our records');
    } else {
        $email_verification_enabled = true; 
        $email_verified = get_user_meta($user->ID, 'email_verified', true);
        if ($email_verification_enabled && !$email_verified) {          
            $response = array('status' => 'failed', 'message' => 'Please verify your email address before logging in');
        } else {    
            $otp = generate_otp();
            update_user_meta($user->ID, 'otp', $otp);

            $expiry_time = time() + 300;
            update_user_meta($user->ID, 'otp_expiry', $expiry_time);

            $subject = "OTP Verification";
            $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">To finalize the OTP verification process, please enter or paste this code into the designated verification field.</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">'.$otp.'</p>';
            $sentotp = passionAuctionEmail($user->ID, $subject, $message);
            if($sentotp){             
                $response = array('status' => 'success', 'message' => 'Please check your email for the OTP verification code');                
            }else{
                $response = array('status' => 'failed', 'message' => 'OTP mail not sent');                
            }
        }
    }
    wp_send_json($response);
    die();
}

/* OTP Verify */
add_action( 'wp_ajax_passion_login_otp_verify', 'passion_login_otp_verify' );
add_action( 'wp_ajax_nopriv_passion_login_otp_verify', 'passion_login_otp_verify' );
function passion_login_otp_verify() {
    $response = array();
    $loginotp = $_POST['loginotp'];
    $remember_me = true;

    $loginemail = $_POST['loginemail'];
    $user = get_user_by( 'email', $loginemail );
    $user_id = $user->ID;
    
    $sendloginotp = get_user_meta($user_id, 'otp', true);
    $expiry_time  = get_user_meta($user_id, 'otp_expiry', true);

    if($loginotp == $expiry_time > time()){
        if ($loginotp == $sendloginotp) {
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id, $remember_me);
            $response = array('status' => 'success', 'message' => 'Verification of OTP completed successfully');
        } else {
            $response = array('status' => 'fail', 'message' => 'OTP appears to be incorrect. Kindly double-check and try again');
        }
    }else{
        $response = array('status' => 'fail', 'message' => 'The OTP has expired');
    }    
    
    wp_send_json($response);
    wp_die();

}

/* resend otp */
add_action('wp_ajax_passion_resend_otp', 'passion_resend_otp');
add_action('wp_ajax_nopriv_passion_resend_otp', 'passion_resend_otp');
function passion_resend_otp() {
    $response = array();

    $loginemail = $_POST['loginemail'];
    $user = get_user_by( 'email', $loginemail );

    $otp = generate_otp();
    update_user_meta($user->ID, 'otp', $otp);

    $expiry_time = time() + 300;
    update_user_meta($user->ID, 'otp_expiry', $expiry_time);

    $subject = "OTP Verification";
    $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">To finalize the OTP verification process, please enter or paste this code into the designated verification field.</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">'.$otp.'</p>';
    $sentotp = passionAuctionEmail($user->ID, $subject, $message);

    if($sentotp){             
        $response = array('status' => 'success', 'message' => 'Please check your email for the OTP verification code');                
    }else{
        $response = array('status' => 'failed', 'message' => 'OTP mail not sent');                
    }

    wp_send_json($response);
    die();
}

/* signup */
add_action( 'wp_ajax_passion_signup', 'passion_signup' );
add_action( 'wp_ajax_nopriv_passion_signup', 'passion_signup' );
function passion_signup() {
    extract($_POST);
    $response = array();

    if (email_exists($passion_signup_email_address)) {
        $response = array('status' => 'failed', 'err_type' => 1, 'message' => 'Email is already been taken, please use another one.');
        wp_send_json($response);
        wp_die();
    }

    $username = generateUniqueUsername($passion_signup_email_address);
    $registerusers = wp_create_user($username, $passion_signup_password, $email = $passion_signup_email_address);

    if (!is_wp_error($registerusers)) {
        $userId =  $registerusers;
        $updateresponse = wp_update_user([
            'ID' => $userId, 
            'first_name' => $passion_signup_first_name,
            'last_name' => $passion_signup_last_name,
        ]);

        update_user_meta($userId, 'phone', '+'.$phonecode.$passion_signup_phone_number);
        update_user_meta($userId, 'user_phone_number', '+'.$phonecode.$passion_signup_phone_number);

        $verification_key = wp_generate_password(20, false);

        update_user_meta($userId, 'email_verification_key', $verification_key);
        update_user_meta($userId, 'address', $passion_signup_address);

        update_user_meta($userId, 'city', $passion_signup_city);
        update_user_meta($userId, 'state', $passion_signup_state);
        update_user_meta($userId, 'zipcode', $passion_signup_zipcode);

        update_user_meta($userId, 'account_type', $account_type);
        update_user_meta($userId, 'company_name', $passion_signup_company_name);
        update_user_meta($userId, 'company_address', $passion_signup_company_address);
        update_user_meta($userId, 'company_country', $passion_signup_company_country);
        update_user_meta($userId, 'country', $passion_signup_country);

        $register_full_name = $passion_signup_first_name . ' ' . $passion_signup_last_name;
        $user_activation_url = add_query_arg( array(
            'action' => 'verify_email',
            'user_id' => $userId,
            'key' => $verification_key,
        ), site_url('/login') );

        $subject = "Registration Confirmation";
        $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Thank you for creating an account with the Passions Auction</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">To complete your registeration, please click the button below:</p><a href="'.$user_activation_url.'" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">Complete Registration</a>';
        $user_mail_sent = passionAuctionEmail($userId, $subject, $message);
        if ($user_mail_sent) {
            $response = array('status' => 'success', 'message' => 'Registration successful! we have sent an email to verify your account on your registered email address');
        } else {
            $response = array('status' => 'failed', 'err_type' => 2, 'message' => 'Sorry! something went wrong while sending the verification email');
        }
    } else {
        $response = array('status' => 'failed', 'err_type' => 3, 'message' => 'Failed to create user account');
    }

    wp_send_json($response);
    wp_die();
}

/* email verification */
add_action('init', 'verify_email');
function verify_email() {
    if(isset($_GET['action']) && $_GET['action'] == 'verify_email' && isset($_GET['user_id']) && isset($_GET['key'])) {
        $user_id = $_GET['user_id'];
        $key = $_GET['key'];
        if(get_user_meta($user_id, 'email_verification_key', true) == $key) {       
            update_user_meta($user_id, 'email_verified', true);
            wp_redirect(add_query_arg('verified', 'true', site_url('/login')));
            exit;
        }
    }
}

/* email authentication */
add_filter('authenticate', 'custom_authenticate', 10, 3);
function custom_authenticate($user, $username, $password) {
    $user = get_user_by('login', $username);
    if ($user && is_wp_error($user)) {
        return $user;
    }
    if($user){
        $email_verified = get_user_meta($user->ID, 'email_verified', true);
        $email_verification_enabled = true;   
        if ($email_verification_enabled && !$email_verified) {       
            return new WP_Error('email_not_verified', __('Please verify your email before logging in'));
        }
    }
    return $user;
}

/* forget password */
add_action('wp_ajax_passion_forgot_password', 'passion_forgot_password');
add_action('wp_ajax_nopriv_passion_forgot_password', 'passion_forgot_password');
function passion_forgot_password() {
    $response = array();
    $email = sanitize_email($_POST['password_reset_email']);
    $user = get_user_by('email', $email);
    if (!$user) {
        $response = array('status' => 'failed', 'message' => 'User not found.');
    } else {
        $reset_key = wp_generate_password(20, false);
        update_user_meta($user->ID, 'custom_reset_key', $reset_key);
        $reset_link = esc_url(add_query_arg(array(
            'action' => 'rp',
            'user_id' => $user->ID,
            'key' => $reset_key,
        ), home_url('/forgot-password/')));
        $subject = 'Password Reset';
        $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">To reset your password, click the following link:</p><a href="'.$reset_link.'" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">Reset Password</a>';
        $email_sent = passionAuctionEmail($user->ID, $subject, $message);

        if ($email_sent) {
            $response = array('status' => 'success', 'message' => 'Password reset email sent successfully');
        } else {
            $response = array('status' => 'failed', 'message' => 'Failed to send password reset email');
        }
    }
    wp_send_json($response);
    die();
}

/* reset password */
add_action('wp_ajax_passion_reset_password', 'passion_reset_password');
add_action('wp_ajax_nopriv_passion_reset_password', 'passion_reset_password');
function passion_reset_password() {
    $response = array();
    $newpassword = sanitize_textarea_field($_POST['new_password']);
    $confirmpassword = sanitize_textarea_field($_POST['confirm_password']);
    $user_id = $_POST['user_id'];
    $exkey = $_POST['key'];
    $get_userdata = get_userdata($user_id);
    $check_password = wp_check_password($confirmpassword, $get_userdata->user_pass, $user_id);
    $newkey = get_user_meta($user_id, 'custom_reset_key', true);

    if ($newkey != $exkey) {
        $response = array('status' => 'failed', 'message' => 'The link you followed has expired');
    } else {
        if ($newpassword !== $confirmpassword) {
            $response = array('status' => 'failed', 'message' => 'The new password and confirm password must be same');
        } else {
            if ($check_password == 1) {
                $response = array('status' => 'failed', 'message' => 'New password must be different from the old password');
            } else {
                wp_set_password($newpassword, $user_id);
                update_user_meta($user_id, 'custom_reset_key', '');
                $first_name = $get_userdata->first_name;
                $to = $get_userdata->user_email;
                $subject = "Password Changed";
                $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">This is to inform you that your Passionsauction password has been successfully changed.</p>';
                $user_mail_sent = passionAuctionEmail($user_id, $subject, $message);

                if ($user_mail_sent) {
                    $response = array('status' => 'success', 'message' => 'Your password reset successfully');
                } else {
                    $response = array('status' => 'failed', 'message' => 'Mail not sent');
                }
            }
        }
    }
    wp_send_json($response);
    die();
}

/* dashboard change password */
add_action('wp_ajax_dashboard_change_password', 'dashboard_change_password');
add_action('wp_ajax_nopriv_dashboard_change_password', 'dashboard_change_password');
function dashboard_change_password() {
    $response = array();
    $oldpassword = sanitize_text_field($_POST['oldpassword']);
    $newpassword = sanitize_text_field($_POST['newpassword']);
    $user_id = $_POST['userid'];
    $user_data = get_userdata($user_id);
    $check_password = wp_check_password($oldpassword, $user_data->user_pass, $user_id);
    
    if (!$check_password) {
        $response = array('status' => 'failed', 'err_type' => '1', 'message' => 'Old password is incorrect');
    } else {
        $check_new_password = wp_check_password($newpassword, $user_data->user_pass, $user_id);     
        if ($check_new_password) {
            $response = array('status' => 'failed', 'err_type' => '2', 'message' => 'New password must be different from the old password');
        } else {
            wp_set_password($newpassword, $user_id);            
            $response = array('status' => 'success', 'message' => 'Your password changed successfully');
        }
    }
    wp_send_json($response);
    die();
}

/* dashboard general change */
add_action('wp_ajax_dashboard_general_change', 'dashboard_general_change');
add_action('wp_ajax_nopriv_dashboard_general_change', 'dashboard_general_change');
function dashboard_general_change(){
    $response = array();
    $userid = get_current_user_id();
    $phonecode = $_POST['phonecode'];
    // $userid = sanitize_text_field($_POST['userid']);
    // $username = sanitize_text_field($_POST['username']);
    
    $first_name = $_POST['dash_user_first_name'];
    $last_name = $_POST['dash_user_last_name'];
        
    if($userid != 0 && $userid != '') {

        $user_data = array(
            'ID' => $userid,
            'first_name' => $first_name,
            'last_name' => $last_name
        );
        $updated = wp_update_user($user_data);

       if (is_wp_error($updated)) {
            $response = array('status'=> 'failed', 'message' => 'Something went wrong', 'error' => $updated->get_error_message());
        } else {
            update_user_meta( $userid, 'address', $_POST['passion_signup_address'] );
            update_user_meta( $userid, 'city', $_POST['dash_user_city'] );
            update_user_meta( $userid, 'state', $_POST['dash_user_state'] );
            update_user_meta( $userid, 'country', $_POST['passion_edit_country'] );
            update_user_meta( $userid, 'address', $_POST['passion_signup_address'] );
            update_user_meta($userid, 'phone', '+'.$phonecode.$_POST['passion_edit_phone_number']);

            $response = array('status'=> 'success', 'message' => 'Profile updated successfully');
        }


        $response = array('status'=> 'success', 'message' => 'Profile updated successfully');
    } else {
        $response = array('status'=> 'failed', 'message' => 'Something went wrong');
    }

    wp_send_json($response);
    die();
}

function verification_files_upload($file) {
    $uploadDir = wp_upload_dir()['path'] . '/verification/';
    $uploadPath = $uploadDir . $file['name'];
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $attachment_data = array(
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($uploadPath)),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_mime_type' => $file['type'],
            'guid'           => $uploadPath
        );
        $attachment_id = wp_insert_attachment($attachment_data, $uploadPath);
        // $attachment_id = wp_insert_attachment($attachment_data, $uploadPath, $post_id);
        return $attachment_id;
    }
}


/* user verification */
add_action('wp_ajax_passion_user_verification', 'passion_user_verification');
add_action('wp_ajax_nopriv_passion_user_verification', 'passion_user_verification');
function passion_user_verification(){
    $response = array();
    $current_user_id = get_current_user_id();
    $account_type = $_POST['account_type'];
    $admin_email = get_option('admin_email');
    $status = get_user_meta($current_user_id, 'userstatus', true);

    /* individual */
    if(isset($_FILES['passport_photo'])) {
        $file = $_FILES['passport_photo'];
        if($file['error'] === 0) {
            $attachment = verification_files_upload($file);
            update_user_meta($current_user_id, 'passport_photo', $attachment);
        }
    }
    if(isset($_POST['passport_details'])) {
        update_user_meta($current_user_id, 'passport_details', $_POST['passport_details']);
    }
    if(isset($_POST['e_kyc'])) {
        update_user_meta($current_user_id, 'e_kyc', $_POST['e_kyc']);
    }

    /* corporate */
    if(isset($_FILES['proof_of_business'])) {
        $busfile = $_FILES['proof_of_business'];
        if($busfile['error'] === 0) {
            $battachment = verification_files_upload($busfile);
            update_user_meta($current_user_id, 'proof_of_business', $battachment);
        }
    }
    if(isset($_FILES['proof_tax_id'])) {
        $taxfile = $_FILES['proof_tax_id'];
        if($taxfile['error'] === 0) {
            $tattachment = verification_files_upload($taxfile);
            update_user_meta($current_user_id, 'proof_tax_id', $tattachment);
        }
    }
    if(isset($_POST['company_uen'])) {
        update_user_meta($current_user_id, 'company_uen', $_POST['company_uen']);
    }
    if(isset($_POST['company_tax_id'])) {
        update_user_meta($current_user_id, 'company_tax_id', $_POST['company_tax_id']);
    }

    if($status == 'rejected') {
        update_user_meta($current_user_id, 'userstatus', '');
    }


    $get_userdata = get_userdata($current_user_id);
    $first_name = $get_userdata->first_name;

    $subject = "Verification - Request";
    $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">';
    if($account_type == 'individual') : 
        $message .= 'New Individual User '.$first_name.' have submit for verification.';
    else :
        $message .= 'New Corporate User '.$first_name.' have submit for verification';
    endif;
    $message .= '</p>';
    $message .= '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">View user details by clicking on the <a href="'.site_url().'/wp-admin/user-edit.php?user_id='.$current_user_id.'" target="_blank">user profile</a>.</p>';

    $mail = passionAuctionEmail(0, $subject, $message);

    if($mail) {
        $response = array('status'=> 'success', 'message' => 'Verification completed successfully');
    }else {
        $response = array('status'=> 'failed', 'message' => 'Verification Error');
    }
    
    wp_send_json($response);
    die();
}