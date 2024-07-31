<?php
add_filter( 'cron_schedules', 'isa_add_every_five_minutes' );
function isa_add_every_five_minutes( $schedules ) {
    $schedules['every_five_minutes'] = array(
        'interval'  => 60 * 2,
        'display'   => __( 'Every 5 Minutes', 'textdomain' )
    );
    return $schedules;
}

if ( ! wp_next_scheduled( 'isa_add_every_five_minutes' ) ) {
    wp_schedule_event( time(), 'every_five_minutes', 'isa_add_every_five_minutes' );
}

add_action( 'isa_add_every_five_minutes', 'every_five_minutes_event_func' );

function every_five_minutes_event_func() {
	$args = array(
	    'post_type'      => 'auction',
	    'posts_per_page' => -1,
	    'post_status'    => 'publish',
	    'meta_query'     => array(
	        array(
	            'key'     => 'end_date',
	            'value'   => current_time('mysql'),
	            'compare' => '<',
	            'type'    => 'DATETIME',
	        ),
	    ),
	);

	$allauctions = get_posts($args);

	foreach ($allauctions as $akey => $auction) {
		$auctionid = $auction->ID;
		global $wpdb;
		$args = array(
		    'post_type'      => 'auctionorders',
		    'posts_per_page' => 1,
		    'meta_key'       => 'auctionid',
		    'meta_value'     => $auctionid,
		    'orderby'        => 'ID',
		    'order'          => 'DESC',
		);

		$orderdata = get_posts($args);
		if($orderdata){

		}else{

			global $wpdb;
			$table_name = $wpdb->prefix . 'bidhistory';
			$query = $wpdb->prepare("
			    SELECT *
			    FROM $table_name
			    WHERE auctionid = %d
			    ORDER BY id DESC
			    LIMIT 1
			", $auctionid);

			$biddata = $wpdb->get_row($query, ARRAY_A);

			if($biddata){
				$maxonline_amount = get_field('maxonline_amount', 'option');
				extract($biddata);
				$paymenttyp ='offline';
				if($bidamount <= $maxonline_amount){
					$paymenttyp ='online';
				}
				$datetimenow = current_time('Y-m-d H:i:s');
				$post_data = array(
				    'post_type'    => 'auctionorders',
				    'post_status'  => 'publish',
				    'post_title'   => 'Order #' . $auctionid,
				);

				$orderid = wp_insert_post($post_data);
				$new_post_data = array(
					'ID'         => $orderid, // The ID of the post you want to update
					'post_title' => 'Order #' . $orderid, // The new title you want to set
				);

				wp_update_post($new_post_data);

				if (!is_wp_error($orderid)) {
					$buyerpremium = get_post_meta($auctionid, 'buyer_premium', true); 
					
					$orderinvoiceid = 'INV/'.current_time('Ymd').'/'.$orderid; 
				    update_post_meta($orderid, 'bidid', $id);
				    update_post_meta($orderid, 'auctionid', $auctionid);
				    update_post_meta($orderid, 'userid', $userid);
				    update_post_meta($orderid, 'amount', $bidamount);
				    update_post_meta($orderid, 'paymenttype', $paymenttyp);
				    update_post_meta($orderid, 'biduser', 1);
				    update_post_meta($orderid, 'status', 'processing');
				    update_post_meta($orderid, 'created', $datetimenow);
				    update_post_meta($orderid, 'orderinvoiceid', $orderinvoiceid);
				    update_post_meta($orderid, 'buyerpremium', $buyerpremium);

				}

				update_post_meta($auctionid, 'orderid', $orderid);
				$senmail = SendAuctionEmail($userid, $orderid);

			}

		}

	}

}

// Second winner assign
add_action( 'isa_add_every_five_minutes', 'assign_second_winner' );
function assign_second_winner() {

	$args = array(
	    'post_type'      => 'auctionorders',
	    'posts_per_page' => -1,
	    'post_status'    => 'publish',
	    'meta_query'     => array(
	        array(
	            'key'     => 'status',
	            'value'   => 'processing',
	            'compare' => '=',
	        ),
	    ),
	);	
	$allauctions = get_posts($args);
	$datetimenow = current_time('Y-m-d H:i:s');
	$diff_days_option = get_field('auction_payment_interval', 'option');
	foreach($allauctions as $key => $value) {
		$aw_date = get_post_meta($value->ID, 'created', true);

		$aw_date_obj = new DateTime($aw_date);
		$datetimenow_obj = new DateTime($datetimenow);
		$interval = $datetimenow_obj->diff($aw_date_obj);
		$days_difference = $interval->days;
		//$minutes_difference = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

		if($days_difference > $diff_days_option) {
		//if($minutes_difference > 5) {

			$auctionid = get_post_meta($value->ID, 'auctionid', true);
			$biduser = get_post_meta($value->ID, 'biduser', true);
			$bidolduserid = get_post_meta($value->ID, 'userid', true);
			$oldorderid = $value->ID;
			update_post_meta($value->ID, 'status', 'cancelled');
			SendAuctionEmail($oldorderid, $bidolduserid, 'ordercancelled');

			global $wpdb;
			$table_name = $wpdb->prefix . 'bidhistory';

			if($biduser == 1) {

				$query = $wpdb->prepare("
				    SELECT *
				    FROM $table_name
				    WHERE auctionid = %d
				    AND bidamount < (
				        SELECT MAX(bidamount)
				        FROM $table_name
				        WHERE auctionid = %d
				    )
				    ORDER BY bidamount DESC
				    LIMIT 1", 
				    $auctionid,
				    $auctionid
				);

				$userorders = $wpdb->get_row($query, ARRAY_A);

				if($userorders) {

					$maxonline_amount = get_field('maxonline_amount', 'option');
					$paymenttyp ='offline';
					if($userorders['bidamount'] <= $maxonline_amount){
						$paymenttyp ='online';
					}		

					$post_data = array(
					    'post_type'    => 'auctionorders',
					    'post_status'  => 'publish',					    
					    'post_title'   => 'Order #' . $auctionid,
					);

					$orderid = wp_insert_post($post_data);

					$new_post_data = array(
						'ID'         => $orderid, 
						'post_title' => 'Order #' . $orderid,
					);					
					
					wp_update_post($new_post_data);

					if (!is_wp_error($orderid)) {
						
						$orderinvoiceid = 'INV/'.current_time('Ymd').'/'.$orderid; 

					    update_post_meta($orderid, 'bidid', $userorders['id']);
					    update_post_meta($orderid, 'auctionid', $auctionid);
					    update_post_meta($orderid, 'userid', $userorders['userid']);
					    update_post_meta($orderid, 'amount', $userorders['bidamount']);
					    update_post_meta($orderid, 'paymenttype', $paymenttyp);
					    update_post_meta($orderid, 'biduser', 2);
					    update_post_meta($orderid, 'status', 'processing');
					    update_post_meta($orderid, 'created', $datetimenow);
					    update_post_meta($orderid, 'orderinvoiceid', $orderinvoiceid);
					}

					update_post_meta($auctionid, 'orderid', $orderid);

					SendAuctionEmail($userorders['userid'], $orderid);
					
				}
			} 
			elseif($biduser == 2){
				$query = $wpdb->prepare("
				    SELECT *
				    FROM $table_name AS t1
				    WHERE auctionid = %d
				    AND bidamount < (
				        SELECT MAX(bidamount)
				        FROM $table_name AS t2
				        WHERE t2.auctionid = %d
				    )
				    AND userid NOT IN (
				        SELECT userid
				        FROM (
				            SELECT userid
				            FROM $table_name AS t3
				            WHERE t3.auctionid = %d
				            ORDER BY bidamount DESC
				            LIMIT 2
				        ) AS subquery
				    )
				    ORDER BY bidamount DESC
				    LIMIT 1", 
				    $auctionid,
				    $auctionid,
				    $auctionid
				);
				$userorders = $wpdb->get_row($query, ARRAY_A);

				if($userorders) {

					$maxonline_amount = get_field('maxonline_amount', 'option');
					$paymenttyp ='offline';
					if($userorders['bidamount'] <= $maxonline_amount){
						$paymenttyp ='online';
					}		

					$post_data = array(
					    'post_type'    => 'auctionorders',
					    'post_status'  => 'publish',					    
					    'post_title'   => 'Order #' . $auctionid,
					);

					$orderid = wp_insert_post($post_data);

					$new_post_data = array(
						'ID'         => $orderid, 
						'post_title' => 'Order #' . $orderid,
					);					
					
					wp_update_post($new_post_data);

					if (!is_wp_error($orderid)) {
						
						$orderinvoiceid = 'INV/'.current_time('Ymd').'/'.$orderid; 

					    update_post_meta($orderid, 'bidid', $userorders['id']);
					    update_post_meta($orderid, 'auctionid', $auctionid);
					    update_post_meta($orderid, 'userid', $userorders['userid']);
					    update_post_meta($orderid, 'amount', $userorders['bidamount']);
					    update_post_meta($orderid, 'paymenttype', $paymenttyp);
					    update_post_meta($orderid, 'biduser', 3);
					    update_post_meta($orderid, 'status', 'processing');
					    update_post_meta($orderid, 'created', $datetimenow);
					    update_post_meta($orderid, 'orderinvoiceid', $orderinvoiceid);
					}

					update_post_meta($auctionid, 'orderid', $orderid);

					SendAuctionEmail($userorders['userid'], $orderid);
					
				}
			}

		}

	}
}

add_action( 'isa_add_every_five_minutes', 'watchlist_auction_mail' );
function watchlist_auction_mail() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'watchlist';
    $current_user_id = get_current_user_id();
	$get_userdata = get_userdata($current_user_id);
	$first_name = $get_userdata->first_name;
   	$get_result = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = '" . $current_user_id . "'");
    if($get_result) { 
    	foreach ($get_result as $key => $value) {
    		$auction_id = $value->auction_id; 
	        $title = get_the_title($auction_id);
	        if($title) : 
	        	$endintime_st = get_field('end_date', $auction_id);
                $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endintime_st)));

                $endin_timestamp = strtotime($endin_time_formatted);

                $startdate_st = get_field('start_date', $auction_id);
                $start_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startdate_st)));
                $start_timestamp = strtotime($start_time_formatted);

                $current_timestamp = current_time('timestamp', true); 
                 if($start_timestamp == $current_timestamp){
                	$subject = "Bid Now: Auction Is Live!";
                	$message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">The highly anticipated '.$title.' is now open for bidding! Take this opportunity to bid on your wishlist items and make a difference through your contributions.</p>';
		            $user_mail_sent = passionAuctionEmail($current_user_id, $subject, $message);
                }

	        endif;
    	}
    }
}

