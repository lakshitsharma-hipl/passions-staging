<?php 
// Add custom class to body tag for product post type
function add_custom_class_to_product_body( $classes ) {
    if ( is_singular('product') || is_post_type_archive('product') ) {
        $classes[] = 'passion-product-body';
    }
    return $classes;
}
add_filter( 'body_class', 'add_custom_class_to_product_body' );

// Product cart table
function create_custom_cart_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'passions_product_cart';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        product_id mediumint(9) NOT NULL,
        quantity mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
add_action( 'init', 'create_custom_cart_table' );

/* product filter */

function pagination_load_products(){

	if(isset($_POST['page'])){

        $page = sanitize_text_field($_POST['page']);
        $cur_page = $page;
        $page -= 1;
        $per_page = get_field('number_of_posts', 'option') ? get_field('number_of_posts', 'option') : 3;
        $previous_btn = true;
        $next_btn = true;
        $first_btn = true;
        $last_btn = true;
        $start = $page * $per_page;
         
        $meta_query = array();
    	$tax_query = array();

        $type = $_POST['type'];
        $category = $_POST['category'];
        $minprice = $_POST['minprice'];
    	$maxprice = $_POST['maxprice'];        

        $tax_query = array('relation' => 'AND');
        $meta_query = array('relation' => 'AND');   

        $current_time = current_time('timestamp');

        if ($category) {
            $tax_query[] = array(
                'taxonomy' => 'product-category',
                'field'    => 'id',
                'terms'    => $category,
                'operator' => 'IN',
            );
        }
        if ($minprice && $maxprice) {
            $meta_query[] = array(
                'key'     => 'regular_price',
                'value'   => array($minprice, $maxprice),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            );
        } elseif ($minprice) {
            $meta_query[] = array(
                'key'     => 'regular_price',
                'value'   => $minprice,
                'compare' => '>=',
                'type'    => 'NUMERIC'
            );
        } elseif ($maxprice) {
            $meta_query[] = array(
                'key'     => 'regular_price',
                'value'   => $maxprice,
                'compare' => '<=',
                'type'    => 'NUMERIC'
            );
        }
        


    	

        if($type== 'all' && empty($category) && empty($minprice) && empty($maxprice) && is_user_logged_in()) {
            $post = new WP_Query(
                array(
                    'post_type'         => 'product',
                    'post_status'       => 'publish',
                    'posts_per_page'    => $per_page,
                    'offset'            => $start,
                    'orderby'           => 'meta_value',
                    'order'             => 'ASC',
                )
            );
            $count = new WP_Query(
                array(
                    'post_type'         => 'product',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                )
            );
        } else {
            $post = new WP_Query(
                array(
                    'post_type'         => 'product',
                    'post_status'       => 'publish',
                    'posts_per_page'    => $per_page,
                    'offset'            => $start,
                    // 's'                 => $search,
                    'orderby'           => 'meta_value',
                    'order'             => 'ASC',
                    'meta_query'        => $meta_query,
                    'tax_query'         => $tax_query,               
                )
            );
            $count = new WP_Query(
                array(
                    'post_type'         => 'product',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                    // 's'                 => $search,
                    'meta_query'        => $meta_query,
                    'tax_query'         => $tax_query,
                )
            );
        }
        $open_posts = array();
        $closed_posts = array();

        $count = $count->post_count;
        if ( $post->have_posts() ) {
            while ( $post->have_posts() ) {
                $post->the_post();
                $postid = get_the_ID(); 
                $image = get_the_post_thumbnail_url($postid); 
                
				$regular_price = get_field('product_regular_price', $postid);
				$sale_price = get_field('product_sale_price', $postid);
                $stock = get_field('stock', $postid);         
                
                
                // Store product data in separate arrays based on stock status
                if ( $stock > 0 ) {
                    $open_posts[] = array(
                        'post_id' => $postid,
                        'image' => $image,
                        'title' => get_the_title(),
                        'regular_price' => $regular_price,
                        'sale_price' => $sale_price,
                        'stock' => $stock
                    );
                } else {
                    $closed_posts[] = array(
                        'post_id' => $postid,
                        'image' => $image,
                        'title' => get_the_title(),
                        'regular_price' => $regular_price,
                        'sale_price' => $sale_price,
                        'stock' => $stock
                    );
                }
            }
                  
            $all_posts = array_merge($open_posts, $closed_posts);
            usort($all_posts, 'custom_sort');

            foreach ($all_posts as $post_data) {
                
                
                $regular_price_p = $post_data['regular_price'];                
                $sale_price_p = $post_data['sale_price'];       
                $stock_count = $post_data['stock'];
                $stock_label = '';
                $stock_class = '';
                if($stock_count > 0) {
                    $stock_label = 'In Stock';
                    $stock_class = 'instock';
                } else if($stock_count <= 0) {
                    $stock_label = 'Out of stock';
                    $stock_class = 'outofstock';
                }
                ?>
                <div id="product-<?php echo $type.'-'.$post_data['post_id'];?>" class="col-12 col-md-6 col-lg-4">
                    <a href="<?php echo get_permalink($post_data['post_id']); ?>" class="listitem">
                        <div class="productimg">
                            <?php if($stock_label){
                                echo '<span class="aulabel '.$stock_class.'">'.$stock_label.'</span>';
                            } ?>
                            <img class="img-fluid" src="<?php if($post_data['image']) { echo $post_data['image']; } else { echo get_template_directory_uri().'/images/default.png';} ?>" />
                        </div>
                        <div class="product-desc">
                            <div class="product-title"><?php echo $post_data['title']; ?></div>
                            <div class="product-price-bid">
                                <p class="highest-bid">Price</p>
                                <?php if($sale_price_p) { ?>
                                    <div class="price regular-price disable-price-reg">$<span class="livebidprice"><?php echo number_format($regular_price_p); ?></span></div>
                                    <div class="price sale-price">$<span class="livebidprice"><?php echo number_format($sale_price_p); ?></span></div>
                                    <?php 
                                } else if($regular_price_p) { ?>
                                    <div class="price regular-price">$<span class="livebidprice"><?php echo number_format($regular_price_p); ?></span></div><?php 
                                } ?>                              
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
                
        } else { ?>
            <div class="noposts">Sorry, No products found.</div><?php
        }

        // pagination
        $no_of_paginations = ceil($count / $per_page);
        if ($cur_page >= 9) {
            $start_loop = $cur_page - 3;
            if ($no_of_paginations > $cur_page + 3)
                $end_loop = $cur_page + 3;
            else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                $start_loop = $no_of_paginations - 6;
                $end_loop = $no_of_paginations;
            } else {
                $end_loop = $no_of_paginations;
            }
        } else {
            $start_loop = 1;
            if ($no_of_paginations > 7)
                $end_loop = 7;
            else
                $end_loop = $no_of_paginations;
        } 
        if($no_of_paginations > 1 ) : ?>
            <div class='paginationpro'>
                <ul class="pagination justify-content-center">
                    <?php
                    if ($previous_btn && $cur_page > 1) {
                        $pre = $cur_page - 1; ?>
                        <li p='<?php echo $pre; ?>' class='active' onclick="loadmoreproducts(this);">
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 1L1 7L7 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </li><?php
                    } else if ($previous_btn) { ?>
                        <li class='inactive'>
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 1L1 7L7 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </li><?php
                    }

                    for ($i = $start_loop; $i <= $end_loop; $i++) {
                        if ($cur_page == $i){ ?>
                            <li p='<?php echo $i; ?>' class = 'selected' ><?php echo $i; ?></li><?php
                        } else { ?>
                            <li p='<?php echo $i; ?>' class='active' onclick="loadmoreproducts(this);"><?php echo $i; ?></li><?php
                        }
                    }

                    if ($next_btn && $cur_page < $no_of_paginations) {
                        $nex = $cur_page + 1; ?>
                        <li p='<?php echo $nex; ?>' class='active' onclick="loadmoreproducts(this);">
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L1 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </li><?php
                    } else if ($next_btn) { ?>
                        <li class='inactive'>
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L1 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </li><?php 
                    } ?>
                </ul>
            </div><?php
        endif;
    }
    exit();
}
add_action( 'wp_ajax_pagination_load_products', 'pagination_load_products' );
add_action( 'wp_ajax_nopriv_pagination_load_products', 'pagination_load_products' ); 

