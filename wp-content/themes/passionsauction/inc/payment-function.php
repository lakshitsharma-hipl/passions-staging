<?php
add_action('wp_ajax_sendPaymentInvoice', 'sendPaymentInvoice');
add_action('wp_ajax_nopriv_sendPaymentInvoice', 'sendPaymentInvoice');
function sendPaymentInvoice() {


    $response = array();
    extract($_POST);
   	$usermessage = $_POST['paymentmsg'];
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $current_user_id = get_current_user_id();

    if(isset($_FILES['attachment']) && isset($_POST['orderid'])) {
        $file = $_FILES['attachment'];
        $post_id = $_POST['orderid'];

        if($file['error'] === 0) {
            $uploadDir = wp_upload_dir()['path'] . '/orderinvoices/';
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
                $attachment_id = wp_insert_attachment($attachment_data, $uploadPath, $post_id);

                $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $uploadPath);
                wp_update_attachment_metadata($attachment_id, $attachment_metadata);
                if(isset($usermessage)){
                	update_post_meta($post_id, 'userpaymentmessage', $usermessage);

                    update_post_meta($post_id, 'address', $paymentstreet);
                    update_post_meta($post_id, 'city', $paymentcity);
                    update_post_meta($post_id, 'state', $paymentstate);
                    update_post_meta($post_id, 'country', $paymentcountry);
                    update_post_meta($post_id, 'zipcode', $paymentzipcode);
                    update_post_meta($post_id, 'phone', $paymentphone);
                }
                update_post_meta($post_id, 'invoiceid', $attachment_id);
                update_post_meta($post_id, 'status', 'payment-processing');
                 $redirecturl = home_url('dashboard/order-history?order_id='.$post_id).'&transaction=bank';
                $response = array('status' => 'success', 'message' => 'Invoice sent to admin successfuly.', 'attachment_id' => $attachment_id, 'url' => $uploadPath, 'redirect_url' =>$redirecturl);

                //SendEmailToAdminOnPaymentDoneByUser($post_id);
                SendAuctionEmail($current_user_id, $post_id, 'paymentsuccess');

            } else {
                $response = array('status' => 'failed', 'message' => 'Error moving file');
            }
        } else {
        	$response = array('status' => 'failed', 'message' => 'Error uploading file');
        }
    } else {
    	$response = array('status' => 'failed', 'message' => 'No file data received');
    }

    wp_send_json($response);
    die();
}

