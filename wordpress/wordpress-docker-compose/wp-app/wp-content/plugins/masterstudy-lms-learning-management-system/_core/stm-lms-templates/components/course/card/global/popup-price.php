<?php
/**
 * @var $course
 */

$course_free_status = masterstudy_lms_course_free_status( $course['single_sale'], $course['price'] );

if ( $course['single_sale'] && ! $course_free_status['zero_price'] ) { ?>
	<div class="masterstudy-course-card__popup-price">
		<div class="masterstudy-course-card__popup-price-single <?php echo ( ! empty( $course['sale_price'] ) && $course['is_sale_active'] ) ? 'masterstudy-course-card__popup-price-single_sale' : ''; ?>">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price( $course['price'] ) ); ?></span>
		</div>
		<?php if ( ! empty( $course['sale_price'] ) && $course['is_sale_active'] ) { ?>
			<div class="masterstudy-course-card__popup-price-sale">
				<span><?php echo esc_html( STM_LMS_Helpers::display_price( $course['sale_price'] ) ); ?></span>
			</div>
		<?php } ?>
	</div>
<?php } elseif ( ! $course['single_sale'] && STM_LMS_Subscriptions::subscription_enabled() && ! $course['not_in_membership'] ) { ?>
	<div class="masterstudy-course-card__popup-price-single masterstudy-course-card__popup-price-single_subscription">
		<i class="stmlms-subscription"></i>
		<span><?php esc_html_e( 'Members Only', 'masterstudy-lms-learning-management-system' ); ?></span>
	</div>
	<?php
} elseif ( $course_free_status['is_free'] ) {
	?>
	<div class="masterstudy-course-card__popup-price">
		<div class="masterstudy-course-card__popup-price-single">
			<span><?php echo esc_html__( 'Free', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	</div>
	<?php
}
