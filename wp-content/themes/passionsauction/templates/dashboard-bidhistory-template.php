<?php 
/* Template Name: Dashboard Bid History*/
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

global $wpdb;
$table_name = $wpdb->prefix . 'bidhistory';
$query = $wpdb->prepare("
    SELECT *
    FROM $table_name
    WHERE userid = %d
    ORDER BY id DESC", $current_user_id);
$userbids = $wpdb->get_results($query, ARRAY_A);

$allbids = array();
foreach ($userbids as $bid) {
    $auction_id = $bid['auctionid'];
    if (!isset($allbids[$auction_id])) {
        $allbids[$auction_id] = array();
    }
    $allbids[$auction_id][] = $bid;
}


$number_of_bids = get_field('number_of_bids', 'options');
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
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
					<ul class="formobile">
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
				</div>
				<div class="dashboard-data">
					<div class="formblock">
						<form>
							<div class="searchform">
								<input type="search" name="" placeholder="Search bid...">
								<span class="searchicon">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<g clip-path="url(#clip0_20_168)">
											<path d="M2 6.66667C2 7.2795 2.12071 7.88634 2.35523 8.45252C2.58975 9.01871 2.93349 9.53316 3.36683 9.9665C3.80017 10.3998 4.31462 10.7436 4.88081 10.9781C5.447 11.2126 6.05383 11.3333 6.66667 11.3333C7.2795 11.3333 7.88634 11.2126 8.45252 10.9781C9.01871 10.7436 9.53316 10.3998 9.9665 9.9665C10.3998 9.53316 10.7436 9.01871 10.9781 8.45252C11.2126 7.88634 11.3333 7.2795 11.3333 6.66667C11.3333 6.05383 11.2126 5.447 10.9781 4.88081C10.7436 4.31462 10.3998 3.80017 9.9665 3.36683C9.53316 2.93349 9.01871 2.58975 8.45252 2.35523C7.88634 2.12071 7.2795 2 6.66667 2C6.05383 2 5.447 2.12071 4.88081 2.35523C4.31462 2.58975 3.80017 2.93349 3.36683 3.36683C2.93349 3.80017 2.58975 4.31462 2.35523 4.88081C2.12071 5.447 2 6.05383 2 6.66667Z" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
											<path d="M14 14L10 10" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
										</g>
										<defs>
											<clipPath id="clip0_20_168">
												<rect width="16" height="16" fill="white"></rect>
											</clipPath>
										</defs>
									</svg>
								</span>
							</div>
							<div class="daterange">
								<input type="text" id="date" name="" placeholder="Select date range" onfocus="(this.type = 'date')">
								<span class="calendaricon">
									<svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1 5C1 4.46957 1.21071 3.96086 1.58579 3.58579C1.96086 3.21071 2.46957 3 3 3H15C15.5304 3 16.0391 3.21071 16.4142 3.58579C16.7893 3.96086 17 4.46957 17 5V17C17 17.5304 16.7893 18.0391 16.4142 18.4142C16.0391 18.7893 15.5304 19 15 19H3C2.46957 19 1.96086 18.7893 1.58579 18.4142C1.21071 18.0391 1 17.5304 1 17V5Z" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M13 1V5" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M5 1V5" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M1 9H17" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M4 12H4.013" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M7.00977 12H7.01477" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M10.0098 12H10.0148" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M13.0146 12H13.0196" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M10.0146 15H10.0196" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M4.00977 15H4.01477" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M7.00977 15H7.01477" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</div>
						</form>
					</div>
					<div class="orderhistory">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
						  	<li class="nav-item" role="presentation">
						    	<button class="nav-link active" id="allbid-tab" data-bs-toggle="tab" data-bs-target="#allbid" type="button" role="tab" aria-controls="allbid" aria-selected="true">ALL BID</button>
						  	</li>
						  	<!--  -->
						  	<li class="nav-item" role="presentation">
						    	<button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">ACTIVE</button>
						  	</li>
						  	<!--  -->
						  	<li class="nav-item" role="presentation">
						    	<button class="nav-link" id="closed-tab" data-bs-toggle="tab" data-bs-target="#closed" type="button" role="tab" aria-controls="closed" aria-selected="false">CLOSED</button>
						  	</li>
						</ul>
						<div class="tab-content" id="myTabContent">
					  		<div class="tab-pane fade show active" id="allbid" role="tabpanel" aria-labelledby="allbid-tab">
					  			<div class="bid-historyblock bidhistory-grid">
					  				<?php foreach ($allbids as $auctionid => $auctiondata) {
					  					$auctionimg = get_the_post_thumbnail_url($auctionid, 'thumbnail');
										$totalbidsofuser = count($auctiondata);
										$auctionname = get_the_title($auctionid);
										$auctionlink = get_the_permalink($auctionid); 
										$lastdate = $auctiondata[0]['created'];
										$timestamplst = strtotime($lastdate);
										$lastbiddate = date("d M Y", $timestamplst);
										$bidenddate = get_post_meta($auctionid, 'end_date', true);
										$current_date = current_time('timestamp');
										$bidenddatestr = strtotime($bidenddate);

										$orderid = get_post_meta($auctionid, 'orderid', true);
										$userid = get_post_meta($orderid, 'userid', true);

										$status = getAuctionStatus($auctionid, $current_user_id);

										$estimate_price_range = get_field('estimate_price_range', $auctionid);

										$lastbiddata = getLetestBidData($auctionid);

										?>

										<div class="product-listitem">
						  					<div class="product-listitem-image"><img src="<?php echo $auctionimg; ?>" class="img-fluid" /></div>
							  				<div class="product-list-desc">
							  					<a href="<?php echo $auctionlink; ?>" class="title"><?php echo $auctionname; ?></a>
							  					<div class="bidding-content">
							  						<?php 
								  						if($lastbiddata){
															$lastbidid = $lastbiddata->id;
															$lastbidamount = $lastbiddata->bidamount;
															$found = false;

															foreach ($auctiondata as $item) {
															    if ($item['id'] == $lastbidid) {
															        $found = true;
															        break;
															    }
															}

															if (!$found) {
															    echo '<div class="lasthighestbid">Highest Bid: '.number_format($lastbidamount).' USD</div>';
															}
														} ?>
							  						<div class="lastbid">Last bid: <?php echo $lastbiddate; ?></div>
								  					<?php 
								  					if ($estimate_price_range && is_array($estimate_price_range) && isset($estimate_price_range['minimum_estimate_price']) && isset($estimate_price_range['maximum_estimate_price'])) {
													    $minimum_estimate_price = $estimate_price_range['minimum_estimate_price'];
													    $maximum_estimate_price = $estimate_price_range['maximum_estimate_price'];
														// echo 'YO: ' . $minimum_estimate_price .'<br>';
														// echo $maximum_estimate_price;
														if (isset($minimum_estimate_price) && is_numeric($minimum_estimate_price)) {
															$formatted_minimum_price = number_format((float)$minimum_estimate_price);
														} else {
															$formatted_minimum_price = 'N/A'; 
														}
														
														if (isset($maximum_estimate_price) && is_numeric($maximum_estimate_price)) {
															$formatted_maximum_price = number_format((float)$maximum_estimate_price);
														} else {
															$formatted_maximum_price = 'N/A'; 
														}

													    // $formatted_minimum_price = number_format($minimum_estimate_price);
													    // $formatted_maximum_price = number_format($maximum_estimate_price);
													    echo '<div class="lastbid">Estimate Price: '.$formatted_minimum_price.' - '.$formatted_maximum_price.' USD</div>';
													  
													}
												
												echo '</div><div class="otherdatablck"> <div class="btnonly">';

												if($lastbiddata){
													$lastbidid = $lastbiddata->id;
													$found = false;

													foreach ($auctiondata as $item) {
													    if ($item['id'] == $lastbidid) {
													        $found = true;
													        break;
													    }
													}

													if ($found) {
													    echo '<p class="bdstatus sts-winning">Winning</p>';
													} else {
													    echo '<p class="bdstatus sts-outbid">Out Bid</p>';
													}
												}
	
							  					if ($current_date < $bidenddatestr) {
												    echo '<span class="badge bg-green">Active</span>';
												    ?>
													<button type="button" data-bs-toggle="modal" data-bs-target="#allbidspp<?php echo $auctionid; ?>" data-bid="<?php echo $auctionid; ?>" class="see-all-bids btn btn-black seebid">View Bids</button>
													</div>
												    <?php
												} else {				
												    echo '<span class="badge bg-red">Closed</span>';

												    ?>
													<button type="button" data-bs-toggle="modal" data-bs-target="#allbidspp<?php echo $auctionid; ?>" data-bid="<?php echo $auctionid; ?>" class="see-all-bids btn btn-black seebid">View Bids</button>
													</div>
												    <?php

												    $finalamount = get_post_meta($orderid, 'amount', true);
												    $orderstatus = get_post_meta($orderid, 'status', true);
												    if($status == 'cancelled') {
											    		echo '<div class="note bg-red">Your bid has been canceled because payment has not been completed.</div>';
											    	} else if($orderid && $userid == $current_user_id){    	

											    		$biddata = getLetestBidData($auctionid);
											    		if($biddata){
											    			$lstbidid = $biddata->id;
											    		}else{
											    			$lstbidid = '';
											    		}
											    		$orderdata = getBidOrderData($lstbidid);

												    	if($orderdata){
												    		$orderurl = home_url('dashboard/order-history?order_id='.$orderid);
												    		if($orderstatus == 'processing' || $orderstatus == 'payment-processing'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn">You win this bid with $'.number_format($finalamount, 2).'. Click to see order</a>';
												    		}elseif($orderstatus == 'completed'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn">Click to see order</a>';
												    		}elseif($orderstatus == 'cancelled'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn ordercancelled">View cancelled order</a>';
												    		}	
												    	} else{
												    		$paymentinterval = get_field('auction_payment_interval', 'option');
												    		echo '<div class="bidawarded 1">
												    				<p>Congratulations! You\'ve won the bid at <strong>$'.number_format($finalamount, 2).'</strong>! We\'re getting your order ready. Expect payment instructions shortly. Please complete your payment within '.$paymentinterval.' days.</p>
												    				<p>Thanks!</p>
												    			</div>';
												    	}
													} else{
														echo '<div class="note f3">You didn\'t win the bidding for this item</div>';
													}

												}
							  					?>
							  				
												<div class="modal fade" id="allbidspp<?php echo $auctionid; ?>" tabindex="-1" aria-labelledby="allbidsppLabel<?php echo $auctionid; ?>" aria-hidden="true">
												  	<div class="modal-dialog  modal-lg modal-dialog-centered modal-dialog-scrollable" >
													    <div class="modal-content">
													    	<div class="modal-header">
												                <h5 class="modal-title" id="allbidsppLabel<?php echo $auctionid; ?>">Bidding</h5>
												                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
												                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
												                        <path d="M13 1L1 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
												                        <path d="M1 1L13 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
												                    </svg>

												                </button>
												            </div>
													      	<div class="modal-body">
													        	<div class="bidhistorytable 1">
											  						<table data-bid="<?php echo $auctionid; ?>" id="auctionbidall-<?php echo $auctionid; ?>" class="table mb-0 tablehis">
											  							<tbody>
											  								<?php 

											  								$query = $wpdb->prepare("
																		    SELECT *
																		    FROM $table_name
																		    WHERE auctionid = %d
																		    ORDER BY id DESC
																		    LIMIT 20 OFFSET %d", $auctionid, $offset);
																			$fullbidinghistory = $wpdb->get_results($query, ARRAY_A);

																			$query1 = $wpdb->prepare("
																		    SELECT *
																		    FROM $table_name
																		    WHERE auctionid = %d
																		    ORDER BY id DESC", $auctionid, $offset);
																			$allbidinghistory = $wpdb->get_results($query1, ARRAY_A);

																			foreach ($fullbidinghistory as $bdkey => $allbidsdata) {
																				$bidprc = $allbidsdata['bidamount'];
											  									$biddate = $allbidsdata['created'];
											  									$biddatestr = new DateTime($biddate);
																				$formatted_date = $biddatestr->format('F j, h:i A');
																				$dbuserid = $allbidsdata['userid'];
								                                                
								                                                if($current_user_id == $dbuserid){
								                                                	$hisclass = 'selfbid';
								                                                }else{
								                                                	$hisclass = 'othersbid';
								                                                }

								                                                $full_name = "User &nbsp;<span class='buids'>" . $dbuserid."<span>"; 
											  									echo '<tr class="'.$hisclass.'">
													  									<th>$'.number_format($bidprc).'</th>
													  									<td>'.$full_name.'</td>
													  									<td>'.$formatted_date.'</td>
													  								</tr>';
																			}
																			?>
											  							</tbody>	
											  						</table>
											  					</div>
													      	</div>
													      	<div class="modal-footer justify-content-center">
													      		<?php if(count($allbidinghistory) > 20) { ?>
									  								<div class="pagination-container" id="pagination-container-all-<?php echo $auctionid; ?>" auc-id="<?php echo $auctionid; ?>" total-bids="<?php echo count($allbidinghistory); ?>"></div>
									  							<?php } ?>
									  						</div>
													    </div>
												  </div>
												</div>

											</div>

							  				</div>
						  				</div>

										<?php
					  				} ?>
					  			</div>
					  		</div>
					  		<div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
					  			<div class="bid-historyblock-main">
					  				<div class="bid-historyblock bidhistory-grid">
					  				<?php foreach ($allbids as $auctionid => $auctiondata) {
					  					$auctionimg = get_the_post_thumbnail_url($auctionid, 'thumbnail');
										$totalbidsofuser = count($auctiondata);
										$auctionname = get_the_title($auctionid);
										$auctionlink = get_the_permalink($auctionid); 
										$lastdate = $auctiondata[0]['created'];
										$timestamplst = strtotime($lastdate);
										$lastbiddate = date("d M Y", $timestamplst);
										$bidenddate = get_post_meta($auctionid, 'end_date', true);
										$current_date = current_time('timestamp');
										$bidenddatestr = strtotime($bidenddate);
										if ($current_date > $bidenddatestr) {
											continue;
										}
										$orderid = get_post_meta($auctionid, 'orderid', true);
										$userid = get_post_meta($orderid, 'userid', true);

										$status = getAuctionStatus($auctionid, $current_user_id);
										$estimate_price_range = get_field('estimate_price_range', $auctionid);

										$lastbiddata = getLetestBidData($auctionid);
										?>

										<div class="product-listitem">
						  					<div class="product-listitem-image"><img src="<?php echo $auctionimg; ?>" class="img-fluid" /></div>
							  				<div class="product-list-desc">
							  					<a href="<?php echo $auctionlink; ?>" class="title"><?php echo $auctionname; ?></a>
							  					<?php 
						  						if($lastbiddata){
													$lastbidid = $lastbiddata->id;
													$lastbidamount = $lastbiddata->bidamount;
													$found = false;

													foreach ($auctiondata as $item) {
													    if ($item['id'] == $lastbidid) {
													        $found = true;
													        break;
													    }
													}

													if (!$found) {
													    echo '<div class="lasthighestbid">Highest Bid: '.number_format($lastbidamount).' USD</div>';
													}
												} ?>
							  					<div class="lastbid">Last bid: <?php echo $lastbiddate; ?></div>
							  					<?php 
							  					if ($estimate_price_range && is_array($estimate_price_range) && isset($estimate_price_range['minimum_estimate_price']) && isset($estimate_price_range['maximum_estimate_price'])) {
												    $minimum_estimate_price = $estimate_price_range['minimum_estimate_price'];
												    $maximum_estimate_price = $estimate_price_range['maximum_estimate_price'];
													if (isset($minimum_estimate_price) && is_numeric($minimum_estimate_price)) {
														$formatted_minimum_price = number_format((float)$minimum_estimate_price);
													} else {
														$formatted_minimum_price = 'N/A'; 
													}
													
													if (isset($maximum_estimate_price) && is_numeric($maximum_estimate_price)) {
														$formatted_maximum_price = number_format((float)$maximum_estimate_price);
													} else {
														$formatted_maximum_price = 'N/A'; 
													}
												    // $formatted_minimum_price = number_format($minimum_estimate_price);
												    // $formatted_maximum_price = number_format($maximum_estimate_price);
												    echo '<div class="lastbid">Estimate Price: '.$formatted_minimum_price.' - '.$formatted_maximum_price.' USD</div>';
												  
												}

												echo '<div class="otherdatablck"> <div class="btnonly">';
	
												if($lastbiddata){
													$lastbidid = $lastbiddata->id;
													$found = false;

													foreach ($auctiondata as $item) {
													    if ($item['id'] == $lastbidid) {
													        $found = true;
													        break;
													    }
													}

													if ($found) {
													    echo '<p class="bdstatus sts-winning">Winning</p>';
													} else {
													    echo '<p class="bdstatus sts-outbid">Out Bid</p>';
													}
												}
												
							  					if ($current_date < $bidenddatestr) {
												    echo '<span class="badge bg-green">Active</span>';
												    ?>
													<button type="button" data-bs-toggle="modal" data-bs-target="#activebdpop<?php echo $auctionid; ?>" data-bid="<?php echo $auctionid; ?>" class="see-all-bids btn btn-black seebid">View Bids</button>
													</div>
												    <?php
												} else {				
												    echo '<span class="badge bg-red">Closed</span>';

												    ?>
													<button type="button" data-bs-toggle="modal" data-bs-target="#activebdpop<?php echo $auctionid; ?>" data-bid="<?php echo $auctionid; ?>" class="see-all-bids btn btn-black seebid">View Bids</button>
													</div>
												    <?php

												    $finalamount = get_post_meta($orderid, 'amount', true);
												    $orderstatus = get_post_meta($orderid, 'status', true);
												    if($status == 'cancelled') {
											    		echo '<div class="note bg-red">Your bid has been canceled because payment has not been completed.</div>';
											    	} else if($orderid && $userid == $current_user_id){    	
											    		
											    		$biddata = getLetestBidData($auctionid);
											    		if($biddata){
											    			$lstbidid = $biddata->id;
											    		}else{
											    			$lstbidid = '';
											    		}
											    		$orderdata = getBidOrderData($lstbidid);

												    	if($orderdata){
												    		$orderurl = home_url('dashboard/order-history?order_id='.$orderid);
												    		if($orderstatus == 'processing' || $orderstatus == 'payment-processing'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn">You win this bid with $'.number_format($finalamount, 2).'. Click to see order</a>';
												    		}elseif($orderstatus == 'completed'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn">Click to see order</a>';
												    		}elseif($orderstatus == 'cancelled'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn ordercancelled">View cancelled order</a>';
												    		}	
												    	} else{
												    		$paymentinterval = get_field('auction_payment_interval', 'option');
												    		echo '<div class="bidawarded 2">
												    				<p>Congratulations! You\'ve won the bid at <strong>$'.number_format($finalamount, 2).'</strong>! We\'re getting your order ready. Expect payment instructions shortly. Please complete your payment within '.$paymentinterval.' days.</p>
												    				<p>Thanks!</p>
												    			</div>';
												    	}
													} else{
														echo '<div class="note f3">You didn\'t win the bidding for this item</div>';
													}

												}
							  					?>
							  				
												<div class="modal fade" id="activebdpop<?php echo $auctionid; ?>" tabindex="-1" aria-labelledby="activebdpopLabel<?php echo $auctionid; ?>" aria-hidden="true">
												  	<div class="modal-dialog  modal-lg modal-dialog-centered modal-dialog-scrollable" >
													    <div class="modal-content">
													    	<div class="modal-header">
												                <h5 class="modal-title" id="activebdpopLabel<?php echo $auctionid; ?>">Bidding</h5>
												                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
												                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
												                        <path d="M13 1L1 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
												                        <path d="M1 1L13 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
												                    </svg>

												                </button>
												            </div>
													      	<div class="modal-body">
													        	<div class="bidhistorytable 1">
											  						<table data-bid="<?php echo $auctionid; ?>" id="auctionbidactive-<?php echo $auctionid; ?>" class="table mb-0 tablehis">
											  							<tbody>
											  								<?php 

																			$query = $wpdb->prepare("
																		    SELECT *
																		    FROM $table_name
																		    WHERE auctionid = %d
																		    ORDER BY id DESC
																		    LIMIT 20 OFFSET %d", $auctionid, $offset);
																			$fullbidinghistory = $wpdb->get_results($query, ARRAY_A);

																			$query1 = $wpdb->prepare("
																		    SELECT *
																		    FROM $table_name
																		    WHERE auctionid = %d
																		    ORDER BY id DESC", $auctionid, $offset);
																			$allbidinghistory = $wpdb->get_results($query1, ARRAY_A);

																			foreach ($fullbidinghistory as $bdkey => $allbidsdata) {
																				$bidprc = $allbidsdata['bidamount'];
											  									$biddate = $allbidsdata['created'];
											  									$biddatestr = new DateTime($biddate);
																				$formatted_date = $biddatestr->format('F j, h:i A');
																				$dbuserid = $allbidsdata['userid'];
								                                                
								                                                if($current_user_id == $dbuserid){
								                                                	$hisclass = 'selfbid';
								                                                }else{
								                                                	$hisclass = 'othersbid';
								                                                }

								                                                $full_name =   "User &nbsp;<span class='buids'>" . $dbuserid."<span>"; 
											  									echo '<tr class="'.$hisclass.'">
													  									<th>$'.number_format($bidprc).'</th>
													  									<td>'.$full_name.'</td>
													  									<td>'.$formatted_date.'</td>
													  								</tr>';
																			}
																			?>
											  							</tbody>
											  						</table>
											  					</div>
													      	</div>
													      	<div class="modal-footer justify-content-center">
													      		<?php if(count($allbidinghistory) > 20) { ?>
										  							<div class="pagination-container" id="pagination-container-active-<?php echo $auctionid; ?>" auc-id="<?php echo $auctionid; ?>" total-bids="<?php echo count($allbidinghistory); ?>"></div>
										  						<?php } ?>
										  					</div>
													    </div>
												  </div>
												</div>

											</div>

							  				</div>
						  				</div>

										<?php
					  				} ?>
					  				</div>
					  			</div>
					  		</div>
					  		<div class="tab-pane fade" id="closed" role="tabpanel" aria-labelledby="closed-tab">
					  			<div class="bid-historyblock bidhistory-grid">
					  				<?php foreach ($allbids as $auctionid => $auctiondata) {
					  					$auctionimg = get_the_post_thumbnail_url($auctionid, 'thumbnail');
										$totalbidsofuser = count($auctiondata);
										$auctionname = get_the_title($auctionid);
										$auctionlink = get_the_permalink($auctionid); 
										$lastdate = $auctiondata[0]['created'];
										$timestamplst = strtotime($lastdate);
										$lastbiddate = date("d M Y", $timestamplst);
										$bidenddate = get_post_meta($auctionid, 'end_date', true);
										$current_date = current_time('timestamp');
										$bidenddatestr = strtotime($bidenddate);
										if ($current_date < $bidenddatestr) {
											continue;
										}
										$orderid = get_post_meta($auctionid, 'orderid', true);
										$userid = get_post_meta($orderid, 'userid', true);

										$status = getAuctionStatus($auctionid, $current_user_id);
										$estimate_price_range = get_field('estimate_price_range', $auctionid);

										$lastbiddata = getLetestBidData($auctionid);
										?>

										<div class="product-listitem">
						  					<div class="product-listitem-image"><img src="<?php echo $auctionimg; ?>" class="img-fluid" /></div>
							  				<div class="product-list-desc">
							  					<a href="<?php echo $auctionlink; ?>" class="title"><?php echo $auctionname; ?></a>
							  					<?php 
						  						if($lastbiddata){
													$lastbidid = $lastbiddata->id;
													$lastbidamount = $lastbiddata->bidamount;
													$found = false;

													foreach ($auctiondata as $item) {
													    if ($item['id'] == $lastbidid) {
													        $found = true;
													        break;
													    }
													}

													if (!$found) {
													    echo '<div class="lasthighestbid">Highest Bid: '.number_format($lastbidamount).' USD</div>';
													}
												} ?>
							  					<div class="lastbid">Last bid: <?php echo $lastbiddate; ?></div>
							  					<?php 
							  					if ($estimate_price_range && is_array($estimate_price_range) && isset($estimate_price_range['minimum_estimate_price']) && isset($estimate_price_range['maximum_estimate_price'])) {
												    $minimum_estimate_price = $estimate_price_range['minimum_estimate_price'];
												    $maximum_estimate_price = $estimate_price_range['maximum_estimate_price'];

													if (isset($minimum_estimate_price) && is_numeric($minimum_estimate_price)) {
														$formatted_minimum_price = number_format((float)$minimum_estimate_price);
													} else {
														$formatted_minimum_price = 'N/A'; 
													}
													
													if (isset($maximum_estimate_price) && is_numeric($maximum_estimate_price)) {
														$formatted_maximum_price = number_format((float)$maximum_estimate_price);
													} else {
														$formatted_maximum_price = 'N/A'; 
													}
												    // $formatted_minimum_price = number_format($minimum_estimate_price);
												    // $formatted_maximum_price = number_format($maximum_estimate_price);
												    echo '<div class="lastbid">Estimate Price: '.$formatted_minimum_price.' - '.$formatted_maximum_price.' USD</div>';
												  
												}

												echo '<div class="otherdatablck"> <div class="btnonly">';

												if($lastbiddata){
													$lastbidid = $lastbiddata->id;
													$found = false;

													foreach ($auctiondata as $item) {
													    if ($item['id'] == $lastbidid) {
													        $found = true;
													        break;
													    }
													}

													if ($found) {
													    echo '<p class="bdstatus sts-winning">Winning</p>';
													} else {
													    echo '<p class="bdstatus sts-outbid">Out Bid</p>';
													}
												}
										
							  					if ($current_date < $bidenddatestr) {
												    echo '<span class="badge bg-green">Active</span>';
												    ?>
													<button type="button" data-bs-toggle="modal" data-bs-target="#closedbdpp<?php echo $auctionid; ?>" data-bid="<?php echo $auctionid; ?>" class="see-all-bids btn btn-black seebid">View Bids</button>
													</div>
												    <?php
												} else {				
												    echo '<span class="badge bg-red">Closed</span>';

												    ?>
													<button type="button" data-bs-toggle="modal" data-bs-target="#closedbdpp<?php echo $auctionid; ?>" data-bid="<?php echo $auctionid; ?>" class="see-all-bids btn btn-black seebid">View Bids</button>
													</div>
												    <?php

												    $finalamount = get_post_meta($orderid, 'amount', true);
												    $orderstatus = get_post_meta($orderid, 'status', true);
												    if($status == 'cancelled') {
											    		echo '<div class="note bg-red">Your bid has been canceled because payment has not been completed.</div>';
											    	} else if($orderid && $userid == $current_user_id){    	
											    		
											    		$biddata = getLetestBidData($auctionid);
											    		if($biddata){
											    			$lstbidid = $biddata->id;
											    		}else{
											    			$lstbidid = '';
											    		}
											    		$orderdata = getBidOrderData($lstbidid);

												    	if($orderdata){
												    		$orderurl = home_url('dashboard/order-history?order_id='.$orderid);
												    		if($orderstatus == 'processing' || $orderstatus == 'payment-processing'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn">You win this bid with $'.number_format($finalamount, 2).'. Click to see order</a>';
												    		}elseif($orderstatus == 'completed'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn">Click to see order</a>';
												    		}elseif($orderstatus == 'cancelled'){
												    			echo '<a href="'.$orderurl.'" class="seeorder btn ordercancelled">View cancelled order</a>';
												    		}	
												    	} else{
												    		$paymentinterval = get_field('auction_payment_interval', 'option');
												    		echo '<div class="bidawarded 3">
												    				<p>Congratulations! You\'ve won the bid at <strong>$'.number_format($finalamount, 2).'</strong>! We\'re getting your order ready. Expect payment instructions shortly. Please complete your payment within '.$paymentinterval.' days.</p>
												    				<p>Thanks!</p>
												    			</div>';
												    	}
													} else{
														echo '<div class="note f3">You didn\'t win the bidding for this item</div>';
													}

												}
							  					?>
							  				
												<div class="modal fade" id="closedbdpp<?php echo $auctionid; ?>" tabindex="-1" aria-labelledby="closedbdppLabel<?php echo $auctionid; ?>" aria-hidden="true">
												  	<div class="modal-dialog  modal-lg modal-dialog-centered modal-dialog-scrollable" >
													    <div class="modal-content">
													    	<div class="modal-header">
												                <h5 class="modal-title" id="closedbdppLabel<?php echo $auctionid; ?>">Bidding</h5>
												                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
												                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
												                        <path d="M13 1L1 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
												                        <path d="M1 1L13 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
												                    </svg>

												                </button>
												            </div>
													      	<div class="modal-body">
													        	<div class="bidhistorytable 1">
											  						<table data-bid="<?php echo $auctionid; ?>" id="auctionbidclosed-<?php echo $auctionid; ?>" class="table mb-0 tablehis">
											  							<tbody>
											  								<?php 

											  								$query = $wpdb->prepare("
																		    SELECT *
																		    FROM $table_name
																		    WHERE auctionid = %d
																		    ORDER BY id DESC
																		    LIMIT 20 OFFSET %d", $auctionid, $offset);
																			$fullbidinghistory = $wpdb->get_results($query, ARRAY_A);

																			$query1 = $wpdb->prepare("
																		    SELECT *
																		    FROM $table_name
																		    WHERE auctionid = %d
																		    ORDER BY id DESC", $auctionid, $offset);
																			$allbidinghistory = $wpdb->get_results($query1, ARRAY_A);

																			foreach ($fullbidinghistory as $bdkey => $allbidsdata) {
																				$bidprc = $allbidsdata['bidamount'];
											  									$biddate = $allbidsdata['created'];
											  									$biddatestr = new DateTime($biddate);
																				$formatted_date = $biddatestr->format('F j, h:i A');
																				$dbuserid = $allbidsdata['userid'];
								                                                
								                                                if($current_user_id == $dbuserid){
								                                                	$hisclass = 'selfbid';
								                                                }else{
								                                                	$hisclass = 'othersbid';
								                                                }

								                                                $full_name =   "User &nbsp;<span class='buids'>" . $dbuserid."<span>"; 
											  									echo '<tr class="'.$hisclass.'">
													  									<th>$'.number_format($bidprc).'</th>
													  									<td>'.$full_name.'</td>
													  									<td>'.$formatted_date.'</td>
													  								</tr>';
																			}
																			?>
											  							</tbody>
											  						</table>
											  					</div>
													      	</div>
													      	<div class="modal-footer justify-content-center">
													      		<?php if(count($allbidinghistory) > 20) { ?>
										  							<div class="pagination-container" id="pagination-container-closed-<?php echo $auctionid; ?>" auc-id="<?php echo $auctionid; ?>" total-bids="<?php echo count($allbidinghistory); ?>"></div>
										  						<?php } ?>
										  					</div>
													    </div>
												  </div>
												</div>

											</div>

							  				</div>
						  				</div>

										<?php
					  				} ?>
					  			</div>
					  		</div>
					  	</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?> 