// Product add to cart 
function pass_product_add_to_cart() {
    $response = array();    
    if (!empty($_POST)) {        
        $product_id = isset($_POST['product_id']) ? passion__decrypt_data($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;        
        $stock = intval(get_field('stock', $product_id)); 

        // Check if there is an existing entry for the same product_id and user_id
        global $wpdb;
        $table_name = $wpdb->prefix . 'passions_product_cart';
        $user_id = isset($_POST['userid']) ? passion__decrypt_data($_POST['userid']) : 0;
        $existing_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE product_id = %d AND user_id = %d",
                $product_id,
                $user_id
            )
        );

        if ($existing_row) {            
            $total_quantity = $existing_row->quantity + $quantity;
                        
            if ($total_quantity <= $stock) {            
                $wpdb->update(
                    $table_name,
                    array('quantity' => $total_quantity),
                    array('id' => $existing_row->id),
                    array('%d'),
                    array('%d')
                );
                // Get total quantity in cart for the user
                $cart_items = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM $table_name WHERE user_id = %d",
                        $user_id
                    )
                );									
                $cart_quantity = 0;								
                foreach ($cart_items as $cart_item) {
                    $cart_quantity += $cart_item->quantity;
                }

                if ($quantity > 1) {
                    $response = array(
                        'status' => 'success',
                        'message' => '"'.get_the_title($product_id).'" &#215; '.$quantity.' has been added to your cart.',
                        'url' => site_url('/cart/'),
                        'cart_quantity' => $cart_quantity
                    );
                } else {
                    $response = array(
                        'status' => 'success',
                        'message' => '"'.get_the_title($product_id).'" has been added to your cart.',
                        'url' => site_url('/cart/'),
                        'cart_quantity' => $cart_quantity
                    );
                }
            } else {
                // If it exceeds stock, send an error response
                $response = array(
                    'status' => 'error',
                    'url' => site_url('/cart/'),
                    'message' => "You cannot add that amount to the cart - we have $stock in stock and you already have {$existing_row->quantity} in your cart."
                );
            }

        } else {
            
            if ($stock >= $quantity) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'user_id' => intval($user_id),
                        'product_id' => intval($product_id),
                        'quantity' => intval($quantity)
                    ),
                    array(
                        '%d',
                        '%d',
                        '%d'
                    )
                );            
                
                $cart_items = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM $table_name WHERE user_id = %d",
                        $user_id
                    )
                );									
                $cart_quantity = 0;								
                foreach ($cart_items as $cart_item) {
                    $cart_quantity += $cart_item->quantity;
                }

                if ($quantity > 1) {
                    $response = array(
                        'status' => 'success',
                        'message' => '"'.get_the_title($product_id).'" &#215; '.$quantity.' has been added to your cart.',
                        'url' => site_url('/cart/'),
                        'cart_quantity' => $cart_quantity
                    );
                } else {
                    $response = array(
                        'status' => 'success',
                        'message' => '"'.get_the_title($product_id).'" has been added to your cart.',
                        'url' => site_url('/cart/'),
                        'cart_quantity' => $cart_quantity
                    );
    
                }
            } else {
                $response = array(
                    'status' => 'error',
                    'url' => site_url('/cart/'),
                    'message' => "You cannot add that amount to the cart - we have only $stock in stock."
                );
            }
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Something went wrong!');
    }
    wp_send_json($response);
    
    wp_die();
}
add_action( 'wp_ajax_pass_product_add_to_cart', 'pass_product_add_to_cart' );
add_action( 'wp_ajax_nopriv_pass_product_add_to_cart', 'pass_product_add_to_cart' );



