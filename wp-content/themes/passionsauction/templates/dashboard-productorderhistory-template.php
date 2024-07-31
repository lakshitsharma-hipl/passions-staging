<?php 
/* Template Name: Dashboard Product Order History*/
if(!is_user_logged_in()){
	wp_redirect('/login/');
	exit;
}

get_header(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>

<?php 

// update_post_meta( 1784, 'product_order_grand_total', 3199 );

$current_user_id = get_current_user_id();
$get_userdata = get_userdata($current_user_id);
$first_name = $get_userdata->first_name;
$last_name = $get_userdata->last_name;
$user_registered = $get_userdata->user_registered;
$date = new DateTime($user_registered);
$formattedDate = $date->format("F Y");

// global $wpdb;
// $table_name = $wpdb->prefix . 'orders';
// $query = $wpdb->prepare("
//     SELECT *
//     FROM $table_name
//     WHERE userid = %d
//     ORDER BY id DESC", $current_user_id);
// $userorders = $wpdb->get_results($query, ARRAY_A);

// echo "Check " .get_post_meta( 1781, 'userid', true );

$args = array(
    'post_type'      => 'product-order',
    'posts_per_page' => -1,
    'meta_key'       => 'userid',
    'meta_value'     => $current_user_id,
    'orderby'        => 'ID',
    'order'          => 'DESC',
);

$userorders = get_posts($args);

$args = array(
    'post_type'      => 'product-order',
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
    'post_type'      => 'product-order',
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
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li class="active"><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
					<ul class="formobile">
						<li class="active"><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>						
						<li><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>						
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
				</div>
				<div class="dashboard-data">
					<?php 
					if(isset($_GET['product_order_id'])){
						$orderid = $_GET['product_order_id'];
						$orderdata = get_post($orderid);
						$transaction = isset($_GET['transaction']) ? $_GET['transaction'] : '';
						$paymentstatus = isset($_GET['paymentstatus']) ? $_GET['paymentstatus'] : '';

						if(empty($orderdata) || $orderdata->post_type !== 'product-order'){
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
										<h4>Payment Successfully Received.</h4>
										
									</div>
								</div>
							</div><?php
						}else{

							$ordermeta = get_post_meta($orderid);							
							
							$orderdate = get_the_date("d/m/Y", $orderid);
							
							$order_all_data = unserialize($ordermeta['auc_order_product_ids'][0]);
							
							// if($orderstatus == 'cancelled') {
							// 	$color = 'red';
							// }else if($orderstatus == 'completed') {
							// 	$color = 'green';
							// } else {
							// 	$color = 'yellow';
							// }
							

							$auctionimg = get_the_post_thumbnail_url($orderid, 'full'); 
							$auctionname = get_the_title($orderid); 
							$auctionlink = get_the_permalink($orderid); 
							$grand_total = get_post_meta( $orderid, 'product_order_grand_total', true ) ;
							$orderstatus = $ordermeta['product_order_status'][0];
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
							  <div class="order-details" data-user="<?php echo $first_name; ?>" data-invoice="<?php echo $orderdata->productorderinvoiceid; ?>">
							    <h2 class="order-details__title">Order details</h2>
							    <div class="order-detail-table">
								    <table class="table table--order-details shop_table order_details mb-0">
								      <thead>
								        <tr>
								          <th class="table__product-name product-name">Item</th>
								          <th class="table__product-name product-name">Qty</th>
								          <th class="table__product-table product-total">Total</th>
								        </tr>
								      </thead>
								      <tbody>
										<?php  
											if(!empty($order_all_data)) {											
												foreach($order_all_data as $product) {
													// $product = unserialize($product);                    
													$prodcut_img = get_the_post_thumbnail_url( $product['product_id'] );
													$product_qty_price_total = $product['product_qty']*$product['product_price'];
													?>
													<tr class="table__line-item order_item">
														<td class="table__product-name product-name">
															<a href="<?php echo get_the_permalink($product['product_id'] ) ?>"><?php echo get_the_title($product['product_id']); ?></a>
														</td>
														<td>
															<?php echo $product['product_qty'] ?>
														</td>
														<td class="table__product-total product-total">
															<span class="Price-amount amount">
																<bdi><span class="Price-currencySymbol">$</span> <?php echo $product_qty_price_total; ?> </bdi>
															</span>
														</td>
													</tr>														
													<?php
													
												}
											}
										?>
								        
								      </tbody>
								      <tfoot>

								        <tr>
								          <th colspan="2" scope="row">Subtotal:</th>
								          <td>
								            <span class="Price-amount amount">
								              <span class="Price-currencySymbol">$</span> <?php echo $grand_total; ?></span>
								          </td>
								        </tr>
								        
								        <tr>
								          <th colspan="2" scope="row">Total:</th>
								          <td>
								            <span class="Price-amount amount">
								              <span class="Price-currencySymbol">$</span> <?php echo $grand_total; ?></span>
								          </td>
								        </tr>
								      </tfoot>
								    </table>									
								</div>
								
							  </div>
							  <!-- Product Order Status block  -->
							  <?php // if($orderstatus == 'processing'){ ?>
							  <!-- <div class="customer-details">
							    <h4 class="column_title">Payment of order is pending. Please complete your payment by clicking the button below:</h4>

							    <a href="/checkout/?orderid=<?php echo $orderid; ?>" class="transactiondetail btn btn-black">Pay Now</a>
							    
							  </div> -->
							<?php // } ?>
							</div>

							<a class="btn btn-green" href="javascript:void(0)" id="downloadInvoiceBtn">Download Invoice</a>
						</div>
						<?php
						}
					}else{
					?>					
					<div class="orderhistory">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
						  	<li class="nav-item" role="presentation">
						    	<button class="nav-link active" id="order-tab" data-bs-toggle="tab" data-bs-target="#order" type="button" role="tab" aria-controls="order" aria-selected="true">ALL ORDER</button>
						  	</li>
						  	<!--  -->
						  	<!-- <li class="nav-item" role="presentation">
						    	<button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">COMPLETED</button>
						  	</li>
						  	  -->
						  	<!-- <li class="nav-item" role="presentation">
						    	<button class="nav-link" id="process-tab" data-bs-toggle="tab" data-bs-target="#process" type="button" role="tab" aria-controls="process" aria-selected="false">ON PROCESS</button>
						  	</li>  -->
						</ul> 
						<div class="tab-content" id="myTabContent">
					  		<div class="tab-pane fade show active" id="order" role="tabpanel" aria-labelledby="order-tab">
					  			<div class="order-historyblock">

					  				<?php if($userorders) {  ?>
										<table class="table">
											<thead>
												<tr>
													<td>Order Id</td>
													<td>Date</td>
													<td>Status</td>
													<td>Product|Qty</td>
													<td>Total</td>
													<td>Action</td>
												</tr>
											</thead>
											<tbody>										
												<?php
													foreach ($userorders as $key => $value) { ?>
														<tr>
															<?php
															$orderid = $value->ID;
															$orderdata = get_post_meta($orderid);
															$product_data = unserialize($orderdata['auc_order_product_ids'][0]);
															$orderdate = get_the_date('d/m/Y');
															$orderstatus = get_post_meta( $orderid, 'product_order_status', true );
															$orderamount = get_post_meta( $orderid, 'product_order_grand_total', true );
															?>
															<td><?php echo $orderid; ?></td>
															<td><?php echo $orderdate; ?></td>
															<td><?php echo ucfirst($orderstatus); ?></td>
															<td>
																<div class="product-order-row">
																	<?php
																	//echo '<span class="product-order-id">Order Id: '.$orderid.'</span>';
																	if(!empty($product_data)) {											
																		foreach($product_data as $product) {
																			// $product = unserialize($product);                    
																			$prodcut_img = get_the_post_thumbnail_url( $product['product_id'] );
																			
																			?>
																				<div class="product-listitem">																					
																					<div class="product-list-desc">
																						<a href="<?php echo get_the_permalink($product['product_id'] ) ?>" class="title"><?php echo get_the_title($product['product_id']); ?></a>
																						
																						<div class="orderqty"> x <?php echo $product['product_qty'] ?></div>																						
																					</div>
																				</div>
																			<?php
																			
																		}
																	}
																	?>
																</div>
															</td>
															<td><?php echo '$'.$orderamount; ?></td>
															<td><a href="<?php echo '?product_order_id='.$orderid; ?>" class="transactiondetail btn btn-black">View</a></td>
														</tr>
													<?php 
													
												} ?>
											</tbody>
										</table> <?php
					  				} else {  ?>
					  					<div class="product-listitem">
					  						<p>No orders found for your account.</p>
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