function paymentByStripe() {
    $data = array();
    extract($_POST);
    $fname = sanitize_text_field($_POST['first_name']);
    $lname = sanitize_text_field($_POST['last_name']);
    $full_name = $first_name .' '. $last_name;
    $email_address = sanitize_email($_POST['email']);
    $stripe_keys = get_field('stripe_details', 'option');

    $currentuserid = get_current_user_id();

    $orderid = sanitize_text_field($_POST['orderid']);
    $post_type = get_post_type($orderid); 

    if($post_type != 'auctionorders'){
        $data = array('status' => 'failed', 'message' => 'Invalid order id. Please try again after sometime.');
        wp_send_json($data);
        wp_die();
    }
    $orderdetails = get_post_meta($orderid);
    $orderuser = $orderdetails['userid'][0];
    $orderstatus = $orderdetails['status'][0];
    $orderamount = $orderdetails['amount'][0];

    $buyerpremium = get_post_meta($orderid, 'buyerpremium', true); 
    if($buyerpremium){ 
        $taxamount = ($buyerpremium / 100) * $orderamount;
        $final_ordertotal = $orderamount + $taxamount;
    }else{
        $final_ordertotal = $orderamount;
    }

    $auctionid = $orderdetails['auctionid'][0];
    $orderinvoiceid = $orderdetails['orderinvoiceid'][0];
    
    $auctionname = get_the_title($auctionid);

    $current_user_id = get_current_user_id();

    if($orderuser != $currentuserid){
        $data = array('status' => 'failed', 'message' => 'Invalid order id. Please try again after sometime.');
        wp_send_json($data);
        wp_die();
    }

    if($orderstatus != 'processing'){
        $data = array('status' => 'failed', 'message' => 'Invalid order status. Please try again after sometime.');
        wp_send_json($data);
        wp_die();
    }

    if(isset($stripe_keys['stripe_secret_key'])){

        $amountinsent = intval($final_ordertotal) * 100; 
        $stripe_secret_key = $stripe_keys['stripe_secret_key'];
        require_once get_template_directory() . '/inc/stripe/init.php';
        \Stripe\Stripe::setApiKey($stripe_secret_key);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = $fname.' '.$lname;
            $existing_customer = \Stripe\Customer::all(['email' => $email_address, 'limit' => 1])->data[0] ?? null;
            if (!$existing_customer) {
                $customer = \Stripe\Customer::create([
                    'email' => $email_address,
                    'source'  => $_POST['stripeToken'],
                    'name' => $fullname,
                    'address' => [ 
                        'line1' => '510 Townsend St',
                        'postal_code' => '98140',
                        'city' => 'San Francisco',
                        'state' => 'CA',
                        'country' => 'US',
                    ],
                ]);
            } else {
                $customer = $existing_customer;
            }

            try {
                $charge = \Stripe\Charge::create([
                    'amount' => $amountinsent,
                    'currency' => 'usd',
                    'customer' => $customer->id,
                    'description' => 'Payment for auction '.$auctionname.' Invoice number '.$orderinvoiceid,
                ]);

                /*$charge = \Stripe\Charge::retrieve($charge->id);
                $invoice = \Stripe\Invoice::create([
                    'customer' => $charge->customer,
                    'description' => 'Invoice for payment for auction "'.$auctionname.'".',
                ]);
                sleep(1);
                $invoice = \Stripe\Invoice::retrieve($invoice->id);
                $invoiceUrl = $invoice->hosted_invoice_url;*/

                update_post_meta($orderid, 'stripe_payment_object', $charge);
                update_post_meta($orderid, 'stripe_payment_id', $charge->id);
                update_post_meta($orderid, 'status', 'completed');

                update_post_meta($orderid, 'address', $paymentstreet);
                update_post_meta($orderid, 'city', $paymentcity);
                update_post_meta($orderid, 'state', $paymentstate);
                update_post_meta($orderid, 'country', $paymentcountry);
                update_post_meta($orderid, 'zipcode', $paymentzipcode);
                update_post_meta($orderid, 'phone', $paymentphone);

                //update_post_meta($orderid, 'invoiceurl', $invoiceUrl);
                $chargeid = $charge->id;
                $redirecturl = home_url('dashboard/order-history?order_id='.$orderid).'&paymentstatus=success&paymentid='.$chargeid;
                $data = array('status' => 'success', 'redirect_url' =>$redirecturl, 'message' => 'Payment Confirmed: Your Transaction with Stripe is Complete!');
                
                SendAuctionEmail($current_user_id, $orderid, 'paymentsuccess');

            } catch (Exception $e) {
                $data = array('status' => 'failed', 'message' => $e->getMessage());
            }   
        
        }
    }else{
        $data = array('status' => 'failed', 'message' => 'Stripe details are missing or invalid. Please try again after sometime.');
    }
    wp_send_json($data);
    wp_die();
}

add_action('wp_ajax_nopriv_paymentByStripe', 'paymentByStripe');
add_action('wp_ajax_paymentByStripe', 'paymentByStripe');

add_action('add_meta_boxes', 'add_auction_order_meta_box');
function add_auction_order_meta_box() {
    add_meta_box(
        'auction_order_details_meta_box',
        'Auction Order Details',
        'render_auction_order_meta_box',
        'auctionorders', 
        'normal', // Context: normal, advanced, or side
        'high' // Priority: high, core, default, or low
    );
}

