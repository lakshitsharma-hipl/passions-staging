<?php 
/* Template Name: Dashboard */
if(!is_user_logged_in()){
	wp_redirect('/login/');
	exit;
}

get_header(); 

$current_user_id = get_current_user_id();
$get_userdata = get_userdata($current_user_id);
$first_name = $get_userdata->first_name;
$last_name = $get_userdata->last_name;
$user_email = $get_userdata->user_email;
$user_registered = $get_userdata->user_registered;
$date = new DateTime($user_registered);
$formattedDate = $date->format("F Y");
$user_roles = $get_userdata->roles;
$status = get_user_meta($current_user_id, 'userstatus', true);

$userPhoneNumber = get_user_meta($current_user_id, 'user_phone_number', true);
$user_country = get_user_meta($current_user_id, 'country', true);
$state = get_user_meta($current_user_id, 'state', true);
$city = get_user_meta($current_user_id, 'city', true);
$address = get_user_meta($current_user_id, 'address', true);
?>

<section class="account-dashboard">
	<div class="customcontainer">
		<div class="dashboardmax">
			<div class="userframe">
				<div class="userimg"><img src="<?php echo get_template_directory_uri(); ?>/images/user.png" class="img-fluid" /></div>
				<div class="userdetails">
					<h6 class="name"><?php echo $first_name.' '.$last_name; ?></h6>
					<p class="mb-0 joindate">Join since <?php echo $formattedDate; ?></p>
				</div>
				<?php if($user_roles[0] == 'subscriber' && !$status) : ?>
				<div class="userapproval_status">
					<p class="mb-0 joindate">Your account is pending approval. Once approved by the administrator, you will receive a notification via email.</p>
				</div>
				<?php endif; ?>
			</div>
			<!--  -->
			<div class="dashboard-wrapper">
				<div class="dashboard-sidebar">
					<ul class="form-desktop">
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
					<ul class="formobile">
						<li class="active"><a href="<?php echo site_url(); ?>/dashboard/">General Account</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/password/">Password</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/verification/">Verification</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/order-history/">Order History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/bid-history/">Bid History</a></li>
						<li><a href="<?php echo site_url(); ?>/dashboard/watchlist/">Watch List</a></li>
						<li><a href="<?php echo site_url(); ?>/product-order-history/">Product Order History</a></li>
						<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
					</ul>
				</div>
				<div class="dashboard-data">
					<form method="post" id="dashboard_edit_form" class="signup-form">
						<div class="row">
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>User ID</label>
									<input type="text" id="userid" value="<?php echo $current_user_id; ?>" class="form-control" readonly/>
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>Email Address</label>
									<input type="email" name="" value="<?php echo $user_email; ?>" placeholder="Email Address" class="form-control" readonly/>
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>First Name <span class="required-form-field">*</span></label>
									<input type="text" id="dash_user_first_name" name="dash_user_first_name" value="<?php echo $first_name; ?>" placeholder="Enter First Name" class="form-control dashboard_validate" />
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>Last Name <span class="required-form-field">*</span></label>
									<input type="text" id="dash_user_last_name" name="dash_user_last_name" value="<?php echo $last_name; ?>" placeholder="Enter Last Name" class="form-control dashboard_validate" />
								</div>
							</div>
							<div class="col-12 col-lg-6">
							<label>Phone Number <span class="required-form-field">*</span> </label>
							<input
								type="tel"
								name="passion_edit_phone_number"
								id="passion_edit_phone_number"
								class="form-control dashboard_validate phonenumber_itnl signupphone"
								placeholder="Enter your phone number"
								oninput="this.value = this.value.replace(/[^0-9]/g, '');" autofocus
							/>
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">								
									<label>Country <span class="required-form-field">*</span> </label>
								<select id="passion_edit_country" name="passion_edit_country" class="form-control dashboard_validate" data-label="Country">
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
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>State <span class="required-form-field">*</span></label>
									<input type="text" id="dash_user_state" name="dash_user_state" value="<?php echo $state; ?>" placeholder="Enter State" class="form-control dashboard_validate" />
								</div> 
							</div>
							<div class="col-12 col-lg-6">
								<div class="form-group">
									<label>City <span class="required-form-field">*</span></label>
									<input type="text" id="dash_user_city" name="dash_user_city" value="<?php echo $city; ?>" placeholder="Enter City" class="form-control dashboard_validate" />
								</div>
							</div>
							<div class="col-12 col-lg-12">
								<div class="form-group">
									<label>Address <span class="required-form-field">*</span></label>
									<textarea name="passion_signup_address" id="passion_signup_address" class="form-control dashboard_validate" placeholder="Enter your address"><?php echo $address; ?></textarea>
									</div>
								</div>
							<div class="col-12 col-lg-12">
								<div class="form-group text-end">
									<button type="submit" class="btn btn-black dash_user_save" data-id="<?php echo get_current_user_id(); ?>">Save Changes</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<script>
                jQuery(function ($) {
					var userPhoneNumber = "<?php echo $userPhoneNumber; ?>";
                    var input = $('#passion_edit_phone_number');

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
		</div>
	</div>
</section>

<?php get_footer(); ?> 