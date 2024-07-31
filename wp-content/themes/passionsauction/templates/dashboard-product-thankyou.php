<?php 
/* Template Name: Thank You Product Template */

if(!is_user_logged_in()){
	wp_redirect('/login/');
	exit;
} else {
    $current_user_id = get_current_user_id();
}

get_header(); 


?>

<section class="account-dashboard">
	<div class="customcontainer">
		<div class="dashboardmax">
			<!--  -->
			<div class="dashboard-wrapper">
				<div class="dashboard-data">
					<?php 
					if(isset($_GET['product_order_id'])){
                        $product_order_id = $_GET['product_order_id'];
                        $order_user_id = get_post_meta( $product_order_id, 'userid', true );
                        if($current_user_id == $order_user_id){
                                                    
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
                                <?php 
                                if($orderstatus == 'payment-processing') { ?>
                                <div class="afterpymsg">
                                    <p>Your payment is pending admin verification. Please wait for the payment status to be updated.</p>
                                </div>
                                <?php } ?>
                                <p class="order-created"> Order # <mark class="order-number"><?php echo $orderid; ?></mark> was created on <mark class="order-date"><?php echo $orderdate; ?></mark> and is currently <mark class="order-status <?php echo $color; ?>"><?php echo $orderstatus; ?></mark>. </p>
                                <div class="order-details">
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


                            </div>
                            <?php
                            }
                        } else {
                            echo '<div class="not-authorised error">You are not authorised</div>';
                        }
					} else {
                        echo '<div class="not-authorised error">No purchse found for this user</div>';
                    } ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php

get_footer(); ?> 