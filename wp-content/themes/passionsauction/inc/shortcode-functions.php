<?php
add_shortcode('account_log', 'login_sign_up');
function login_sign_up(){
	ob_start();	
	$menu_name = 'User Menu';
	$menu_object = wp_get_nav_menu_object($menu_name);
	global $wpdb;
	$table_name = $wpdb->prefix . 'passions_product_cart';	


	if ($menu_object) {
	    $menu_id = $menu_object->term_id;
	    $menu_items = wp_get_nav_menu_items($menu_id);
	    $user_id = get_current_user_id();
	    if ($menu_items) {
	    	if(!wp_is_mobile()){
		    	echo '<div class="loginsignup-end">';
				if(get_user_meta(get_current_user_id(), 'userstatus', true) == 'accepted' && is_user_logged_in()) {
					$current_user_id = get_current_user_id();	
					$cart_items = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM $table_name WHERE user_id = %d",
							$current_user_id
						)
					);									
					$cart_counter = 0;								
					foreach ($cart_items as $cart_item) {
						$cart_counter += $cart_item->quantity;
					}					
					echo '<div class="header-carticon"><a href="'.home_url( '/cart/').'"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span class="cart-counter">'.$cart_counter.'</span></div>';
				}
		    	$count = 1;
		        foreach ($menu_items as $menu_item) {
		        	$outclass = $menu_item->classes[0];
		        	if($count == 1){
		        		$inclass = 'btn-white';
		        	}else{
		        		$inclass = 'btn-black';
		        	}
		        	if($user_id != 0 && $outclass == 'loggedin'){
		        		echo '<div class="'.$outclass.' menuitem'.$count.'">
			    			<a href="'.$menu_item->url.'" class="btn '.$inclass.'" target="'.$menu_item->target.'">'.$menu_item->title.'</a>
			    		</div>';
		        	}elseif($user_id == 0 && $outclass == 'notlogin'){
		        		echo '<div class="'.$outclass.' menuitem'.$count.'">
			    			<a href="'.$menu_item->url.'" class="btn '.$inclass.'" target="'.$menu_item->target.'">'.$menu_item->title.'</a>
			    		</div>';
		        	}else{

		        	}
		           	
			    	$count++;
		        }
		        echo '</div>';
		    }else{
		    	if($user_id == 0 ){
		    		$userurl = home_url('login');
		    	}else{
		    		$userurl = home_url('dashboard');
		    	}
		        echo '<div class="loginsignup-mobile"> 
		        		<ul>';
		        	echo '<li><a href="'.$userurl.'" class="useraccount desktop"><img src="'.home_url().'/wp-content/uploads/2024/02/user.svg" /></a></li>';
		        		$count = 1;
				        foreach ($menu_items as $menu_item) {
				        	if ($count == 1) {
				        		$count++;
						        continue;
						    }
				        	$outclass = $menu_item->classes[0];
				        	$inclass = 'btn-black';

				        	if($user_id != 0 && $outclass == 'loggedin'){
					    		echo '<li><a href="'.$menu_item->url.'" class="btn '.$inclass.'" target="'.$menu_item->target.'">'.$menu_item->title.'</a></li>';
				        	}elseif($user_id == 0 && $outclass == 'notlogin'){
				        		echo '<li><a href="'.$menu_item->url.'" class="btn '.$inclass.'" target="'.$menu_item->target.'">'.$menu_item->title.'</a></li>';
				        	}else{

				        	}
				           	
					    	$count++;
				        }
				        echo '<li>
		        				<button type="button" class="searchtype" data-modal="myModal1"><img src="'.home_url().'/wp-content/uploads/2024/02/search-1.svg" /></button>
		        				<form class="search-full modal" id="myModal1">
		        					<button type="button" class="closebtn">
			        					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M13 1L1 13" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M1 1L13 13" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</button>
		        					<div class="formdesign">
			        					<input type="search" name="search" placeholder="Search auctions..." />
			        					<button type="submit" class="searchbtn"><img src="'.home_url().'/wp-content/uploads/2024/02/search-1.svg" /></button>
		        					</div>
		        				</form>
		        			</li>';
				        echo '</ul>
		        	</div>';

		    }
	    }
	} 
	
	$content = ob_get_clean();
	return $content;
}

