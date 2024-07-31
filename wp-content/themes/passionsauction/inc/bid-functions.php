<?php
require_once 'pusher/vendor/autoload.php';
/* Place a Bid */
add_action('wp_ajax_placeAuctionBid', 'placeAuctionBid');
add_action('wp_ajax_nopriv_placeAuctionBid', 'placeAuctionBid');
function placeAuctionBid() {
    $response = array();
    extract($_POST);
    $auctionurl = get_the_permalink($auctionid);
    if(!is_user_logged_in()){
    	$expiration = time() + 900;
	   	$cookie_path = '/';
	    $cookie_domain = $_SERVER['HTTP_HOST'];
	    $cookie_name = 'redirect';
	    $cookie_value = $auctionurl;
	    setcookie( $cookie_name, $cookie_value, $expiration, $cookie_path, $cookie_domain );
	    $redirecturl = home_url('login');
	    $response = array('status' => 'failed', 'redirect' => $redirecturl, 'message' => 'Please log in to bid. You will be redirected to the login page shortly.');
    }else{

    	$cuid = get_current_user_id();
    	$status = get_user_meta($cuid, 'userstatus', true);
    	
    	if($cuid != $userid || empty($auctionid)){
    		$response = array('status' => 'failed', 'message' => 'Something went wrong; please try again after some time.');
    	} elseif(isset($status) && $status != 'accepted') {
    		$msg = "We're sorry, but it seems that your account has not been verified yet.";
    		$response = array('status' => 'failed', 'message' => $msg);
    	} else{
    		global $wpdb;
			$table_name = $wpdb->prefix . 'bidhistory';
			$auctionmeta = get_post_meta($auctionid);
			$baseprice = $auctionmeta['base_price'][0]; 
			$increase_live_bid_amount = !empty($auctionmeta['increase_live_bid_amount'][0]) ? $auctionmeta['increase_live_bid_amount'][0] : 1;
			$increase_show_hand_amount = !empty($auctionmeta['increase_show_hand_amount'][0]) ? $auctionmeta['increase_show_hand_amount'][0] : 1;

			$increase_seconds = $auctionmeta['increase_time_interval'][0]; 
			$start_date = $auctionmeta['start_date'][0]; 
			$end_date = $auctionmeta['end_date'][0]; 
			$usermeta = get_user_meta($userid);
			$location = $usermeta['address'][0];
			
			$current_date = current_time('timestamp');
			$startdatestr = strtotime($start_date);
			if($startdatestr > $current_date){
				$response = array('status' => 'failed', 'popup'=>'close', 'message' => 'Apologies, bidding for this auction begins on '.date("j F Y, g:i A", $startdatestr).'.');
				wp_send_json($response);
    			die();
			}
			$enddatestr = strtotime($end_date);
			if($current_date > $enddatestr){
				$response = array('status' => 'failed', 'popup'=>'close', 'message' => 'Apologies, bidding for this auction is closed.');
				wp_send_json($response);
    			die();
			}

			if(empty($baseprice)){
				$response = array('status' => 'failed', 'message' => 'No base price found; please try again after some time.');
			}else{
				
				$lastbiddata = getLetestBidData($auctionid);
				if($lastbiddata->userid == $cuid){
					$response = array('status' => 'failed', 'message' => 'You\'re in the lead with your previous bid. No need for additional bids.');
				}else{

					if($lastbiddata){
						$lastbidamt = $lastbiddata->bidamount; 
					}else{
						$lastbidamt = $baseprice;
					}

					$showhandtimebefore = !empty(get_field('showhand_time_before',$auctionid)) ? get_field('showhand_time_before', $auctionid) : 10;
		            $remaining_time = $enddatestr - $current_date;
		            $remaining_minutes = $remaining_time / 60;						
		            
		            if ($remaining_time && $remaining_minutes >= $showhandtimebefore && $bidtype == 'Showhand') {
		                $response = array('status' => 'failed', 'message' => 'Right now, the Show Hand window is disabled.');					
						wp_send_json($response);
		    			die();
		            }

					if($bidtype == 'Livebid' && !empty($increase_live_bid_amount)){
						$expectedbidamt = $lastbidamt + $increase_live_bid_amount;
						if($bidamount != $expectedbidamt){
							$response = array('status' => 'failed', 'message' => 'The live bid amount should be only $'.number_format($expectedbidamt).'.');
							wp_send_json($response);
		    				die();
						}

					}else if($bidtype == 'Showhand' && !empty($increase_show_hand_amount)){
						$expectedbidamt = $lastbidamt + $increase_show_hand_amount;
					}else{
						$expectedbidamt = $lastbidamt;
					}

					if($bidamount < $expectedbidamt){
						$response = array('status' => 'failed', 'message' => 'Minimum bid amount is '.$expectedbidamt.'.');
					}else{

						$auction_bidtype = get_field('auction_bidtype', $auctionid);
						$bidusers_varification = get_field('bidusers_varification', $auctionid);
						if(!empty($auction_bidtype) && $auction_bidtype == 'registerbid'){
							$bidregusers = get_post_meta($auctionid, 'bidregusers', true);
							if(!isset($bidregusers[$userid]) || $bidregusers[$userid]['status'] == 'pending'){
								$response = array('status' => 'failed', 'message' => 'You need to register for bidding first or wait for admin approval if already registered.');
								wp_send_json($response);
		    					die();
							}
						}

						$datetimenow = current_time('Y-m-d H:i:s');
						$wpdb->insert( 
						    $table_name, 
						    array( 
						        'userid' => $userid, 
						        'auctionid' => $auctionid, 
						        'bidamount' => $bidamount,
						        'previous_bidamount' => $lastbidamt,
						        'bidtype' => $bidtype,
						        'location' => $location,
						        'auction_baseprice' => $baseprice,
						        'extra' => '',
						        'created' =>$datetimenow
						    ) 
						);
						$bid_id = $wpdb->insert_id;
						if($bid_id){

							if($increase_seconds){
								$end_date1 = DateTime::createFromFormat('d/m/Y h:i a', $end_date);
								$newdatetime = new DateTime($end_date);
								$timesec = '+'.$increase_seconds.' seconds';
								$newdatetime->modify($timesec);
								$new_enddate_str = $newdatetime->format('Y-m-d H:i:s');
								update_post_meta($auctionid, 'end_date', $new_enddate_str);

							}
							// sendBidEmailToOtherUsers($auctionid, $userid, $bid_id);
							$options = array(
							    'cluster' => 'ap2',
							    'useTLS' => true
							);
							$pusher = new Pusher\Pusher(
							    'e4bbf991aaac16fd100c',
							    '90a7ef5aaf8097720dba',
							    '1761377',
							    $options
							);

							$data['bidid'] 		= $bid_id;
							$data['auctionid'] 	= $auctionid;
							$data['bidamount'] 	= $bidamount;
							$data['location'] 	= $location;
							$data['datetime'] 	= $datetimenow;
							$data['userid'] 	= $userid;
							$data['inctime'] 	= $increase_seconds;
							$pushertr = $pusher->trigger('auction', 'newbid', $data);
							$redirectto = get_the_permalink($auctionid);
							$response = array('status' => 'success', 'scredirect' => $redirectto, 'auction_id' => $auctionid, 'userid' => $userid, 'bidid' => $bid_id, 'message' => 'Bid placed succesfully.');
						}else{
							$response = array('status' => 'failed', 'message' => 'Problem in bid placement; please try again after some time.');
						}
					}
				}

			}
    	}
    }
    
    wp_send_json($response);
    die();
}

