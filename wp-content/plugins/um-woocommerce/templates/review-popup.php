<?php

/**

 * Template for the "View order" popup

 * Used on the "Profile" page, "My Orders" tab

 * Called from the WooCommerce_Main_API->ajax_get_order() method

 *

 * This template can be overridden by copying it to yourtheme/ultimate-member/um-woocommerce/order-popup.php

 */

if( !defined( 'ABSPATH' ) ) {

	exit;

}

?>



<!-- um-woocommerce/templates/order-popup.php -->

<div class="um-woo-order-head um-popup-header">



	<div class="um-woo-customer">

		<?php echo get_avatar( get_current_user_id(), 34 ); ?>

		<span><?php echo esc_html( um_user( 'display_name' ) ); ?></span>

	</div>



	<div class="um-woo-orderid">

		<?php printf( __( 'Add Review on Order no.# %s', 'um-woocommerce' ), $order_id ); ?>

		<a href="#" class="um-woo-order-hide"><i class="um-icon-close"></i></a>

	</div>



	<div class="um-clear"></div>

</div>



<div class="um-woo-order-body um-popup-autogrow2">
    <div class="review-form">
    	
    	<form class="um-reviews-form" id="reviewFormSubmit">



				<span class="um-reviews-rate" data-key="rating" data-number="5" data-score="0" style="cursor: pointer;">
					<i data-alt="1" class="star-off-png" title="1 Star"></i>
					<i data-alt="2" class="star-off-png" title="2 Star"></i>
					<i data-alt="3" class="star-off-png" title="3 Star"></i>
					<i data-alt="4" class="star-off-png" title="4 Star"></i>
					<i data-alt="5" class="star-off-png" title="5 Star"></i>
					<input name="rating" id="selected-review" type="hidden">
				</span>

              <br/>

				<span class="um-reviews-title"><input type="text" required="required" name="title" id="review_title" placeholder="Enter subject..." maxlength="60"></span>
                <br/>
                 <br/>

				<span class="um-reviews-content"><textarea name="content" required="required" id="review_details" placeholder="Enter your review..."></textarea></span>

               <br/>

				<input type="hidden" name="user_id" id="user_id" value="<?php echo get_current_user_id(); ?>">

				<span class="um-reviews-send"><input type="submit" value="Submit Review" class="um-button"></span>



			</form>
    </div>

    
</div>



<div class="um-popup-footer" style="height:30px"></div>