// Header Menu
add_shortcode('header_menubar', 'header_menu');
function header_menu(){
	ob_start();
	$menu_name = 'Mobile Menu';
	$menu_object = wp_get_nav_menu_object($menu_name);
	if ($menu_object) {
	    $menu_id = $menu_object->term_id;
	    $menu_items = wp_get_nav_menu_items($menu_id);
	    $user_id = get_current_user_id();
	    if ($menu_items) {
	    	echo '<div class="header-menu-mobile">';
	    	echo '<div class="top">
	    			<button type="button" class="togglebtn" id="togglebtn-menu">
		    			<span class="togglebar"></span>
		    			<span class="togglebar"></span>
		    			<span class="togglebar"></span>
		    		</button>
		    	</div>';
		    echo '<div class="outer-block"><div class="menuwith-login">';
		    if (!is_user_logged_in()){
		    	echo '<div class="top-wrapper">
					<a href="'.home_url("login").'" class="btn btn-white">Login</a>
    			</div>';	
		    }
		    
	    	echo '<ul class="menublock">';
	    	$count = 1;
	        foreach ($menu_items as $menu_item) {
	        	$outclass = $menu_item->classes[0];
	        	if($count == 1){
	        		$inclass = 'btn-white';
	        	}else{
	        		$inclass = 'btn-black';
	        	}

	        	if($user_id == 0 ){
		    		$userurl = home_url('login');
		    	}else{
		    		$userurl = home_url('dashboard');
		    	}

	        	if($user_id != 0 && $outclass == 'loggedin'){
	        		echo '<li><a href="'.$userurl.'" class="useraccount"><img src="'.home_url().'/wp-content/uploads/2024/02/user.svg" /></a></li>';
	        		echo '<li class="'.$outclass.' menuitem'.$count.'">
	        				<a href="'.$menu_item->url.'" class="btn '.$inclass.'" target="'.$menu_item->target.'">'.$menu_item->title.'</a>
	        			 </li>';
	        	}elseif($user_id == 0 && $outclass == 'notlogin'){
	        		echo '<li><a href="'.$userurl.'" class="useraccount"><img src="'.home_url().'/wp-content/uploads/2024/02/user.svg" /></a></li>';
	        		echo '<li class="'.$outclass.' menuitem'.$count.'">
	        				<a href="'.$menu_item->url.'" class="btn '.$inclass.'" target="'.$menu_item->target.'">'.$menu_item->title.'</a>
	        			 </li>';
	        	}elseif($outclass == ''){
	        		echo '<li class="'.$outclass.' menuitem'.$count.'">
	        				<a href="'.$menu_item->url.'" target="'.$menu_item->target.'">'.$menu_item->title.'</a>
	        			 </li>';
	        	}
	           	
		    	$count++;
	        }
	        echo '</ul>';
	        echo do_shortcode('[gtranslate]');
	        echo '</div></div>';
	    }
	} 

	$content = ob_get_clean();
	return $content;
	
}

