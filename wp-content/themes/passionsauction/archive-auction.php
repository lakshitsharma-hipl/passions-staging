<?php get_header(); ?>
<section class="auction-list">
	<div class="customcontainer">
		<div class="heading-with-searchgrid">
			<div class="heading"><h2>Welcome to Passions Auctions</h2></div>
			<div class="search-withview">
				<form>
					<div class="searchform">
						<input type="search" id="auctionsearch" placeholder="Search auctions...">
						<span class="searchicon" id="searchclick">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0_20_168)">
									<path d="M2 6.66667C2 7.2795 2.12071 7.88634 2.35523 8.45252C2.58975 9.01871 2.93349 9.53316 3.36683 9.9665C3.80017 10.3998 4.31462 10.7436 4.88081 10.9781C5.447 11.2126 6.05383 11.3333 6.66667 11.3333C7.2795 11.3333 7.88634 11.2126 8.45252 10.9781C9.01871 10.7436 9.53316 10.3998 9.9665 9.9665C10.3998 9.53316 10.7436 9.01871 10.9781 8.45252C11.2126 7.88634 11.3333 7.2795 11.3333 6.66667C11.3333 6.05383 11.2126 5.447 10.9781 4.88081C10.7436 4.31462 10.3998 3.80017 9.9665 3.36683C9.53316 2.93349 9.01871 2.58975 8.45252 2.35523C7.88634 2.12071 7.2795 2 6.66667 2C6.05383 2 5.447 2.12071 4.88081 2.35523C4.31462 2.58975 3.80017 2.93349 3.36683 3.36683C2.93349 3.80017 2.58975 4.31462 2.35523 4.88081C2.12071 5.447 2 6.05383 2 6.66667Z" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M14 14L10 10" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
								<defs>
									<clipPath id="clip0_20_168">
										<rect width="16" height="16" fill="white"/>
									</clipPath>
								</defs>
							</svg>
						</span>
					</div>
					<!--  -->
					<div class="layout-view">
						<p>Layout view</p>
						<button type="button" class="grid-view-btn active">
							<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 2C1 1.73478 1.10536 1.48043 1.29289 1.29289C1.48043 1.10536 1.73478 1 2 1H6C6.26522 1 6.51957 1.10536 6.70711 1.29289C6.89464 1.48043 7 1.73478 7 2V6C7 6.26522 6.89464 6.51957 6.70711 6.70711C6.51957 6.89464 6.26522 7 6 7H2C1.73478 7 1.48043 6.89464 1.29289 6.70711C1.10536 6.51957 1 6.26522 1 6V2Z" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11 2C11 1.73478 11.1054 1.48043 11.2929 1.29289C11.4804 1.10536 11.7348 1 12 1H16C16.2652 1 16.5196 1.10536 16.7071 1.29289C16.8946 1.48043 17 1.73478 17 2V6C17 6.26522 16.8946 6.51957 16.7071 6.70711C16.5196 6.89464 16.2652 7 16 7H12C11.7348 7 11.4804 6.89464 11.2929 6.70711C11.1054 6.51957 11 6.26522 11 6V2Z" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M1 12C1 11.7348 1.10536 11.4804 1.29289 11.2929C1.48043 11.1054 1.73478 11 2 11H6C6.26522 11 6.51957 11.1054 6.70711 11.2929C6.89464 11.4804 7 11.7348 7 12V16C7 16.2652 6.89464 16.5196 6.70711 16.7071C6.51957 16.8946 6.26522 17 6 17H2C1.73478 17 1.48043 16.8946 1.29289 16.7071C1.10536 16.5196 1 16.2652 1 16V12Z" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11 12C11 11.7348 11.1054 11.4804 11.2929 11.2929C11.4804 11.1054 11.7348 11 12 11H16C16.2652 11 16.5196 11.1054 16.7071 11.2929C16.8946 11.4804 17 11.7348 17 12V16C17 16.2652 16.8946 16.5196 16.7071 16.7071C16.5196 16.8946 16.2652 17 16 17H12C11.7348 17 11.4804 16.8946 11.2929 16.7071C11.1054 16.5196 11 16.2652 11 16V12Z" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>

						</button>
						<button type="button" class="list-view-btn">
							<svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M11 2H19" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11 6H16" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11 12H19" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11 16H16" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M1 2C1 1.73478 1.10536 1.48043 1.29289 1.29289C1.48043 1.10536 1.73478 1 2 1H6C6.26522 1 6.51957 1.10536 6.70711 1.29289C6.89464 1.48043 7 1.73478 7 2V6C7 6.26522 6.89464 6.51957 6.70711 6.70711C6.51957 6.89464 6.26522 7 6 7H2C1.73478 7 1.48043 6.89464 1.29289 6.70711C1.10536 6.51957 1 6.26522 1 6V2Z" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M1 12C1 11.7348 1.10536 11.4804 1.29289 11.2929C1.48043 11.1054 1.73478 11 2 11H6C6.26522 11 6.51957 11.1054 6.70711 11.2929C6.89464 11.4804 7 11.7348 7 12V16C7 16.2652 6.89464 16.5196 6.70711 16.7071C6.51957 16.8946 6.26522 17 6 17H2C1.73478 17 1.48043 16.8946 1.29289 16.7071C1.10536 16.5196 1 16.2652 1 16V12Z" stroke="#959595" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</div>
				</form>
			</div>
		</div>
		<div class="archieve-auction">
			<div class="filterbtn">
				<button type="button" id="filterwrap">Filter 
					<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M1.5 1H13.5V2.629C13.4999 3.02679 13.3418 3.40826 13.0605 3.6895L9.75 7V12.25L5.25 13.75V7.375L1.89 3.679C1.63909 3.40294 1.50004 3.0433 1.5 2.67025V1Z" stroke="#959595" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			</div>
			<div class="filtersidebar">
				<div class="filterheading">
					<h5 class="mb-0">Filter</h5>
					<button type="button" class="clearbtn">Clear All</button>
					<button class="closepop" id="closepop">
						<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M13 1L1 13" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M1 1L13 13" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>

					</button>
				</div>
				<div class="filterblocks">
				<?php if(isset($_GET['category'])){
						$selectedcat = $_GET['category'];
						$catqueclass = '';
						$catanswerclass = 'collapse show';
						$catareaexpand = 'true';
					}else{
						$selectedcat = '';
						$catqueclass = 'collapsed';
						$catanswerclass = 'collapse';
						$catareaexpand = 'false';
					} ?>
					<input type="hidden" id="checked_auctioncategory" value="<?php echo $selectedcat; ?>">
					<input type="hidden" id="checked_auctionevent" value="">
					<input type="hidden" id="checked_auctionyear" value="">
					<input type="hidden" id="checked_auctionminprice" value="">
					<input type="hidden" id="checked_auctionmaxprice" value="">
					<div class="filterblock-wrapper">
					<a class="btn <?php echo $catqueclass; ?>" data-bs-toggle="collapse" href="#categorylist" role="button" aria-expanded="<?php echo $catareaexpand; ?>" aria-controls="categorylist">
							Category
							<span><svg width="12" height="6" viewBox="0 0 12 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 0.75L6 5.25L10.5 0.75" stroke="#151515" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
						</a>
					    <?php
						$taxonomy = 'auction-category';
						$terms = get_terms(array(
						    'taxonomy' => $taxonomy,
						    'parent' => 0,
						    'hide_empty' => false,
						));
						if (!empty($terms)) {
							echo '<div class="'.$catanswerclass.'" id="categorylist">';
						    foreach ($terms as $term) {
						        $count = $term->count;
								if($selectedcat == $term->term_id){
						        	$chst = 'checked';
						        }else{
						        	$chst = '';
						        }
						        echo '<div class="filterlist-item">';
						        echo '<label>';
						        echo '<input type="checkbox" value="' . $term->term_id . '" name="auctioncategory" '.$chst.'>';
						        echo '<span>' . $term->name . '</span>';
						        echo '</label>';
						        echo '<div class="number">(' . $count . ')</div>';
						        echo '</div>';
						    }
						    echo '</div>';
						} else {
						    echo '<p>No category found.</p>';
						}
						?>

					</div>
					<!--  -->
					<div class="filterblock-wrapper">
						<a class="btn collapsed" data-bs-toggle="collapse" href="#Event" role="button" aria-expanded="false" aria-controls="Event">
							Event
							<span><svg width="12" height="6" viewBox="0 0 12 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 0.75L6 5.25L10.5 0.75" stroke="#151515" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
						</a>
						
					    <?php
						$taxonomy = 'auction-event';
						$terms = get_terms(array(
						    'taxonomy' => $taxonomy,
						    'parent' => 0,
						    'hide_empty' => false,
						));
						if (!empty($terms)) {
						    echo '<div class="collapse" id="Event">';
						    foreach ($terms as $term) {
						        $count = $term->count;
						        echo '<div class="filterlist-item">';
						        echo '<label>';
						        echo '<input type="checkbox" value="' . $term->term_id . '" name="auctionevent">';
						        echo '<span>' . $term->name . '</span>';
						        echo '</label>';
						        echo '<div class="number">(' . $count . ')</div>';
						        echo '</div>';
						    }
						    echo '</div>';
						} else {
						    echo '<p>No events found.</p>';
						}
						?>
					</div>
					<!--  -->
					<div class="filterblock-wrapper">
						<a class="btn collapsed" data-bs-toggle="collapse" href="#Year" role="button" aria-expanded="false" aria-controls="Year">
							Year
							<span><svg width="12" height="6" viewBox="0 0 12 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 0.75L6 5.25L10.5 0.75" stroke="#151515" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
						</a>
						<?php
						$taxonomy = 'auction-year';
						$terms = get_terms(array(
						    'taxonomy' => $taxonomy,
						    'parent' => 0,
						    'hide_empty' => false,
						));
						if (!empty($terms)) {
						    echo '<div class="collapse" id="Year">';
						    echo '<div class="filterlist-item"><select id="auctionyear">';
						    echo '<option value="">Choose Year</option>';
						    foreach ($terms as $term) {
						        $count = $term->count;
						        echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
						       
						    }
						    echo '</select>';
						    echo '</div>';
						    echo '</div>';
						} else {
						    echo '<p>No years found.</p>';
						}
						?>
					</div>
					<div class="filterblock-wrapper">
						<a class="btn collapsed" data-bs-toggle="collapse" href="#Price" role="button" aria-expanded="false" aria-controls="Price">
							Price
							<span><svg width="12" height="6" viewBox="0 0 12 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 0.75L6 5.25L10.5 0.75" stroke="#151515" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
						</a>
						<div class="collapse" id="Price">
					      	<div class="filterlist-item price">
					      		<input type="text" placeholder="$ Min" id="auctionminprice" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control">
					      		<input type="text" placeholder="$ Max" id="auctionmaxprice" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control">
					      	</div>
					    </div>
					</div>
					<div class="filter-apply-clear">
						<button type="button" class="btn btn-border clearall">Clear all</button>
						<button type="button" class="btn btn-black applyfilter">Apply Filter</button>
					</div>
				</div>
			</div>
			<!--  -->
			<div class="auction-wrapper-archieve">
				<!--  -->
				<div class="auction-list-grid grid">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
					  	<li class="nav-item" role="presentation">
					    	<button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All</button>
					  	</li>
					  	<!--  -->
					  	<li class="nav-item" role="presentation">
					    	<button class="nav-link" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab" aria-controls="new" aria-selected="false">Ongoing</button>
					  	</li>
					  	<!--  -->
					  	<li class="nav-item" role="presentation">
					    	<button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="false">Upcoming</button>
					  	</li>
					  	<!--  -->
					  	<li class="nav-item" role="presentation">
					    	<button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">Past</button>
					  	</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<input type="hidden" id="checked_auction_type" value="all">
						<div class="auction-loader" style="display: none;">
							<div class="center-loader">
							</div>
						</div>
					  	<div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
					  		<div class="row auctions-posts">

					  		</div>
					  	</div>
					  	<!--  -->
					  	<div class="tab-pane fade" id="new" role="tabpanel" aria-labelledby="new-tab">
					  		<div class="row auctions-posts">
					  			
					  		</div>
					  	</div>
					  	<!--  -->
					  	<div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
					  		<div class="row auctions-posts">
					  			
					  		</div>
					  	</div>
					  	<!--  -->
					  	<div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
					  		<div class="row auctions-posts">
					  			
					  		</div>
					  	</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>