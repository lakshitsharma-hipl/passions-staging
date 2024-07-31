<?php

function product_order_email_function($to_mail, $mail_subject, $mail_message, $product_data, $grand_total) {

    $user_id = get_current_user_id(); 
    $user_info = get_userdata($user_id); 

    if ($user_info) {
        $first_name = $user_info->first_name;        
    }

    $admin_email = get_option('admin_email');

    if ($to_mail == $admin_email) {
        $name = 'Admin';
    } else {
        $name = $first_name;
    }

    $html = '<!DOCTYPE html>
                <html>
                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <title></title>
                        <link rel="preconnect" href="https://fonts.googleapis.com">
                        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                    </head>
                    <body style="margin: 0;padding: 20px; background: #e5e6ff;font-family: \'Noto Sans\', sans-serif;">
                        <table style="margin: 0 auto;max-width: 540px;background: #fff;padding: 20px;">
                            <thead>        
                                <tr>
                                    <th style="text-align: center;padding: 20px; border-bottom: solid 1px #ddd;"><img src="'.site_url().'/wp-content/uploads/2024/04/site-email-logo.png"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h5 style="color: #0D8080; margin:10px 0 10px;">Dear '.$name.'</h5>
                                        <p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">' . $mail_message . '</p>
                                        <p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Order details</p>
                                        <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <thead>
                                                <tr>
                                                    <th style="color: #fff;padding:8px; border:solid 1px #ddd;background: #000;">Item</th>
                                                    <th style="color: #fff;padding:8px; border:solid 1px #ddd;background: #000;">Qty</th>
                                                    <th style="color: #fff;padding:8px; border:solid 1px #ddd;background: #000;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            foreach ($product_data as $product) {
                                                $product_name = get_the_title($product['product_id']);
                                                $product_qty = $product['product_qty'];
                                                $product_price_total = $product['product_qty'] * $product['product_price'];
                                                $html .= '<tr>
                                                            <td style="border:solid 1px #ddd;padding:8px;">
                                                                <a href="' . get_the_permalink($product['product_id']) . '">' . $product_name . '</a>
                                                            </td>
                                                            <td style="border:solid 1px #ddd;padding:8px;">'.$product_qty.'</td>
                                                            <td style="border:solid 1px #ddd;padding:8px;">
                                                                <span><bdi><span>$</span>' . $product_price_total . '</bdi></span>
                                                            </td>
                                                        </tr>';
                                            }
                                           $html .= '</tbody>
                                            <tfoot>
                                                <tfoot>
                                                    <tr>
                                                        <th style="border:solid 1px #ddd;padding:8px;" colspan="2" scope="row">Subtotal:</th>
                                                        <td style="border:solid 1px #ddd;padding:8px;">
                                                            <span>
                                                            <span>$</span>' . $grand_total . '</span>
                                                        </td>
                                                    </tr>
                                                <tr>
                                                    <th style="border:solid 1px #ddd;padding:8px;" colspan="2" scope="row">Total:</th>
                                                    <td style="border:solid 1px #ddd;padding:8px;">
                                                        <span>
                                                        <span>$</span>' . $grand_total . '</span>
                                                    </td>
                                                </tr>
                                           </tfoot>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody style="background:#0D8080;">
                                <tr>
                                    <td>
                                        <table style="width:100%;">
                                            <tr>
                                                <td style="width: 50%; padding: 15px;">
                                                    <p style="color:#fff;font-size: 16px;margin:0 0 20px;font-family: \'Noto Sans\', sans-serif;">Keep up to date, follow us!</p>
                                                    <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                            <tr>
                                                                <td align="left"  style="vertical-align: top; width: 40px;">
                                                                    <a href="#" target="_blank"> <img style="max-width: 32px;" src="'.site_url().'/wp-content/uploads/2024/04/icon-insta.png"> </a>
                                                                </td>
                                                                <td align="left"  class="margin" style="vertical-align: top; width: 40px;">
                                                                    <a href="#" target="_blank"> <img style="max-width: 32px;" src="'.site_url().'/wp-content/uploads/2024/04/icon-twitter.png"> </a>
                                                                </td>
                                                                <td align="left"  style="vertical-align: top; width: 40px;">
                                                                    <a href="#" target="_blank"> <img style="max-width: 32px;" src="'.site_url().'/wp-content/uploads/2024/04/icon-fb.png"> </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="width: 50%; padding: 15px;">
                                                    <p style="color:#fff;font-size: 16px;margin:0 0 20px;font-family: \'Noto Sans\', sans-serif;"><a href="mailto:info@gmail.com" target="_blank" style="color:#fff; text-decoration: none;">info@gmail.com</a></p>
                                                    <p style="color:#fff;font-size: 16px;margin:0 0 20px;font-family: \'Noto Sans\', sans-serif;"><a href="tel:+6596945671" target="_blank" style="color:#fff; text-decoration: none;">+65 9694 5671</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </body>
                </html>';

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";        
            $from_email = 'Wordpress@' . $_SERVER['SERVER_NAME'];
            $headers .= 'From: '.get_bloginfo().' <' . $from_email . '>' . "\r\n";
            wp_mail($to_mail, $mail_subject, $html, $headers);
}