// home banner slider
add_shortcode('homebanner', 'homebanner_slider');
function homebanner_slider()
{
	ob_start(); 
	$post = new WP_Query(
            array(
                'post_type'         => 'auction',
                'post_status'       => 'publish',
                'posts_per_page'    => 6,
                'orderby'           => 'post_date',
                'order'             => 'DESC',
                'meta_query'        => array( 
                	array(
				        'key'     => 'featured_auction',
				        'value'   => 1,
				        'compare' => '=',
				    )
            	),
            )
        ); 
        if ( $post->have_posts() ) : ?>
		<section class="homebanner-slider">
		    <div class="bannerslider">
		    	<div class="owl-carousel owl-theme" id="bannerslide"><?php 
		    	while ( $post->have_posts() ) {
                	$post->the_post();
                	$postid = get_the_ID(); 
	                $image = get_the_post_thumbnail_url($postid); ?>
					    <div class="item">
					    	<div class="sliderblock-wrapper">
					    		<div class="content-left">
					    			<div class="content-leftblock">
					    				<div class="subtitle">Auction</div>
						    			<h2><?php the_title(); ?></h2>
						    			<p><?php echo get_the_excerpt(); ?></p>
						    			<a href="<?php the_permalink(); ?>" class="btn btn-white">Browse Auction</a>	
					    			</div>
					    		</div>
					    		<div class="imgblock-wrapper"><?php 
					    		if($image) {
					    			echo '<img src="'.$image.'" alt="slider-img">';
					    		} else {
					    			echo '<img src="'.get_template_directory_uri().'/images/default.png" alt="slider-img">';
					    		} ?>
					    		</div>
					    	</div>	
					    </div><?php 
					} ?>
				</div>
		    </div>
		</section><?php
	endif; 
    	$content = ob_get_clean();
    	return $content;
		
	}

// subscribe_form
add_shortcode('subscribe_form', 'subsription_letter');
function subsription_letter()
{
	ob_start(); 
	$sub_image = get_field('sub_image', 12);
	$sub_content = get_field('sub_content', 12);
	$sub_form = get_field('sub_form', 12); ?>
    	<section class="subscribe-letter">
    		<div class="customcontainer">
    			<div class="subscribe-letter-form">
    				<div class="sideimg">
    					<img src="<?php echo esc_url($sub_image['url']); ?>" alt="<?php echo esc_attr($sub_image['alt']); ?>" />
    				</div>
    				<div class="form-content">
    					<p><?php echo $sub_content; ?></p>
    					<?php echo $sub_form; ?>
    				</div>
    			</div>
    		</div>
    	</section>
<?php
    	$content = ob_get_clean();
    	return $content;
	
}


//On Going Auction

add_shortcode('ongoing_products', 'ongoing_auction');
	function ongoing_auction()
	{
		ob_start();
		$current_time = current_time('timestamp', true);
		$post = new WP_Query(
            array(
                'post_type'         => 'auction',
                'post_status'       => 'publish',
                'posts_per_page'    => 6,
                'orderby'           => 'meta_value',
                'order'             => 'DESC',
                'meta_query'     => array(
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
		        ),
            )
        );
        if ( $post->have_posts() ) { ?>
	    	<section class="ongoing-product">
    			<div class="owl-carousel owl-theme" id="ongoingpro"><?php 
    			while ( $post->have_posts() ) {
                	$post->the_post();
                	$postid = get_the_ID(); 
	                $image = get_the_post_thumbnail_url($postid); 
					$base_price = get_field('base_price', $postid);

					$end_date_str = get_field('end_date', $postid);
	                $endin_time_formatted = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $end_date_str)));
	                $end_datetime = strtotime($endin_time_formatted);

					if (!$end_date_str) {
					   $total_duration = '';
					} else {
					    $interval = $end_datetime - $current_time;
	                    $days = floor($interval / (60 * 60 * 24));
	                    $hours = floor(($interval % (60 * 60 * 24)) / (60 * 60));
	                    $minutes = floor(($interval % (60 * 60)) / 60);
					    $total_duration = sprintf('%d days %d hours %d minutes', $days, $hours, $minutes);
					} 
					$terms = wp_get_post_terms($postid, 'auction-event'); 
					if($days >= 0 || $hours >= 0 || $minutes > 0) { ?> 
				    <div class="item">
				    	<a href="<?php the_permalink(); ?>" class="ongoing-product-wrapper">
				    		<div class="imgpart">
				    			<img src="<?php if($image) { echo $image; } else { echo get_template_directory_uri().'/images/default.png';} ?>" />
				    			<div class="live">Live</div>
                                <div class="duration">
                                    <?php echo $total_duration; ?>    
                                </div>
				    		</div>
				    		<div class="product-content">
				    			<div class="lotnum">Lot# : <?php echo $postid; ?></div>
				    			<?php if (!empty($terms)) { ?><div class="weekly"><?php $first_term = $terms[0]; echo $first_term->name; ?></div><?php } ?>
				    			<h4><?php the_title(); ?></h4>
				    		</div>
				    	</a>
				    </div><?php }
				    } ?> 
				</div>
	    	</section>
	<?php
}
	    	$content = ob_get_clean();
	    	return $content;
		
	}


