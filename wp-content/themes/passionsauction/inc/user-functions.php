<?php 

function custom_sort($a, $b) {
    if ($a['total_duration'] === 'Closed' && $b['total_duration'] === 'Closed') {
        return $a['post_id'] - $b['post_id'];
    }
    if ($a['total_duration'] === 'Closed') {
        return 1;
    } elseif ($b['total_duration'] === 'Closed') {
        return -1;
    }
    $a_timestamp = strtotime($a['total_duration']);
    $b_timestamp = strtotime($b['total_duration']);
    return $a_timestamp - $b_timestamp;
}
function sortByLabel($a, $b) {
    $order = array('Ongoing', 'Upcoming', 'Past');
    $aOrder = array_search($a['label'], $order);
    $bOrder = array_search($b['label'], $order);
    return $aOrder - $bOrder;
}
/* auction filter */
add_action( 'wp_ajax_pagination_load_auctions', 'pagination_load_auctions' );
add_action( 'wp_ajax_nopriv_pagination_load_auctions', 'pagination_load_auctions' ); 

function pagination_load_auctions(){

	if(isset($_POST['page'])){

        $page = sanitize_text_field($_POST['page']);
        $cur_page = $page;
        
        $page -= 1;
        $per_page = get_field('number_of_posts', 'option') ? get_field('number_of_posts', 'option') : 3;
        $offset = ($cur_page - 1) * $per_page;
        $previous_btn = true;
        $next_btn = true;
        $first_btn = true;
        $last_btn = true;
        $start = $page * $per_page;
         
        $meta_query = array();
    	$tax_query = array();

        $type = $_POST['type'];
        $category = $_POST['category'];
        $event = $_POST['event'];
        $year = $_POST['year'];
        $minprice = $_POST['minprice'];
    	$maxprice = $_POST['maxprice'];
        $search = sanitize_text_field($_POST['search']);

        $tax_query = array('relation' => 'AND');
        $meta_query = array('relation' => 'AND');   

        $current_time = current_time('timestamp');

        if ($category) {
            $tax_query[] = array(
                'taxonomy' => 'auction-category',
                'field'    => 'id',
                'terms'    => $category,
                'operator' => 'IN',
            );
        }

        if ($event) {
            $tax_query[] = array(
                'taxonomy' => 'auction-event',
                'field'    => 'id',
                'terms'    => $event,
                'operator' => 'IN',
            );
        }   

        if ($year) {
            $tax_query[] = array(
                'taxonomy' => 'auction-year',
                'field'    => 'id',
                'terms'    => $year,
                'operator' => 'IN',
            );
        }

        if ($minprice && $maxprice) {
            $meta_query[] = array(
                'key'     => 'base_price',
                'value'   => array($minprice, $maxprice),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            );
        } elseif ($minprice) {
            $meta_query[] = array(
                'key'     => 'base_price',
                'value'   => $minprice,
                'compare' => '>=',
                'type'    => 'NUMERIC'
            );
        } elseif ($maxprice) {
            $meta_query[] = array(
                'key'     => 'base_price',
                'value'   => $maxprice,
                'compare' => '<=',
                'type'    => 'NUMERIC'
            );
        }

        if(is_user_logged_in()) {
            if ($type == 'new') {
                $meta_query[] = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'start_date',
                        'value'   => date('Y-m-d H:i:s', $current_time),
                        'compare' => '<=',
                        'type'    => 'DATETIME'
                    ),
                    array(
                        'key'     => 'end_date',
                        'value'   => date('Y-m-d H:i:s', $current_time),
                        'compare' => '>=',
                        'type'    => 'DATETIME'
                    )
                );
            } elseif ($type == 'past') {
                $meta_query[] = array(
                    'key'     => 'end_date',
                    'value'   => date('Y-m-d H:i:s', $current_time),
                    'compare' => '<',
                    'type'    => 'DATETIME'
                );
            } elseif ($type == 'upcoming') {
                $meta_query[] = array(
                    'key'     => 'start_date',
                    'value'   => date('Y-m-d H:i:s', $current_time),
                    'compare' => '>=',
                    'type'    => 'DATETIME'
                );
            } 
        } else {
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key'     => 'start_date',
                    'value'   => date('Y-m-d H:i:s', $current_time),
                    'compare' => '<=',
                    'type'    => 'DATETIME'
                ),
                array(
                    'key'     => 'end_date',
                    'value'   => date('Y-m-d H:i:s', $current_time),
                    'compare' => '>=',
                    'type'    => 'DATETIME'
                )
            );
        }

    	

        if($type== 'all' && empty($category) && empty($event) && empty($year) && empty($minprice) && empty($maxprice) && empty($search) && is_user_logged_in()) {
            $post = new WP_Query(
                array(
                    'post_type'         => 'auction',
                    'post_status'       => 'publish',
                    'posts_per_page'    => $per_page,
                    'offset'            => $start,
                    'orderby'           => 'meta_value',
                    'order'             => 'ASC',
                )
            );
            $count = new WP_Query(
                array(
                    'post_type'         => 'auction',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                )
            );
            $all_pst = new WP_Query(
                array(
                    'post_type'         => 'auction',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                )
            );
        } else {
            $post = new WP_Query(
                array(
                    'post_type'         => 'auction',
                    'post_status'       => 'publish',
                    'posts_per_page'    => $per_page,
                    'offset'            => $start,
                    's'                 => $search,
                    'orderby'           => 'meta_value',
                    'order'             => 'ASC',
                    'meta_query'        => $meta_query,
                    'tax_query'         => $tax_query,               
                )
            );
            $count = new WP_Query(
                array(
                    'post_type'         => 'auction',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                    's'                 => $search,
                    'meta_query'        => $meta_query,
                    'tax_query'         => $tax_query,
                )
            );
            $all_pst = new WP_Query(
                array(
                    'post_type'         => 'auction',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                    's'                 => $search,
                    'meta_query'        => $meta_query,
                    'tax_query'         => $tax_query,
                )
            );
        }
        $open_posts = array();
        $closed_posts = array();

        $counter = $count->post_count;
        // echo '<pre>';
        //     print_r($post);
        // echo '</pre>';
        if($all_pst->have_posts()) {
            $all_posts_mine = array();
            
            while ( $all_pst->have_posts()) {
                $all_pst->the_post();
                $postid = get_the_ID(); 
                
                $image = get_the_post_thumbnail_url($postid); 
				$base_price = get_field('base_price', $postid);

				$end_date_str = get_field('end_date', $postid);
                $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $end_date_str)));
                $end_datetime = strtotime($endin_time_formatted);

                $start_date_str = get_field('start_date', $postid);
                $start_dateformatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $start_date_str)));
                $str_datetime = strtotime($start_dateformatted);

                $current_date = current_time('timestamp');

                $aulabel = checkAuctionStatus($str_datetime, $end_datetime, $current_date);

                if($type == 'upcoming' || $aulabel == 'Upcoming') {
                    if (!$start_date_str) {
                       $total_duration = '';
                    } else {
                        $interval = $str_datetime - $current_time;
                        $days = floor($interval / (60 * 60 * 24));
                        $hours = floor(($interval % (60 * 60 * 24)) / (60 * 60));
                        $minutes = floor(($interval % (60 * 60)) / 60);
                        if ($days >= 0 || $hours >= 0 || $minutes > 0) {
                            $total_duration = sprintf('%d days %d hours %d minutes', $days, $hours, $minutes);
                        }
                    }
                } else {
                    if (!$end_date_str) {
                       $total_duration = '';
                    } else {
                        $interval = $end_datetime - $current_time;
                        $days = floor($interval / (60 * 60 * 24));
                        $hours = floor(($interval % (60 * 60 * 24)) / (60 * 60));
                        $minutes = floor(($interval % (60 * 60)) / 60);
                        if ($days >= 0 || $hours >= 0 || $minutes > 0) {
                            $total_duration = sprintf('%d days %d hours %d minutes', $days, $hours, $minutes);
                        } else {
                            $total_duration = 'Closed';
                        }
                    }
                }
                if($aulabel == 'Ongoing') {
                    $all_posts_mine['ongoing'][] =  array(
                        'post_id' => $postid,
                        'image' => $image,
                        'title' => get_the_title(),
                        'base_price' => $base_price,
                        'total_duration' => $total_duration,
                        'label' => $aulabel,
                    );    
                } else if($aulabel == 'Upcoming') {
                    $all_posts_mine['upcoming'][] =  array(
                        'post_id' => $postid,
                        'image' => $image,
                        'title' => get_the_title(),
                        'base_price' => $base_price,
                        'total_duration' => $total_duration,
                        'label' => $aulabel,
                    );
                } else {
                    $all_posts_mine['past'][] =  array(
                        'post_id' => $postid,
                        'image' => $image,
                        'title' => get_the_title(),
                        'base_price' => $base_price,
                        'total_duration' => $total_duration,
                        'label' => $aulabel,
                    );

                }
            }
        }
        if(!empty($all_posts_mine)) {
            if(empty($all_posts_mine['ongoing'])) {
                $all_posts_mine['ongoing'] = array();
            }
            if(empty($all_posts_mine['upcoming'])) {
                $all_posts_mine['upcoming'] = array();
            }
            if(empty($all_posts_mine['past'])) {
                $all_posts_mine['past'] = array();
            }
            $all_post = array_merge($all_posts_mine['ongoing'], $all_posts_mine['upcoming'], $all_posts_mine['past']);            
            usort($all_post, 'sortByLabel');


            $total_posts = count($all_post);
            $total_pages = ceil($total_posts / $per_page);
            $all_post_paginated = array_slice($all_post, $offset, $per_page);
            // echo 'Hello<pre>';
            // print_r($all_post);
            // echo '</pre>';
        }


        if ( $all_pst->have_posts() ) {
            // while ( $post->have_posts() ) {
            //     $post->the_post();
            //     $postid = get_the_ID(); 
            //     $image = get_the_post_thumbnail_url($postid); 
			// 	$base_price = get_field('base_price', $postid);

			// 	$end_date_str = get_field('end_date', $postid);
            //     $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $end_date_str)));
            //     $end_datetime = strtotime($endin_time_formatted);

            //     $start_date_str = get_field('start_date', $postid);
            //     $start_dateformatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $start_date_str)));
            //     $str_datetime = strtotime($start_dateformatted);

            //     $current_date = current_time('timestamp');

            //     $aulabel = checkAuctionStatus($str_datetime, $end_datetime, $current_date);

            //     if($type == 'upcoming' || $aulabel == 'Upcoming') {
            //         if (!$start_date_str) {
            //            $total_duration = '';
            //         } else {
            //             $interval = $str_datetime - $current_time;
            //             $days = floor($interval / (60 * 60 * 24));
            //             $hours = floor(($interval % (60 * 60 * 24)) / (60 * 60));
            //             $minutes = floor(($interval % (60 * 60)) / 60);
            //             if ($days >= 0 || $hours >= 0 || $minutes > 0) {
            //                 $total_duration = sprintf('%d days %d hours %d minutes', $days, $hours, $minutes);
            //             }
            //         }
            //     } else {
            //         if (!$end_date_str) {
            //            $total_duration = '';
            //         } else {
            //             $interval = $end_datetime - $current_time;
            //             $days = floor($interval / (60 * 60 * 24));
            //             $hours = floor(($interval % (60 * 60 * 24)) / (60 * 60));
            //             $minutes = floor(($interval % (60 * 60)) / 60);
            //             if ($days >= 0 || $hours >= 0 || $minutes > 0) {
            //                 $total_duration = sprintf('%d days %d hours %d minutes', $days, $hours, $minutes);
            //             } else {
            //                 $total_duration = 'Closed';
            //             }
            //         }
            //     }

            //     if ($total_duration && $type == 'all') {
            //         if ($total_duration === 'Closed') {
            //             $closed_posts[] = array(
            //                 'post_id' => $postid,
            //                 'image' => $image,
            //                 'title' => get_the_title(),
            //                 'base_price' => $base_price,
            //                 'total_duration' => $total_duration,
            //                 'label' => $aulabel,
            //             );
            //         } else {
            //             $open_posts[] = array(
            //                 'post_id' => $postid,
            //                 'image' => $image,
            //                 'title' => get_the_title(),
            //                 'base_price' => $base_price,
            //                 'total_duration' => $total_duration,
            //                 'label' => $aulabel,
            //             );
            //         }
            //     } elseif($total_duration) {
            //         $open_posts[] = array(
            //             'post_id' => $postid,
            //             'image' => $image,
            //             'title' => get_the_title(),
            //             'base_price' => $base_price,
            //             'total_duration' => $total_duration,
            //             'label' => $aulabel,
            //         );
            //     }
            // }
                  if ($type !== 'all') {
                    usort($open_posts, 'custom_sort');
                    foreach ($all_post_paginated as $post_data) {
                        $letestdata = getLetestBidData($post_data['post_id']);
                        if($letestdata){
                            $letestbid = $letestdata->bidamount; 
                        }else{
                            $letestbid = $post_data['base_price'];
                        }
                        $bidcount = getTotalBidcount($post_data['post_id']);
                        ?>
                        <div id="auction-<?php echo $type.'-'.$post_data['post_id'];?>" class="col-12 col-md-6 col-lg-4">
                            <a href="<?php echo get_permalink($post_data['post_id']); ?>" class="listitem">
                                <div class="productimg">
                                    <?php if($post_data['label']){
                                        echo '<span class="aulabel '.$post_data['label'].'">'.$post_data['label'].'</span>';
                                    } ?>
                                    <img class="img-fluid" src="<?php if($post_data['image']) { echo $post_data['image']; } else { echo get_template_directory_uri().'/images/default.png';} ?>" />
                                    <?php if($post_data['total_duration'] && $type !== 'past') { ?>
                                        <div class="duration">
                                            <?php echo $post_data['total_duration']; ?>    
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="product-desc">
                                    <div class="product-title"><?php echo $post_data['title']; ?></div>
                                    <div class="product-price-bid">
                                        <p class="highest-bid">Highest bid</p>
                                        <?php if($letestbid) { ?><div class="price">$<span class="livebidprice"><?php echo number_format($letestbid); ?></span></div><?php } ?>
                                        <?php if($bidcount){ ?>
                                            <div class="bids"><span class="bidcount"><?php echo $bidcount; ?></span> bids</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                } else {

                    $all_posts = array_merge($open_posts, $closed_posts);
                    usort($all_posts, 'custom_sort');
                    foreach ($all_post_paginated as $post_data) {

                        $letestdata = getLetestBidData($post_data['post_id']);
                        if($letestdata){
                            $letestbid = $letestdata->bidamount; 
                        }else{
                            $letestbid = $post_data['base_price'];
                        }
                        $bidcount = getTotalBidcount($post_data['post_id']);

                        ?>
                        <div id="auction-<?php echo $type.'-'.$post_data['post_id'];?>" class="col-12 col-md-6 col-lg-4">
                            <a href="<?php echo get_permalink($post_data['post_id']); ?>" class="listitem">
                                <div class="productimg">
                                    <?php if($post_data['label']){
                                        echo '<span class="aulabel '.$post_data['label'].'">'.$post_data['label'].'</span>';
                                    } ?>
                                    <img class="img-fluid" src="<?php if($post_data['image']) { echo $post_data['image']; } else { echo get_template_directory_uri().'/images/default.png';} ?>" />
                                    <?php if($post_data['total_duration']) { ?>
                                        <div class="duration">
                                            <?php echo $post_data['total_duration']; ?>    
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="product-desc">
                                    <div class="product-title"><?php echo $post_data['title']; ?></div>
                                    <div class="product-price-bid">
                                        <p class="highest-bid">Highest bid</p>
                                        <?php if($letestbid) { ?><div class="price">$<span class="livebidprice"><?php echo number_format($letestbid); ?></span></div><?php } ?>
                                        <?php if($bidcount){ ?>
                                            <div class="bids"><span class="bidcount"><?php echo $bidcount; ?></span> bids</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                }
        } else { ?>
            <div class="noposts">Sorry, No auctions found.</div><?php
        }

        // pagination
        // $no_of_paginations = ceil($counter / $per_page);
        $no_of_paginations = $total_pages;
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
                        <li p='<?php echo $pre; ?>' class='active' onclick="loadmoreauctions(this);">
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
                            <li p='<?php echo $i; ?>' class='active' onclick="loadmoreauctions(this);"><?php echo $i; ?></li><?php
                        }
                    }

                    if ($next_btn && $cur_page < $no_of_paginations) {
                        $nex = $cur_page + 1; ?>
                        <li p='<?php echo $nex; ?>' class='active' onclick="loadmoreauctions(this);">
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

/* auction watchlist */
add_action( 'wp_ajax_auction_watchlist', 'auction_watchlist' );
add_action( 'wp_ajax_nopriv_auction_watchlist', 'auction_watchlist' ); 
function auction_watchlist(){
    extract($_POST);
    $response = array();
    global $wpdb;
    $table_name = $wpdb->prefix . 'watchlist';
    $created = date('Y-m-d H:i:s');

    $auctionurl = get_the_permalink($postid);
    if(!is_user_logged_in()){
        $expiration = time() + 900;
        $cookie_path = '/';
        $cookie_domain = $_SERVER['HTTP_HOST'];
        $cookie_name = 'redirect';
        $cookie_value = $auctionurl;
        setcookie( $cookie_name, $cookie_value, $expiration, $cookie_path, $cookie_domain );
        $redirecturl = site_url('/login/');
        $response = array('status' => 'failed', 'redirect' => $redirecturl, 'message' => 'Please log in to bid. You will be redirected to the login page shortly.');
        wp_send_json($response);
        wp_die();
    }else{
        $watchlist_args = array(
            'user_id'  => $userid,
            'auction_id'   => $postid,
            'created' =>$created
        );
        $insert_watchlist = $wpdb->insert($table_name,$watchlist_args);
        if($insert_watchlist){
            $url = site_url('/dashboard/watchlist/');
            $response = array('status'=>'success', 'url'=>$url);
        }else{
            $response = array('status'=>'failed');
        }
    }
    wp_send_json($response);
    wp_die();
}

/* delete auction watchlist */
add_action( 'wp_ajax_auction_delete_watchlist', 'auction_delete_watchlist' );
add_action( 'wp_ajax_nopriv_auction_delete_watchlist', 'auction_delete_watchlist' ); 
function auction_delete_watchlist(){
    extract($_POST);
    $response = array();
    global $wpdb;
    $table_name = $wpdb->prefix . 'watchlist';

    $delete = $wpdb->query($wpdb->prepare(
        "DELETE FROM $table_name WHERE user_id = %d AND auction_id = %d",
        $userid,
        $postid
    ));

    if ($delete !== false) {
        $response = array('status'=>'success');
    } else {
        $response = array('status'=>'failed');
    }

    wp_send_json($response);
    wp_die();
}