<?php 
/* Template Name: Dashboard Password*/
if(!is_user_logged_in()){
	wp_redirect('/login/');
	exit;
}

get_header(); 

$current_user_id = get_current_user_id();
$get_userdata = get_userdata($current_user_id);
$first_name = $get_userdata->first_name;
$last_name = $get_userdata->last_name;
$user_registered = $get_userdata->user_registered;
$date = new DateTime($user_registered);
$formattedDate = $date->format("F Y");
$user_roles = $get_userdata->roles;
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
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
					<ul class="formobile">
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
				</div>
				<div class="dashboard-data">
					<div class="row">
						<div class="col-12 col-lg-12">
							<div class="form-group">
								<label>Old Password</label>
								<input type="password" id="dash_old_password" placeholder="Old Password" class="form-control pass-toggle" oninput="this.value = this.value.replace(/\s/g, '');" />
								<span class="fa fa-fw field-icon toggle-password showhide fa-eye-slash"></span>
							</div>
						</div>
						<div class="col-12 col-lg-12">
							<div class="form-group">
								<label>New Password</label>
								<input type="password" id="dash_new_password" placeholder="New Password" class="form-control pass-toggle" oninput="this.value = this.value.replace(/\s/g, '');" />
								<span class="fa fa-fw field-icon toggle-password showhide1 fa-eye-slash"></span>
							</div>
						</div>
						<div class="col-12 col-lg-12">
							<div class="form-group text-end">
								<input type="hidden" name="dash_userid" id="dash_userid" value="<?php echo get_current_user_id(); ?>">
								<a class="btn btn-black dash_pass_change">Change Password</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?> 