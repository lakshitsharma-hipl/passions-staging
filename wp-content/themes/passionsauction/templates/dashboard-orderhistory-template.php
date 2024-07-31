<?php 
/* Template Name: Dashboard Order History*/
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

/*global $wpdb;
$table_name = $wpdb->prefix . 'orders';
$query = $wpdb->prepare("
    SELECT *
    FROM $table_name
    WHERE userid = %d
    ORDER BY id DESC", $current_user_id);
$userorders = $wpdb->get_results($query, ARRAY_A);*/

$args = array(
    'post_type'      => 'auctionorders',
    'posts_per_page' => -1,
    'meta_key'       => 'userid',
    'meta_value'     => $current_user_id,
    'orderby'        => 'ID',
    'order'          => 'DESC',
);

$userorders = get_posts($args);


$args = array(
    'post_type'      => 'auctionorders',
    'posts_per_page' => -1,
    'meta_query'     => array(
        'relation' => 'AND',
        array(
            'key'     => 'userid',
            'value'   => $current_user_id,
            'compare' => '=',
        ),
        array(
            'key'     => 'status',
            'value'   => 'completed',
            'compare' => '=',
        ),
    ),
    'orderby'        => 'ID',
    'order'          => 'DESC',
);

$comletedorders = get_posts($args);

$args = array(
    'post_type'      => 'auctionorders',
    'posts_per_page' => -1,
    'meta_query'     => array(
        'relation' => 'AND',
        array(
            'key'     => 'userid',
            'value'   => $current_user_id,
            'compare' => '=',
        ),
        array(
            'key'     => 'status',
            'value'   => 'processing',
            'compare' => '=',
        ),
    ),
    'orderby'        => 'ID',
    'order'          => 'DESC',
);

$inprocessorders = get_posts($args);