// Send email to previous users 

add_action('wp_ajax_send_email_previous_bidders', 'send_email_previous_bidders');
add_action('wp_ajax_nopriv_send_email_previous_bidders', 'send_email_previous_bidders');

function send_email_previous_bidders() {
    // Extract POST variables
    $auction_id = isset($_POST['auction_id']) ? $_POST['auction_id'] : 0;
    $userid = isset($_POST['userid']) ? $_POST['userid'] : 0;
    $lastbidid = isset($_POST['lastbidid']) ? $_POST['lastbidid'] : 0;

	$reponse = array();
	if($auction_id != 0 && $userid != 0 && $lastbidid) {		
		sendBidEmailToOtherUsers($auction_id, $userid, $lastbidid);
		$reponse = array('status' => 'success', 'message'=> 'Emails sent successfully');
	}  else {
		$reponse = array('status' => 'error', 'message'=> 'Data is not sufficient enough for emails');
	}
	wp_send_json($reponse); 
    wp_die();
}

function getLetestBidData($auction_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'bidhistory';
	$query = $wpdb->prepare("
	    SELECT *
	    FROM $table_name
	    WHERE auctionid = %d
	    ORDER BY id DESC
	    LIMIT 1
	", $auction_id);
	$lastbid = $wpdb->get_row($query);
	return $lastbid;
}
function getBidOrderData($bidid){

	$args = array(
	    'post_type'      => 'auctionorders',
	    'posts_per_page' => 1,
	    'meta_key'       => 'bidid',
	    'meta_value'     => $bidid,
	    'orderby'        => 'ID',
	    'order'          => 'DESC',
	);

	$order_posts = get_posts($args);

	if($order_posts){
		$orderdata = $order_posts[0];
	}else{
		$orderdata = '';
	}
	
	return $orderdata;
}
function getauctionOrderId($auctionid){
	/*global $wpdb;
	$table_name = $wpdb->prefix . 'orders';
	$query = $wpdb->prepare("
	    SELECT id
	    FROM $table_name
	    WHERE auctionid = %d
	    ORDER BY id DESC
	    LIMIT 1
	", $auctionid);
	$lastbid = $wpdb->get_row($query);
	return $lastbid;*/

	$args = array(
	    'post_type'      => 'auctionorders',
	    'posts_per_page' => 1,
	    'meta_key'       => 'auctionid',
	    'meta_value'     => $auctionid,
	    'orderby'        => 'ID',
	    'order'          => 'DESC',
	);

	$order_posts = get_posts($args);

	if($order_posts){
		$orderdata = $order_posts[0];
	}else{
		$orderdata = '';
	}
	
	return $orderdata;
}
function getTotalBidcount($auctionid){
	global $wpdb;
	$table_name = $wpdb->prefix . 'bidhistory';
	$query = $wpdb->prepare("
	    SELECT COUNT(*) as bid_count
	    FROM $table_name
	    WHERE auctionid = %d", $auctionid);

	$bid_count = $wpdb->get_var($query);
	return $bid_count;
}

/*function getBeforeTime($bidtime){
	$bid_datetime = new DateTime($bidtime);
	$current_datetime = new DateTime();
	$bid_timezone = $bid_datetime->getTimezone();
	$current_datetime->setTimezone($bid_timezone);
	$time_difference = $current_datetime->diff($bid_datetime);
	if ($time_difference->y > 0) {
	    return $time_difference->y . " years ago";
	} elseif ($time_difference->m > 0) {
	    return $time_difference->m . " months ago";
	} elseif ($time_difference->d > 0) {
	    return $time_difference->d . " days ago";
	} elseif ($time_difference->h > 0) {
	    return $time_difference->h . " hours ago";
	} elseif ($time_difference->i > 0) {
	    return $time_difference->i . " minutes ago";
	} else {
	    return "Just now";
	}
}*/

function getBeforeTime($bidtime){
    $bid_datetime = new DateTime($bidtime);
    $current_datetime = new DateTime(current_time('mysql')); // Getting current time in WordPress
    $bid_timezone = $bid_datetime->getTimezone();
    $current_datetime->setTimezone($bid_timezone);
    $time_difference = $current_datetime->diff($bid_datetime);
    if ($time_difference->y > 0) {
        return $time_difference->y . " years ago";
    } elseif ($time_difference->m > 0) {
        return $time_difference->m . " months ago";
    } elseif ($time_difference->d > 0) {
        return $time_difference->d . " days ago";
    } elseif ($time_difference->h > 0) {
        return $time_difference->h . " hours ago";
    } elseif ($time_difference->i > 0) {
        return $time_difference->i . " minutes ago";
    } else {
        return "Just now";
    }
}


function checkAuctionStatus($start_date, $end_date, $current_time) {
    if ($current_time >= $start_date && $current_time <= $end_date) {
        return "Ongoing";
    } elseif ($current_time < $start_date) {
        return "Upcoming";
    } else {
        return "Past";
    }
}

add_action('wp_ajax_updateLiveAuctionData', 'updateLiveAuctionData');
add_action('wp_ajax_nopriv_updateLiveAuctionData', 'updateLiveAuctionData');
function updateLiveAuctionData() {
	extract($_POST);

	$pricehtml = number_format($bidamount);
	$bidcount = getTotalBidcount($auctionid);

	global $wpdb;
	$table_name = $wpdb->prefix . 'bidhistory';
	$query = $wpdb->prepare("
	    SELECT *
	    FROM $table_name
	    WHERE auctionid = %d
	    ORDER BY id DESC", $auctionid);

	$bidinghistory = $wpdb->get_results($query, ARRAY_A);

	$bidhistoryhtml = '';
	$bidhistoryhtml .= '<table class="table"><tbody>';
	
	foreach ($bidinghistory as $bkey => $bidvalue) {
        $bidamt = $bidvalue['bidamount'];
        $location = $bidvalue['location'];		
        $created = $bidvalue['created'];
        $beforetime = getBeforeTime($created);
		$userid = $bidvalue['userid'];
		$first_name = get_user_meta( $userid, 'first_name', true );
		$last_name = get_user_meta( $userid, 'last_name', true ); 
		$user_with_id =   "User &nbsp;" . $userid ;  

        $bidhistoryhtml .= '<tr>
                <th>$'. number_format($bidamt, 2, '.', ',').'</th>
                <td>'.$beforetime.'</td>
                <td>'.$user_with_id.'</td>
            </tr>';
    }             
    $bidhistoryhtml .= '</tbody></table>';
    
    if($inctime > 0){
	    $endintime_st = get_field('end_date', $auctionid);
	    $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endintime_st)));
	    $endin_timestamp = strtotime($endin_time_formatted);
	    $current_timestamp = current_time('timestamp', true);

	    $difference = $endin_timestamp - $current_timestamp;
		$daynum = floor($difference / (60 * 60 * 24));
	    $hournum = floor(($difference % (60 * 60 * 24)) / (60 * 60));
	    $minnum = floor(($difference % (60 * 60)) / 60);
	}else{
		$daynum = 0;
		$hournum = 0;
		$minnum = 0;
	}	
	$response = array('status' => 'success', 'price' => $pricehtml, 'bidcount' => $bidcount, 'bidhistory' => $bidhistoryhtml, 'daynum' => $daynum, 'hournum' => $hournum, 'minnum' => $minnum);
	wp_send_json($response);
    die();
}

