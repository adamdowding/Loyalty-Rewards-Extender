<?php
/*
Plugin Name: MyRewards Extender
Plugin URI: https://webqo.uk/
Description: Extend MyRewards Plugin
Version: 1.0.0
Author: Adam Dowding
Author URI: https://webqo.uk/
Text Domain: acf
Domain Path: /lang
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
Shows potential points available as per the MyRewards plugin.
*/

function show_potential_points(){
	global $post, $product; //gets globabl post and product variables
	if($product->is_on_sale){ //if it's on sale
		$points = ceil($product->get_sale_price()); //use the sale price to calculate points
	}
	else{ //if it isn't on sale
		$points = ceil($product->get_price()); //use normal price to calculate points
	}
	if ( is_user_logged_in() ) {
		echo "<p id='wq_points'>You will receive {$points} points for this purchase.</p>";
	}
	else{
		echo "<p id='wq_points'>You could receive {$points} points for this purchase. <a href='/my-account'>Login or Register.</a></p>";
	}
}

add_action('woocommerce_before_add_to_cart_form','show_potential_points');

/*
Creates a list of a users Used and Unused coupons, accessed via coupon_list shortcode
*/
add_shortcode('get_user_coupons', 'coupon_list' );
function coupon_list() {
    $coupon_posts = get_posts( array(
        'posts_per_page'   => -1,
        'orderby'          => 'name',
        'order'            => 'asc',
        'post_type'        => 'shop_coupon',
        'post_status'      => 'publish',
    ) );

    $active_coupon_codes = [];
	$inactive_coupon_codes = [];
	$user_email = strtolower(wp_get_current_user()->user_email);
    foreach( $coupon_posts as $coupon_post) {
		if(strtolower($coupon_post->customer_email[0]) == $user_email && $coupon_post->usage_count < 1){
			$active_coupon_codes[] = $coupon_post->post_name;
		}
		else if(strtolower($coupon_post->customer_email[0]) == $user_email && $coupon_post->usage_count >= 1){
			$inactive_coupon_codes[] = $coupon_post->post_name;
		}
    }
	?>
	<br>

	<div class="coupon-row">
		<h3>Your Coupons</h3>
		<p>Your reward codes can only be used once.</p>
	  <div class="coupon-column">
		<h4>Available Coupons</h4>
		  <?php
		  foreach( $active_coupon_codes as $active_code){
			  echo "<p class='active-coupon'>".$active_code."</p><br>";
		  }
		  ?>
	  </div>
	  <div class="coupon-column">
		<h4>Used Coupons</h4>
		  		  <?php
		  foreach( $inactive_coupon_codes as $inactive_code){
			  echo "<p class='inactive-coupon'>".$inactive_code."</p><br>";
		  }
		  ?>

	  </div>
	</div>
<p>Don't know what's going on here? <a href="https://oceanvape.co.uk/oceanvape-rewards/">Learn More</a></p>
<?php 
}

/*
 * Display point information on checkout or cart pages via shortcode or action*/
function checkout_points_calc(){
	$subtotal = WC()->cart->get_subtotal(); //get the subtotal of the current cart
	$points = $subtotal; //points are equal to total
	$points = ceil($points); //round up points
	$user_points = do_shortcode('[wr_simple_points system="default"]'); //uses the shortcode available in MyRewards to get users points.
	if(!is_user_logged_in() ) { //if they are not logged in
	echo "<p>You could earn {$points} points with this order if you register an account. <a href='/my-account'>Register Now</a></p>"; //echo what they could earn
		}
	else if(is_user_logged_in() ) { // if they are logged in
		echo "<p>You will earn {$points} points with this order!</p>"; //echo what they will earn
		}
}
add_action('woocommerce_cart_totals_before_shipping','checkout_points_calc');
add_shortcode('get_points_for_order', 'checkout_points_calc' );