//Bottom Product

add_shortcode('bottom_products', 'bottom_auction_product');
	function bottom_auction_product()
	{
		ob_start();
		$featured_posts = get_field('homepage_bottom_section', 'option');
        if ( $featured_posts ) { ?>
	    	<section class="bottom-product-section">
    			<div class="bottom-product"><?php 
    			foreach( $featured_posts as $featured_post ):
                	$permalink = get_permalink( $featured_post->ID );
        			$title = get_the_title( $featured_post->ID );
        			$excerpt = get_the_excerpt( $featured_post->ID );
                	$featured_img_url = get_the_post_thumbnail_url($featured_post->ID);?> 
				    <div class="item">
				    	<div class="left-content">
				    		<div class="aucton-img">
				    			<img src="<?php if($featured_img_url) { echo $featured_img_url; } else { echo get_template_directory_uri().'/images/default.png';} ?>">
				    		</div>
				    	</div>
				    	<div class="right-content">
				    		<span>Featured</span>
				    		<h2><?php echo esc_html( $title ); ?></h2>
				    		<?php echo $excerpt; ?>
				    		<a href="<?php echo esc_url( $permalink ); ?>" class="btn">View Listing</a>
				    	</div>
				    </div><?php
				    endforeach; ?> 
				</div>
	    	</section>
	<?php 
}
	$content = ob_get_clean();
	return $content;
		
	}



function custom_search_template_redirect() {
    global $wp_query;
    if ( $wp_query->is_search ) {
    	$search_query = urlencode( get_search_query() );
        wp_redirect(  home_url( '/auction/?sq=' . $search_query ) );
        exit;
    }
}
add_action( 'template_redirect', 'custom_search_template_redirect' );



// Upcoming Auction