/* get the */
function getAuctionStatus($orderid, $userid) {
    $args = array(
        'post_type'      => 'auctionorders',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'auctionid',
                'value'   => $orderid,
                'compare' => '=',
            ),
            array(
                'key'     => 'userid',
                'value'   => $userid,
                'compare' => '=',
            ),
        ),
    );  
    $allauctions = get_posts($args);
    $status = '';
    if (!empty($allauctions)) {
        $status = get_post_meta($allauctions[0]->ID, 'status', true);
    }
    return $status;
}

// 
add_action('wp_ajax_registerBidVarification', 'registerBidVarification');
add_action('wp_ajax_nopriv_registerBidVarification', 'registerBidVarification');

function registerBidVarification() {
    // Extract POST variables
    extract($_POST);
	$reponse = array();
	if($auctionid && $userid != 0) {	
		$previoususers = get_post_meta($auctionid, 'bidregusers', true);

		$varificationtype = get_field('bidusers_varification', $auctionid);
		if($varificationtype == 'auto'){
			$vrstatus = 'verified';
			$successmessgae = 'Successfully registered to bid in the auction.';
		}else{
			$vrstatus = 'pending';
			$successmessgae = 'Registration successful. Please wait for admin approval. We will inform you once you are verified.';
		}
		if(empty($previoususers)){
			$userregdata = array(
			    $userid => array(
			        'full_name' => $username,
			        'first_name' => $first_name,
			        'last_name' => $last_name,
			        'city' => $city,
			        'state' => $state,
			        'country' => $country,
			        'zipcode' => $zipcode,
			        'phone_number' => '+'.$phonecode.' '.$phone,
			        'address' => $address,
			        'status' => $vrstatus
			    ),
			);
			update_post_meta($auctionid, 'bidregusers', $userregdata);
		}else{
			$previoususers[$userid] = array(
				'full_name' => $username,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'city' => $city,
				'state' => $state,
				'country' => $country,
				'zipcode' => $zipcode,
				'phone_number' => '+'.$phonecode.' '.$phone,
				'address' => $address,
				'status' => $vrstatus

			    );
			update_post_meta($auctionid, 'bidregusers', $previoususers);
		}

		$auctionName = get_the_title($auctionid);
		$viewAuctionLink = site_url().'/wp-admin/admin.php?page=view_bid_users&post_id='.$auctionid;

		$subject = 'New User Registration for Bidding on ' . $auctionName;
		$message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">A new user has registered for bidding on the auction <strong>' . $auctionName . '</strong>.</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Please review and verify the registration as needed.</p><a href="'.$viewAuctionLink.'" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">View Auction Users</a>';

		$user_mail_sent = passionAuctionEmail(0, $subject, $message);

		if($user_mail_sent) {
			$reponse = array('status' => 'success', 'message'=> $successmessgae);
		}else {
			$reponse = array('status' => 'failed', 'message'=> 'Something went wrong in email.');
		}

		
	}  else {
		$reponse = array('status' => 'error', 'message'=> 'Something went wrong please try again after some time.');
	}
	wp_send_json($reponse); 
    wp_die();
}

