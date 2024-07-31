<?php

/**

 * auction functions and definitions

 *

 * @link https://developer.wordpress.org/themes/basics/theme-functions/

 *

 * @package auction

 */



if ( ! defined( '_S_VERSION' ) ) {

	// Replace the version number of the theme on each release.

	define( '_S_VERSION', '1.0.0' );

}

require( 'inc/ajax-functions.php' );

require( 'inc/user-functions.php' );

require( 'inc/product-functions.php' );

require( 'inc/shortcode-functions.php' );

require( 'inc/bid-functions.php' );

require( 'inc/pusher.function.php' );

require( 'inc/cron-functions.php' );

require( 'inc/email-functions.php' );

require( 'inc/payment-function.php' );

require( 'inc/product-payment-function.php' );

require( 'inc/auction-functions.php' );
//require( 'inc/test-functions.php' );



function enqueue_custom_styles() {

    $random_version = time();

    wp_enqueue_style('bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');

    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

    wp_enqueue_style('custom-styles', get_template_directory_uri() . '/css/customstyles.css', array(), $random_version, 'all');

    wp_enqueue_style('responsive-styles', get_template_directory_uri() . '/css/responsive.css', array('custom-styles'), $random_version, 'all');

    wp_enqueue_style('owl-carousel-style', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');

    wp_enqueue_style('owl-theme-style', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css');

    wp_enqueue_style('slick-slider-style', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css');

    wp_enqueue_style('slick-slidertheme-style', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css');
    wp_enqueue_style('intlTelInput-style', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/css/intlTelInput.css');



    /*scripts*/

	wp_enqueue_script('jquery');



	wp_register_script( 'main', get_template_directory_uri().'/assets/js/main.js', array(), true );

    wp_localize_script( 'main', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'homeurl' => home_url(), 'number_of_bids' => get_field('number_of_bids', 'options') ));

    wp_enqueue_script( 'main' );



    /*Payment - Stripe*/

    wp_enqueue_script('stripe-gateway', 'https://js.stripe.com/v3/', array(), $random_version, true);
    // wp_enqueue_script('pdf-genrator', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array('jquery'), '', true);
    if (is_page('checkout')) {

    	$stripe_publish_key = get_field('stripe_details', 'option');

		if($stripe_publish_key){

			$spbkey = $stripe_publish_key['stripe_publish_key'];

		}else{

			$spbkey = '';

		}

        wp_register_script( "payment-script", get_stylesheet_directory_uri().'/assets/js/payment-scipt.js', array(), $random_version, true );

        wp_localize_script( 'payment-script', 'paymentAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wpsecurity' => wp_create_nonce( 'front_upload' ),'stripe_publish_key' => $spbkey),

        );            

        wp_enqueue_script( 'payment-script' );

    }   

    if (is_page('product-checkout')) {
    	$stripe_publish_key = get_field('stripe_details', 'option');

		if($stripe_publish_key){

			$spbkey = $stripe_publish_key['stripe_publish_key'];

		}else{
			$spbkey = '';
		}

        wp_register_script( "product-payment-script", get_stylesheet_directory_uri().'/assets/js/product-payment-script.js', array(), $random_version, true );

        wp_localize_script( 'product-payment-script', 'productPaymentAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wpsecurity' => wp_create_nonce( 'front_upload' ),'stripe_publish_key' => $spbkey),

        );            

        wp_enqueue_script( 'product-payment-script' );
    }

    /*Script of auction pages*/

    wp_register_script( 'auctionscript', get_template_directory_uri().'/assets/js/auctionscript.js', array(), true );

    wp_localize_script( 'auctionscript', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'homeurl' => home_url(), 'number_of_bids' => get_field('number_of_bids', 'options') ));

    wp_enqueue_script( 'auctionscript' );

    /*Script of Product pages*/
    
    
    wp_register_script('productscript', get_template_directory_uri().'/assets/js/productscript.js', array(), '', true);

    wp_localize_script( 'productscript', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'homeurl' => home_url(), 'number_of_bids' => get_field('number_of_bids', 'options') ));

    wp_enqueue_script( 'productscript' );


    // Enqueue pusher script

     wp_enqueue_script('pusher', 'https://js.pusher.com/8.2.0/pusher.min.js', array(), null, true);
     wp_enqueue_script('intlTelInput', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/js/intlTelInput.min.js', array(), null, true);



    /*Script of Biding Process*/

    wp_register_script( 'bidscript', get_template_directory_uri().'/assets/js/bidscript.js', array(), true );

    wp_localize_script( 'bidscript', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'homeurl' => home_url(), 'number_of_bids' => get_field('number_of_bids', 'options') ));

    wp_enqueue_script( 'bidscript' );

    /*Script of Register for Biding Process*/

    wp_register_script( 'registerbidscript', get_template_directory_uri().'/assets/js/registerbidscript.js', array(), true );

    wp_localize_script( 'registerbidscript', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'homeurl' => home_url(), 'number_of_bids' => get_field('number_of_bids', 'options') ));

    wp_enqueue_script( 'registerbidscript' );



    wp_enqueue_script('popper-script', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js', array('jquery'), '', true);

    wp_enqueue_script('bootstrap-script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js', array('jquery'), '', true);

    wp_enqueue_script('slick-slider-script', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array('jquery'), '', true);

    wp_enqueue_script('owl-carousel-script', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_script('html2canvas-script', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.0.0-rc.7/html2canvas.min.js', array('jquery'), '', true);

}	

add_action('wp_enqueue_scripts', 'enqueue_custom_styles');



function enqueue_admin_files(){

	wp_enqueue_style('adminstyle', get_template_directory_uri() . '/css/adminstyle.css');
    wp_enqueue_script('pdfmake-cus', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js');
    wp_enqueue_script('vfsfonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js');    

    wp_register_script( 'adminregister', get_template_directory_uri().'/assets/js/admin-bidscript.js', array(), true );
    wp_localize_script( 'adminregister', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'homeurl' => home_url(), 'number_of_bids' => get_field('number_of_bids', 'options') ));
    wp_enqueue_script( 'adminregister' );

}

add_action( 'admin_enqueue_scripts', 'enqueue_admin_files' );



/**

 * Sets up theme defaults and registers support for various WordPress features.

 *

 * Note that this function is hooked into the after_setup_theme hook, which

 * runs before the init hook. The init hook is too late for some features, such

 * as indicating support for post thumbnails.

 */

function auction_setup() {

	/*

		* Make theme available for translation.

		* Translations can be filed in the /languages/ directory.

		* If you're building a theme based on auction, use a find and replace

		* to change 'auction' to the name of your theme in all the template files.

		*/

	load_theme_textdomain( 'auction', get_template_directory() . '/languages' );



	// Add default posts and comments RSS feed links to head.

	add_theme_support( 'automatic-feed-links' );



	/*

		* Let WordPress manage the document title.

		* By adding theme support, we declare that this theme does not use a

		* hard-coded <title> tag in the document head, and expect WordPress to

		* provide it for us.

		*/

	add_theme_support( 'title-tag' );



	/*

		* Enable support for Post Thumbnails on posts and pages.

		*

		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/

		*/

	add_theme_support( 'post-thumbnails' );



	// This theme uses wp_nav_menu() in one location.

	register_nav_menus(

		array(

			'menu-1' => esc_html__( 'Primary', 'auction' ),

		)

	);



	/*

		* Switch default core markup for search form, comment form, and comments

		* to output valid HTML5.

		*/

	add_theme_support(

		'html5',

		array(

			'search-form',

			'comment-form',

			'comment-list',

			'gallery',

			'caption',

			'style',

			'script',

		)

	);



	// Set up the WordPress core custom background feature.

	add_theme_support(

		'custom-background',

		apply_filters(

			'auction_custom_background_args',

			array(

				'default-color' => 'ffffff',

				'default-image' => '',

			)

		)

	);



	// Add theme support for selective refresh for widgets.

	add_theme_support( 'customize-selective-refresh-widgets' );



	/**

	 * Add support for core custom logo.

	 *

	 * @link https://codex.wordpress.org/Theme_Logo

	 */

	add_theme_support(

		'custom-logo',

		array(

			'height'      => 250,

			'width'       => 250,

			'flex-width'  => true,

			'flex-height' => true,

		)

	);

}

add_action( 'after_setup_theme', 'auction_setup' );



/**

 * Set the content width in pixels, based on the theme's design and stylesheet.

 *

 * Priority 0 to make it available to lower priority callbacks.

 *

 * @global int $content_width

 */

function auction_content_width() {

	$GLOBALS['content_width'] = apply_filters( 'auction_content_width', 640 );

}

add_action( 'after_setup_theme', 'auction_content_width', 0 );



/**

 * Register widget area.

 *

 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar

 */

function auction_widgets_init() {

	register_sidebar(

		array(

			'name'          => esc_html__( 'Sidebar', 'auction' ),

			'id'            => 'sidebar-1',

			'description'   => esc_html__( 'Add widgets here.', 'auction' ),

			'before_widget' => '<section id="%1$s" class="widget %2$s">',

			'after_widget'  => '</section>',

			'before_title'  => '<h2 class="widget-title">',

			'after_title'   => '</h2>',

		)

	);

}

add_action( 'widgets_init', 'auction_widgets_init' );



/**

 * Enqueue scripts and styles.

 */

function auction_scripts() {

	wp_enqueue_style( 'auction-style', get_stylesheet_uri(), array(), _S_VERSION );

	wp_style_add_data( 'auction-style', 'rtl', 'replace' );



	wp_enqueue_script( 'auction-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );



	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {

		wp_enqueue_script( 'comment-reply' );

	}

}

add_action( 'wp_enqueue_scripts', 'auction_scripts' );



/**

 * Implement the Custom Header feature.

 */

require get_template_directory() . '/inc/custom-header.php';



/**

 * Custom template tags for this theme.

 */

require get_template_directory() . '/inc/template-tags.php';



/**

 * Functions which enhance the theme by hooking into WordPress.

 */

require get_template_directory() . '/inc/template-functions.php';



/**

 * Customizer additions.

 */

require get_template_directory() . '/inc/customizer.php';



/**

 * Load Jetpack compatibility file.

 */

if ( defined( 'JETPACK__VERSION' ) ) {

	require get_template_directory() . '/inc/jetpack.php';

}



/* template redirect */

function custom_template_redirect() {

    if (is_user_logged_in() && (is_page('login') || is_page('signup'))) {

      

        wp_redirect(home_url('/dashboard'));

        exit();

    } elseif (!is_user_logged_in() && is_page('dashboard')) {

       

        wp_redirect(home_url('/login'));

        exit();

    }
    if (is_singular('product') || is_page('cart') || is_page('product-checkout')) {
        if (!current_user_can('administrator')) {
            if (!is_user_logged_in()) {            
                wp_redirect(home_url('/login'));
                exit;
            } else if(get_user_meta(get_current_user_id(), 'userstatus', true) !== 'accepted' && is_user_logged_in()) {
                wp_redirect(home_url('/dashboard/verification/'));
                exit;
            }
        }
    }
}

add_action('template_redirect', 'custom_template_redirect');



/* unique name */

function generateUniqueUsername($email) {

    $username = strstr($email, '@', true);

    $username .= '_' . generateRandomString(4);  

    return $username;

}



function generateRandomString($length = 6) {

    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $randomString = '';

    $charactersLength = strlen($characters);

    for ($i = 0; $i < $length; $i++) {

        $randomString .= $characters[rand(0, $charactersLength - 1)];

    }   

    return $randomString;

}



/* hide admin bar */

function hide_admin_bar_non_admin() {

    if (!current_user_can('administrator')) {

        add_filter('show_admin_bar', '__return_false');

    }

}

add_action('after_setup_theme', 'hide_admin_bar_non_admin');



/* watchlist */
function watchlist_db() {
   global $wpdb;
   $charset_collate = $wpdb->get_charset_collate();
   $watchlist_table_name = $wpdb->prefix . 'watchlist';
   $autobids_table_name = $wpdb->prefix . 'autobids';

   $watchlist_sql = "CREATE TABLE IF NOT EXISTS $watchlist_table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      user_id int(11) NOT NULL,
      auction_id int(11) NOT NULL,
      created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY id (id)
   ) $charset_collate;";

   $autobids_sql = "CREATE TABLE IF NOT EXISTS $autobids_table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      auctionid int(11) NOT NULL,
      userid int(11) NOT NULL,
      amount VARCHAR(255) NOT NULL,
      status VARCHAR(255) NOT NULL,
      created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY id (id)
   ) $charset_collate;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($watchlist_sql);
   dbDelta($autobids_sql);
}

add_action('init', 'watchlist_db');



/* breadcrumb */

function custom_breadcrumb() {

    echo '<ul class="breadcrump-main">';

    echo '<li><a href="' . esc_url(home_url()) . '">Home</a></li>';

    

    if (is_singular('auction')) {
        $taxonomy_slug = 'auction-category';
        $terms = wp_get_post_terms(get_the_ID(), $taxonomy_slug);

        if (!empty($terms)) {

            foreach ($terms as $term) {

                $termurl = home_url('auction').'?category='.$term->term_id;
                echo '<li><a href="' . esc_url($termurl) . '">' . esc_html($term->name) . '</a></li>';

                if ($term->parent) {

                    $parent_term = get_term($term->parent, $taxonomy_slug);
                    $parenttermurl = home_url('auction').'?category='.$parent_term->term_id;
                    echo '<li><a href="' . esc_url($parenttermurl) . '">' . esc_html($parent_term->name) . '</a></li>';

                }

            }

        }

    }  
    if (is_singular('product')) {
        $taxonomy_slug = 'product-category';
        $terms = wp_get_post_terms(get_the_ID(), $taxonomy_slug);

        if (!empty($terms)) {

            foreach ($terms as $term) {

                $termurl = home_url('auction').'?category='.$term->term_id;
                echo '<li><a href="' . esc_url($termurl) . '">' . esc_html($term->name) . '</a></li>';

                if ($term->parent) {

                    $parent_term = get_term($term->parent, $taxonomy_slug);
                    $parenttermurl = home_url('auction').'?category='.$parent_term->term_id;
                    echo '<li><a href="' . esc_url($parenttermurl) . '">' . esc_html($parent_term->name) . '</a></li>';

                }

            }

        }

    }
    if (is_home()) {

        echo '<li><span>Events</span></li>';

    } else {

        echo '<li><span>' . get_the_title() . '</span></li>';

    }

    echo '</ul>';

}



/* accept-decline user request */

function custom_user_column_header($columns) {

    $new_columns = array();

     foreach ($columns as $key => $value) {

        if ($key === 'role') {

            $new_columns['customer_type'] = 'Customer Type';

        }

        $new_columns[$key] = $value;

    }

    $new_columns['status'] = 'Status';   

    return $new_columns;

}

add_filter('manage_users_columns', 'custom_user_column_header');



function custom_user_column_content($value, $column_name, $user_id) {

    $user = get_userdata($user_id);

    if ($column_name == 'status' && in_array('subscriber', (array) $user->roles)) {

        $status = get_user_meta($user_id, 'userstatus', true);

        if (empty($status)) {

            $status = 'Pending';

        }

        return $status;

    }

    if ($column_name == 'customer_type' && in_array('subscriber', (array) $user->roles)) {

        $account_type = get_user_meta($user_id, 'account_type', true);

        return $account_type;

    }

    return $value;

}

add_filter('manage_users_custom_column', 'custom_user_column_content', 10, 3);



function add_accept_decline_row_actions($actions, $user_object) {

    if (in_array('subscriber', (array) $user_object->roles)) {

        $status = get_user_meta($user_object->ID, 'userstatus', true);

        if($status == 'accepted') {

             $actions['decline'] = '<span class="decline" data-userid="' . $user_object->ID . '">Decline</span>';

        } elseif($status == 'rejected'){

            $actions['accept'] = '<span class="accept" data-userid="' . $user_object->ID . '">Accept</span>';

        } else {

            $actions['accept'] = '<span class="accept" data-userid="' . $user_object->ID . '">Accept</span>';

            $actions['decline'] = '<span class="decline" data-userid="' . $user_object->ID . '">Decline</span>';

        }

    }

    return $actions;

}

add_filter('user_row_actions', 'add_accept_decline_row_actions', 10, 2);



function enqueue_accept_decline_script() {

    ?>

    <script>

        jQuery(document).ready(function($) {

            $('.accept, .decline').on('click', function(e) {

                e.preventDefault();

                var userId = $(this).data('userid');

                var action = $(this).hasClass('accept') ? 'accept' : 'decline';

                $.ajax({

                    url: ajaxurl,

                    type: 'POST',

                    data: {

                        action: 'update_user_status',

                        user_id: userId,

                        status_action: action

                    },

                    success: function(response) {

                        window.location.reload();

                    },

                    error: function(xhr, status, error) {

                        console.error(error);

                    }

                });

            });

        });

    </script>

    <?php

}

add_action('admin_footer', 'enqueue_accept_decline_script');



function handle_accept_decline_actions() {
    if (isset($_POST['action']) && $_POST['action'] == 'update_user_status' && isset($_POST['user_id']) && isset($_POST['status_action'])) {
        $user_id = intval($_POST['user_id']);
        $status_action = $_POST['status_action'];
        $status = $status_action == 'accept' ? 'accepted' : 'rejected';
        update_user_meta($user_id, 'userstatus', $status);
        $subject = "Verification - Result";
        $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">';
        if($status == 'accepted') {
            $message .= 'Your account has been approved by the administrator. You are now able to participate in auctions.</p>';
        } else if($status == 'rejected') {
            $message .= 'Your account has been rejected by the administrator. You are not able to participate in auctions.</p>';
            $message .= '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">If you have any questions, please feel free to submit them through our <a href="'.site_url().'/contact-us/" target="_blank">contact page</a>.</p>';
        } else {
            $message .= 'Your account is currently under administrative control, limiting your participation in auctions. You will receive notifications regarding any changes made by the administrator.</p>';
        }
        $user_mail_sent = passionAuctionEmail($user_id, $subject, $message);
    }
    wp_die();
}
add_action('wp_ajax_update_user_status', 'handle_accept_decline_actions');


function add_custom_user_edit_fields($user) {

    if (in_array('subscriber', (array) $user->roles)) {

        $user_status = get_user_meta($user->ID, 'userstatus', true);

        ?>

        <h3>User Status</h3>

        <table class="form-table">

            <tr>

                <th><label for="user_accept">User Status</label></th>

                <td>

                    <select name="user_status" id="user_status">

                        <option value="">Please Select Status</option>

                        <option value="accepted" <?php echo ($user_status=='accepted' ? 'selected' : '')?>>Accept</option>

                        <option value="rejected" <?php echo ($user_status=='rejected' ? 'selected' : '')?>>Decline</option>

                    </select>

                </td>

            </tr>

        </table>

        <?php

    }

}

add_action('edit_user_profile', 'add_custom_user_edit_fields');
add_action('show_user_profile', 'add_custom_user_edit_fields');



function save_custom_user_edit_fields($user_id) {
    if (current_user_can('edit_users')) {
        if (isset($_POST['user_status'])) {
            $new_status = sanitize_text_field($_POST['user_status']);
            $old_status = get_user_meta($user_id, 'userstatus', true);
            if ($new_status === $old_status) {
                return;
            }
            update_user_meta($user_id, 'userstatus', $new_status);
            $subject = "Verification - Result";

            $message = '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">';
            if($new_status == 'accepted') { 
                $message .= 'Your account has been approved by the administrator. You are now able to participate in auctions.</p>';
            } else if($new_status == 'rejected') {
                $message .= 'Your account has been rejected by the administrator. You are not able to participate in auctions.</p>';
                $message .= '<p style="font-size: 16px;margin:0 0 15px;font-family: \'Noto Sans\', sans-serif;">If you have any questions, please feel free to submit them through our <a href="'.site_url().'/contact-us/" target="_blank">contact page</a>.</p>';
            } else {
                $message .= 'Your account is currently under administrative control, limiting your participation in auctions. You will receive notifications regarding any changes made by the administrator.</p>';
            }

            $user_mail_sent = passionAuctionEmail($user_id, $subject, $message);
        }
    }
}
add_action('personal_options_update', 'save_custom_user_edit_fields');
add_action('edit_user_profile_update', 'save_custom_user_edit_fields');

// Passion encrypt and decrypt function

function passion__encrypt_data($encrypt_data) {
    $secret_key = 'passion_encryption_imp_key_qwerzxcv';
    $iv = openssl_random_pseudo_bytes(16); // Generate a random IV of 16 bytes

    $encrypted_id = base64_encode($iv . openssl_encrypt($encrypt_data, 'aes-256-cbc', $secret_key, OPENSSL_RAW_DATA, $iv));

    return $encrypted_id;
}

function passion__decrypt_data($decrypt_data) {
    $secret_key = 'passion_encryption_imp_key_qwerzxcv';

    $decoded_data = base64_decode($decrypt_data);
    $iv = substr($decoded_data, 0, 16); // Extract IV from the beginning of the decoded data
    $encrypted_data = substr($decoded_data, 16); // Extract the rest as encrypted data

    $decrypted_id = openssl_decrypt($encrypted_data, 'aes-256-cbc', $secret_key, OPENSSL_RAW_DATA, $iv);

    return $decrypted_id;
}

$post_type = 'auctionorders';
// Register the columns.
add_filter( "manage_{$post_type}_posts_columns", function ( $defaults ) {
$defaults['custom-one'] = 'Auction Name';
$defaults['custom-two'] = 'Price';
$defaults['custom-three'] = 'User';
$defaults['custom-four'] = 'Invoice Number';
$defaults['custom-five'] = 'Order Status';
$defaults['custom-six'] = 'Payment Mode';
return $defaults;
} );
// Handle the value for each of the new columns.
add_action( "manage_{$post_type}_posts_custom_column", function ( $column_name, $post_id ) {

    $orderdata = get_post_meta($post_id);
    $auctionid = $orderdata['auctionid'][0]; 
    $auctiontitle = get_the_title($auctionid); 
    $orderamount = $orderdata['amount'][0];
    $userid = $orderdata['userid'][0];
    $orderinvoiceid = $orderdata['orderinvoiceid'][0];

    $paymenttype = $orderdata['paymenttype'][0];
    $biduser = $orderdata['biduser'][0];
    if(isset($orderdata['invoiceid'][0])){
        $invoiceid = $orderdata['invoiceid'][0];
    }else{
        $invoiceid = '';
    }

    if(isset($orderdata['invoiceurl'][0])){
        $invoiceurl = $orderdata['invoiceurl'][0];
    }else{
        $invoiceurl = '';
    }

    if(isset($orderdata['userpaymentmessage'][0])){
        $userpaymentmessage = $orderdata['userpaymentmessage'][0];
    }else{
        $userpaymentmessage = '';
    }

    $user_info = get_userdata($userid);
    if ($user_info) {
        $user_details = $user_info->first_name . ' ' . $user_info->last_name . ' (' . $user_info->user_email . ')';
    }else{
        $user_details = '';
    }    
if ( $column_name == 'custom-one' ) {
echo $auctiontitle;
}
if ( $column_name == 'custom-two' ) {
echo '$ '.number_format($orderamount, 2);
}
if ( $column_name == 'custom-three' ) {
echo $user_details;
}
if ( $column_name == 'custom-four' ) {
 echo esc_html($orderinvoiceid);
}
if ( $column_name == 'custom-five' ) {
echo get_field( 'status', $post_id );
}
if ( $column_name == 'custom-six' ) {
echo esc_html($paymenttype);
}
}, 10, 3 );


// Product Order admin table

$post_type_product_order = 'product-order';
// Register the columns.
add_filter( "manage_{$post_type_product_order}_posts_columns", function ( $defaults ) {
    
    // $defaults['custom-two'] = 'Date';
    $defaults['custom-three'] = 'Price';
    $defaults['custom-four'] = 'User';
    $defaults['custom-five'] = 'Order Status';
    return $defaults;
} );

// Handle the value for each of the new columns.
add_action( "manage_{$post_type_product_order}_posts_custom_column", function ( $column_name, $post_id ) {

    // if ( $column_name === 'custom-two' ) {
    //     echo get_the_date( '', $post_id );
    // }
    if ( $column_name === 'custom-three' ) {
        // Get the price from post meta
        $price = get_post_meta( $post_id, 'product_order_grand_total', true );
        if($price) {
            echo '$ ' . number_format( $price, 2 );    
        }        
    }
    if ( $column_name === 'custom-four' ) {
        // Get the user associated with the order
        $user_id = get_post_meta( $post_id, 'userid', true );
        $user_info = get_userdata( $user_id );
        if ( $user_info ) {
            echo $user_info->first_name . ' ' . $user_info->last_name . ' (' . $user_info->user_email . ')';
        } else {
            echo 'Unknown User';
        }
    }
    if ( $column_name === 'custom-five' ) {
        // Get the user associated with the order
        $order_status = get_post_meta( $post_id, 'product_order_status', true );
        if($order_status) {
            echo ucfirst($order_status);
        }
    }
}, 10, 2 );

