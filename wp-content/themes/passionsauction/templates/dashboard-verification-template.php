<?php 

/* Template Name: Verification */

if(!is_user_logged_in()){
	wp_redirect('/login/');
	exit;
}

get_header(); 

$current_user_id = get_current_user_id();
$get_userdata = get_userdata($current_user_id);
$first_name = $get_userdata->first_name;
$last_name = $get_userdata->last_name;
$user_email = $get_userdata->user_email;
$user_registered = $get_userdata->user_registered;
$date = new DateTime($user_registered);
$formattedDate = $date->format("F Y");

$user_roles = $get_userdata->roles;
$user_photo = get_user_meta($current_user_id, 'passport_photo', true);
$proof_of_business = get_user_meta($current_user_id, 'proof_of_business', true);
$status = get_user_meta($current_user_id, 'userstatus', true);
?>
<section class="account-dashboard">
	<div class="customcontainer">
		<div class="dashboardmax">
			<div class="userframe">
				<div class="userimg"><img src="<?php echo get_template_directory_uri(); ?>/images/user.png" class="img-fluid" /></div>
				<div class="userdetails">
					<h6 class="name"><?php echo $first_name.' '.$last_name; ?></h6>
					<p class="mb-0 joindate">Join since <?php echo $formattedDate; ?></p>
				</div>
				<?php if($user_roles[0] == 'subscriber' && !$status) : ?>
				<div class="userapproval_status">
					<p class="mb-0 joindate">Your account is pending approval. Once approved by the administrator, you will receive a notification via email.</p>
				</div>
				<?php endif; ?>
			</div>
			<!--  -->
			<div class="dashboard-wrapper">
				<div class="dashboard-sidebar">
					<ul class="form-desktop">
						<li><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
					<ul class="formobile">
						<li><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
				</div>
				<?php $account_type = get_user_meta($current_user_id, 'account_type', true); ?>
				<div class="dashboard-data">
					<div class="row">
						<?php if(isset($status) && $status == 'accepted') { ?>
							<div class="col-12 col-lg-12">
								<div class="verification-user success">
									<img src="<?php echo get_template_directory_uri(); ?>/images/checkmark.svg" class="img-fluid">
									<h4>Verified</h4>
								</div>						
							</div>
						<?php } elseif((($account_type == 'individual' && $user_photo) || ($account_type == 'corporate' && $proof_of_business)) && $status != 'rejected') {  ?>
							<div class="col-12 col-lg-12">
								<div class="verification-user pending">
									<img src="<?php echo get_template_directory_uri(); ?>/images/checkmark.svg" class="img-fluid">
									<h4>Document Uploaded Successfully</h4>
									<p>Pending for administrator approval</p>
								</div>
							</div>
						<?php } else { ?>
						<div class="col-12 col-lg-12">
							<form method="post" id="passion_verification_form" class="verification-form">
							<?php if($account_type == 'individual') : ?>
								<div class="individual-form-fields">
									<div class="form-group">
										<label>Passport Photo</label>
										<input type="file" name="passport_photo" id="passport_photo" class="form-control verificationvalidate" />
									</div>
									<div class="form-group">
										<label>Passport details/Number</label>
										<input type="text" name="passport_details" placeholder="Passport details/Number" class="form-control verificationvalidate"/>
									</div>
									<div class="form-group">
										<label>E-KYC</label>
										<input type="text" name="e_kyc" id="e_kyc" placeholder="E-KYC" class="form-control verificationvalidate"/>
									</div>
								</div>
							<?php else :?>
								<div class="corporate-form-fields">
									<div class="form-group">
										<label>Company Identification Number / UEN</label>
										<input type="text" name="company_uen" id="company_uen" placeholder="Company Identification Number / UEN" class="form-control verificationvalidate"/>
									</div>
									<div class="form-group">
										<label>ACRA / Proof of business</label>
										<input type="file" name="proof_of_business" id="proof_of_business" class="form-control verificationvalidate" />
									</div>
									<div class="form-group">
										<label>Company Tax ID</label>
										<input type="text" name="company_tax_id" id="company_tax_id" placeholder="Company Tax ID" class="form-control verificationvalidate"/>
									</div>
									<div class="form-group">
										<label>Proof of Tax ID</label>
										<input type="file" name="proof_tax_id" id="proof_tax_id" class="form-control verificationvalidate" />
									</div>
								</div>
							<?php endif; ?>
								<div class="form-group text-end">
									<button type="submit" class="btn btn-black user_verification_btn">Submit</button>
									<input type="hidden" name="account_type" value="<?php echo $account_type; ?>">
								</div>
							</form>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?> 