add_action('wp_ajax_addMaxBidAmount', 'addMaxBidAmount');
add_action('wp_ajax_nopriv_addMaxBidAmount', 'addMaxBidAmount');

function addMaxBidAmount() {

    extract($_POST);

	$cuid = get_current_user_id();
	$url     = wp_get_referer();
	$cpostid = url_to_postid( $url ); 
	
	$reponse = array();
	if($auctionid && $userid != 0) {	

		if($cuid != $userid){
			$reponse = array('status' => 'failed', 'message'=> 'The maximum bid is only allowed for yourself.');
		}else if($cpostid != $auctionid){
			$reponse = array('status' => 'failed', 'message'=> 'The maximum bid is only allowed for the current auction.');
		}else{

			global $wpdb;
			$status = 'active';
			$table_name = $wpdb->prefix . 'autobids';
			$added = $wpdb->insert(
			    $table_name,
			    array(
			        'auctionid' => $auctionid,
			        'userid' => $userid,
			        'amount' => $bidamount,
			        'status' => $status,
			    ),
			    array('%d','%d','%s','%s')
			);
			if($added){
				$last_id = $wpdb->insert_id;
				checkForAutoBid($auctionid, $userid);
				$reponse = array('status' => 'success', 'insid' => $last_id, 'message'=> 'Max bid amount added successfully.');
			}else{
				$reponse = array('status' => 'error', 'message'=> 'Something went wrong please try again after some time.');
			}
		}
		
	}else{
		$reponse = array('status' => 'error', 'message'=> 'Something went wrong please try again after some time.');
	}

	wp_send_json($reponse); 
    wp_die();
}

