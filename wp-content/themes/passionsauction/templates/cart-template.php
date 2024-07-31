<?php
/* Template Name: Cart */
get_header();

global $wpdb;
$table_name = $wpdb->prefix . 'passions_product_cart';

// Get current user's ID
$current_user_id = get_current_user_id();

// Retrieve cart items for the current user from the custom table
$cart_items = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d",
        $current_user_id
    )
);
?>
<section class="account-section">
<div class="container custom-aucproduct-cart">
<?php
if ($cart_items) {
    echo '<div class="heading"><h2>Cart Items</h2></div>';
    
    echo '<span class="loader-single-product"><img src="' . get_stylesheet_directory_uri() . '/images/loader.gif" /></span>';
    
    echo '<div class="cart-table-set"><table class="cart table" id="auc_product_crt_table">';
    echo '<thead>
            <tr>
                <th></th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th></th>
            </tr>
          </thead>';
    echo '<tbody>';
    $grand_total = 0;
    foreach ($cart_items as $cart_item) {
        
        $product_id = $cart_item->product_id;
        $product = get_post($product_id);
        $product_name = $product->post_title;
        $product_permalink = get_permalink($product_id);
        
        $product_image = get_the_post_thumbnail_url($product_id);
        $sale_price_check = get_field('product_sale_price', $product_id);
        if ($sale_price_check) {
            $product_price = $sale_price_check;
        } else {
            $product_price = get_field('product_regular_price', $product_id);
        }
        
        $item_total = $cart_item->quantity * $product_price;
        $grand_total += $item_total;
        echo '<tr>';
            echo '<td class="cart-product-image-td">';
                echo '<span class="cart-product-image">';
                if($product_image) {
                    echo '<img src="' . get_the_post_thumbnail_url($product_id) . '" />';
                } else {
                    echo '<img src="' . get_stylesheet_directory_uri() .'/images/no-image.png" />';
                }
                echo '</span>';
            echo '</td>';
            echo '<td><a href="' . $product_permalink . '">' . $product_name . '</a></td>';
            // echo '<td>' . $cart_item->quantity . '</td>'; ?>
            <td>
                <div class="quantity-control">
                    <button class="quantity-decrease">-</button>
                    <?php echo '<input type="number" data-product="'.passion__encrypt_data($product_id).'" data-item-id="'. passion__encrypt_data($cart_item->id) .'" class="quantity-input" value="' . $cart_item->quantity . '" min="1">'; ?>
                    <button class="quantity-increase">+</button>
                </div>
            </td>
            <?php
            echo '<td>$ ' . $product_price . '</td>';
            echo '<td>$ ' . $item_total . '</td>';
            echo '<td>' . '<a class="delete-cart-item" href="javascript:void(0)" data-item-id="'. passion__encrypt_data($cart_item->id) .'"><i class="fa fa-trash"></i></a></td>';
        echo '</tr>';
    }
    // Add Grand Total row
    echo '<tr>';
        echo '<td colspan="4" style="text-align: right;"><strong>Grand Total:</strong></td>';
        echo '<td colspan="2">$ ' . $grand_total . '</td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table></div>';

    echo '<div class="checkout-btn-row"><a class="btn btn-green" href="'. home_url( 'product-checkout' ) .'">Proceed to checkout</a></div>';
    echo '<span class="loader-img-cart-item">';
        echo '<img src="'. get_stylesheet_directory_uri() .'/images/loader.gif" />';
    echo '</span>';
    echo '<span class="cart-message" style="display: none;"></span>';
} else {
    echo '<p>Your cart is empty.</p>';
}
?>
</div></section>
<?php
get_footer();

