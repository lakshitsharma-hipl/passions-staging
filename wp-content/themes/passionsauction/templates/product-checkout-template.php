<?php 
/* Template Name: Product Checkout*/

global $wpdb;


get_header(); 


$user_id = get_current_user_id();
$fname = get_user_meta($user_id, 'first_name', true);
$lname = get_user_meta($user_id, 'last_name', true);
$user_data = get_userdata($user_id);

if ($user_data) {
    $email = $user_data->user_email;
}else{
	$email = '';
}


$user_street = get_user_meta($user_id, 'address', true);
$user_city = get_user_meta($user_id, 'city', true);
$user_state = get_user_meta($user_id, 'state', true);
$user_country = get_user_meta($user_id, 'country', true);
$user_zipcode = get_user_meta($user_id, 'zipcode', true);
$user_phone = get_user_meta($user_id, 'phone', true);


$table_name = $wpdb->prefix . 'passions_product_cart';

// Get current user's ID
$current_user_id = get_current_user_id();

// Retrieve cart items for the current user from the custom table
$cart_items = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d",
        $current_user_id
    )
);?>
<section class="payment-checkout">
<?php
if ($cart_items) { ?>
	<div class="divide-block">
		<div class="payment-product">
			<table class="table">
				<thead>
					<tr>
						<th>Product Name</th>
						<th>Qty</th>
						<th>Price</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$grand_total = 0; // Initialize grand total variable
					foreach ($cart_items as $cart_item) {
						$product_id = $cart_item->product_id;
						$product = get_post($product_id);
						$product_name = $product->post_title;
						$product_permalink = get_permalink($product_id);
						$sale_price_check = get_field('product_sale_price', $product_id);
						$product_image = get_the_post_thumbnail_url($product_id);
						if ($sale_price_check) {
							$product_price = $sale_price_check;
						} else {
							$product_price = get_field('product_regular_price', $product_id);
						}
						// Calculate total for each item
						$item_total = $cart_item->quantity * $product_price;
						$grand_total += $item_total; // Add item total to grand total
						?>
						<tr>
							<td><a href="<?php echo $product_permalink; ?>"><?php echo $product_name; ?></a></td>
							<td><?php echo $cart_item->quantity; ?></td>
							<td>$<?php echo $item_total; ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<div class="product-price-data">							
				<!-- <div class="delivery-fee">Sub Total: <span>$ </span></div> -->				
				<div class="total">Total: <span>$ <?php echo $grand_total ?></span></div>
			</div>
		</div>
		<div class="payment-type">
			<div class="heading"><h2>Payment</h2></div>
			<?php
			$maxonline_amount = get_field('maxonline_amount_products', 'option');
			if($grand_total < $maxonline_amount) { ?>
			<form id="paymentform" class="payment-tab">
				<div class="tab-block">
					<div class="tab-block-head">
						<!-- <input class="paymentradio" type="radio" name="tab" value="stripe" checked> -->
						<label>
							<span class="label-heading">Credit / debit card</span>
							<span class="label-content">Secure transfer using your bank account</span>
						</label>
					</div>
					<div id="stripe" class="tab-block-content">
						<div class="row" id="custom-card-element">
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>First Name <span>*</span></label>
									<input id="first_name" type="text" name="fname" data-label="First Name" placeholder="First Name" class="form-control required" value="<?php echo $fname; ?>" />
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>Last Name <span>*</span></label>
									<input id="last_name" type="text" name="lname" data-label="Last Name" placeholder="Last Name" class="form-control required" value="<?php echo $lname; ?>" />
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>Email <span>*</span></label>
									<input id="email_address" type="email" name="email" data-label="Email" placeholder="Email" class="form-control required" value="<?php echo $email; ?>" />
								</div>
							</div>

							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label for="paymentphone">Phone Number <span>*</span></label>
									<!-- <input type="number" id="paymentphone" data-label="Phone Number" name="paymentphone" placeholder="" class="form-control required" value="<?php // echo $user_phone ?>"> -->
									<input
										type="tel"
										name="passion_product_phone_number"
										id="passion_product_phone_number"
										class="form-control phonenumber_itnl required"
										placeholder="Enter your phone number"
										oninput="this.value = this.value.replace(/[^0-9]/g, '');" autofocus
									/>

								</div>
							</div>

							<div class="col-12 col-lg-12">
								<div class="form-group">
									<label for="paymentstreet">Address <span>*</span></label>

									<textarea type="text" id="paymentstreet" data-label="Street" name="paymentstreet" placeholder="" class="form-control required"><?php echo $user_street; ?></textarea>
									<!-- <input type="text" id="paymentstreet" data-label="Street" name="paymentstreet" placeholder="" class="form-control required" value="<?php // echo $user_street; ?>" /> -->
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label for="paymentcity">City <span>*</span></label>
									<input type="text" id="paymentcity" data-label="City" name="paymentcity" placeholder="" class="form-control required" value="<?php echo $user_city; ?>" />
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label for="paymentstate">State <span>*</span></label>
									<input type="text" id="paymentstate" data-label="State" name="paymentstate" placeholder="" class="form-control required" value="<?php echo $user_state; ?>" />
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label for="paymentcountry">Country <span>*</span></label>
									<select id="paymentcountry" name="paymentcountry" class="form-control required" data-label="Country">
			                                <option value="">Select Country</option>
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
			                                "Zimbabwe");
											foreach ($countries as $country) {
												echo '<option value="' . $country . '"' . (($user_country == $country) ? ' selected' : '') . '>' . $country . '</option>';
											}
											?>
			                            </select>
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label for="paymentzipcode">Zip Code <span>*</span></label>
									<input type="number" id="paymentzipcode" data-label="Zip Code" value="<?php echo $user_zipcode; ?>" name="paymentzipcode" placeholder="" class="form-control required" />
								</div>
							</div>

							<input type="hidden" id="product_payment_buc" value="<?php echo passion__encrypt_data($grand_total); ?>" />

							<div class="col-12 col-lg-6">
		                        <div class="stripe-input">
		                            <div class="full-width" id="card-number"></div>
		                        </div>
		                    </div>
		                    <div class="col-12 col-lg-6">
		                        <div class="stripe-input-group">
		                            <div class="half-width" id="card-expiry"></div>
		                            <div class="half-width" id="card-cvc"></div>
		                        </div>
		                    </div>
		                    <div class="col-12 col-lg-6">
		                        <div class="stripe-input-group">
		                            <div class="half-width" id="card-postal-code"></div>
		                        </div>
		                    </div>
		                    <div id="card-element"></div>
		                    <div id="card-errors" role="alert"></div>
		                    
                    		<span id="stripe_card_error"></span>
                    		<input type="hidden" id="orderid" name="orderid" value="<?php echo $orderid; ?>">
		                    <div class="col-12 col-lg-6">
								<div class="button-form">
									<button id="paybystripe" type="submit" class="btn btn-green">Save Card and Make Payment</button>
									<span class="loaderimg" style="display:none;"><img src="<?php echo get_stylesheet_directory_uri().'/images/loader.gif'; ?>" class="ldrimg"></span>
								</div>
							</div>

						</div>
					</div>
				</div>
				<span class="finalresponse" style="display:none;"></span>

			</form>
			<?php } else { 
				$additional_comment = get_field('additional_comment', 'option'); 
				if(have_rows('bank_details', 'option') || $additional_comment) { ?>
					<div class="row">
						<div class="col-12 col-lg-12">
							<div class="bankdetails"><?php 
								while(have_rows('bank_details', 'option')) { 
									the_row(); 
									$label = get_sub_field('label');
									$values = get_sub_field('values');?>
								<div class="detail">
									<div><?php echo $label; ?></div>
									<div><?php echo $values; ?></div>
								</div><?php 
							} ?>
							</div>
							<?php if($additional_comment) { ?>
							<div class="additional-comment">
								<?php echo $additional_comment; ?>
							</div>
							<?php } ?>
						</div>
					</div>					
				<?php } ?>

				<div class="row tab-block-content">
						<form id="uploadinvoice_product" method="POST">
							<p>After making the payment to the bank details listed above, please send the invoice to the admin.</p>
						    <div class="row">
								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label>First Name <span>*</span></label>
										<input id="first_name" type="text" name="fname" data-label="First Name" placeholder="First Name" class="form-control required" value="<?php echo $fname; ?>" />
									</div>
								</div>
								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label>Last Name <span>*</span></label>
										<input id="last_name" type="text" name="lname" data-label="Last Name" placeholder="Last Name" class="form-control required" value="<?php echo $lname; ?>" />
									</div>
								</div>
								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label>Email <span>*</span></label>
										<input id="email_address" type="email" name="email" data-label="Email" placeholder="Email" class="form-control required" value="<?php echo $email; ?>" />
									</div>
								</div>

								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label for="paymentphone">Phone Number <span>*</span></label>
										<!-- <input type="number" id="paymentphone" data-label="Phone Number" name="paymentphone" placeholder="" class="form-control required" value="<?php // echo $user_phone ?>"> -->
										<input
											type="tel"
											name="passion_product_phone_number"
											id="passion_product_phone_number"
											class="form-control phonenumber_itnl required"
											placeholder="Enter your phone number"
											oninput="this.value = this.value.replace(/[^0-9]/g, '');" autofocus
										/>

									</div>
								</div>

								<div class="col-12 col-lg-12">
									<div class="form-group">
										<label for="paymentstreet">Address <span>*</span></label>

										<textarea type="text" id="paymentstreet" data-label="Street" name="paymentstreet" placeholder="" class="form-control required"><?php echo $user_street; ?></textarea>
										<!-- <input type="text" id="paymentstreet" data-label="Street" name="paymentstreet" placeholder="" class="form-control required" value="<?php // echo $user_street; ?>" /> -->
									</div>
								</div>
								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label for="paymentcity">City <span>*</span></label>
										<input type="text" id="paymentcity" data-label="City" name="paymentcity" placeholder="" class="form-control required" value="<?php echo $user_city; ?>" />
									</div>
								</div>
								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label for="paymentstate">State <span>*</span></label>
										<input type="text" id="paymentstate" data-label="State" name="paymentstate" placeholder="" class="form-control required" value="<?php echo $user_state; ?>" />
									</div>
								</div>
								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label for="paymentcountry">Country <span>*</span></label>
										<select id="paymentcountry" name="paymentcountry" class="form-control required" data-label="Country">
												<option value="">Select Country</option>
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
												"Zimbabwe");
												foreach ($countries as $country) {
													echo '<option value="' . $country . '"' . (($user_country == $country) ? ' selected' : '') . '>' . $country . '</option>';
												}
												?>
											</select>
									</div>
								</div>
								<div class="col-12 col-lg-6">
									<div class="form-group">
										<label for="paymentzipcode">Zip Code <span>*</span></label>
										<input type="number" id="paymentzipcode" data-label="Zip Code" value="<?php echo $user_zipcode; ?>" name="paymentzipcode" placeholder="" class="form-control required" />
									</div>
								</div>

								<input type="hidden" id="product_payment_buc" value="<?php echo passion__encrypt_data($grand_total); ?>" />

						    	
								<div class="col-md-12">
							      	<div class="form-group">
						            	<label for="uploadinvoice">Upload Payment Invoice</label>
						                <input id="uploadinvoiceinput" type="file" accept=".pdf,image/*" name="attachment" class="form-control" />
							            <span class="uploaderror" style="display:none;"></span>
							        </div>
							    </div>
							    <div class="col-md-12">
							      	<div class="form-group">
							         	<label for="paymentmsg">Add Your Message:</label>
										<textarea id="paymentmsg" name="paymentmsg" rows="4" cols="50" class="form-control"></textarea>
							        </div>
							    </div>
						    </div>
						    <div class="input-group-btn">
						    	<!-- <input type="hidden" name="action" value="sendProductPaymentInvoice"> -->
						    	<input type="hidden" name="orderid" value="<?php echo $orderid; ?>">
								<div class="col-12 col-lg-6">
									<div class="button-form">
										<button id="paybyoffiline_product" type="submit" class="btn btn-green">Process Order</button>
										<span class="loaderimg" style="display:none;"><img src="<?php echo get_stylesheet_directory_uri().'/images/loader.gif'; ?>" class="ldrimg"></span>
									</div>
								</div>
				          	</div>
						</form>
						<span class="finalmessage" style="display:none;"></span>
					</div>
				<?php 
			} ?>
		</div>
	</div>

			<script>
                jQuery(function ($) {
					var userPhoneNumber = "<?php echo $user_phone; ?>";
                    var input = $('#passion_product_phone_number');

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
			</script>
<?php
} else {
	?>
	<div class="container">
		<p>Your Cart is empty!</p>
	</div>
	
	<?php
}
?>
</section>

<?php get_footer(); ?> 