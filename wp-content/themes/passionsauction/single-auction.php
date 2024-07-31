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


if($user_id){
    $fullname = get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true);
    $address = get_user_meta($user_id, 'address', true);
    $phone = get_user_meta($user_id, 'user_phone_number', true);
}else{
    $fullname = '';
    $address = '';
    $phone = '';
}

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

$bidverify = array('status' => '', 'msg' =>''); 
$auction_bidtype = get_field('auction_bidtype');
$bidusers_varification = get_field('bidusers_varification');

if(is_user_logged_in()) {
    if(!empty($auction_bidtype) && $auction_bidtype == 'registerbid'){
        $bidregusers = get_post_meta($auctionid, 'bidregusers', true);
        $userPhoneNumber = get_user_meta($user_id, 'user_phone_number', true);
        $user_country = get_user_meta($user_id, 'country', true);
        $state = get_user_meta($user_id, 'state', true);
        $city = get_user_meta($user_id, 'city', true);
        $address = get_user_meta($user_id, 'address', true);
        $zipcode = get_user_meta($user_id, 'zipcode', true);
        $get_userdata = get_userdata($user_id);
        $first_name = $get_userdata->first_name;
        $last_name = $get_userdata->last_name;
        if(!isset($bidregusers[$user_id])){
            $bidverify = array('status' => 'failed', 'msg' =>'To participate in this auction, please register by filling out the form below.', 'verifyform' =>'show', 'verification' =>$bidusers_varification);
        }else{
            if($bidregusers[$user_id]['status'] == 'verified'){
                $bidverify = array('status' => 'success', 'msg' =>'', 'verifyform' =>'hide');
            }elseif($bidregusers[$user_id]['status'] == 'rejected'){
                $bidverify = array('status' => 'failed', 'msg' =>'Your previous submission was rejected by the administrator. Please complete the form below again with proper information.', 'verifyform' =>'show');
            }else{
                $bidverify = array('status' => 'failed', 'msg' =>'Please wait for admin approval. We will inform you once you are verified.', 'verifyform' =>'hide');
            }
        }
    } else {
        $bidverify = array('status' => 'success', 'msg' =>'', 'verifyform' =>'hide');
    }
} else {
    $bidverify = array('status' => 'success', 'msg' =>'', 'verifyform' =>'hide');
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
<section class="auction-detail">
    <div class="customcontainer">
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
                    <div class="lotnum">Lot# : <?php echo $auctionid; ?></div>
                    <h2 class="productdetail-title"><?php the_title(); ?></h2>
                    <div class="productdetail-content"><?php the_excerpt(); ?></div>
                    <script>
                        function copyURL() {
                            var copyText = document.createElement("textarea");
                            copyText.value = "<?php the_permalink(); ?>";
                            document.body.appendChild(copyText);
                            copyText.select();
                            document.execCommand("copy");
                            document.body.removeChild(copyText);
                            
                            var copyMessage = document.getElementById("copyMessage");
                            copyMessage.innerHTML = "URL copied to clipboard";
                            
                            setTimeout(function() {
                                copyMessage.innerHTML = "";
                            }, 3000);
                        }
                    </script>  
                    <?php 
                    $endintime_st = get_field('end_date');
                    $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endintime_st)));
                    $endin_timestamp = strtotime($endin_time_formatted);

                    $startdate_st = get_field('start_date');
                    $start_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startdate_st)));
                    $start_timestamp = strtotime($start_time_formatted);

                    $current_timestamp = current_time('timestamp', true);
                    if($current_timestamp > $endin_timestamp){
                        ?>
                        <div class="closed">
                        <div class="currentbid">Auction closed: <span class="price">$<span class="livebidprice"><?php echo number_format($letestbid); ?></span></span>
                            <?php if($bidcount > 0){
                                echo '<span class="bids">(<span class="bidcount">'.$bidcount.'</span> bids)</span>';
                            }?>
                        </div>
                    <?php
                    }else{ ?>
                    <div class="currentbid">Current bid:<span class="price">$<span class="livebidprice"><?php echo number_format($letestbid); ?></span></span>
                        <?php if($bidcount > 0){
                            echo '<span class="bids">(<span class="bidcount">'.$bidcount.'</span> bids)</span>';
                        }?>
                    </div>

                    <?php } ?>
                    <?php            
                    if (($start_timestamp <= $current_timestamp) && ($endin_timestamp > $current_timestamp) && empty($orderid)) { ?>
                        <div class="deal-counter test">
                            <div class="endin">Ends in:</div>
                            <?php 
                                $endin_time = get_field('end_date');
                                if($endin_time) {
                                    $endintime = DateTime::createFromFormat('d/m/Y h:i a', $endin_time);
                                    $endcnttime = $endintime->format('M d, Y H:i:s');
                                    echo '<input type="hidden" id="endin_time" value="'.$endcnttime.'">';
                                    $current_time = new DateTime(current_time('mysql'));
                                    $current_time_formatted = $current_time->format('M d, Y H:i:s');

                                    echo '<input type="hidden" class="sd" id="timenowis" value="'.$current_time_formatted.'">';
                                  
                                }
                            ?>
                            <div id="countdown" class="cdpag">
                                <?php
                                $difference = $endin_timestamp - $current_timestamp;

                                $days = floor($difference / (60 * 60 * 24));
                                $hours = floor(($difference % (60 * 60 * 24)) / (60 * 60));
                                $minutes = floor(($difference % (60 * 60)) / 60);
                                $seconds = $difference % 60;
                                ?>
                                <ul>
                                    <li>
                                        <div id="daysp">
                                            <span><?php echo str_pad($days, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($days, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        DAYS
                                    </li>
                                    <li>
                                        <div id="hoursp">
                                            <span><?php echo str_pad($hours, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($hours, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        HOURS
                                    </li>
                                    <li>
                                        <div id="minutesp">
                                            <span><?php echo str_pad($minutes, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($minutes, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        MINUTES
                                    </li>
                                    <li>
                                        <div id="secondsp">
                                            <span><?php echo str_pad($seconds, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($seconds, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        SECONDS
                                    </li>
                                </ul>

                            </div>
                        </div>
                        <div class="product-btngroup">
                            <div class="row">
                                 <div class="share-buttons">
                                        <span>Share </span>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" rel="nofollow" class="share-facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                        <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank" rel="nofollow" class="share-twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                                        <a href="https://www.linkedin.com/shareArticle?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank" rel="nofollow" class="share-linkedin"><i class="fa fa-linkedin" aria-hidden="true"></i></a>                        
                                        <button onclick="copyURL()" class="copy-url"><i class="fa fa-copy" aria-hidden="true"></i></button>
                                        <span id="copyMessage" class="copy-message"></span>

                                    </div>
                                <div class="col-12 col-sm-6 col-md-6 col-lg-6 watchlistgrp"><?php
                                    global $wpdb;
                                    $table_name = $wpdb->prefix . 'watchlist';
                                    $get_result = $wpdb->get_row("SELECT * FROM $table_name WHERE user_id = ".get_current_user_id()." AND auction_id = ".get_the_ID()."");
                                    if($get_result) {
                                        echo '<a href="'.site_url('/dashboard/watchlist/').'" class="btn btn-border addedwatchlist watchlist-btn">View Watch List</a>';
                                    }else {
                                        echo '<a onClick="addWatchlist('.get_the_ID().', '.get_current_user_id().', this)" class="btn btn-border watchlist-btn">Add to Watch List</a>';          
                                    } ?>
                                </div>
                            <?php   if($letestbid){ 
                                        if($bidverify['status'] == 'success'){
                                            ?>
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                    <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#placebid">Place Bid</button>
                                                </div>
                                            <?php
                                        }else{
                                            if($bidverify['verifyform'] == 'show'){
                                            ?>
                                                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                                    <button type="button" id="showbidregform" class="btn btn-green">Register to Bid</button>
                                                </div>
                                                <div id="bidregform" class="col-12 col-sm-12 col-md-12 col-lg-12" style="display:none;">
                                                    
                                                    <div class="verifybiddata">
                                                        <?php if($bidverify['msg']){ ?>
                                                            <div class="message">
                                                                <?php echo $bidverify['msg']; ?>
                                                            </div>
                                                        <?php }?>
                                                        <div class="reginputs">
                                                            <div class="form-group">
                                                                <label for="auc_user_first_name">First Name:</label>
                                                                <!-- <input type="text" id="verifyname" name="verifyname" class="requiredfld" value="<?php // echo $fullname; ?>"> -->
                                                                <input type="text" id="auc_user_first_name" name="auc_user_first_name" value="<?php echo $first_name; ?>" placeholder="Enter First Name" class="form-control requiredfld" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="auc_user_last_name">Last Name:</label>
                                                                <!-- <input type="text" id="verifyname" name="verifyname" class="requiredfld" value="<?php // echo $fullname; ?>"> -->
                                                                <input type="text" id="auc_user_last_name" name="auc_user_last_name" value="<?php echo $last_name; ?>" placeholder="Enter Last Name" class="form-control requiredfld" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="verifypnumber">Phone Number:</label>                                                                
                                                                <input
                                                                    type="tel"
                                                                    name="verifypnumber"
                                                                    id="verifypnumber"
                                                                    class="form-control requiredfld phonenumber_itnl"
                                                                    placeholder="Enter your phone number"
                                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" autofocus
                                                                />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="auc_user_city">City:</label>
                                                                <!-- <input type="text" id="verifyname" name="verifyname" class="requiredfld" value="<?php // echo $fullname; ?>"> -->
                                                                <input type="text" id="auc_user_city" name="auc_user_city" value="<?php echo $city; ?>" placeholder="Enter City" class="form-control requiredfld" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="auc_user_state">State:</label>
                                                                <!-- <input type="text" id="verifyname" name="verifyname" class="requiredfld" value="<?php // echo $fullname; ?>"> -->
                                                                <input type="text" id="auc_user_state" name="auc_user_state" value="<?php echo $state; ?>" placeholder="Enter State" class="form-control requiredfld" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="auc_user_country">Country:</label>
                                                                <!-- <input type="text" id="verifyname" name="verifyname" class="requiredfld" value="<?php // echo $fullname; ?>"> -->
                                                                <select id="auc_user_country" name="auc_user_country" class="form-control requiredfld" data-label="Country">
                                                                    <?php 
                                                                        $countries = array(                                                                                         
                                                                            "Afghanistan",
                                                                            "Åland Islands",
                                                                            "Albania",
                                                                            "Algeria",
                                                                            "American Samoa",
                                                                            "Andorra",
                                                                            "Angola",
                                                                            "Anguilla",
                                                                            "Antarctica",
                                                                            "Antigua and Barbuda",
                                                                            "Argentina",
                                                                            "Armenia",
                                                                            "Aruba",
                                                                            "Australia",
                                                                            "Austria",
                                                                            "Azerbaijan",
                                                                            "Bahamas",
                                                                            "Bahrain",
                                                                            "Bangladesh",
                                                                            "Barbados",
                                                                            "Belarus",
                                                                            "Belgium",
                                                                            "Belize",
                                                                            "Benin",
                                                                            "Bermuda",
                                                                            "Bhutan",
                                                                            "Bolivia",
                                                                            "Bosnia and Herzegovina",
                                                                            "Botswana",
                                                                            "Bouvet Island",
                                                                            "Brazil",
                                                                            "British Indian Ocean Territory",
                                                                            "Brunei Darussalam",
                                                                            "Bulgaria",
                                                                            "Burkina Faso",
                                                                            "Burundi",
                                                                            "Cambodia",
                                                                            "Cameroon",
                                                                            "Canada",
                                                                            "Cape Verde",
                                                                            "Cayman Islands",
                                                                            "Central African Republic",
                                                                            "Chad",
                                                                            "Chile",
                                                                            "China",
                                                                            "Christmas Island",
                                                                            "Cocos (Keeling) Islands",
                                                                            "Colombia",
                                                                            "Comoros",
                                                                            "Congo",
                                                                            "Congo, The Democratic Republic of The",
                                                                            "Cook Islands",
                                                                            "Costa Rica",
                                                                            "Cote D'ivoire",
                                                                            "Croatia",
                                                                            "Cuba",
                                                                            "Cyprus",
                                                                            "Czech Republic",
                                                                            "Denmark",
                                                                            "Djibouti",
                                                                            "Dominica",
                                                                            "Dominican Republic",
                                                                            "Ecuador",
                                                                            "Egypt",
                                                                            "El Salvador",
                                                                            "Equatorial Guinea",
                                                                            "Eritrea",
                                                                            "Estonia",
                                                                            "Ethiopia",
                                                                            "Falkland Islands (Malvinas)",
                                                                            "Faroe Islands",
                                                                            "Fiji",
                                                                            "Finland",
                                                                            "France",
                                                                            "French Guiana",
                                                                            "French Polynesia",
                                                                            "French Southern Territories",
                                                                            "Gabon",
                                                                            "Gambia",
                                                                            "Georgia",
                                                                            "Germany",
                                                                            "Ghana",
                                                                            "Gibraltar",
                                                                            "Greece",
                                                                            "Greenland",
                                                                            "Grenada",
                                                                            "Guadeloupe",
                                                                            "Guam",
                                                                            "Guatemala",
                                                                            "Guernsey",
                                                                            "Guinea",
                                                                            "Guinea-bissau",
                                                                            "Guyana",
                                                                            "Haiti",
                                                                            "Heard Island and Mcdonald Islands",
                                                                            "Holy See (Vatican City State)",
                                                                            "Honduras",
                                                                            "Hong Kong",
                                                                            "Hungary",
                                                                            "Iceland",
                                                                            "India",
                                                                            "Indonesia",
                                                                            "Iran, Islamic Republic of",
                                                                            "Iraq",
                                                                            "Ireland",
                                                                            "Isle of Man",
                                                                            "Israel",
                                                                            "Italy",
                                                                            "Jamaica",
                                                                            "Japan",
                                                                            "Jersey",
                                                                            "Jordan",
                                                                            "Kazakhstan",
                                                                            "Kenya",
                                                                            "Kiribati",
                                                                            "Korea, Democratic People's Republic of",
                                                                            "Korea, Republic of",
                                                                            "Kuwait",
                                                                            "Kyrgyzstan",
                                                                            "Lao People's Democratic Republic",
                                                                            "Latvia",
                                                                            "Lebanon",
                                                                            "Lesotho",
                                                                            "Liberia",
                                                                            "Libyan Arab Jamahiriya",
                                                                            "Liechtenstein",
                                                                            "Lithuania",
                                                                            "Luxembourg",
                                                                            "Macao",
                                                                            "Macedonia, The Former Yugoslav Republic of",
                                                                            "Madagascar",
                                                                            "Malawi",
                                                                            "Malaysia",
                                                                            "Maldives",
                                                                            "Mali",
                                                                            "Malta",
                                                                            "Marshall Islands",
                                                                            "Martinique",
                                                                            "Mauritania",
                                                                            "Mauritius",
                                                                            "Mayotte",
                                                                            "Mexico",
                                                                            "Micronesia, Federated States of",
                                                                            "Moldova, Republic of",
                                                                            "Monaco",
                                                                            "Mongolia",
                                                                            "Montenegro",
                                                                            "Montserrat",
                                                                            "Morocco",
                                                                            "Mozambique",
                                                                            "Myanmar",
                                                                            "Namibia",
                                                                            "Nauru",
                                                                            "Nepal",
                                                                            "Netherlands",
                                                                            "Netherlands Antilles",
                                                                            "New Caledonia",
                                                                            "New Zealand",
                                                                            "Nicaragua",
                                                                            "Niger",
                                                                            "Nigeria",
                                                                            "Niue",
                                                                            "Norfolk Island",
                                                                            "Northern Mariana Islands",
                                                                            "Norway",
                                                                            "Oman",
                                                                            "Pakistan",
                                                                            "Palau",
                                                                            "Palestinian Territory, Occupied",
                                                                            "Panama",
                                                                            "Papua New Guinea",
                                                                            "Paraguay",
                                                                            "Peru",
                                                                            "Philippines",
                                                                            "Pitcairn",
                                                                            "Poland",
                                                                            "Portugal",
                                                                            "Puerto Rico",
                                                                            "Qatar",
                                                                            "Reunion",
                                                                            "Romania",
                                                                            "Russian Federation",
                                                                            "Rwanda",
                                                                            "Saint Helena",
                                                                            "Saint Kitts and Nevis",
                                                                            "Saint Lucia",
                                                                            "Saint Pierre and Miquelon",
                                                                            "Saint Vincent and The Grenadines",
                                                                            "Samoa",
                                                                            "San Marino",
                                                                            "Sao Tome and Principe",
                                                                            "Saudi Arabia",
                                                                            "Senegal",
                                                                            "Serbia",
                                                                            "Seychelles",
                                                                            "Sierra Leone",
                                                                            "Singapore",
                                                                            "Slovakia",
                                                                            "Slovenia",
                                                                            "Solomon Islands",
                                                                            "Somalia",
                                                                            "South Africa",
                                                                            "South Georgia and The South Sandwich Islands",
                                                                            "Spain",
                                                                            "Sri Lanka",
                                                                            "Sudan",
                                                                            "Suriname",
                                                                            "Svalbard and Jan Mayen",
                                                                            "Swaziland",
                                                                            "Sweden",
                                                                            "Switzerland",
                                                                            "Syrian Arab Republic",
                                                                            "Taiwan",
                                                                            "Tajikistan",
                                                                            "Tanzania, United Republic of",
                                                                            "Thailand",
                                                                            "Timor-leste",
                                                                            "Togo",
                                                                            "Tokelau",
                                                                            "Tonga",
                                                                            "Trinidad and Tobago",
                                                                            "Tunisia",
                                                                            "Turkey",
                                                                            "Turkmenistan",
                                                                            "Turks and Caicos Islands",
                                                                            "Tuvalu",
                                                                            "Uganda",
                                                                            "Ukraine",
                                                                            "United Arab Emirates",
                                                                            "United Kingdom",
                                                                            "United States",
                                                                            "United States Minor Outlying Islands",
                                                                            "Uruguay",
                                                                            "Uzbekistan",
                                                                            "Vanuatu",
                                                                            "Venezuela",
                                                                            "Viet Nam",
                                                                            "Virgin Islands, British",
                                                                            "Virgin Islands, U.S.",
                                                                            "Wallis and Futuna",
                                                                            "Western Sahara",
                                                                            "Yemen",
                                                                            "Zambia",
                                                                            "Zimbabwe"
                                                                        );
                                                                        foreach ($countries as $country) {
                                                                            echo '<option value="' . $country . '"' . (($user_country == $country) ? ' selected' : '') . '>' . $country . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                            </div>
                                                            <div class="form-group full-width">
                                                                <label for="verifypnumber">Zip Code:</label>                                                                
                                                                <input type="text" id="auc_zipcode" name="auc_zipcode" value="<?php echo $zipcode; ?>" placeholder="Enter Zipcode" class="form-control requiredfld" />
                                                            </div>
                                                            <div class="form-group address full-width">
                                                                <label for="verifyaddress">Address:</label>
                                                                <textarea id="verifyaddress" name="verifyaddress" class="requiredfld form-control" > <?php echo $address; ?> </textarea>
                                                            </div>
                                                            <input type="hidden" id="auctionid" value="<?php echo $auctionid; ?>">
                                                            <input type="hidden" id="userid" value="<?php echo $user_id; ?>">
                                                           
                                                            <div class="form-group text-center full-width">
                                                                <button type="button" id="submitverification" class="btn btn-green">Submit</button>
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div id="afterresponse" style="display: none;">
                                                </div>
                                            <?php
                                            }else{
                                                ?>
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                    <?php if($bidverify['msg']){ ?>
                                                        <div class="waitmessage">
                                                            <?php echo $bidverify['msg']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php
                                            }
                                        }
                                    } ?>
                            </div>
                        </div><?php 
                    } elseif($start_timestamp > $current_timestamp && empty($orderid)) { ?>
                        <div class="deal-counter test">
                            <div class="endin">Starts in:</div>
                            <?php 
                                $endin_time = get_field('start_date');
                                if($endin_time) {
                                    $endintime = DateTime::createFromFormat('d/m/Y h:i a', $endin_time);
                                    $endcnttime = $endintime->format('M d, Y H:i:s');
                                    echo '<input type="hidden" id="endin_time" value="'.$endcnttime.'">';
                                    $current_time = new DateTime(current_time('mysql'));
                                    $current_time_formatted = $current_time->format('M d, Y H:i:s');

                                    echo '<input type="hidden" class="sd" id="timenowis" value="'.$current_time_formatted.'">';
                                  
                                }
                            ?>
                            <div id="countdown" class="cdpag">
                                <?php
                                $difference = $start_timestamp - $current_timestamp;

                                $days = floor($difference / (60 * 60 * 24));
                                $hours = floor(($difference % (60 * 60 * 24)) / (60 * 60));
                                $minutes = floor(($difference % (60 * 60)) / 60);
                                $seconds = $difference % 60;

                                ?>
                                <ul>
                                    <li>
                                        <div id="daysp">
                                            <span><?php echo str_pad($days, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($days, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        DAYS
                                    </li>
                                    <li>
                                        <div id="hoursp">
                                            <span><?php echo str_pad($hours, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($hours, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        HOURS
                                    </li>
                                    <li>
                                        <div id="minutesp">
                                            <span><?php echo str_pad($minutes, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($minutes, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        MINUTES
                                    </li>
                                    <li>
                                        <div id="secondsp">
                                            <span><?php echo str_pad($seconds, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                            <span><?php echo str_pad($seconds, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                        </div>
                                        SECONDS
                                    </li>
                                </ul>

                            </div>
                        </div>
                        <div class="product-btngroup">
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-6 col-lg-6 watchlistgrp"><?php
                                    global $wpdb;
                                    $table_name = $wpdb->prefix . 'watchlist';
                                    $get_result = $wpdb->get_row("SELECT * FROM $table_name WHERE user_id = ".get_current_user_id()." AND auction_id = ".get_the_ID()."");
                                    if($get_result) {
                                        echo '<a href="'.site_url('/dashboard/watchlist/').'" class="btn btn-border addedwatchlist watchlist-btn">View Watch List</a>';
                                    }else {
                                        echo '<a onClick="addWatchlist('.get_the_ID().', '.get_current_user_id().', this)" class="btn btn-border watchlist-btn">Add to Watch List</a>';          
                                    } ?>
                                </div>
                            </div>
                        </div><?php 
                    } ?>
                </div>
            </div>
        </div>
        <div class="productdetail-overview">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">OVERVIEW</button>
                </li>
                <!--  -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="itemdetails-tab" data-bs-toggle="tab" data-bs-target="#itemdetails" type="button" role="tab" aria-controls="itemdetails" aria-selected="false">ITEM DETAILS</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="overviewmain">
                        <div class="row">
                            <div class="col-12 col-lg-6"><?php 
                            $sale_overview = get_field('sale_overview');
                            if($sale_overview) { ?>
                                <div class="sale-overview">
                                    <h5>SALE OVERVIEW</h5>
                                    <p><?php echo $sale_overview; ?></p>
                                </div><?php 
                            } 
                            if(have_rows('product_overview')) { ?>
                                <div class="product-overview">
                                    <h5>PRODUCT OVERVIEW</h5>
                                    <ul><?php 
                                    while(have_rows('product_overview')) { 
                                        the_row();
                                        $label = get_sub_field('label');
                                        $value = get_sub_field('value'); 
                                        if($label || $value){?>
                                            <li><?php echo $label; ?>: <?php echo $value; ?></li><?php 
                                        }
                                    } ?>
                                    </ul>
                                </div><?php 
                            } 
                            $start_date = get_field('start_date');
                            $end_date = get_field('end_date'); 
                            if($start_date || $end_date) { ?>
                                <div class="auction-time">
                                    <h5>AUCTION TIME</h5>
                                    <p>
                                    <?php 
                                        $startdate = DateTime::createFromFormat('d/m/Y h:i a', $start_date);
                                        $fstartdate = $startdate->format('j M Y');
                                        $enddate = DateTime::createFromFormat('d/m/Y h:i a', $end_date);
                                        $fenddate = $enddate->format('j M Y');
                                        if($fstartdate && $fenddate) :
                                            echo $fstartdate.' - '.$fenddate; 
                                        elseif($fstartdate) : 
                                            echo $fstartdate;
                                        else :
                                            echo $fenddate;
                                        endif;
                                    ?>
                                    </p>
                                </div><?php 
                            }
                            $location = get_field('location');
                            if($location) { ?>
                                <div class="auction-location">
                                    <h5>LOCATION</h5>
                                    <p><?php echo $location; ?></p>
                                </div><?php 
                            } ?>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="bid-history">
                                    <h5>BID HISTORY</h5>
                                    <div class="historytable">
                                    <?php if($bidinghistory){
                                        ?>
                                        <table class="table">
                                        <tbody>
                                            <?php foreach ($bidinghistory as $bkey => $bidvalue) {
                                                $bidamt = $bidvalue['bidamount'];
                                                $location = $bidvalue['location'];
                                                $created = $bidvalue['created'];
                                                $beforetime = getBeforeTime($created);  
                                                
                                                $userid = $bidvalue['userid'];
                                                $first_name = get_user_meta( $userid, 'first_name', true );
                                                $last_name = get_user_meta( $userid, 'last_name', true ); 
                                                $full_name =   "User &nbsp;" . $userid ;                                            

                                                echo '<tr>
                                                        <th>$'. number_format($bidamt, 2, '.', ',').'</th>
                                                        <td>'.$beforetime.'</td>
                                                        <td>'.$full_name.'</td>                                                        
                                                    </tr>';
                                            }?>
                                            
                                        </tbody>
                                    </table>
                                        <?php
                                    }else{
                                        ?>
                                        <div><h5>It all begins with your first bid.</h5></div>
                                        <?php
                                    }?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
                <?php if(get_the_content()) { ?>
                <div class="tab-pane fade" id="itemdetails" role="tabpanel" aria-labelledby="itemdetails-tab">
                    <div class="itemdetails-main">
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="placebid" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Placing Bid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 1L1 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1 1L13 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                </button>
            </div>
            <div class="modal-body">
                <div class="wholebody-block">
                    <div class="placebid-block"><?php 
                    if(get_the_post_thumbnail_url()) { ?>
                        <div class="product-listitem-image">
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="img-fluid">
                        </div><?php 
                    } ?>
                        <div class="placebid-blockcontent">
                            <a href="" class="title"><?php the_title(); ?></a>
                            <div class="currentbid">Current bid:<span class="price">$<span class="livebidprice"><?php echo number_format($letestbid); ?></span>
                                <?php if($bidcount > 0){ ?>
                                    <span class="bids">(<span class="bidcount"><?php echo $bidcount; ?></span> bids)</span>
                                <?php } ?>
                            </div>
                            <div class="deal-counter">
                                <div class="endin">Ends in:</div>
                                <?php 
                                    $endin_time = get_field('end_date');
                                    if($endin_time) {
                                        $endintime = DateTime::createFromFormat('d/m/Y h:i a', $endin_time);
                                        $endcnttime = $endintime->format('M d, Y h:i:s');
                                        echo '<input type="hidden" id="endin_time" value="'.$endcnttime.'">';
                                ?>
                                <div id="countdown" class="cdpup">
                                    <?php
                                    $endintime_st = get_field('end_date');
                                    $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endintime_st)));

                                    $endin_timestamp = strtotime($endin_time_formatted);
                                    //$current_timestamp = current_time('timestamp', true);
                                    

                                    $current_timestampfull = new DateTime(current_time('mysql'));
                                    $timestamp = $current_timestampfull->getTimestamp();
                                    $current_timestamp = sprintf("%010d", $timestamp);
                                   
                                    $difference = $endin_timestamp - $current_timestamp;

                                    $days1 = floor($difference / (60 * 60 * 24));
                                    $hours1 = floor(($difference % (60 * 60 * 24)) / (60 * 60));
                                    $minutes1 = floor(($difference % (60 * 60)) / 60);
                                    $seconds1 = $difference % 60;

                                    ?>
                                    <ul>
                                        <li>
                                            <div id="dayspop">
                                               <span><?php echo str_pad($days1, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                                <span><?php echo str_pad($days1, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                            </div>
                                            DAYS
                                        </li>
                                        <li>
                                            <div id="hourspop">
                                                <span><?php echo str_pad($hours1, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                                <span><?php echo str_pad($hours1, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                            </div>
                                            HOURS
                                        </li>
                                        <li>
                                            <div id="minutespop">
                                                <span><?php echo str_pad($minutes1, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                                <span><?php echo str_pad($minutes1, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                            </div>
                                            MINUTES
                                        </li>
                                        <li>
                                            <div id="secondspop">
                                                <span><?php echo str_pad($seconds1, 2, '0', STR_PAD_LEFT)[0]; ?></span>
                                                <span><?php echo str_pad($seconds1, 2, '0', STR_PAD_LEFT)[1]; ?></span>
                                            </div>
                                            SECONDS
                                        </li>
                                    </ul>
                                </div><?php 
                            } ?>
                            </div>
                        </div>
                    </div>

                    <div class="bidtable">
                        <div class="bidtable-title">Bid History</div>
                        <div class="scrolltable f1">
                            <?php if($bidinghistory){
                                ?>
                                <table class="table">
                                <tbody>
                                    <?php foreach ($bidinghistory as $bkey => $bidvalue) {
                                        $bidamt = $bidvalue['bidamount'];
                                        $location = $bidvalue['location'];
                                        $created = $bidvalue['created'];
                                        $beforetime = getBeforeTime($created);

                                        $userid = $bidvalue['userid'];
                                        $first_name = get_user_meta( $userid, 'first_name', true );
                                        $last_name = get_user_meta( $userid, 'last_name', true ); 
                                        $full_name =   "User &nbsp;" . $userid ;

                                        echo '<tr>
                                                <th>$'. number_format($bidamt, 2, '.', ',').'</th>
                                                <td>'.$beforetime.'</td>
                                                <td>'.$full_name.'</td>
                                            </tr>';
                                    }?>
                                    
                                </tbody>
                            </table>
                                <?php
                            }else{
                                ?>
                                <div>Don't wait, make the first bid!</div>
                                <?php
                            }?>
                            
                        </div>
                    </div>
                </div>
                <?php 
                //$letestbid = get_field('base_price');
                $increase_livebid = get_field('increase_live_bid_amount');
                $increase_showhand = get_field('increase_show_hand_amount');
                $value = intval($letestbid) + intval($increase_livebid);

                
                $end_date_str = get_field('end_date');
                $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $end_date_str)));
                $end_datetime = strtotime($endin_time_formatted);
                $showhandtimebefore = !empty(get_field('showhand_time_before')) ? get_field('showhand_time_before') : 10;
                $current_datetime = current_time('timestamp');
                $remaining_time = $end_datetime - $current_datetime;
                $remaining_minutes = $remaining_time / 60;
                
                if ($remaining_time && $remaining_minutes <= $showhandtimebefore) {
                    $showhandinput = '';
                }else{
                    $showhandinput = 'disabled';
                }


                if($letestbid || $increase_livebid || $increase_showhand) { ?>
                <div class="yourbid">
                    
                    <div class="bidloadimg" style="display:none;">
                        <div class="ldimg">
                            <img src="<?php echo get_stylesheet_directory_uri() .'/images/loaderimg.png';?> "/>
                        </div>
                    </div>
                    <?php $buyerpremium = get_post_meta($auctionid, 'buyer_premium', true); 
                    $increseamt = empty($increase_livebid) ? 1 : $increase_livebid;
                    $totalletestbid = $letestbid + $increseamt; ?>
                    
                    <div class="title">Your Bid</div>
                    <form>
                        <div class="live-show">
                            <div class="switches-container">
                                <input type="radio" id="livebid" name="bid" value="Livebid" checked="checked" />
                                <input type="radio" id="showbid" name="bid" value="Showhand" <?php echo $showhandinput; ?>/>
                                <label for="livebid">LIVE BID</label>
                                <label id="showbidlabel" class="<?php echo $showhandinput; ?>" for="showbid">SHOW HAND</label>
                                <div class="switch-wrapper">
                                    <div class="switch">
                                        <div>LIVE BID</div>
                                        <div>SHOW HAND</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="d-none your-credit">Your credit: <span>$80,000</span></label>
                                <input type="text" id="bid_value" value="<?php echo $letestbid; ?>" onkeypress="handleKeyPress(event)" placeholder="MIN. $14,002" readonly>
                            </div> 

                            <div class="autobidsection">
                                <?php
                                if($user_id){
                                    if($showhandinput == 'disabled'){
                                        echo '<input type="hidden" id="cuserid" value="'.$user_id.'">';
                                        echo '<input type="hidden" id="caucid" value="'.$auctionid.'">';
                                        global $wpdb;
                                        $mxtable_name = $wpdb->prefix . 'autobids';
                                        $mxquery = $wpdb->prepare(
                                            "SELECT * FROM $mxtable_name WHERE auctionid = %d AND userid = %d AND status = %s",
                                            $auctionid, $user_id, 'active'
                                        );
                                        $mxresults = $wpdb->get_row($mxquery);
                                        if ($mxresults) {
                                            $rowid = $mxresults->id;
                                            ?>
                                            <div class="exmxbidamt">
                                                <div class="mxdatablock">
                                                    <span> Active Max Bid : $<?php echo number_format($mxresults->amount); ?></span>
                                                    <a href="javascript:void(0);" id="removemaxbid" class="updatemaxbid-auction">Update Max Bid</a>
                                                </div>
                                                <div class="maxbidblock" style="display:none;">
                                                    <div class="maxfields">
                                                        <input type="text" id="maxbidamount" data-minamt="<?php echo $totalletestbid; ?>" value="<?php echo $mxresults->amount; ?>">
                                                        <a href="javascript:void(0);" onclick="updateMaxBidForAuction(<?php echo $rowid; ?>, <?php echo $auctionid; ?>, <?php echo $user_id; ?>);" id="addmaxbid">Update Max Bid</a>
                                                    </div>
                                                    <span id="minbidvalue-auction"><b>Min</b> = (<?php echo $totalletestbid; ?> $)</span>
                                                    <span id="maxresponse" class="" style="display:none"></span>
                                                    <div class="bidinfo"><span><i class="fa fa-info-circle" aria-hidden="true"></i></span> Your bid is the maximum amount that you are willing to pay for this item.The auction software will bid on your behalf up to this amount.</div>
                                                    <!-- Old Premium Texts of 3 lines was here -->
                                                </div>
                                                <span id="maxresponse" class="" style="display:none"></span>
                                            </div>
                                            <?php
                                        }else{
                                        ?>
                                            <a href="javascript:void(0);" id="showmaxwraper">Add Max Bid</a>
                                            <div class="maxbidblock" style="display:none;">
                                                <div class="maxfields">
                                                    <input type="text" id="maxbidamount" data-minamt="<?php echo $totalletestbid; ?>" value="<?php echo $totalletestbid; ?>">
                                                    <a href="javascript:void(0);" onclick="addMaxBidForAuction();" id="addmaxbid">Submit Max Bid</a>
                                                </div>
                                                <span id="minbidvalue-auction"><b>Min</b> = (<?php echo $totalletestbid; ?> $)</span>
                                                <span id="maxresponse" class="" style="display:none"></span>
                                                <div class="bidinfo"><span><i class="fa fa-info-circle" aria-hidden="true"></i></span> Your bid is the maximum amount that you are willing to pay for this item.The auction software will bid on your behalf up to this amount.</div>
                                                <!-- Old Premium Texts of 3 lines was here -->
                                            </div>
                                        <?php 
                                        }
                                    } 
                                }?>
                            </div>   

                            <input type="hidden" id="letestbidprc" value="<?php echo $letestbid; ?>">
                            <input type="hidden" id="buyerpremium" value="<?php echo $buyerpremium; ?>">
                            
                            <script>

                            jQuery(document).ready(function($) {
                                $('#bid_value').keyup(function() {
                                    var buyerpremium = jQuery('#buyerpremium').val();
                                    if(buyerpremium){
                                        var orderamount = parseFloat($('#bid_value').val()) || 0;
                                        var final_ordertotal;
                                        if (buyerpremium) {
                                            var taxamount = (buyerpremium / 100) * orderamount;
                                            final_ordertotal = orderamount + taxamount;
                                        } else {
                                            final_ordertotal = orderamount;
                                        }

                                        var formattedTotal = final_ordertotal.toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                        jQuery('.priceafterpremium').html(formattedTotal);
                                    }
                                });
                                $('#maxbidamount').keyup(function() {
                                    var buyerpremium = jQuery('#buyerpremium').val();
                                    if(buyerpremium){
                                        var orderamount = parseFloat($('#maxbidamount').val()) || 0;
                                        var final_ordertotal;
                                        if (buyerpremium) {
                                            var taxamount = (buyerpremium / 100) * orderamount;
                                            final_ordertotal = orderamount + taxamount;
                                        } else {
                                            final_ordertotal = orderamount;
                                        }

                                        var formattedTotal = final_ordertotal.toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                        jQuery('.priceafterpremium').html(formattedTotal);
                                    }
                                });
                            });

                               function handleKeyPress(event) {
                                    if (event.keyCode === 13) {
                                        event.preventDefault();
                                        document.querySelector('#placeaucbid').click();
                                    }
                                } 

                            document.addEventListener('DOMContentLoaded', function () {
                                var livebidRadio = document.getElementById('livebid');
                                var showbidRadio = document.getElementById('showbid');
                                var bidValueInput = document.getElementById('bid_value');
                                var buyerpremium = jQuery('#buyerpremium').val();

                                var basePrice = jQuery('#letestbidprc').val();
                                var increaseLivebid = <?php echo empty($increase_livebid) ? 1 : $increase_livebid; ?>;
                                var increaseShowhand = <?php echo empty($increase_showhand) ? 1 : $increase_showhand; ?>;

                                var value = parseInt(basePrice) + parseInt(increaseLivebid);
                                bidValueInput.value = value;

                                livebidRadio.addEventListener('change', function () {
                                    var basePrice = jQuery('#letestbidprc').val();
                                    value = parseInt(basePrice) + parseInt(increaseLivebid);
                                    bidValueInput.value = value;
                                    bidValueInput.readOnly = true;

                                    var final_ordertotal;
                                    if (buyerpremium) {
                                        var taxamount = (buyerpremium / 100) * value;
                                        final_ordertotal = value + taxamount;
                                    } else {
                                        final_ordertotal = value;
                                    }
                                    var formattedTotal = final_ordertotal.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    jQuery('.priceafterpremium').html(formattedTotal);

                                });

                                showbidRadio.addEventListener('change', function () {
                                    var basePrice = jQuery('#letestbidprc').val();
                                    value = parseInt(basePrice) + parseInt(increaseShowhand);
                                    bidValueInput.value = value;
                                    bidValueInput.readOnly = false;
                                    
                                    var final_ordertotal;
                                    if (buyerpremium) {
                                        var taxamount = (buyerpremium / 100) * value;
                                        final_ordertotal = value + taxamount;
                                    } else {
                                        final_ordertotal = value;
                                    }
                                    var formattedTotal = final_ordertotal.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    jQuery('.priceafterpremium').html(formattedTotal);

                                });
                            });
                            </script>    
                        </div><?php 
                        if($buyerpremium){ 
                            $taxamount = ($buyerpremium / 100) * $totalletestbid;
                            $total_amount_with_tax = $totalletestbid + $taxamount;
                            ?>
                            <span class="buyerpremiuminfo">Winning bid incurs a <?php echo $buyerpremium; ?>% buyer premium. Grand total: $<span class="priceafterpremium"><?php echo number_format($total_amount_with_tax, 2, '.', ','); ?></span>.</span>
                        <?php } 
                            if($buyerpremium){                         
                                $taxamount = ($buyerpremium / 100) * $totalletestbid;
                                $total_amount_with_tax = $totalletestbid + $taxamount;
                                ?>
                                <div class="bidpremiumbuyer-info">
                                    <span class="buyerinfo"><b>With Buyer's Premium</b> <span class="priceafterpremium"><?php echo number_format($total_amount_with_tax, 2, '.', ','); ?> $</span></span>
                                    <span class="buyerinfo"><b>Buyer's premium </b><?php echo $buyerpremium; ?>% </span>
                                    <p>By submitting my bid, I agree to abide by the Terms and Conditions</p>
                                </div>   
                            <?php } else { ?>
                                    <div class="bidpremiumbuyer-info">
                                    <span class="buyerinfo">With Buyer's Premium $<span class="priceafterpremium"><?php echo number_format($totalletestbid, 2, '.', ','); ?></span></span>
                                    <span class="buyerinfo"><b>Buyer's premium </b>0%</span>
                                    <p>By submitting my bid, I agree to abide by the Terms and Conditions</p>
                                </div> 
                            <?php } ?>
                        <button type="button" onclick="placeBid(<?php echo $auctionid ?>, <?php echo $user_id ?>)" class="btn btn-green w-100" id="placeaucbid">Place Bid</button>
                        
                        <span id="infoblc" class="" style="display:none;"></span>
                    </form>
                </div><?php 
            } ?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="daynum" value="<?php echo $days; ?>">
<input type="hidden" id="hournum" value="<?php echo $hours; ?>">
<input type="hidden" id="minutesnum" value="<?php echo $minutes; ?>">
<input type="hidden" id="secondsnum" value="<?php echo $seconds+1; ?>">
<input type="hidden" id="showhandtimenum" value="<?php echo $showhandtimebefore; ?>">
<script>
            
                jQuery(function ($) {
                    var userPhoneNumber = "<?php echo $phone; ?>";
                    var input = $('#verifypnumber');

                    input.intlTelInput({
                        autoHideDialCode: true,
                        autoPlaceholder: "ON",
                        dropdownContainer: document.body,
                        formatOnDisplay: true,
                        // hiddenInput: "full_number",
                        initialCountry: "us",
                        // nationalMode: true,
                        placeholderNumberType: "MOBILE",
                        preferredCountries: ['us', 'uk'],
                        separateDialCode: true,
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js" // Include the utils.js script
                    });
                    input.intlTelInput('setNumber', userPhoneNumber);

                    // input.on("countrychange", function (e, countryData) {
                    //     var dialCode = countryData.dialCode;
                    //     input.intlTelInput("setNumber", "+" + dialCode);
                    // });
                });
                
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