function paymentByStripeForProduct() {
    global $wpdb;
    $data = array();
    extract($_POST);

    $fname = sanitize_text_field($_POST['first_name']);
    $lname = sanitize_text_field($_POST['last_name']);
    $full_name = $first_name .' '. $last_name;
    $email_address = sanitize_email($_POST['email']);
    $stripe_keys = get_field('stripe_details', 'option');
    $total_amount = passion__decrypt_data($_POST['grandTotal']);
    $currentuserid = get_current_user_id();    
    $phonecode = $_POST['phonecode'];
    $orderamount = $total_amount;
    // $auctionname = Use Product name from order
    $current_user_id = get_current_user_id();

    $cart_data = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}passions_product_cart WHERE user_id = %d",
            $current_user_id
        ),
        ARRAY_A
    );

    // $product_data = array(); // Array to store product IDs

    foreach ($cart_data as $cart_item) {        
        $product_id = $cart_item['product_id'];
        $product_qty = intval($cart_item['quantity']);
        // Retrieve product price
        $sale_price_check = get_field('product_sale_price', $product_id);
        if ($sale_price_check) {
            $product_price = $sale_price_check;
        } else {
            $product_price = get_field('product_regular_price', $product_id);
        }

        // Store product ID and price as a multidimensional array
        $product_data_all[] = array(
            'product_id' => $product_id,
            'product_qty' => $product_qty,
            'product_price' => $product_price
        );
    }

    if(isset($stripe_keys['stripe_secret_key'])){

        $amountinsent = intval($orderamount) * 100; 
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
                    'description' => 'Payment for '.$auctionname,
                ]);

                /*$charge = \Stripe\Charge::retrieve($charge->id);
                $invoice = \Stripe\Invoice::create([
                    'customer' => $charge->customer,
                    'description' => 'Invoice for payment for auction "'.$auctionname.'".',
                ]);
                sleep(1);
                $invoice = \Stripe\Invoice::retrieve($invoice->id);
                $invoiceUrl = $invoice->hosted_invoice_url;*/

                $post_title = 'Product Order Stripe';
                $post_content = ''; // You can set the post content as needed
                
                $post_data = array(
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_type'     => 'product-order',
                    'post_status'   => 'publish',
                );
                $orderid = wp_insert_post($post_data);

                if ($orderid) {
                    // Update post title with order ID
                    $new_post_data = array(
                        'ID'         => $orderid,
                        'post_title' => 'Product Order #' . $orderid,
                    );
                    $productorderinvoiceid = 'INV/'.current_time('Ymd').'/'.$orderid; 
                    wp_update_post($new_post_data);
                    update_post_meta($orderid, 'auc_order_product_ids', $product_data_all);
                    update_post_meta($orderid, 'stripe_payment_object', $charge);
                    update_post_meta($orderid, 'stripe_payment_id', $charge->id);
                    update_post_meta($orderid, 'product_order_status', 'processing');
                    update_post_meta($orderid, 'product_order_grand_total', $orderamount);
                    update_post_meta($orderid, 'userid', $current_user_id );
                    update_post_meta($orderid, 'order_email', $email_address );
                    update_post_meta($orderid, 'address', $paymentstreet);
                    update_post_meta($orderid, 'city', $paymentcity);
                    update_post_meta($orderid, 'state', $paymentstate);
                    update_post_meta($orderid, 'country', $paymentcountry);
                    update_post_meta($orderid, 'zipcode', $paymentzipcode);
                    update_post_meta($orderid, 'phone', '+'.$phonecode.$_POST['passion_product_phone_number']);
                    update_post_meta( $orderid, 'productorderinvoiceid', $productorderinvoiceid );
                    update_post_meta($orderid, 'order_datetime', current_time( 'timestamp' ));

                    update_user_meta($currentuserid, 'address', $paymentstreet);
                    update_user_meta($currentuserid, 'city', $paymentcity);
                    update_user_meta($currentuserid, 'state', $paymentstate);
                    update_user_meta($currentuserid, 'country', $paymentcountry);
                    update_user_meta($currentuserid, 'zipcode', $paymentzipcode);
                    // update_user_meta($currentuserid, 'phone', $paymentphone);
                    // update_user_meta($currentuserid, 'phone', );
                    


                    // Handle email sending to admin
                    $admin_email = get_option('admin_email');
                    
                    if($admin_email) {
                        $admin_subject = 'New Order Received';
                        $admin_message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">You have received a new order. Below are the order details:</p>';    
                        product_order_email_function($admin_email, $admin_subject, $admin_message, $product_data_all, $orderamount);
                    }
                    if($email_address) {
                        $customer_subject = 'Order Confirmation';
                        $customer_message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Thank you, Your order has been received. Below are the order details:</p>';
    
                        product_order_email_function($email_address, $customer_subject, $customer_message, $product_data_all, $orderamount);
                    }
                    // foreach ($product_data_all as $product_info) {
                        
                        // $admin_message .= "\nProduct: " . get_the_title($product_info['product_id']) . ", Quantity: " . $product_info['product_qty'] . ", Price: " . $product_info['product_price'];
                    // }
                    // wp_mail($admin_email, $admin_subject, $admin_message);

                    // Handle email sending to customer
                    
                    // Append product details to the email message
                    // foreach ($product_data_all as $product_info) {
                        
                        // $customer_message .= "\nProduct: " . get_the_title($product_info['product_id']) . ", Quantity: " . $product_info['product_qty'] . ", Price: " . $product_info['product_price'];
                    // }
                    // wp_mail($email_address, $customer_subject, $customer_message);


                    foreach ($product_data_all as $product_info) {
                        $product_id = $product_info['product_id'];
                        $product_qty = $product_info['product_qty'];                    
                        $current_stock = get_field('stock', $product_id);                    
                        $new_stock = $current_stock - $product_qty;                    
                        update_field('stock', $new_stock, $product_id);
                    }
                    // Delete rows related to the current user in passions_product_cart table
                    $wpdb->delete(
                        $wpdb->prefix . 'passions_product_cart',
                        array('user_id' => $current_user_id),
                        array('%d')
                    );
    
                    //update_post_meta($orderid, 'invoiceurl', $invoiceUrl);
                    $chargeid = $charge->id;
                    $redirecturl = home_url('thank-you?product_order_id='.$orderid).'&paymentstatus=success&paymentid='.$chargeid;
                    $data = array('status' => 'success', 'redirect_url' =>$redirecturl, 'message' => 'Payment Confirmed: Transaction completed.');
                    
                    // SendAuctionEmail($current_user_id, $orderid, 'paymentsuccess');
                        
                } else {
                    throw new Exception('Failed to create product order post.');
                }    
                
                
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