add_action('wp_ajax_updateMaxBidAmount', 'updateMaxBidAmount');
add_action('wp_ajax_nopriv_updateMaxBidAmount', 'updateMaxBidAmount');

function updateMaxBidAmount() {
    $auctionid = isset($_POST['auctionid']) ? intval($_POST['auctionid']) : 0;
    $userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
    $rowid = isset($_POST['rowid']) ? intval($_POST['rowid']) : 0;
    $bidamount = isset($_POST['bidamount']) ? floatval($_POST['bidamount']) : 0;

    $response = array();
    $current_uid = get_current_user_id();

    if ($auctionid && $userid != 0 && $userid == $current_uid) {
        global $wpdb;
        $table = $wpdb->prefix . 'autobids';
        $get_result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE id = %d AND auctionid = %d AND userid = %d AND status = %s",
                $rowid,
                $auctionid,
                $userid,
                'active'
            )
        );

        if ($get_result) {
            $data = array(
                'amount' => $bidamount,
            );
            $where = array(
                'id' => $rowid,
                'auctionid' => $auctionid,
                'userid' => $userid,
                'status' => 'active'
            );
            $update_result = $wpdb->update(
                $table,
                $data,
                $where
            );

            if ($update_result !== false) {
                $response = array('status' => 'success', 'message' => 'Max bid amount updated successfully.');
            } else {
                $response = array('status' => 'error', 'message' => 'Failed to update max bid amount.');
            }
        } else {
            $response = array('status' => 'error', 'message' => 'No active bid found for the given conditions.');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Invalid parameters or unauthorized access.');
    }
    wp_send_json($response);
    wp_die();
}