add_shortcode('upcoming_products', 'upcoming_auction');
	function upcoming_auction()
	{
		ob_start(); 
		$current_time = current_time('timestamp', true);
		$post = new WP_Query(
            array(
                'post_type'         => 'auction',
                'post_status'       => 'publish',
                'posts_per_page'    => 6,
                'orderby'           => 'meta_value',
                'order'             => 'ASC',
                'meta_query'        => array( 
                	array(
				        'key'     => 'start_date',
				        'value'   => date('Y-m-d H:i:s', $current_time),
				        'compare' => '>',
				        'type'    => 'DATETIME'
				    )
            	),
            )
        );
        if ( $post->have_posts() ) { ?>
	    	<section class="upcoming-auction">
	    		<div class="customcontainer">
	    			<div class="headingbar-global">
	    				<h2>Upcoming Auction</h2>
	    				<a href="/auction/?tab=upcoming">See all <img src="<?php echo site_url(); ?>/wp-content/uploads/2024/02/arrow.svg" /></a>
	    			</div>
	    			<div class="gridblocks auctionset"><?php 
	    			while ( $post->have_posts() ) {
	                	$post->the_post();
	                	$postid = get_the_ID(); 
		                $image = get_the_post_thumbnail_url($postid);
		                $location = get_field('location', $postid); 
		                $start_date = get_field('start_date', $postid); 
		                $startdate = DateTime::createFromFormat('d/m/Y h:i a', $start_date);
                        $fstartdate = $startdate->format('M y');?> 
	    				<a href="<?php the_permalink(); ?>" class="auctionset-wrapper">
	    					<div class="imgwrapper"><img src="<?php if($image) { echo $image; } else { echo get_template_directory_uri().'/images/default.png';} ?>" /></div>
	    					<div class="wrapper-content">
	    						<div class="date-live"><?php echo $fstartdate; ?> | Live Auction</div>
	    						<h5><?php the_title(); ?></h5>
	    						<div class="location">
	    							<span>
	    								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<g clip-path="url(#clip0_17_393)">
										<path d="M9 11C9 11.7956 9.31607 12.5587 9.87868 13.1213C10.4413 13.6839 11.2044 14 12 14C12.7956 14 13.5587 13.6839 14.1213 13.1213C14.6839 12.5587 15 11.7956 15 11C15 10.2044 14.6839 9.44129 14.1213 8.87868C13.5587 8.31607 12.7956 8 12 8C11.2044 8 10.4413 8.31607 9.87868 8.87868C9.31607 9.44129 9 10.2044 9 11Z" stroke="#A6A5AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M17.657 16.657L13.414 20.9C13.039 21.2746 12.5306 21.485 12.0005 21.485C11.4704 21.485 10.962 21.2746 10.587 20.9L6.343 16.657C5.22422 15.5381 4.46234 14.1127 4.15369 12.5608C3.84504 11.009 4.00349 9.40047 4.60901 7.93868C5.21452 6.4769 6.2399 5.22749 7.55548 4.34846C8.87107 3.46943 10.4178 3.00024 12 3.00024C13.5822 3.00024 15.1289 3.46943 16.4445 4.34846C17.7601 5.22749 18.7855 6.4769 19.391 7.93868C19.9965 9.40047 20.155 11.009 19.8463 12.5608C19.5377 14.1127 18.7758 15.5381 17.657 16.657Z" stroke="#A6A5AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</g>
										<defs>
										<clipPath id="clip0_17_393">
										<rect width="24" height="24" fill="white"/>
										</clipPath>
										</defs>
										</svg>
									</span>
		    						<?php echo $location; ?>
		    					</div>
	    					</div>
	    				</a><?php
				    } ?> 
	    			</div>
	    		</div>
	    	</section>
	<?php
}
	    	$content = ob_get_clean();
	    	return $content;
		
	}