$number_of_orders = get_field('number_of_orders', 'options');

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
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
					<ul class="formobile">
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
				</div>
				<div class="dashboard-data">
					<?php 
					if(isset($_GET['order_id'])){
						$orderid = $_GET['order_id'];
						$orderdata = get_post($orderid);
						$transaction = isset($_GET['transaction']) ? $_GET['transaction'] : '';
						$paymentstatus = isset($_GET['paymentstatus']) ? $_GET['paymentstatus'] : '';

						if(empty($orderdata) || $orderdata->post_type !== 'auctionorders'){
							?>
							<div id="messageContainer">
								<p>We couldn't find any valid orders associated with this order id.</p>
							</div>
							<?php
						}else if(!empty($orderdata) && !empty($transaction)) { 


						 ?>
							<div class="singleorderhistory">
								<div class="dashboard-content">
									<div class="order-successfull" style="display: block;">
										<img src="<?php echo get_template_directory_uri(); ?>/images/checkmark.svg" class="img-fluid">
										<h4>Payment Invoice Successfully Received.</h4>
										<p>Your payment is pending admin verification. Please wait for the payment status to be updated.</p>
									</div>
								</div>
							</div><?php
						}else{

							$ordermeta = get_post_meta($orderid);
							$auctionid = $ordermeta['auctionid'][0];
							$created = $ordermeta['created'][0];
							$orderstatus = $ordermeta['status'][0];
							if($orderstatus == 'cancelled') {
								$color = 'red';
							}else if($orderstatus == 'completed') {
								$color = 'green';
							} else {
								$color = 'yellow';
							}
							$orderamount = $ordermeta['amount'][0];
							$paymenttype = $ordermeta['paymenttype'][0];
							$orderdate = date("d/m/Y", strtotime($created));

							$auctionimg = get_the_post_thumbnail_url($auctionid, 'full'); 
							$auctionname = get_the_title($auctionid); 
							$auctionlink = get_the_permalink($auctionid);

							$buyerpremium = get_post_meta($auctionid, 'buyer_premium', true); 
		                    if($buyerpremium){ 
		                        $taxamount = ($buyerpremium / 100) * $orderamount;
		                        $final_ordertotal = $orderamount + $taxamount;
		                    }else{
		                    	$final_ordertotal = $orderamount;
		                    }


						?>
						<div class="singleorderhistory">
							<div class="dashboard-content">
								<?php if(!empty($paymentstatus) && $paymentstatus == 'success'){ ?>
								<div class="order-successfull" style="display:block; margin-bottom: 50px;">
									<img src="<?php echo get_template_directory_uri(); ?>/images/checkmark.svg" class="img-fluid">
									<h4>Payment Successful</h4>
									<p>Your payment has been successfully processed. Thank you for your purchase!</p>
								</div>
								<?php } ?>
							  <div class="notices-wrapper"></div>
							  <p class="order-created"> Order # <mark class="order-number"><?php echo $orderid; ?></mark> was created on <mark class="order-date"><?php echo $orderdate; ?></mark> and is currently <mark class="order-status <?php echo $color; ?>"><?php echo $orderstatus; ?></mark>. </p>
							  <div class="order-details">
							    <h2 class="order-details__title">Order details</h2>
							    <div class="order-detail-table">
								    <table class="table table--order-details shop_table order_details mb-0">
								      <thead>
								        <tr>
								          <th class="table__product-name product-name">Item</th>
								          <th class="table__product-table product-total">Total</th>
								        </tr>
								      </thead>
								      <tbody>
								        <tr class="table__line-item order_item">
								          <td class="table__product-name product-name">
								            <a href="<?php echo $auctionlink; ?>"><?php echo $auctionname; ?></a>
								          </td>
								          <td class="table__product-total product-total">
								            <span class="Price-amount amount">
								              <bdi>
								                <span class="Price-currencySymbol">$</span><?php echo number_format($orderamount, 2);?> </bdi>
								            </span>
								          </td>
								        </tr>
								      </tbody>
								      <tfoot>
								        <tr>
								          <th scope="row">Subtotal:</th>
								          <td>
								            <span class="Price-amount amount">
								              <span class="Price-currencySymbol">$</span><?php echo number_format($orderamount, 2);?> </span>
								          </td>
								        </tr>
								        <?php if($buyerpremium){ ?>
								        <tr>
								          <th scope="row">Buyer Premium (<?php echo $buyerpremium.'%'; ?>):</th>
								          <td>
								            <span class="Price-amount amount">
								              <span class="Price-currencySymbol">$</span><?php echo number_format($taxamount, 2);?> </span>
								          </td>
								        </tr>
								    	<?php } ?>
								        <tr>
								          <th scope="row">Payment method:</th>
								          <td><?php echo $paymenttype; ?></td>
								        </tr>
								        <tr>
								          <th scope="row">Total:</th>
								          <td>
								            <span class="Price-amount amount">
								              <span class="Price-currencySymbol">$</span><?php echo number_format($final_ordertotal, 2);?> </span>
								          </td>
								        </tr>
								      </tfoot>
								    </table>
								</div>
							  </div>
							  <?php if($orderstatus == 'processing'){ ?>
							  <div class="customer-details">
							    <h4 class="column_title">Payment of order is pending. Please complete your payment by clicking the button below:</h4>

							    <a href="/checkout/?orderid=<?php echo $orderid; ?>" class="transactiondetail btn btn-black">Pay Now</a>
							    
							  </div>
							<?php } ?>
							</div>


						</div>
						<?php
						}
					}else{
					?>

					<div class="formblock">
						<form>
							<div class="searchform">
								<input type="search" name="" placeholder="Search order...">
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
						    	<button class="nav-link active" id="order-tab" data-bs-toggle="tab" data-bs-target="#order" type="button" role="tab" aria-controls="order" aria-selected="true">ALL ORDER</button>
						  	</li>
						  	<!--  -->
						  	<li class="nav-item" role="presentation">
						    	<button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">COMPLETED</button>
						  	</li>
						  	<!--  -->
						  	<li class="nav-item" role="presentation">
						    	<button class="nav-link" id="process-tab" data-bs-toggle="tab" data-bs-target="#process" type="button" role="tab" aria-controls="process" aria-selected="false">ON PROCESS</button>
						  	</li>
						</ul>
						<div class="tab-content" id="myTabContent">
					  		<div class="tab-pane fade show active" id="order" role="tabpanel" aria-labelledby="order-tab">
					  			<div class="order-historyblock">
					  				<?php if($userorders) { 
					  					foreach ($userorders as $key => $value) {
					  					$orderid = $value->ID;
					  					$ordermeta = get_post_meta($orderid);
					  					$auctionid = $ordermeta['auctionid'][0];
					  					$created = $ordermeta['created'][0];
					  					$orderstatus = $ordermeta['status'][0];
					  					$orderinvoiceid = $ordermeta['orderinvoiceid'][0];
					  					$orderimg = get_the_post_thumbnail_url($auctionid, 'thumbnail');
					  					$ordername = get_the_title($auctionid); 
					  					$orderlink = get_the_permalink($auctionid);
					  					$orderdate = date("d M Y", strtotime($created)); 
					  					if($orderstatus == 'processing') {
					  						$orderstatuslbl = 'On Process';
					  						$ordercolor = 'bg-orange';
					  					}elseif($orderstatus == 'payment-processing') {
					  						$orderstatuslbl = 'Payment In Processing';
					  						$ordercolor = 'bg-orange';
					  					}elseif($orderstatus == 'completed'){
					  						$orderstatuslbl = 'Completed';
					  						$ordercolor = 'bg-green';
					  					}else {
					  						$orderstatuslbl = 'Cancelled';
					  						$ordercolor = 'bg-red';
					  					} 
					  					$orderamount = $ordermeta['amount'][0]; ?>
					  				<div class="product-listitem">
					  					<div class="product-listitem-image"><img src="<?php echo $orderimg; ?>" class="img-fluid" /></div>
						  				<div class="product-list-desc">
						  					<a href="<?php echo $orderlink; ?>" class="title"><?php echo $ordername; ?></a>
						  					<div class="orderprice">$<?php echo number_format($orderamount, 2);?></div>
						  					<div class="orderdate"><?php echo $orderdate; ?> <span class="badge <?php echo $ordercolor; ?>"><?php echo $orderstatuslbl; ?></span></div>
						  					<div class="orderid"><?php echo $orderinvoiceid; ?></div>
						  					<a href="<?php echo '?order_id='.$orderid; ?>" class="transactiondetail btn btn-black">See Transaction Detail</a>
						  					<?php if($orderstatus == 'processing') { echo '<a href="/checkout/?orderid='.$orderid.'" class="transactiondetail btn btn-black">Pay Now</a>';}?>
						  				</div>
					  				</div>
					  				<?php }
					  				} else {  ?>
					  					<div class="product-listitem">
					  						<p>No orders found for your account.</p>
					  					</div>
					  				<?php } ?>
					  			</div>
					  		</div>
					  		<div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
					  			<div class="order-historyblock">
					  				<?php if($comletedorders) { foreach ($comletedorders as $key => $value) { 

					  					$orderid = $value->ID;
					  					$ordermeta = get_post_meta($orderid);
					  					$auctionid = $ordermeta['auctionid'][0];
					  					$created = $ordermeta['created'][0];
					  					$orderstatus = $ordermeta['status'][0];
					  					$orderinvoiceid = $ordermeta['orderinvoiceid'][0];
					  					$orderimg = get_the_post_thumbnail_url($auctionid, 'thumbnail');
					  					$ordername = get_the_title($auctionid); 
					  					$orderlink = get_the_permalink($auctionid); 
					  					$orderdate = date("d M Y", strtotime($created));
					  					$orderamount = $ordermeta['amount'][0]; ?>
					  				<div class="product-listitem">
					  					<div class="product-listitem-image"><img src="<?php echo $orderimg; ?>" class="img-fluid" /></div>
						  				<div class="product-list-desc">
						  					<a href="<?php echo $orderlink; ?>" class="title"><?php echo $ordername; ?></a>
						  					<div class="orderprice">$<?php echo number_format($orderamount, 2);?></div>
						  					<div class="orderdate"><?php echo $orderdate; ?> <span class="badge bg-green">Completed</span></div>
						  					<div class="orderid"><?php echo $orderinvoiceid; ?></div>
						  					<a href="" class="transactiondetail btn btn-black">See Transaction Detail</a>
						  				</div>
					  				</div>
					  				<?php }
					  				} else {  ?>
					  					<div class="product-listitem">
					  						<p>No completed orders found for your account.</p>
					  					</div>
					  				<?php } ?>
					  			</div>
					  		</div>
					  		<div class="tab-pane fade" id="process" role="tabpanel" aria-labelledby="process-tab">
					  			<div class="order-historyblock">
					  				<?php if($inprocessorders) { 
					  					foreach ($inprocessorders as $key => $value) { 

					  						$orderid = $value->ID;
						  					$ordermeta = get_post_meta($orderid);
						  					$auctionid = $ordermeta['auctionid'][0];
						  					$created = $ordermeta['created'][0];
						  					$orderstatus = $ordermeta['status'][0];
						  					$orderinvoiceid = $ordermeta['orderinvoiceid'][0];
						  					$orderimg = get_the_post_thumbnail_url($auctionid, 'thumbnail');
						  					$ordername = get_the_title($auctionid); 
						  					$orderlink = get_the_permalink($auctionid); 
						  					$orderdate = date("d M Y", strtotime($created));
						  					$orderamount = $ordermeta['amount'][0]; 

					  					?>
					  				<div class="product-listitem">
					  					<div class="product-listitem-image"><img src="<?php echo $orderimg; ?>" class="img-fluid" /></div>
						  				<div class="product-list-desc">
						  					<a href="<?php echo $orderlink; ?>" class="title"><?php echo $ordername; ?></a>
						  					<div class="orderprice">$<?php echo number_format($orderamount, 2);?></div>
						  					<div class="orderdate"><?php echo $orderdate; ?> <span class="badge bg-orange">On Process</span></div>
						  					<div class="orderid"><?php echo $orderinvoiceid; ?></div>
						  					<a href="" class="transactiondetail btn btn-black">See Transaction Detail</a>
						  					<a href="/checkout/?orderid=<?php echo $orderid; ?>" class="transactiondetail btn btn-black">Pay Now</a>
						  				</div>
					  				</div>
					  				<?php }
					  				} else {  ?>
					  					<div class="product-listitem">
					  						<p>No inprocess orders found for your account.</p>
					  					</div>
					  				<?php } ?>
					  			</div>
					  		</div>
					  	</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?> 