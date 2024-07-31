<?php
//add_action('shutdown', 'sendBidEmailToOtherUserstest');
function sendBidEmailToOtherUsers($auctionid, $dibuserid, $lastbidid)
{  
   global $wpdb;
   $table_name = $wpdb->prefix . 'bidhistory';
   $query = $wpdb->prepare("
       SELECT *
       FROM $table_name
       WHERE auctionid = %d", $auctionid);

   $results = $wpdb->get_results($query, ARRAY_A);
   
   $unique_userids = [];
   foreach ($results as $result) {
       if ($result["userid"] != $dibuserid && !isset($unique_userids[$result["userid"]])) {
           $unique_userids[$result["userid"]] = true;
       }
   }
   $unique_userids = array_keys($unique_userids);

   if($unique_userids){
      foreach ($unique_userids as $userid) {
         $user = get_userdata($userid);
         if ($user) {
            $first_name = get_user_meta($userid, 'first_name', true);
            $last_name = get_user_meta($userid, 'last_name', true);
              
            if (!empty($first_name) && !empty($last_name)) {
               $username = $first_name . ' ' . $last_name;
            } else {
               $username = $user->display_name;
            }
            $user_email = $user->user_email;
               
            $bid_name = get_the_title($auctionid);
            $bid_link = get_the_permalink($auctionid); 

            $subject = "New bid on auction you previously bid on";
            $message = '<!DOCTYPE html>
                           <html>
                              <head>
                                  <meta charset="utf-8">
                                  <meta name="viewport" content="width=device-width, initial-scale=1">
                                  <title>New Bid on Auction</title>
                                  <link rel="preconnect" href="https://fonts.googleapis.com">
                                  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                                  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                              </head>
                               <body style="margin: 0;padding: 20px; background: #fff;font-family: \'Noto Sans\', sans-serif;">
                                  <table style="margin: 0 auto;max-width: 540px;background: #fff;padding: 20px;">
                                      <thead>        
                                          <tr>
                                              <th style="text-align: center;padding: 20px; border-bottom: solid 1px #ddd;"><img src="'.site_url().'/wp-content/uploads/2024/04/site-email-logo.png"></th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <tr>
                                              <td style="padding:0 15px">
                                                   <h5 style="color: #0D8080; margin:10px 0 10px;">Dear '.$username.'</h5>
                                                   <p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">A new bid has been placed on the <a style="color: #0D8080;text-decoration: none;font-weight: 800;font-size: 16px;" href='.$bid_link.'>'.$bid_name.'</a> auction you previously bid on.</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Please log in to see the details.</p><a href="'.site_url('login').'" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">Login Now</a>
                                                  <p style="font-size: 16px;margin:15px 0 25px;font-family: \'Noto Sans\', sans-serif;">- Passions Auction</p>
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
               wp_mail($user_email, $subject, $message, $headers);
          }
      }
   }
}


function SendAuctionEmail($user, $order, $type =''){
   $userdata = get_userdata($user);
   $ordermeta = get_post_meta($order);
   $full_name = $userdata->first_name .' '.$userdata->last_name;
   $email = $userdata->user_email;
   $auctionid = $ordermeta['auctionid'][0];
   $orderimg = get_the_post_thumbnail_url($auctionid, 'full'); 
   $ordertitle = get_the_title($auctionid); 
   $orderamount = $ordermeta['amount'][0];
   $link = get_the_permalink($auctionid);
   if($orderamount) {
      $orderamount = number_format($orderamount, 2);
   }
   $paymentdays = get_field('auction_payment_interval', 'option');
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
                  <body style="margin: 0;padding: 20px; background: #fff;font-family: \'Noto Sans\', sans-serif;">
                      <table style="margin: 0 auto;max-width: 540px;background: #fff;padding: 20px;">
                          <thead>        
                              <tr>
                                  <th style="text-align: center;padding: 20px; border-bottom: solid 1px #ddd;"><img src="'.site_url().'/wp-content/uploads/2024/04/site-email-logo.png"></th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td style="padding:0 15px">
                                       <h5 style="color: #0D8080; margin:10px 0 10px;">Dear '.$full_name.'</h5>
                                       <p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Congratulations! Your bid for the item <a href="'.$link.'" target="_blank" style="color: #222;text-decoration: none;"><strong>'.$ordertitle.'</strong></a> has won the auction.</p>
                                       <p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">The winning bid amount is <strong>$'.$orderamount.'</strong>.</p>
                                       <a target="_blank" style="text-decoration: none;" href="'.$link.'"><img border="0" vspace="0" hspace="0" src="'.$orderimg.'" title="Hero Image" width="560" style=" width: 100%; max-width: 560px; color: #000000; font-size: 13px; margin: 0; padding: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;"/></a>';
                                       if($type != 'paymentsuccess' && $type != 'ordercancelled'){
                                          $html .= '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Please complete your payment within '.$paymentdays.' days.</p>';
                                       } else if($type == 'ordercancelled'){
                                          $html .= '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Your order has been cancelled as we did not receive payment for your winning auction within '.$paymentdays.' days.</p>';
                                       }
                                       $html .= '<a href="'.site_url().'/dashboard/order-history/" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">View Order</a><p style="font-size: 16px;margin:15px 0 25px;font-family: \'Noto Sans\', sans-serif;">- Passions Auction</p>';
                                    $html .= '</td>
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

   //$to = 'wordpressdeveloper.his@gmail.com';
 	$to = $email;
   if($type == 'paymentsuccess'){
      $subject = 'Thank You for Your Payment: "'.$ordertitle.'"';
   }elseif($type == 'ordercancelled'){
      $subject = 'Important: Your Order Cancelled - Payment Not Received for Winning Auction';
   }else{
   	$subject = 'Congratulations! You\'ve Secured "'.$ordertitle.'" in the Auction ';
   }
   
	$headers = "MIME-Version: 1.0" . "\r\n";
   $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
   $from_email = 'Wordpress@' . $_SERVER['SERVER_NAME'];
   $headers .= 'From: '.get_bloginfo().' <' . $from_email . '>' . "\r\n";

	$emailstatus = wp_mail( $to, $subject, $html, $headers );
	return $emailstatus;
}

function sendBidRegisterEmail($userid, $subject, $message){
   $user = get_userdata($userid);
   if ($user) {
         $first_name = get_user_meta($userid, 'first_name', true);
       $last_name = get_user_meta($userid, 'last_name', true);
       $user_email = $user->user_email;
       if (!empty($first_name) && !empty($last_name)) {
           $username = $first_name . ' ' . $last_name;

       } else {
           $username = $user->display_name;
       }

      $bodyhtml = '<html>
            <head>
            <title>New Bid on Auction</title>
         </head>
         <body>
            <p style="font-size: 16px;">Dear '.$username.',</p>
            '.$message.'
            <p style="font-size: 15px;">Thank you.</p>
         </body>
         </html>';
      }
      $headers[] = 'Content-Type: text/html; charset=UTF-8';
      wp_mail($user_email, $subject, $bodyhtml, $headers);
}


function passionAuctionEmail($userid = 0, $subject, $message) {

   $userdata = get_userdata($userid);
   $first_name = $userdata->first_name;
   $last_name = $userdata->last_name;
   if($userid != 0 && $userdata !== false) {
      $name = $userdata->first_name .' '.$userdata->last_name;
      $email = $userdata->user_email;
   } else {
      $name = 'Admin';
      $email = get_option('admin_email');
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
                  <body style="margin: 0;padding: 20px; background: #fff;font-family: \'Noto Sans\', sans-serif;">
                      <table style="margin: 0 auto;max-width: 540px;background: #fff;padding: 20px;">
                          <thead>        
                              <tr>
                                  <th style="text-align: center;padding: 20px; border-bottom: solid 1px #ddd;"><img src="'.site_url().'/wp-content/uploads/2024/04/site-email-logo.png"></th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td style="padding:0 15px">
                                       <h5 style="color: #0D8080; margin:10px 0 10px;">Dear '.$name.'</h5>
                                      '.$message.'
                                      <p style="font-size: 16px;margin:15px 0 25px;font-family: \'Noto Sans\', sans-serif;">- Passions Auction</p>
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
      $emailstatus = wp_mail($email, $subject, $html, $headers);
      return $emailstatus;
}