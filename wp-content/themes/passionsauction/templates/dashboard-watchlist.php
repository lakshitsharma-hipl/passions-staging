<?php 
/* Template Name: Dashboard Watchlist */
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
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
					<ul class="formobile">
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
				</div>
				<div class="dashboard-data">
					<div class="row">
						<div class="col-12 col-lg-12">
							<div class="table-responsive">
								<table class="userwatchlist">
								  	<tr>
									    <th>Image</th>
									    <th>Title</th>
									    <th>Status</th>
									    <th>Action</th>
								  	</tr><?php 
								  	global $wpdb;
	                                $table_name = $wpdb->prefix . 'watchlist';
	                               	$get_result = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = '" . get_current_user_id() . "' ORDER BY created DESC");  
	                                if($get_result) { 
	                                	$count = 1;
	                                	$countArray = [];
	                                	foreach ($get_result as $key => $value) {
	                                		$countArray[] = $count;
	                                	  	$auction_id = $value->auction_id; 
	                                	  	$title = get_the_title($auction_id);
	                                	  	if($title) : 

		                                	  	$endintime_st = get_field('end_date', $auction_id);
							                    $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endintime_st)));

							                    $endin_timestamp = strtotime($endin_time_formatted);

							                    $startdate_st = get_field('start_date', $auction_id);
							                    $start_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startdate_st)));
							                    $start_timestamp = strtotime($start_time_formatted);

								                $current_timestamp = current_time('timestamp', true); ?>
										  		<tr>
										    		<td><img src="<?php echo get_the_post_thumbnail_url($auction_id); ?>"></td>
										    		<td><a href="<?php echo get_the_permalink($auction_id); ?>"><?php echo $title; ?></a>
										    			<?php 
														if($start_timestamp > $current_timestamp){
															$difference = $start_timestamp - $current_timestamp;
															$days = floor($difference / (60 * 60 * 24));
							                                $hours = floor(($difference % (60 * 60 * 24)) / (60 * 60));
							                                $minutes = floor(($difference % (60 * 60)) / 60);
							                                $seconds = $difference % 60;

														?>
														<input type="hidden" id="daynum<?php echo $count; ?>" value="<?php echo $days; ?>">
														<input type="hidden" id="hournum<?php echo $count; ?>" value="<?php echo $hours; ?>">
														<input type="hidden" id="minutesnum<?php echo $count; ?>" value="<?php echo $minutes; ?>">
														<input type="hidden" id="secondsnum<?php echo $count; ?>" value="<?php echo $seconds+1; ?>">
														<div class="upcoming-watchlist-countdown" id="countdown">
															<span>Starts in</span>
															<ul>
								                                    <li>
								                                        <div id="daysp<?php echo $count; ?>">
								                                            <span><?php echo str_pad($days, 2, '0', STR_PAD_LEFT)[0]; ?></span>
								                                            <span><?php echo str_pad($days, 2, '0', STR_PAD_LEFT)[1]; ?></span>
								                                        </div>
								                                        DAYS
								                                    </li>
								                                    <li>
								                                        <div id="hoursp<?php echo $count; ?>">
								                                            <span><?php echo str_pad($hours, 2, '0', STR_PAD_LEFT)[0]; ?></span>
								                                            <span><?php echo str_pad($hours, 2, '0', STR_PAD_LEFT)[1]; ?></span>
								                                        </div>
								                                        HOURS
								                                    </li>
								                                    <li>
								                                        <div id="minutesp<?php echo $count; ?>">
								                                            <span><?php echo str_pad($minutes, 2, '0', STR_PAD_LEFT)[0]; ?></span>
								                                            <span><?php echo str_pad($minutes, 2, '0', STR_PAD_LEFT)[1]; ?></span>
								                                        </div>
								                                        MINUTES
								                                    </li>
								                                    <li>
								                                        <div id="secondsp<?php echo $count; ?>">
								                                            <span><?php echo str_pad($seconds, 2, '0', STR_PAD_LEFT)[0]; ?></span>
								                                            <span><?php echo str_pad($seconds, 2, '0', STR_PAD_LEFT)[1]; ?></span>
								                                        </div>
								                                        SECONDS
								                                    </li>
								                                </ul>
							                                </div><?php 
							                            } ?>
										    		</td>
										    		<td>
										    			<?php

										    				$bdend_date = get_post_meta($auction_id, 'end_date', true);
															$end_timestamp = strtotime($bdend_date);
															$current_time = new DateTime(current_time('mysql'));
															$current_timestamp = $current_time->getTimestamp();
															if ($end_timestamp > $current_timestamp) {
															    $bdstlabel = "active";
															} else {
															    $bdstlabel =  "closed";
															}
										    				?>
										    			<span class="austatus <?php echo $bdstlabel ?>"><?php echo $bdstlabel ?></span></td>
											    	<td>
													    <span class="delete-watchlist" onclick="deleteWatchlist(<?php echo $auction_id; ?>, <?php echo get_current_user_id(); ?>, this)">
													        <i class="fa fa-trash" aria-hidden="true"></i>
													    </span>
													</td>
										  		</tr><?php 
										  	endif; 
										  	$count++;
										}
									} ?>
									<input type="hidden" id="upcomingcountwatch" value="<?php echo htmlentities(json_encode($countArray)); ?>">
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
jQuery(document).ready(function($) {
	function updateCountdown(count) {
	    var initialDays = parseInt(jQuery('#daynum' + count).val());
	    var initialHours = parseInt(jQuery('#hournum' + count).val());
	    var initialMinutes = parseInt(jQuery('#minutesnum' + count).val());
	    var initialSeconds = parseInt(jQuery('#secondsnum' + count).val()); 

	    var daysElement = document.getElementById("daysp" + count);
	    var hoursElement = document.getElementById("hoursp" + count);
	    var minutesElement = document.getElementById("minutesp" + count);
	    var secondsElement = document.getElementById("secondsp" + count);

	    initialSeconds -= 1;
	   
	    if (initialSeconds < 0) {
	        initialSeconds = 59;
	        initialMinutes -= 1;

	        if (initialMinutes < 0) {
	            initialMinutes = 59;
	            initialHours -= 1;

	            if (initialHours < 0) {
	                initialHours = 23;
	                initialDays -= 1;

	                if (initialDays < 0) {
	                    clearInterval(countdownInterval);
	                    initialDays = 0;
	                    initialHours = 0;
	                    initialMinutes = 0;
	                    initialSeconds = 0;
	                }
	            }
	        }
	    }

	    if (daysElement && hoursElement && minutesElement && secondsElement) {
	        daysElement.children[0].innerText = Math.floor(initialDays / 10);
	        daysElement.children[1].innerText = initialDays % 10;
	        hoursElement.children[0].innerText = Math.floor(initialHours / 10);
	        hoursElement.children[1].innerText = initialHours % 10;
	        minutesElement.children[0].innerText = Math.floor(initialMinutes / 10);
	        minutesElement.children[1].innerText = initialMinutes % 10;
	        secondsElement.children[0].innerText = Math.floor(initialSeconds / 10);
	        secondsElement.children[1].innerText = initialSeconds % 10;
	    }

	    jQuery('#secondsnum' + count).val(initialSeconds);
	    jQuery('#minutesnum' + count).val(initialMinutes);
	    jQuery('#hournum' + count).val(initialHours);
	    jQuery('#daynum' + count).val(initialDays);

	}

	var counts = JSON.parse(jQuery('#upcomingcountwatch').val());
    counts.forEach(function(count) {
        updateCountdown(count);
        setInterval(function() {
            updateCountdown(count);
        }, 1000);
    });

});

</script>

<?php get_footer(); ?> 