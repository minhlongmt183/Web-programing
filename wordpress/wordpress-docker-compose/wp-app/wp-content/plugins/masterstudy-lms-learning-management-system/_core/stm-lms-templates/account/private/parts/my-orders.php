<?php
stm_lms_register_style( 'user-orders' );
wp_enqueue_style( 'masterstudy-pagination' );
wp_enqueue_script( 'masterstudy-orders' );
wp_localize_script(
	'masterstudy-orders',
	'masterstudy_orders',
	array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'ms_lms_nonce' ),
		'no_order_title'       => esc_html__( 'No orders yet', 'masterstudy-lms-learning-management-system' ),
		'no_order_description' => esc_html__( 'All information about your orders will be displayed here', 'masterstudy-lms-learning-management-system' ),
	),
);

if ( ! STM_LMS_Cart::woocommerce_checkout_enabled() ) :
	?>
	<div class="masterstudy-orders">
		<?php
		STM_LMS_Templates::show_lms_template(
			'account/private/parts/top_info',
			array(
				'title' => esc_html__( 'My Orders', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="masterstudy-orders-container">
			<div class="ms_lms_loader_"></div>
			<template id="masterstudy-order-template">
				<div class="masterstudy-orders-table">
					<div class="masterstudy-orders-table__header">
						<div class="masterstudy-orders-course-info">
							<div class="masterstudy-orders-course-info__id" data-order-id></div>
							<div class="order-status" data-order-status></div>
						</div>
						<div class="masterstudy-orders-course-info">
							<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Date', 'masterstudy-lms-learning-management-system' ); ?>:</div>
							<div class="masterstudy-orders-course-info__value" data-order-date></div>
						</div>
						<div class="masterstudy-orders-course-info">
							<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Payment Method', 'masterstudy-lms-learning-management-system' ); ?>:</div>
							<div class="masterstudy-orders-course-info__value" data-order-payment></div>
						</div>
					</div>
					<div class="masterstudy-orders-table__body"></div>
					<div class="masterstudy-orders-table__footer">
						<div class="masterstudy-orders-course-info">
							<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system' ); ?>:</div>
							<div class="masterstudy-orders-course-info__price" data-order-total></div>
						</div>
					</div>
				</div>
			</template>
		</div>
		<div class="masterstudy-orders-table-navigation">
			<div class="masterstudy-orders-table-navigation__pagination"></div>
			<div class="masterstudy-orders-table-navigation__per-page">
			<?php
				STM_LMS_Templates::show_lms_template(
					'components/select',
					array(
						'select_id'    => 'orders-per-page',
						'select_width' => '170px',
						'select_name'  => 'per_page',
						'placeholder'  => esc_html__( '10 per page', 'masterstudy-lms-learning-management-system' ),
						'default'      => 10,
						'is_queryable' => false,
						'options'      => array(
							'25'  => esc_html__( '25 per page', 'masterstudy-lms-learning-management-system' ),
							'50'  => esc_html__( '50 per page', 'masterstudy-lms-learning-management-system' ),
							'75'  => esc_html__( '75 per page', 'masterstudy-lms-learning-management-system' ),
							'100' => esc_html__( '100 per page', 'masterstudy-lms-learning-management-system' ),
						),
					)
				);
			?>
			</div>
		</div>
	</div>
	<?php
else :
	STM_LMS_Templates::show_lms_template( 'account/private/parts/woocommerce-orders' );
endif;