add_action('wp_ajax_nopriv_paymentByStripeForProduct', 'paymentByStripeForProduct');
add_action('wp_ajax_paymentByStripeForProduct', 'paymentByStripeForProduct');

function sendProductPaymentInvoice() {
    global $wpdb;
    $data = array();
    extract($_POST);
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    $fname = sanitize_text_field($_POST['first_name']);
    $lname = sanitize_text_field($_POST['last_name']);
    $full_name = $first_name .' '. $last_name;
    $email_address = sanitize_email($_POST['email']);
    $stripe_keys = get_field('stripe_details', 'option');
    $total_amount = passion__decrypt_data($_POST['grandTotal']);
    $currentuserid = get_current_user_id();    
    $phonecode = $_POST['phonecode'];
    $usermessage = $_POST['usermessage'];
    $orderamount = $total_amount;
    // $auctionname = Use Product name from order
    $current_user_id = get_current_user_id();

    $cart_data = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}passions_product_cart WHERE user_id = %d",
            $current_user_id
        ),
        ARRAY_A
    );

    // $product_data = array(); // Array to store product IDs

    foreach ($cart_data as $cart_item) {        
        $product_id = $cart_item['product_id'];
        $product_qty = intval($cart_item['quantity']);
        // Retrieve product price
        $sale_price_check = get_field('product_sale_price', $product_id);
        if ($sale_price_check) {
            $product_price = $sale_price_check;
        } else {
            $product_price = get_field('product_regular_price', $product_id);
        }

        // Store product ID and price as a multidimensional array
        $product_data_all[] = array(
            'product_id' => $product_id,
            'product_qty' => $product_qty,
            'product_price' => $product_price
        );
    }
  
    if(isset($_FILES['attachment'])) {
        $file = $_FILES['attachment'];
        // $post_id = $_POST['orderid'];

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
                
                $fullname = $fname.' '.$lname;
                $post_title = 'Product Order Stripe';
                $post_content = ''; // You can set the post content as needed
                
                $post_data = array(
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_type'     => 'product-order',
                    'post_status'   => 'publish',
                );
                $orderid = wp_insert_post($post_data);
                $attachment_id = wp_insert_attachment($attachment_data, $uploadPath, $orderid);

                $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $uploadPath);
                wp_update_attachment_metadata($attachment_id, $attachment_metadata);
                if(!empty($product_data_all)){
                    
                    if ($orderid) {                
                        $new_post_data = array(
                            'ID'         => $orderid,
                            'post_title' => 'Product Order #' . $orderid,
                        );
                        $productorderinvoiceid = 'INV/'.current_time('Ymd').'/'.$orderid; 
                        wp_update_post($new_post_data);
                        if(isset($usermessage)){
                            update_post_meta($orderid, 'auc_order_user_message', $usermessage);
                        }
                        update_post_meta($orderid, 'auc_order_product_ids', $product_data_all);
                        update_post_meta($orderid, 'product_order_status', 'payment-processing');
                        update_post_meta($orderid, 'product_order_grand_total', $orderamount);
                        update_post_meta($orderid, 'userid', $current_user_id );
                        update_post_meta($orderid, 'order_email', $email_address );
                        update_post_meta($orderid, 'address', $paymentstreet);
                        update_post_meta($orderid, 'city', $paymentcity);
                        update_post_meta($orderid, 'state', $paymentstate);
                        update_post_meta($orderid, 'country', $paymentcountry);
                        update_post_meta($orderid, 'zipcode', $paymentzipcode);
                        update_post_meta($orderid, 'phone', '+'.$phonecode.$_POST['passion_product_phone_number']);
                        update_post_meta( $orderid, 'productorderinvoiceid', $productorderinvoiceid );
                        update_post_meta($orderid, 'attachment_invoiceid', $attachment_id);
                        update_post_meta($orderid, 'order_datetime', current_time( 'timestamp' ));

                        update_user_meta($currentuserid, 'address', $paymentstreet);
                        update_user_meta($currentuserid, 'city', $paymentcity);
                        update_user_meta($currentuserid, 'state', $paymentstate);
                        update_user_meta($currentuserid, 'country', $paymentcountry);
                        update_user_meta($currentuserid, 'zipcode', $paymentzipcode);

                        update_field('payment_mode', 'offline', $orderid);
                        update_field('product_order_status', 'payment-processing', $orderid);
                        // update_user_meta($currentuserid, 'phone', $paymentphone);
                        // update_user_meta($currentuserid, 'phone', );
                        // Handle email sending to admin
                        $admin_email = get_option('admin_email');                
                        if($admin_email) {
                            $admin_subject = 'New Order Received';
                            $admin_message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">You have received a new order. Below are the order details:</p>';    
                            product_order_email_function($admin_email, $admin_subject, $admin_message, $product_data_all, $orderamount);
                        }
                        if($email_address) {
                            $customer_subject = 'Order Confirmation';
                            $customer_message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Thank you, Your order has been received. Below are the order details:</p>';

                            product_order_email_function($email_address, $customer_subject, $customer_message, $product_data_all, $orderamount);
                        }                  

                        foreach ($product_data_all as $product_info) {
                            $product_id = $product_info['product_id'];
                            $product_qty = $product_info['product_qty'];                    
                            $current_stock = get_field('stock', $product_id);                    
                            $new_stock = $current_stock - $product_qty;                    
                            update_field('stock', $new_stock, $product_id);
                        }
                        // Delete rows related to the current user in passions_product_cart table
                        $wpdb->delete(
                            $wpdb->prefix . 'passions_product_cart',
                            array('user_id' => $current_user_id),
                            array('%d')
                        );    
                        //update_post_meta($orderid, 'invoiceurl', $invoiceUrl);            
                        $redirecturl = home_url('thank-you?product_order_id='.$orderid).'&paymentstatus=pending&type=offline';
                        $data = array('status' => 'success', 'redirect_url' =>$redirecturl, 'attach' => $attachment_id, 'message' => 'Payment Confirmed: Transaction completed.');                    
                        // SendAuctionEmail($current_user_id, $orderid, 'paymentsuccess');                        
                    } else {
                        $data = array('status' => 'failed', 'message' => 'Failed to create new order');
                    }            
                
                }
            } else {
                $data = array('status' => 'failed', 'message' => 'Error moving file');
            }
        } else {
            $data = array('status' => 'failed', 'message' => 'Error uploading file');
        }
    } else {
        $data = array('status' => 'failed', 'message' => 'No file data received');
    }
    wp_send_json($data);
    wp_die();
}
add_action('wp_ajax_nopriv_sendProductPaymentInvoice', 'sendProductPaymentInvoice');
add_action('wp_ajax_sendProductPaymentInvoice', 'sendProductPaymentInvoice');

