<?php get_header(); 
$auctionid = get_the_ID();
$user_id = get_current_user_id();
global $wpdb;
$table_name = $wpdb->prefix . 'bidhistory';
$query = $wpdb->prepare("
    SELECT *
    FROM $table_name
    WHERE auctionid = %d
    ORDER BY id DESC", $auctionid);

$bidinghistory = $wpdb->get_results($query, ARRAY_A);

// echo '<pre>';
// print_r($bidinghistory);
// die;

/*if($bidinghistory){
    $bidcount = count($bidinghistory);
}else{
    $bidcount = 0;
}*/
$bidcount = getTotalBidcount($auctionid);
$letestdata = getLetestBidData($auctionid);
if($letestdata){
    $letestbid = $letestdata->bidamount; 
}else{
    $letestbid = get_field('base_price');
}

if(empty($letestbid)){
    $letestbid = 0;
}

$orderdata = getauctionOrderId($auctionid);
if($orderdata){
    $orderid = $orderdata->id;
}else{
    $orderid = 0;
}


?>
 <!-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
        var pusher = new Pusher('e4bbf991aaac16fd100c', {
        cluster: 'ap2'
        });
        console.log('pusher ', pusher);
        var channel = pusher.subscribe('auction');
        console.log('channel ', channel);
        channel.bind('newbid', function(data) {
        alert(JSON.stringify(data));
            //console.log('DATA UPDATE');
        });
    </script> -->
<!--  <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('e4bbf991aaac16fd100c', {
      cluster: 'ap2'
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
      alert(JSON.stringify(data));
    });
  </script> -->

<div class="breadcrump">
    <div class="customcontainer">
        <?php echo custom_breadcrumb(); ?>
    </div>
</div>
<section class="auction-detail woo_single_product_cont">
    <div class="customcontainer">
        <div class="row woo-product-messages"></div>
        <div class="row thumbnail-row">
            <div class="col-12 col-lg-6">
                <?php $image = get_the_post_thumbnail_url();
                $gallery_images = get_field('gallery_images'); 
                if($gallery_images) { ?>
                <div class="thumbnail-slider">
                    <div class="slider-galeria-thumbs"><?php 
                        if($image) { ?>
                        <div>
                            <div class="thumbblock">
                                <img src="<?php echo $image; ?>" class="thumb-img">
                            </div>       
                        </div><?php
                        } foreach( $gallery_images as $gimage ): ?>
                        <div>
                            <div class="thumbblock">
                                <img src="<?php echo esc_url($gimage['url']); ?>" alt="<?php echo esc_attr($gimage['alt']); ?>" class="thumb-img">
                            </div>
                        </div><?php 
                    endforeach; ?>  
                    </div>
                    <div class="slider-galeria"><?php 
                        if($image) { ?>
                        <div>
                            <div class="slidermain-img">
                                <img src="<?php echo $image; ?>" class="main-img">
                            </div>
                        </div><?php
                        } foreach( $gallery_images as $gimage ): ?>
                        <div>
                            <div class="slidermain-img">
                                <img src="<?php echo esc_url($gimage['url']); ?>" alt="<?php echo esc_attr($gimage['alt']); ?>" class="thumb-img">
                            </div>
                        </div><?php 
                    endforeach; ?>   
                    </div>
                </div><?php 
            }  else { ?>
            <div class="thumbnail-slider nogallery-img">
                <div class="slidermain-img">
                    <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="full-img">
                </div>
            </div><?php } ?>
            </div>
            <div class="col-12 col-lg-6">
                <div class="auction-product-detail">
                    <h2 class="productdetail-title"><?php the_title(); ?></h2>
                    <div class="productdetail-content"><?php the_excerpt(); ?></div>
                    <div class="product-info">
                        <?php
                            $regular_price = get_field('product_regular_price');
                            $sale_price = get_field('product_sale_price');
                            $stock = intval(get_field('stock'));
                            
                            $sku_number = get_field('sku_number');
                            $categories = get_the_terms(get_the_ID(), 'product-category');


                            if($sale_price) { ?>
                                <div class="currentbid">
                                    Price
                                    <div class="price regular-price disable-price-reg">$<span class="livebidprice"><?php echo number_format($regular_price); ?></span></div>
                                    <div class="price sale-price">$<span class="livebidprice"><?php echo number_format($sale_price); ?></span></div>
                                </div>
                                <?php 
                            } else if($regular_price) { ?>
                                <div class="currentbid">
                                    Price
                                    <div class="price regular-price">$<span class="livebidprice"><?php echo number_format($regular_price); ?></span></div>
                                </div><?php 
                                
                            }
                            if($stock != ''){ ?>
                                <div class="desc_row"><?php
                                    if($stock > 0) { ?>
                                        <span class="stock-label label">Stock: </span><span class="instock desc_value"><?=$stock?></span><?php
                                    } else if($stock <= 0) { ?>
                                        <span class="stock-label label outofstock">Stock: </span><span class="outofstock desc_value">Out of Stock</span><?php
                                    } ?>
                                </div><?php
                            }
                            if($sku_number) { ?>
                                <div class="desc_row">
                                    <span class="sku_number-label label">SKU Number: </span><span class="sku_number desc_value"><?=$sku_number?></span>
                                </div><?php
                            }
                            if ($categories && !is_wp_error($categories)) {
                                ?>
                                <div class="desc_row"><?php
                                echo '<span class="category-label label">Category: </span>';
                                echo '<span class="category desc_value">';

                                    $category_names = array();
                                    foreach ($categories as $category) {
                                        $category_names[] = '<a href="' . home_url( '/product/') . '?category='.$category->term_id.'">' . esc_html($category->name) . '</a>';
                                        
                                    }
                                    echo implode(', ', $category_names);
                                echo '</span>'; ?>
                            </div><?php 
                            }
                            if ($stock && $stock > 0) { ?>
                                <div class="desc_row quanity-card">
                                    <label for="quantity">Quantity:</label>
                                    <div class="quantity-wrapper-cont">
                                        <div class="quantity-wrapper">
                                            <button type="button" class="decrease">-</button>
                                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $stock; ?>">
                                            <button type="button" class="increase">+</button>
                                        </div>
                                    
                                    <a class="add-to-cart btn btn-green addto_cart_passion" id="add_cart_passion" data-user="<?= passion__encrypt_data(get_current_user_id()); ?>" data-product="<?= passion__encrypt_data(get_the_ID()); ?>" href="javascript:void(0);">Add to Cart</a>

                                    </div>
                                    <span class="loader-single-product"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/loader.gif" /></span>
                                </div>
                            <?php }?>                        
                    </div>
                </div>
            </div>
        </div>
        <div class="productdetail-overview">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">DESCRIPTION</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <?php if(get_the_content()) { ?>                    
                        <div class="itemdetails-main">
                            <?php the_content(); ?>
                        </div>                    
                    <?php } else {
                        echo 'No description available!';
                    } ?>

                </div>
                <!--  -->
            </div>
        </div>
    </div>