function pass_product_delete_from_cart() {
    $response = array();
    // Check if item_id is provided in the POST data
    if (isset($_POST['item_id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'passions_product_cart';
        $item_id = intval(passion__decrypt_data($_POST['item_id']));
        
        $deleted = $wpdb->delete(
            $table_name,
            array('id' => $item_id),
            array('%d')
        );

        if ($deleted !== false) {  
            $response = array(
                'status' => 'success',
                'message' => 'Removed successfully!'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Failed to delete item from cart.'
            );
        }
    } else {        
        $response = array(
            'status' => 'error',
            'message' => 'Product ID is missing!'
        );
    }    
    wp_send_json( $response);
    wp_die();
}
add_action( 'wp_ajax_pass_product_delete_from_cart', 'pass_product_delete_from_cart' );
add_action( 'wp_ajax_nopriv_pass_product_delete_from_cart', 'pass_product_delete_from_cart' );

add_action('wp_ajax_auc_product_quantity_update', 'auc_product_quantity_update');
add_action('wp_ajax_nopriv_auc_product_quantity_update', 'auc_product_quantity_update'); // Allow AJAX for non-logged-in users

function auc_product_quantity_update() {

    $encrypted_product_id = $_POST['productId'];
    $encrypted_item_id = $_POST['itemId'];
    $new_quantity = intval($_POST['quantity']);
    $type = $_POST['type'];
    
    $product_id = passion__decrypt_data( $encrypted_product_id );
    $item_id = passion__decrypt_data( $encrypted_item_id );

    // Initialize response array
    $response = array();

    
    $product = get_post( $product_id );
    if ( ! $product ) {
        $response['status'] = 'failed';
        $response['message'] = 'Product not found';
        wp_send_json( $response );
    }
    $available_stock = intval(get_field( 'stock', $product_id ));
    if ( $available_stock <= 0 ) {
        $response['status'] = 'failed';
        $response['message'] = 'Product stock not available';
        wp_send_json( $response );
    }
    
    if ( $new_quantity > $available_stock ) {
        $response['status'] = 'failed';
        $response['message'] = 'Insufficient stock';
        wp_send_json( $response );
    } else {
        // Update the cart item quantity in the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'passions_product_cart';
        $updated = $wpdb->update(
            $table_name,
            array( 'quantity' => $new_quantity ),
            array( 'id' => $item_id ),
            array( '%d' ),
            array( '%d' )
        );

        // Check if update was successful
        if ( $updated ) {
            $response['status'] = 'success';
            $response['new_quantity'] = $new_quantity;
            $response['message'] = 'Cart updated successfully';

        } else {
            $response['status'] = 'failed';
            $response['message'] = 'Failed to update cart';
        }
        // Send JSON response
        wp_send_json( $response );

    }

}
// Hook into the pre_update_field filter
add_filter('acf/update_value/name=product_order_status', 'check_product_order_status_change', 10, 3);

function check_product_order_status_change($value, $post_id, $field) {
    if (get_post_type($post_id) === 'product-order') {
        $old_value = get_field('product_order_status', $post_id);

        if ($old_value != $value && ($value == 'completed' || $value == 'cancelled')) {
            $user_email = get_post_meta( $post_id, 'order_email', true );
            $user_id = get_post_meta( $post_id, 'userid', true );

            if($value == 'completed') {
                $subject = 'Your order has been completed';  
                $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Your order has been successfully completed. Thank you for shopping with us!</p>';
            } else if($value == 'cancelled') {
                $subject = 'Your order has been cancelled';  
                $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Your order has been cancelled. We apologize for any inconvenience caused.</p>';
            }
            $user_mail_sent = passionAuctionEmail($user_id, $subject, $message);
        }
    }
    return $value;
}