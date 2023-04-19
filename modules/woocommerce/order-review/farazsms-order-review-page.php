<?php


if ( isset( $_GET['order_id'] ) ) {
	$order_id = intval( $_GET['order_id'] );
	$order = wc_get_order( $order_id );
	$billing_first_name = $order->get_billing_first_name();
	$billing_last_name = $order->get_billing_last_name();
	$user_full_name = $billing_first_name . ' ' . $billing_last_name;
	$user_email = $order->get_billing_email();
	$items = $order ? $order->get_items() : [];
} else {
	$items = [];
	echo '<h2>'. esc_html__( 'No order ID provided', 'farazsms' ) .'</h2>';
}

?>

<div class="order-review-tabs">
	<div class="tab-content review-content">
		<?php foreach ( $items as $item_id => $item ) : ?>
			<?php $product = $item->get_product(); ?>
			<div class="review-content-form">
				<h3 class="review-title"><?php echo __('Product name: ', 'farazsms') . $product->get_name(); ?></h3>

				<form action="" method="post" class="review-form">
					<input type="hidden" name="product_id" id="fsms_order_review_product_id" class="fsms_order_review_product_id" value="<?php echo $product->get_id(); ?>">
					<input type="hidden" name="user_full_name" class="fsms_order_review_user_name" value="<?php echo esc_attr( $user_full_name ); ?>">
					<input type="hidden" name="user_email" class="fsms_order_review_user_email" value="<?php echo $user_email ?>">

					<div class="form-group">
						<label for="fsms_order_review_rating_<?php echo $item_id; ?>" class="review-label">Rating</label>
						<select id="fsms_order_review_rating_<?php echo $item_id; ?>" name="rating" class="form-control review-select fsms_order_review_rating" required>
							<option value=""><?php esc_html__('Select a rating', 'farazsms') ?></option>
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
								<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="fsms_order_review_review_<?php echo $item_id; ?>" class="review-label"><?php esc_html__('Review', 'farazsms') ?></label>
						<textarea id="fsms_order_review_review_<?php echo $item_id; ?>" name="review" class="form-control review-textarea fsms_order_review_review" rows="5" required></textarea>
					</div>
					<div id="fsms_order_review_message_<?php echo $item_id; ?>" class="fsms_order_review_message" style="display: none;"></div>
					<button type="button" class="btn btn-primary review-submit" id="fsms_order_review_submit_button_<?php echo $item_id; ?>"><?php esc_html__('Submit Review', 'farazsms') ?></button>
				</form>
			</div>
		<?php endforeach; ?>
	</div>
</div>