</section>


<input type="hidden" id="daynum" value="<?php echo $days; ?>">
<input type="hidden" id="hournum" value="<?php echo $hours; ?>">
<input type="hidden" id="minutesnum" value="<?php echo $minutes; ?>">
<input type="hidden" id="secondsnum" value="<?php echo $seconds+1; ?>">
<input type="hidden" id="showhandtimenum" value="<?php echo $showhandtimebefore; ?>">
<script>

function updateCountdown() {
    var initialDays = parseInt(jQuery('#daynum').val());
    var initialHours = parseInt(jQuery('#hournum').val());
    var initialMinutes = parseInt(jQuery('#minutesnum').val());
    var initialSeconds = parseInt(jQuery('#secondsnum').val()); 
    var showhandtimenum = parseInt(jQuery('#showhandtimenum').val()); 
    
    var daysElement = document.getElementById("daysp");
    var hoursElement = document.getElementById("hoursp");
    var minutesElement = document.getElementById("minutesp");
    var secondsElement = document.getElementById("secondsp");

    var daysElementpopup = document.getElementById("dayspop");
    var hoursElementpopup = document.getElementById("hourspop");
    var minutesElementpopup = document.getElementById("minutespop");
    var secondsElementpopup = document.getElementById("secondspop");

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

    // Update DOM elements with new values
    daysElement.children[0].innerText = Math.floor(initialDays / 10);
    daysElement.children[1].innerText = initialDays % 10;
    hoursElement.children[0].innerText = Math.floor(initialHours / 10);
    hoursElement.children[1].innerText = initialHours % 10;
    minutesElement.children[0].innerText = Math.floor(initialMinutes / 10);
    minutesElement.children[1].innerText = initialMinutes % 10;
    secondsElement.children[0].innerText = Math.floor(initialSeconds / 10);
    secondsElement.children[1].innerText = initialSeconds % 10;

    daysElementpopup.children[0].innerText = Math.floor(initialDays / 10);
    daysElementpopup.children[1].innerText = initialDays % 10;
    hoursElementpopup.children[0].innerText = Math.floor(initialHours / 10);
    hoursElementpopup.children[1].innerText = initialHours % 10;
    minutesElementpopup.children[0].innerText = Math.floor(initialMinutes / 10);
    minutesElementpopup.children[1].innerText = initialMinutes % 10;
    secondsElementpopup.children[0].innerText = Math.floor(initialSeconds / 10);
    secondsElementpopup.children[1].innerText = initialSeconds % 10;
    jQuery('#secondsnum').val(initialSeconds);
    jQuery('#minutesnum').val(initialMinutes);
    jQuery('#hournum').val(initialHours);
    jQuery('#daynum').val(initialDays);

    jQuery('.cdpag, .cdpup').removeClass('auctionalert');

    var totalmin = initialDays * 24 * 60 + initialHours * 60 + initialMinutes+1;
    if(showhandtimenum == totalmin){
        jQuery('#showbid').removeAttr('disabled');
        jQuery('#showbidlabel').removeClass('disabled');
        jQuery('.cdpag, .cdpup').removeClass('auctionalert');
    } else if(totalmin <=  1440) {
        jQuery('.cdpag').addClass('auctionalert');
        jQuery('.cdpup').addClass('auctionalert');
    }
}

updateCountdown();
var countdownInterval = setInterval(updateCountdown, 1000);


</script>

<?php get_footer(); ?>