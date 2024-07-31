<?php
function custom_auction_columns($columns) {
    $new_columns = array();

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        if ($key == 'title') {
            $new_columns['auctionbtype'] = 'Bid Registered users';
        }
    }
    
    return $new_columns;
}
add_filter('manage_auction_posts_columns', 'custom_auction_columns');

function custom_auction_column_content($column, $post_id) {
    if ($column == 'auctionbtype') {
        $auction_bidtype = get_field('auction_bidtype', $post_id);
        if($auction_bidtype == 'registerbid'){
            $bidusers_varification = get_field('bidusers_varification', $post_id);
            $allregusers = get_post_meta($post_id, 'bidregusers', true);
            if($allregusers){
                $totalusrs = '('.count($allregusers).')';
            }else{
                $totalusrs = '';
            }
            echo '<a href="admin.php?page=view_bid_users&post_id=' . $post_id . '" class="viewbidregusers">View Users '.$totalusrs.'</a>';
        }
    }
}
add_action('manage_auction_posts_custom_column', 'custom_auction_column_content', 10, 2);

function view_bid_users_page() {
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    echo '<div class="wrap">';
    if (!$post_id) {
        wp_die('<h1 class="cstbltitle">Invalid post ID.</h1>');
    }

    $auction_bidtype = get_field('auction_bidtype', $post_id);
    if ($auction_bidtype != 'registerbid') {
        wp_die('<h1 class="cstbltitle">This auction does not require users to register for bidding; it is a direct bid auction.</h1>');
    }

    $bid_users = get_post_meta($post_id, 'bidregusers', true);

    ?>
        <h1 class="cstbltitle">List of Users Enrolled in the <strong><?php echo get_the_title($post_id); ?></strong> Auction.</h1>
        
        <?php if ($bid_users) : ?>
            <div id="afterresponse" style="display: none;"></div>
            <table class="rwd-table widefat fixed striped custombidtbl">
                <thead>
                    <tr>
                        <th style="width:100px;">Sr. No.</th>
                        <th>User Name</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rowcounter = 1;
                    $bid_users = array_reverse($bid_users, true);
                    foreach ($bid_users as $userid => $buser) : ?>
                        <tr>
                            <td><?php echo $rowcounter++; ?></td>
                            <td><?php echo esc_html($buser['full_name']); ?></td>
                            <td><?php echo esc_html($buser['phone_number']); ?></td>
                            <td><?php echo esc_html($buser['address']); ?></td>
                            <td><span class="inlabel <?php echo esc_html($buser['status']); ?>"><?php echo esc_html($buser['status']); ?></span></td>
                            <td>
                                <?php if ($buser['status'] == 'pending') : ?>
                                    <a class="adminbtnsml btnverify" href="javascript:void(0);" onclick="updateVerificationStatus(<?php echo $userid; ?>, <?php echo $post_id; ?>, 'verify')">Verify</a>
                                    <a class="adminbtnsml btnreject" href="javascript:void(0);" onclick="updateVerificationStatus(<?php echo $userid; ?>, <?php echo $post_id; ?>, 'reject')">Reject</a>
                                <?php elseif ($buser['status'] == 'verified') : ?>
                                    <a class="adminbtnsml btnreject" href="javascript:void(0);" onclick="updateVerificationStatus(<?php echo $userid; ?>, <?php echo $post_id; ?>, 'reject')">Reject</a>
                                    <!-- <a class="adminbtnsml btnpending" href="javascript:void(0);" onclick="updateVerificationStatus(<?php echo $userid; ?>, <?php echo $post_id; ?>, 'pending')">Pending</a> -->
                                <?php elseif ($buser['status'] == 'rejected') : ?>
                                    <a class="adminbtnsml btnverify" href="javascript:void(0);" onclick="updateVerificationStatus(<?php echo $userid; ?>, <?php echo $post_id; ?>, 'verify')">Verify</a>
                                    <!-- <a class="adminbtnsml btnpending" href="javascript:void(0);" onclick="updateVerificationStatus(<?php echo $userid; ?>, <?php echo $post_id; ?>, 'pending')">Pending</a> -->
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <h1 class="cstbltitle">No users have registered for this auction yet.</h1>
        <?php endif; ?>
    </div>
    <?php
}

function add_view_bid_users_page() {
    add_submenu_page(
        'edit.php?post_type=auction',
        'View Bid Users',
        'View Bid Users',
        'manage_options',
        'view_bid_users',
        'view_bid_users_page'
    );
}
add_action('admin_menu', 'add_view_bid_users_page');

//Admin Status update for bid registerd users
add_action('wp_ajax_updateUserVarification', 'updateUserVarification');
add_action('wp_ajax_nopriv_updateUserVarification', 'updateUserVarification');

function updateUserVarification() {
    // Extract POST variables
    extract($_POST);
    $reponse = array();
    if($auctionid && $userid != 0) {    
        $alldata = get_post_meta($auctionid, 'bidregusers', true);
        $auctionName = get_the_title($auctionid);
        $viewAuctionLink = get_the_permalink($auctionid);
        if ($actiontype == 'verify') {
            $alldata[$userid]['status'] = 'verified';
            $successmessgae = 'User verified successfully.';
            
            $emailSubject = 'Your Registration for Bidding has been Verified';
            $emailMessage = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Your registration for bidding on the auction <strong>' . $auctionName . '</strong> has been verified by the admin.</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Thank you for registering.</p><a href="'.$viewAuctionLink.'" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">View Auction</a>';

        } elseif ($actiontype == 'reject') {
            $alldata[$userid]['status'] = 'rejected';
            $successmessgae = 'User verification rejected.';

            $emailSubject = 'Your Registration for Bidding has been Rejected';
            $emailMessage = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">We regret to inform you that your registration for bidding on the auction <strong>' . $auctionName . '</strong> has been rejected by the admin.</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">If you have any queries, please contact us or you may apply again.</p><a href="'.$viewAuctionLink.'" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">View Auction</a>';

        } else {
            $alldata[$userid]['status'] = 'pending';
            $successmessgae = 'User verification changed to pending.';

            $emailSubject = 'Your Registration for Bidding on ' . $auctionName . ' is Pending Verification';
            $emailMessage = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">Your registration for bidding on the auction <strong>' . $auctionName . '</strong> is currently pending verification by the admin.</p><p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">We will notify you once the verification process is completed.</p><a href="'.$viewAuctionLink.'" style="text-decoration: none; font-family: \'Noto Sans\', sans-serif;font-size: 16px;font-weight: 500;line-height: 24px; fill: #FFFFFF;color: #FFFFFF;background-color: #000000; border-style: solid;border-width: 1px 1px 1px 1px;border-color: #000000;border-radius: 4px 4px 4px 4px;padding: 12px 24px 12px 24px;margin: 20px 0; display: inline-block;">View Auction</a>';
        }
        
        update_post_meta($auctionid, 'bidregusers', $alldata);

        passionAuctionEmail($userid, $emailSubject, $emailMessage);
        // die();
        $reponse = array('status' => 'success', 'message'=> $successmessgae);
    }  else {
        $reponse = array('status' => 'error', 'message'=> 'Something went wrong please try again after some time.');
    }
    wp_send_json($reponse); 
    wp_die();
}