// Add custom column to product-order post list
function custom_product_order_columns($columns) {
    unset($columns['date']);
    $columns['custom_data'] = 'Date&Time';
    return $columns;
}
add_filter('manage_product-order_posts_columns', 'custom_product_order_columns');

// Populate custom column with data for product-order post type
function populate_custom_product_order_columns($column, $post_id) {
    if ($column === 'custom_data') {
        $custom_data = get_post_meta($post_id, 'order_datetime', true);
        if($custom_data) {
            echo date( 'l, j F Y, g:i a', $custom_data );
        }      
    }
}
add_action('manage_product-order_posts_custom_column', 'populate_custom_product_order_columns', 10, 2);



add_action('add_meta_boxes', 'add_product_order_meta_box');
function add_product_order_meta_box() {
    add_meta_box(
        'product_order_details_meta_box',
        'Product Order Details',
        'render_product_order_meta_box',
        'product-order', 
        'normal', // Context: normal, advanced, or side
        'high' // Priority: high, core, default, or low
    );
}

function render_product_order_meta_box($post) {
    // Retrieve the meta value for the specific meta key
    
    $orderdata = get_post_meta($post->ID);
    // echo "<pre>";
        
    //     print_r(get_field('auc_order_product_ids_acf', $post->ID));
    // echo "</pre>";
    // echo "<pre>";
        
    //     print_r(unserialize($orderdata['auc_order_product_ids'][0]));
    // echo "</pre>";
    if($orderdata && !empty($orderdata['auc_order_product_ids'][0])) {
    
        $product_data = unserialize($orderdata['auc_order_product_ids'][0]);
        
        $userid = $orderdata['userid'][0];

        
        if(!empty($userid) ) {
            
            $user_info = get_userdata($userid);
            if ($user_info) {
                $user_details = $user_info->first_name . ' ' . $user_info->last_name . ' (' . $user_info->user_email . ')';
            }else{
                $user_details = '';
            }
        }
        $image_url = home_url().'/wp-content/uploads/2024/02/telegram-cloud-photo-size-5-6258266120985362215-y-1.png';
        $invoice_id = isset($orderdata['productorderinvoiceid'][0]) ? $orderdata['productorderinvoiceid'][0] : '';

        ?>
        <div class="product-order-details" data-logo="<?php echo $image_url ?>" data-invoice="<?php echo $invoice_id ?>">
            <!-- <div class="meta-item">
                <span class="meta-label">Product Name:</span>
                <span class="meta-label">Price</span>
            </div> -->
            <table class="table admin-product-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php   
                        if(!empty($product_data)) {
                            foreach($product_data as $product) {
                                // $product = unserialize($product);                    
                                ?>
                                <tr>
                                    <td> <?php echo get_the_title($product['product_id']); ?></td>
                                    <td> <?php echo $product['product_qty']; ?></td>
                                    <td> $ <?php echo $product['product_price'] * $product['product_qty']; ?></td>
                                </tr>
                                <?php
                            }
                            ?> 
                            <tr>
                                <th colspan="2">Total</th>
                                <th> $ <?php echo $orderdata['product_order_grand_total'][0]; ?></th>
                            </tr>
                            <?php
                        }
                        ?>                
                </tbody> 
            </table>
            <!-- <div class="meta-item">
                <span class="meta-label">Product Name:</span>
                <span class="meta-value"></?php echo esc_html($auctiontitle); ?></span>
            </div> -->
            <?php  if(isset($orderdata['order_datetime'][0])){ ?>
            <div class="meta-item">
                <span class="meta-label">Date&Time:</span>
                <span class="meta-value username-invoice"><?php echo date( 'l, j F Y, g:i a', $orderdata['order_datetime'][0] ); ?></span>
            </div>
            <?php } ?>
            <div class="meta-item">
                <span class="meta-label">User:</span>
                <span class="meta-value username-invoice"><?php echo esc_html($user_details); ?></span>
            </div>
            <?php  if(isset($orderdata['payment_mode'][0])){ ?>
            <div class="meta-item">
                <span class="meta-label">Payment Type:</span>
                <span class="meta-value"><?php echo ucfirst(esc_html($orderdata['payment_mode'][0])); ?></span>
            </div>
            <?php } ?>
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
            <?php  if(isset($orderdata['productorderinvoiceid'][0])){ ?>
            <div class="meta-item">
                <span class="meta-label">Invoice Id:</span>
                <span class="meta-value"><?php echo esc_html($orderdata['productorderinvoiceid'][0]); ?></span>
            </div>
            <?php } ?>
            <?php  if(isset($orderdata['stripe_payment_id'][0])){ ?>
            <div class="meta-item">
                <span class="meta-label">Sripe Payment ID:</span>
                <span class="meta-value"><?php echo esc_html($orderdata['stripe_payment_id'][0]); ?></span>
            </div>
            <?php } ?>
            <div class="meta-item">
                <span class="meta-label">Download Invoice:</span>
                <span class="meta-value"><a href="javascript:void(0)" id="downloadInvoiceBtn" class="btn btn-green">Download Invoice</a></span>
            </div>
            <div class="mask"></div>
            <div class="orderinvoices-img">
                    <?php 
                    if(isset($orderdata['attachment_invoiceid'][0])) {
                        $attachment_id = $orderdata['attachment_invoiceid'][0];
                        $image_url = wp_get_attachment_url($attachment_id);
                        if($image_url) {
                            echo '<img src="'. $image_url .'" class="user_payment_offline_reciet" />';
                        }    
                    }
                ?>
            </div>
        </div>
        <style>
            .product-order-details {
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
                width: 35%;
                font-weight: 600;
            }
            .meta-value {
                margin-left: 10px;
                font-size: 15px;
                display: inline-block;
                width: 63%;
            }
            .admin-product-table {
                width: 100%;
                margin-bottom: 20px;
            }
            .admin-product-table {
                
                border-collapse: collapse;
                width: 100%;
            }

            .admin-product-table td, .admin-product-table th {
                border: 1px solid #ddd;
                padding: 8px;
            }

            .admin-product-table tr:nth-child(even){background-color: #f2f2f2;}

            .admin-product-table tr:hover {background-color: #ddd;}

            .admin-product-table th {
            
                text-align: left;
            
            }
        </style>
        <script>

            jQuery(document).ready(function ($) {
            $('.orderinvoices-img').on('click', function(e){
                e.preventDefault();

                var imgLink = $(this).children('img').attr('src');

                $('.mask').html('<div class="img-box"><img src="'+ imgLink +'"><a class="close">&times;</a>');

                $('.mask').addClass('is-visible fadein').on('animationend', function(){
                  $(this).removeClass('fadein is-visible').addClass('is-visible');
                  $('body').addClass('open-img');
                });

                $('.close').on('click', function(){
                  $(this).parents('.mask').addClass('fadeout').on('animationend', function(){
                    $(this).removeClass('fadeout is-visible')
                  $('body').removeClass('open-img');
                  });
                });

          });
            function getImageDataUrl(url, callback) {
                var xhr = new XMLHttpRequest();
                xhr.onload = function() {
                    var reader = new FileReader();
                    reader.onloadend = function() {
                        callback(reader.result);
                    }
                    reader.readAsDataURL(xhr.response);
                };
                xhr.open('GET', url);
                xhr.responseType = 'blob';
                xhr.send();
            }
        function downloadInvoiceAsPDF() {
            
        // Define the content before and after the order details
        var logo = $('.product-order-details').data('logo');
        var invoice_id = $('.product-order-details').data('invoice');
        var first_name = $('.username-invoice').html();
        getImageDataUrl(logo, function(dataUrl) {
            var contentBefore = [
                {
                    columns: [
                        // Left side: Website logo
                        { image: dataUrl, fit: [100, 100], alignment: 'left' },
                        // Right side: Invoice text
                        { text: 'Invoice', alignment: 'right', width: '*' }
                    ],
                    margin: [0, 0, 0, 10] // Adjust margins as needed
                },       
                // Additional details div row
                {
                columns: [
                    // Left side: Bill To
                    { text: 'Bill To: '+first_name, alignment: 'left' },
                    // Right side: Payment Method
                    { text: 'Payment Method: Stripe', alignment: 'right' }
                ],
                margin: [0, 0, 0, 10] 
            },
            {
                columns: [
                    // Left side: Bill To
                    { text: '', alignment: 'left' },
                    // Right side: Payment Method
                    { text: 'Invoice No.: '+invoice_id, alignment: 'right' }
                ],
                margin: [0, 0, 0, 10] 
            }
                    
            ];
            // Define padding for table cells
            var styles = {
                td: {
                    padding: 5 // Adjust padding as needed
                }
            };
                        // var contentAfter = 'Additional Content After Order Details';
    
            var tableData = [];
            $('.product-order-details tbody tr').each(function() {
                var rowData = [];
                var columnIndex = 0;
                $(this).find('td, th').each(function() {
                    var colspan = parseInt($(this).attr('colspan')) || 1;
                    var cellText = $(this).text().trim();
                    for (var i = 0; i < colspan; i++) {
                        if (i === 0) {
                            rowData[columnIndex++] = cellText; // Insert cell text only for the first column of the span
                        } else {
                            rowData[columnIndex++] = ""; // For subsequent columns of the span, insert empty string
                        }
                    }
                });
                tableData.push(rowData);
            });

            console.log('Table Data:', tableData);

            // Extract the data from tfoot
            var tfootData = [];
            $('.product-order-details tfoot tr').each(function() {
                var rowData = [];
                var columnIndex = 0;
                $(this).find('td, th').each(function() {
                    var colspan = parseInt($(this).attr('colspan')) || 1;
                    var cellText = $(this).text().trim();
                    for (var i = 0; i < colspan; i++) {
                        if (i === 0) {
                            rowData[columnIndex++] = cellText; // Insert cell text only for the first column of the span
                        } else {
                            rowData[columnIndex++] = ""; // For subsequent columns of the span, insert empty string
                        }
                    }
                });
                tfootData.push(rowData);
            });

            // Concatenate tbody and tfoot data
            var tableContent = tableData.concat(tfootData);
    
            // Define the order details content dynamically
            var orderDetailsContent = [
                { text: 'Order details', style: 'header' },
                {
                    table: {
                        headerRows: 1,
                        widths: ['*', '*', '*'],
                        body: [
                            ['Item', 'Qty', 'Total']
                        ].concat(tableContent) // Add the table data
                    }
                }
            ];
    
            // Define the document definition
            var docDefinition = {
                content: [
                    contentBefore,
                    orderDetailsContent
                    // contentAfter
                ],
                styles: {
                    header: {
                        fontSize: 18,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    },
                    tableStyle: {
                        td: {
                            padding: 5 // Padding for table cells
                        }
                    }
                }
            };
    
            // Generate PDF
            pdfMake.createPdf(docDefinition).download('invoice.pdf');
        });
    }
    
    // Add click event listener to the button using jQuery
        $('#downloadInvoiceBtn').on('click', downloadInvoiceAsPDF);
    
        });

            </script>
        <?php
    }

    // Display the meta value in the meta box
    //echo '<p>' . esc_html($auction_details) . '</p>';
}