function render_auction_order_meta_box($post) {
    // Retrieve the meta value for the specific meta key
    $orderdata = get_post_meta($post->ID);
    $auctionid = $orderdata['auctionid'][0]; 
    $auctiontitle = get_the_title($auctionid); 
    $orderamount = $orderdata['amount'][0];
    $userid = $orderdata['userid'][0];
    $orderinvoiceid = $orderdata['orderinvoiceid'][0];

    $paymenttype = $orderdata['paymenttype'][0];
    $biduser = $orderdata['biduser'][0];

    if(isset($orderdata['invoiceid'][0])){
        $invoiceid = $orderdata['invoiceid'][0];
    }else{
        $invoiceid = '';
    }

    if(isset($orderdata['invoiceurl'][0])){
        $invoiceurl = $orderdata['invoiceurl'][0];
    }else{
        $invoiceurl = '';
    }

    if(isset($orderdata['userpaymentmessage'][0])){
        $userpaymentmessage = $orderdata['userpaymentmessage'][0];
    }else{
        $userpaymentmessage = '';
    }

    $user_info = get_userdata($userid);
    if ($user_info) {
        $user_details = $user_info->first_name . ' ' . $user_info->last_name . ' (' . $user_info->user_email . ')';
    }else{
        $user_details = '';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'bidhistory';
    $query = $wpdb->prepare("
        SELECT t1.*
        FROM $table_name AS t1
        INNER JOIN (
            SELECT userid, MAX(bidamount) AS max_bid
            FROM $table_name
            WHERE auctionid = %d
            GROUP BY userid
            ORDER BY max_bid DESC
            LIMIT 3
        ) AS t2 ON t1.userid = t2.userid AND t1.bidamount = t2.max_bid
        WHERE t1.auctionid = %d
        ORDER BY t1.bidamount DESC
        LIMIT 3", 
        $auctionid,
        $auctionid
    );

    $bid_user_results = $wpdb->get_results($query);

    $orderid = $post->ID;
    $buyerpremium = get_post_meta($orderid, 'buyerpremium', true); 
    if($buyerpremium){ 
        $taxamount = ($buyerpremium / 100) * $orderamount;
        $final_ordertotal = $orderamount + $taxamount;
    }else{
        $final_ordertotal = $orderamount;
    }

    ?>
    <div class="auction-order-details">
        <div class="meta-item">
            <span class="meta-label">Auction Name:</span>
            <span class="meta-value"><?php echo esc_html($auctiontitle); ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Price:</span>
            <span class="meta-value"><?php echo '$ '.number_format($final_ordertotal, 2); ?></span>
        </div>

        <?php if($buyerpremium){ ?>
            <div class="meta-item">
                <span class="meta-label">Buyer Premium (<?php echo $buyerpremium.'%'; ?>):</span>
                <span class="meta-value"><?php echo '$ '.number_format($taxamount, 2); ?></span>
            </div>
        <?php } ?>

        <div class="meta-item">
            <span class="meta-label">User:</span>
            <span class="meta-value"><?php echo esc_html($user_details); ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Invoice Number:</span>
            <span class="meta-value"><?php echo esc_html($orderinvoiceid); ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Payment Type:</span>
            <span class="meta-value"><?php echo esc_html($paymenttype); ?></span>
        </div>
        <?php  if(isset($orderdata['phone'][0])){ ?>
        <div class="meta-item">
            <span class="meta-label">Phone:</span>
            <span class="meta-value"><?php echo esc_html($orderdata['phone'][0]); ?></span>
        </div>
        <?php } ?>

        <?php  if(isset($orderdata['city'][0])){ ?>
        <div class="meta-item">
            <span class="meta-label">City:</span>
            <span class="meta-value"><?php echo esc_html($orderdata['city'][0]); ?></span>
        </div>
        <?php } ?>

        <?php  if(isset($orderdata['state'][0])){ ?>
        <div class="meta-item">
            <span class="meta-label">State:</span>
            <span class="meta-value"><?php echo esc_html($orderdata['state'][0]); ?></span>
        </div>
        <?php } ?>

        <?php  if(isset($orderdata['country'][0])){ ?>
        <div class="meta-item">
            <span class="meta-label">Country:</span>
            <span class="meta-value"><?php echo esc_html($orderdata['country'][0]); ?></span>
        </div>
        <?php } ?>

        <?php  if(isset($orderdata['zipcode'][0])){ ?>
        <div class="meta-item">
            <span class="meta-label">Zip code:</span>
            <span class="meta-value"><?php echo esc_html($orderdata['zipcode'][0]); ?></span>
        </div>
        <?php } ?>

        <?php  if(isset($orderdata['address'][0])){ ?>
        <div class="meta-item">
            <span class="meta-label">Address:</span>
            <span class="meta-value"><?php echo esc_html($orderdata['address'][0]); ?></span>
        </div>
        <?php } ?>


        <?php if($invoiceid){ ?>
        <div class="meta-item">
            <span class="meta-label">User Payment Reference:</span>
            <?php $inurl = wp_get_attachment_url($invoiceid) ?>
            <span class="meta-value"><a href="<?php echo $inurl ?>" target="_blank" download>Download Payment Reference</a></span>
        </div>
        <?php if(!empty($userpaymentmessage)){ ?>
        <div class="meta-item">
            <span class="meta-label">User offline payment message:</span>
            <span class="meta-value"><?php echo esc_html($userpaymentmessage); ?></span>
        </div>
        <?php } ?>
        <?php } ?>

        <?php if($bid_user_results) { 
            $count = 1;
            foreach ($bid_user_results as $key => $value) { 
                $user_info = get_userdata($value->userid);
                if ($user_info) {
                    $user_details = $user_info->first_name . ' ' . $user_info->last_name . ' (' . $user_info->user_email . ')';
                }else{
                    $user_details = '';
                } ?>
            <div class="meta-item">
                <span class="meta-label"><?php echo $count; ?> Highest Bid</span>
                <span class="meta-value">User : <?php echo $user_details; ?> / Price : <?php echo '$ '.number_format($value->bidamount, 2); ?></span>
            </div><?php $count++;
            }
        } ?>

    </div>
    <style>
        .auction-order-details {
            margin-top: 20px;
        }
        .meta-item {
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e4e4;
            padding-bottom: 10px;
        }
        span.meta-label {
            font-size: 16px;
            display: inline-block;
            width: 18%;
            font-weight: 600;
        }
        .meta-value {
            margin-left: 10px;
            font-size: 15px;
            display: inline-block;
            width: 75%;
        }
    </style>
    <?php

    // Display the meta value in the meta box
    //echo '<p>' . esc_html($auction_details) . '</p>';
}