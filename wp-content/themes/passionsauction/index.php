<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package auction
 */

get_header();
global $post;
$numpost = get_option( 'posts_per_page' );

$postscount = wp_count_posts();
$allposts = $postscount->publish;
$currentpage = max(1, get_query_var('paged'));
$offset = ($currentpage - 1) * $numpost;
$postargs = array('numberposts' => $numpost, 'post_status'    => 'publish', 'offset' => $offset );

$posts = get_posts($postargs);
?>
<main id="primary" class="site-main">
	<section class="heading-banner innerbaner">
		<div class="customcontainer">
			<?php echo custom_breadcrumb(); ?>
	        <h2><?php echo get_field('event_title', 'option');  ?></h2>
	        <p><?php echo get_field('event_subheading', 'option'); ?></p>
		</div>
	</section>

		<section class="blog-listing">
			<div class="customcontainer">
				<div class="row blog-listingpost mt-4">
					<?php foreach ($posts as $pkey => $pvalue) {
						
						$postid = $pvalue->ID;
						$post_title = $pvalue->post_title;
						$post_date = $pvalue->post_date;
						$content = $pvalue->post_excerpt;
						$datestr = strtotime($post_date);
						$formattedDate = date("d M Y", $datestr);
						$postlink = get_the_permalink($postid);
						$featured_image = get_the_post_thumbnail_url($postid);
						
						?>
						<div class="col-12 col-md-6 col-lg-6 col-xl-4">
						<div class="blogitem">
		  					<div class="blog-img">
		  						<?php if($featured_image){ ?>
		  						<img class="img-fluid" alt="<?php echo $post_title; ?>" src="<?php echo $featured_image; ?>">
		  						<?php } ?>
		  					</div>
		  					<div class="blog-content">
		  						<ul class="blog-meta">
	                           		<li class="date"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class="hovered-paths"><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#0d8080" opacity="1" data-original="#000000"></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0h39.333m78.895 0h39.333M-118 0h39.333M0 118h39.333m78.895 0h39.333M-118 118h39.333m-137.666 98.667h472.227M-137.439-98H177c43.572 0 78.894 35.322 78.894 78.895v274.877c0 43.572-35.322 78.895-78.894 78.895h-314.439c-43.572 0-78.894-35.323-78.894-78.895V-19.105c0-43.573 35.322-78.895 78.894-78.895zm275.333 373.667V374m-236.227-98.333V374" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(236.333 118)" fill="none" stroke="#0d8080" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" opacity="1" class="hovered-path"></path></g></g></svg> <?php echo $formattedDate; ?></li>
	                           	</ul>
		  						<div class="blog-title"><?php echo $post_title; ?></div>
		  						<div class="blog-desc"><?php echo $content; ?></div>
		  						<div class="read-morebtn"><a href="<?php echo $postlink; ?>" class="btn btn-black stretched-link">Read More</a></div>
		  					</div>
		  				</div>
					</div>
						<?php

					} ?>
					
					<!-- <div class="col-12 col-md-6 col-lg-6 col-xl-4">
						<div class="blogitem">
		  					<div class="blog-img">
		  						<img class="img-fluid" src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/02/hgk22122-mkt-48-desk-2560x1440-1.jpg">
		  					</div>
		  					<div class="blog-content">
		  						<ul class="blog-meta">
	                           		<li class="date"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class="hovered-paths"><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#0d8080" opacity="1" data-original="#000000"></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0h39.333m78.895 0h39.333M-118 0h39.333M0 118h39.333m78.895 0h39.333M-118 118h39.333m-137.666 98.667h472.227M-137.439-98H177c43.572 0 78.894 35.322 78.894 78.895v274.877c0 43.572-35.322 78.895-78.894 78.895h-314.439c-43.572 0-78.894-35.323-78.894-78.895V-19.105c0-43.573 35.322-78.895 78.894-78.895zm275.333 373.667V374m-236.227-98.333V374" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(236.333 118)" fill="none" stroke="#0d8080" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" opacity="1" class="hovered-path"></path></g></g></svg> 23 March 2022</li>
	                           	</ul>
		  						<div class="blog-title">Fine and Rare Wines Spirits Online: Festive Edition</div>
		  						<div class="blog-desc">We denounce with righteous indige nation and dislike men who are so beguiled and demo realized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue cannot foresee.</div>
		  						<div class="read-morebtn"><a href="#" class="btn btn-black stretched-link">Read More</a></div>
		  					</div>
		  				</div>
					</div> -->
					
				</div>
				<?php if($allposts > $numpost){ ?>
				<!-- <div class="paginationpro">
	                <ul class="pagination justify-content-center">
	                    <li class="inactive">
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 1L1 7L7 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
	                    </li>                            
	                    <li class="selected">1</li>                            
	                    <li class="active">2</li>                        
	                    <li>
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L1 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
	                    </li>                
	                </ul>
	            </div> -->

	            <?php

				$total_pages = ceil($allposts / $numpost);
				
				if ($currentpage > 1) {
					    $prev_page = $currentpage - 1;
					    $prev_html = '<li class="inactive">
					                    <a href="' . home_url('events/page/' . $prev_page) . '">
					                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
					                            <path d="M7 1L1 7L7 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
					                        </svg>
					                    </a>
					                </li>';
					}

				if ($currentpage < $total_pages) {
				    $next_page = $currentpage + 1;
				    $next_html = '<li>
				                    <a href="' . home_url('events/page/' . $next_page) . '">
				                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
				                            <path d="M1 1L7 7L1 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
				                        </svg>
				                    </a>
				                </li>';
				}

				
				$html = '<div class="paginationpro">
				            <ul class="pagination justify-content-center">';
				if ($total_pages > 1 && isset($prev_html)) {
				    $html .= $prev_html;
				}
				for ($i = 1; $i <= $total_pages; $i++) {
				    if ($i == $currentpage) {
				        $html .= '<li class="selected">' . $i . '</li>';
				    } else {
				        $html .= '<li class="active"><a href="'.home_url('events/page/'.$i).'">' . $i . '</li>';
				    }
				}
				if ($total_pages > 1 && isset($next_html)) {
				    $html .= $next_html;
				}
				$html .= '</ul></div>';

				echo $html;

	            } ?>
			</div>
		</section>
</main>
<?php
get_footer();