add_filter( 'cron_schedules', 'check_every_minute_for_autobid' );
function check_every_minute_for_autobid( $schedules ) {
    $schedules['every_one_minute'] = array(
        'interval'  => 60,
        'display'   => __( 'Every 1 Minute', 'auction' )
    );
    return $schedules;
}

if ( ! wp_next_scheduled( 'check_every_minute_for_autobid' ) ) {
    wp_schedule_event( time(), 'every_one_minute', 'check_every_minute_for_autobid' );
}

//add_action('shutdown', 'testautobidcode');
add_action( 'check_every_minute_for_autobid', 'checkForAuctionAutobidInMinute' );
function checkForAuctionAutobidInMinute(){
	global $wpdb;
    $mxtable_name = $wpdb->prefix . 'autobids';

    $mxquery = $wpdb->prepare(
        "SELECT * FROM $mxtable_name WHERE status = %s",
        'active'
    );
    $mxresults = $wpdb->get_results($mxquery, ARRAY_A);

    $max_bid_function = get_field('max_bid_function', 'option');

    $grouped_results = array();
	foreach ($mxresults as $result) {
	    $auctionidgrp = $result['auctionid'];
	    if (!isset($grouped_results[$auctionidgrp])) {
	        $grouped_results[$auctionidgrp] = array();
	    }
	    $grouped_results[$auctionidgrp][] = $result;
	}

	foreach ($grouped_results as &$inner_array) {
	    usort($inner_array, function($a, $b) {
	        return $b['amount'] - $a['amount'];
	    });
	}
	unset($inner_array);

	foreach ($grouped_results as $auctionid => $aumaxdata) {

		$increase_livebid = get_field('increase_live_bid_amount', $auctionid);
        $increase_showhand = get_field('increase_show_hand_amount', $auctionid);
        
        $end_date_str = get_field('end_date', $auctionid);
        $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $end_date_str)));
        $end_datetime = strtotime($endin_time_formatted);
        $showhandtimebefore = !empty(get_field('showhand_time_before', $auctionid)) ? get_field('showhand_time_before', $auctionid) : 10;
        $current_datetime = current_time('timestamp');
        $remaining_time = $end_datetime - $current_datetime;
        $remaining_minutes = $remaining_time / 60;
        
        if ($remaining_time && $remaining_minutes <= $showhandtimebefore) {

            $showhandinput = 'inactive';
            foreach ($aumaxdata as $otkey => $otherusersmaxdata) {
				$new_status = 'inactive';
				$wpdb->update(
				    $mxtable_name,
				    array('status' => $new_status),
				    array(
				        'auctionid' => $otherusersmaxdata['auctionid'],
				        'userid' => $otherusersmaxdata['userid']
				    ),
				    array('%s'),
				    array('%d', '%d')
				);
			}
			continue;
        }

		if(count($aumaxdata) > 1){
			$nextbidamount = $aumaxdata[1]['amount'];
		}else{
			$nextbidamount = '';
		}
		$userid = $aumaxdata[0]['userid'];
		
	    $maxbidamount = $aumaxdata[0]['amount'];
	    unset($aumaxdata[0]);
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

			if($nextbidamount && $bidamount < $nextbidamount){
				$bidamount = $nextbidamount;
			}

			if($bidamount < $maxbidamount && $max_bid_function != 'disable'){

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

					if($aumaxdata){
						foreach ($aumaxdata as $otkey => $otherusersmaxdata) {
							
							$new_status = 'inactive';
							$wpdb->update(
							    $mxtable_name,
							    array('status' => $new_status),
							    array(
							        'auctionid' => $otherusersmaxdata['auctionid'],
							        'userid' => $otherusersmaxdata['userid']
							    ),
							    array('%s'),
							    array('%d', '%d')
							);
						}
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
}