add_shortcode('contact_info', 'contact_detail');
	function contact_detail()
	{
		
		ob_start();
	    	?>
	    	<div class="contact-box-wrap">
    		<?php 
			$contactdetailsval = get_field('contact_details', 'option');		
			
			if(isset($contactdetailsval['emails']) && !empty($contactdetailsval['emails'][0]['email'])){
				
				?>
				<div class="single-contact-box">					
					<?php 
					if(isset($contactdetailsval['phone_email_title']) && !empty($contactdetailsval['phone_email_title'])){
						echo '<h6>' . $contactdetailsval['phone_email_title'] . '</h6>'; 							
					}
					?>					

					<?php foreach ($contactdetailsval['emails'] as $emlkey => $emvalues) {
						if(!empty($emvalues['email'])){
							?>
							<a href="mailto:<?php echo $emvalues['email']; ?>" class="mail"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill-rule="evenodd" d="m62.843 98.364 138.32 138.38c30.168 30.11 79.482 30.136 109.675 0l138.32-138.38a3.144 3.144 0 0 0-.426-4.814c-14.108-9.839-31.273-15.672-49.763-15.672H113.033c-18.491 0-35.656 5.834-49.764 15.672a3.144 3.144 0 0 0-.426 4.814zm-36.964 66.667a86.483 86.483 0 0 1 9.955-40.353 3.144 3.144 0 0 1 5.019-.762l136.569 136.569c43.247 43.31 113.885 43.335 157.158 0l136.569-136.569a3.144 3.144 0 0 1 5.019.762 86.498 86.498 0 0 1 9.955 40.353v181.937c0 48.093-39.121 87.154-87.154 87.154H113.033c-48.032 0-87.154-39.061-87.154-87.154z" clip-rule="evenodd" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg> <?php echo $emvalues['email']; ?></a> 
							<?php
						}
					}
					if(isset($contactdetailsval['phones'])){
						foreach ($contactdetailsval['phones'] as $phkey => $phvalues) {
							if(!empty($phvalues['phone'])){
								?>
								<a href="tel:<?php echo $phvalues['phone']; ?>" class="phone"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M29.393 23.36c-.874-.733-6-3.979-6.852-3.83-.4.071-.706.412-1.525 1.389a11.687 11.687 0 0 1-1.244 1.347 10.757 10.757 0 0 1-2.374-.88 14.7 14.7 0 0 1-6.784-6.786 10.757 10.757 0 0 1-.88-2.374 11.687 11.687 0 0 1 1.347-1.244c.976-.819 1.318-1.123 1.389-1.525.149-.854-3.1-5.978-3.83-6.852C8.334 2.243 8.056 2 7.7 2 6.668 2 2 7.772 2 8.52c0 .061.1 6.07 7.689 13.791C17.41 29.9 23.419 30 23.48 30c.748 0 6.52-4.668 6.52-5.7 0-.356-.243-.634-.607-.94zM23 15h2a8.009 8.009 0 0 0-8-8v2a6.006 6.006 0 0 1 6 6z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M28 15h2A13.015 13.015 0 0 0 17 2v2a11.013 11.013 0 0 1 11 11z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg><?php echo $phvalues['phone']; ?></a>
								<?php
							}
						}
					}
					?>
					
				</div>
				<?php
			}
			
			if(isset($contactdetailsval['locations']) && !empty($contactdetailsval['locations'][0]['location'])){
				?>
				<div class="single-contact-box">				
				<?php 
					if(isset($contactdetailsval['location_title']) && !empty($contactdetailsval['location_title'])){
						echo '<h6>' . $contactdetailsval['location_title'] . '</h6>'; 							
					}
				?>
				<?php
				foreach ($contactdetailsval['locations'] as $lckey => $lcvalues) {
					if(!empty($lcvalues['location'])){
						?>
						
						<div class="location"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M256 0C153.755 0 70.573 83.182 70.573 185.426c0 126.888 165.939 313.167 173.004 321.035 6.636 7.391 18.222 7.378 24.846 0 7.065-7.868 173.004-194.147 173.004-321.035C441.425 83.182 358.244 0 256 0zm0 278.719c-51.442 0-93.292-41.851-93.292-93.293S204.559 92.134 256 92.134s93.291 41.851 93.291 93.293-41.85 93.292-93.291 93.292z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg> <?php echo $lcvalues['location']; ?></div>
					
						<?php
					}
				}
				?>
				</div>
				<?php
			}
			?>

				<?php $sociallink = get_field('social_site_links', 'option'); 
				if($sociallink && !empty($sociallink[0]['social_icon'])){
					?>
					<div class="single-contact-box">
					<h6>Social Site Link</h6>
					<?php
					foreach ($sociallink as $slkey => $slvalue) {
						if(isset($slvalue['social_icon']) && isset($slvalue['social_url'])){
							?>
							<a href="<?php echo $slvalue['social_url']; ?>" class="weblink"><img width="30" src="<?php echo $slvalue['social_icon']; ?>" class="socialicons"/> <?php echo $slvalue['social_url']; ?></a>
							<?php
						}
					}
					?></div><?php
				}?>
		</div>
		<?php
    	$content = ob_get_clean();
    	return $content;
	
}