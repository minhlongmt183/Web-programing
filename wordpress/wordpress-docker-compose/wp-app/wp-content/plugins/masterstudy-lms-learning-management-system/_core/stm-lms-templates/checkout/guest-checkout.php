<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * $var $user_id
 */

$items         = STM_LMS_Guest_Checkout::get_cart_items();
$guest_enabled = STM_LMS_Guest_Checkout::guest_enabled();
?>
<div class="masterstudy-checkout-container__top">
<?php
	STM_LMS_Templates::show_lms_template(
		'account/private/parts/top_info',
		array(
			'title' => esc_html__( 'Checkout', 'masterstudy-lms-learning-management-system' ),
		)
	);
	?>
</div>
<?php
if ( empty( $items ) ) :
	STM_LMS_Templates::show_lms_template( 'checkout/empty-cart' );
else :
	$total = 0;
	?>
	<div class="masterstudy-guest-checkout">
		<div class="masterstudy-checkout-container">
			<div class="masterstudy-checkout-table">
				<div class="masterstudy-checkout-table__header">
					<div class="masterstudy-checkout-course-info">
						<div class="masterstudy-checkout-course-info__value"><?php echo esc_html__( 'Order items', 'masterstudy-lms-learning-management-system' ); ?></div>
					</div>
				</div>
				<div class="masterstudy-checkout-table__body">
				<?php
				foreach ( $items as $item ) :
					if ( ! get_post_type( $item['item_id'] ) ) {
						continue;
					}
						$terms        = wp_get_post_terms( $item['item_id'], 'stm_lms_course_taxonomy', array( 'fields' => 'ids' ) );
						$category_ids = ! is_wp_error( $terms ) && ! empty( $terms ) ? array_map( 'intval', $terms ) : array();
						$total       += $item['price'];
					?>
					<div class="masterstudy-checkout-table__body-row">
						<div class="masterstudy-checkout-course-info">
							<div class="masterstudy-checkout-course-info__image">
								<a href="<?php echo esc_url( get_the_permalink( $item['item_id'] ) ); ?>">
								<?php
								if ( function_exists( 'stm_get_VC_attachment_img_safe' ) ) :
									echo wp_kses_post( stm_get_VC_attachment_img_safe( get_post_thumbnail_id( $item['item_id'] ), 'full' ) );
								else :
									?>
									<img src="<?php echo esc_url( get_the_post_thumbnail_url( $item['item_id'], 'full' ) ); ?>">
								<?php endif; ?>
								</a>
							</div>
							<div class="masterstudy-checkout-course-info__common">
								<div class="masterstudy-checkout-course-info__title">
									<a href="<?php echo esc_url( get_the_permalink( $item['item_id'] ) ); ?>">
										<?php echo esc_html( apply_filters( 'stm_lms_single_item_cart_title', sanitize_text_field( get_the_title( $item['item_id'] ) ), $item ) ); ?>
									</a>
									<?php
									$additional_info  = '';
									$enterprise_title = '';

									if ( ! empty( $item['enterprise'] ) && intval( $item['enterprise'] ) !== 0 ) {
										$additional_info  = '<span class="masterstudy-checkout-course-info__status">enterprise</span>';
										$enterprise_title = get_the_title( $item['enterprise'] );
									} elseif ( ! empty( $item['bundle'] ) && intval( $item['bundle'] ) !== 0 ) {
										$additional_info = '<span class="masterstudy-checkout-course-info__status">' . __( 'bundle', 'masterstudy-lms-learning-management-system' ) . '</span>';
									}

									echo wp_kses_post( $additional_info );
									?>
								</div>
								<div class="masterstudy-checkout-course-info__category">
								<?php
								if ( ! empty( $enterprise_title ) ) {
									echo esc_html__( 'for group', 'masterstudy-lms-learning-management-system' ) . ' ' . esc_html( $enterprise_title );
								} else {
									STM_LMS_Templates::show_lms_template(
										'components/course/categories',
										array(
											'term_ids' => $category_ids,
											'only_one' => false,
											'inline'   => true,
										)
									);
								}

								if ( isset( $item['bundle'] ) ) {
									$bundle_count = STM_LMS_Order::get_bundle_courses_count( $item['bundle'] );

									if ( isset( $bundle_count ) && $bundle_count > 0 ) {
										echo esc_html( $bundle_count . ' ' . esc_html__( 'courses in bundle', 'masterstudy-lms-learning-management-system' ) );
									}
								}
								?>
								</div>
							</div>
							<div class="masterstudy-checkout-course-info__price">
								<span><?php echo esc_html( STM_LMS_Helpers::display_price( $item['price'] ) ); ?></span>
								<div class="stm_lms_cart__item_delete">
									<i class="stmlms-trash1"
									<?php
									if ( ! empty( $item['enterprise'] ) ) {
										?>
											data-delete-enterprise="<?php echo esc_attr( $item['enterprise'] ); ?>"
											<?php
									}
									if ( $guest_enabled ) {
										?>
											data-delete-guest="guest"
											<?php
									}
									?>
									data-delete-course="<?php echo intval( $item['item_id'] ); ?>"></i>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				</div>
				<div class="masterstudy-checkout-table__footer">
					<div class="masterstudy-checkout-course-info">
						<div class="masterstudy-checkout-course-info__label"><?php echo esc_html__( 'Total:', 'masterstudy-lms-learning-management-system' ); ?></div>
						<div class="masterstudy-checkout-course-info__price"><?php echo esc_html( STM_LMS_Helpers::display_price( $total ) ); ?></div>
					</div>
				</div>
			</div>
		</div>
		<?php if ( is_user_logged_in() ) : ?>
			<div class="stm_lms_checkout">
				<?php STM_LMS_Templates::show_lms_template( 'checkout/payment', compact( 'user_id', 'total' ) ); ?>
			</div>
		<?php else : ?>
			<?php STM_LMS_Templates::show_lms_template( 'account/v1/fast_login/main' ); ?>
		<?php endif; ?>
	</div>
	<?php
endif;