function checkForAutoBid($auctionid, $userid){


	global $wpdb;
    $mxtable_name = $wpdb->prefix . 'autobids';
    $mxquery = $wpdb->prepare(
        "SELECT * FROM $mxtable_name WHERE auctionid = %d AND userid = %d AND status = %s",
        $auctionid, $userid, 'active'
    );
    $mxresults = $wpdb->get_row($mxquery);

    if(!$mxresults){
    	return;
    }

    $maxbidamount = $mxresults->amount;

	$letestdata = getLetestBidData($auctionid);
	$lastbiduser = $letestdata->userid;
	$previousbidamount = $letestdata->bidamount;
	if($lastbiduser != $userid && $maxbidamount > $previousbidamount){

		global $wpdb;
		$table_name = $wpdb->prefix . 'bidhistory';
		$auctionmeta = get_post_meta($auctionid);
		$baseprice = $auctionmeta['base_price'][0]; 
		$increase_live_bid_amount = !empty($auctionmeta['increase_live_bid_amount'][0]) ? $auctionmeta['increase_live_bid_amount'][0] : 1;
		
		$increase_seconds = $auctionmeta['increase_time_interval'][0]; 
		$start_date = $auctionmeta['start_date'][0]; 
		$end_date = $auctionmeta['end_date'][0]; 
		$usermeta = get_user_meta($userid);
		$location = $usermeta['address'][0];
			
		$current_date = current_time('timestamp');
		$startdatestr = strtotime($start_date);
		$enddatestr = strtotime($end_date);
		
		if($previousbidamount){
			$lastbidamt = $previousbidamount; 
		}else{
			$lastbidamt = $baseprice;
		}

		$bidamount = $lastbidamt + $increase_live_bid_amount;

		if($bidamount <= $maxbidamount){

			$datetimenow = current_time('Y-m-d H:i:s');
			$dataarray = array( 
			        'userid' => $userid, 
			        'auctionid' => $auctionid, 
			        'bidamount' => $bidamount,
			        'previous_bidamount' => $lastbidamt,
			        'bidtype' => 'Livebid',
			        'location' => $location,
			        'auction_baseprice' => $baseprice,
			        'extra' => '',
			        'created' =>$datetimenow
			    );
			$wpdb->insert( 
			    $table_name, 
			    $dataarray 
			);
			$bid_id = $wpdb->insert_id;
			
			if($bid_id){

				if($increase_seconds){
					$end_date1 = DateTime::createFromFormat('d/m/Y h:i a', $end_date);
					$newdatetime = new DateTime($end_date);
					$timesec = '+'.$increase_seconds.' seconds';
					$newdatetime->modify($timesec);
					$new_enddate_str = $newdatetime->format('Y-m-d H:i:s');
					update_post_meta($auctionid, 'end_date', $new_enddate_str);

				}
				sendBidEmailToOtherUsers($auctionid, $userid, $bid_id);
				
				$options = array(
				    'cluster' => 'ap2',
				    'useTLS' => true
				);
				$pusher = new Pusher\Pusher(
				    'e4bbf991aaac16fd100c',
				    '90a7ef5aaf8097720dba',
				    '1761377',
				    $options
				);

				$data['bidid'] 		= $bid_id;
				$data['auctionid'] 	= $auctionid;
				$data['bidamount'] 	= $bidamount;
				$data['location'] 	= $location;
				$data['datetime'] 	= $datetimenow;
				$data['userid'] 	= $userid;
				$data['inctime'] 	= $increase_seconds;
				$pushertr = $pusher->trigger('auction', 'newbid', $data);
				
				$redirectto = get_the_permalink($auctionid);
				$response = array('status' => 'success', 'scredirect' => $redirectto, 'auction_id' => $auctionid, 'userid' => $userid, 'bidid' => $bid_id, 'message' => 'Bid placed succesfully.');
			}else{
				return;
			}
		}else{

			global $wpdb;
			$new_status = 'inactive';
			$wpdb->update(
			    $mxtable_name,
			    array('status' => $new_status),
			    array(
			        'auctionid' => $auctionid,
			        'userid' => $userid
			    ),
			    array('%s'),
			    array('%d', '%d')
			);

			return;

		}
	}
}

add_action('wp_ajax_load_bids_pagination', 'load_bids_pagination');
add_action('wp_ajax_nopriv_load_bids_pagination', 'load_bids_pagination');

function load_bids_pagination() {
    global $wpdb;

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $itemsPerPage = isset($_POST['itemsPerPage']) ? intval($_POST['itemsPerPage']) : 20;
    $auctionid = isset($_POST['auctionid']) ? intval($_POST['auctionid']) : 0;
    $offset = ($page - 1) * $itemsPerPage;

    $table_name = $wpdb->prefix . 'bidhistory'; // Replace with your actual table name

    $query = $wpdb->prepare("
        SELECT *
        FROM $table_name
        WHERE auctionid = %d
        ORDER BY id DESC
        LIMIT %d OFFSET %d", $auctionid, $itemsPerPage, $offset);

    $fullbidinghistory = $wpdb->get_results($query, ARRAY_A);

    $output = '';
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
		$output .= '<tr class="'.$hisclass.'">
						<th>$'.number_format($bidprc).'</th>
						<td>'.$full_name.'</td>
						<td>'.$formatted_date.'</td>
					</tr>';
	}

    echo $output;
    wp_die();
}
