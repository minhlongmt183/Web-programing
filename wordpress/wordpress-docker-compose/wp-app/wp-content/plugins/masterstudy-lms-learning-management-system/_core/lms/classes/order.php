<?php

STM_LMS_Order::init();

class STM_LMS_Order {
	public static function init() {
		/* Redirect if after PayPal method */
		add_action(
			'template_redirect',
			function () {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$is_paypal = ! empty( $_GET['paypal_order_id'] );

				if ( $is_paypal ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$order_id = intval( $_GET['paypal_order_id'] );
					$location = add_query_arg( 'stm_lms_paypal_order', $order_id, STM_LMS_User::user_page_url() . '#' . $order_id );
					wp_safe_redirect( $location );
				}
			}
		);

		add_action( 'wp_ajax_stm_lms_get_order_info', 'STM_LMS_Order::ajax_get_order_info' );

		add_action( 'wp_ajax_stm_lms_get_user_orders', 'STM_LMS_Order::user_orders' );

		add_action( 'save_post', 'STM_LMS_Order::save_order', 20 );

		add_filter( 'manage_stm-orders_posts_columns', 'STM_LMS_Order::column_names' );

		add_action( 'manage_stm-orders_posts_custom_column', 'STM_LMS_Order::column_fields', 10, 2 );

		add_action( 'wp_ajax_get_pagination', 'STM_LMS_Order::get_pagination' );
		add_action( 'wp_ajax_nopriv_get_pagination', 'STM_LMS_Order::get_pagination' );
	}

	public static function ajax_get_order_info() {
		check_ajax_referer( 'get_order_info', 'nonce' );

		$order = self::get_order_info( intval( $_GET['order_id'] ?? 0 ) );

		wp_send_json( $order );
	}

	public static function get_order_info( $order_id = '' ) {
		$author_id = get_post_field( 'user_id', $order_id );

		if ( empty( $author_id ) ) {
			$author_id = get_current_user_id();
		}
		if ( empty( $order_id ) || ! is_user_logged_in()
			|| ( get_current_user_id() !== intval( $author_id )
			&& ! current_user_can( 'manage_options' )
			&& ! STM_LMS_Instructor::is_instructor() ) ) {
			die;
		}

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$order_meta  = apply_filters( 'stm_lms_order_details', array(), $order_id );

		if ( empty( $order_meta ) && ! STM_LMS_Cart::woocommerce_checkout_enabled() ) {
			$order_meta = STM_LMS_Helpers::parse_meta_field( $order_id );
		}

		$cart_items = array();
		$total      = 0;

		if ( isset( $order_meta['items'] ) && is_array( $order_meta['items'] ) ) {
			foreach ( $order_meta['items'] as $course ) {
				$terms = stm_lms_get_terms_array( $course['item_id'], 'stm_lms_course_taxonomy', 'name' );

				$cart_items[ $course['item_id'] ] = array(
					'thumbnail_id'    => get_post_thumbnail_id( $course['item_id'] ),
					'title'           => get_the_title( $course['item_id'] ),
					'link'            => get_the_permalink( $course['item_id'] ),
					'image'           => get_the_post_thumbnail( $course['item_id'], 'img-300-225' ),
					'image_url'       => get_the_post_thumbnail_url( $course['item_id'] ),
					'image_full'      => get_the_post_thumbnail( $course['item_id'], 'full' ),
					'placeholder'     => STM_LMS_URL . '/assets/img/image_not_found.png',
					'price'           => $course['price'],
					'terms'           => ! empty( $terms ) ? $terms : wp_list_pluck( get_the_terms( $course['item_id'], 'product_cat' ), 'name' ),
					'price_formatted' => STM_LMS_Helpers::display_price( $course['price'] ),
				);
				$total                           += $course['price'] + ( isset( $course['tax'] ) ? $course['tax'] : 0 );

				$bundle               = $course['bundle'] ?? null;
				$stm_lms_courses_meta = get_post_meta( $order_id, 'stm_lms_courses', true );

				if ( is_array( $stm_lms_courses_meta ) ) {
					if ( isset( $stm_lms_courses_meta[0]['bundle_id'] ) ) {
						$bundle = $stm_lms_courses_meta[0]['bundle_id'];
					} elseif ( isset( $stm_lms_courses_meta[0]['item_id'] ) ) {
						$bundle = $stm_lms_courses_meta[0]['item_id'];
					}
				}

				if ( ! empty( $bundle ) && get_post( (int) $bundle ) ) {
					$bundle_ids   = get_post_meta( (int) $bundle, 'stm_lms_bundle_ids', true );
					$bundle_count = is_array( $bundle_ids ) ? count( $bundle_ids ) : 0;
				} else {
					$bundle_count = self::get_bundle_courses_count( $order_id );
				}

				$cart_items[ $course['item_id'] ]['bundle_courses_count'] = $bundle_count;
				$cart_items[ $course['item_id'] ]['enterprise_name']      = ( ! empty( $course['enterprise'] ) && '0' !== $course['enterprise'] && get_post( (int) $course['enterprise'] ) ) ? get_the_title( (int) $course['enterprise'] ) : '';
			}
		}

		$i18n = self::translates();

		$timezone = get_option( 'gmt_offset' );
		$diff     = ( ! empty( $timezone ) ) ? $timezone * 60 * 60 : 0;
		$diff     = apply_filters( 'stm_lms_gmt_offset', $diff );

		return array_merge(
			$order_meta,
			$i18n,
			array(
				'id'             => $order_id,
				'date'           => $order_meta['date'] ?? '',
				'date_formatted' => isset( $order_meta['date'] )
					? date_i18n( $date_format . ' ' . $time_format, $order_meta['date'] + $diff )
					: '',
				'cart_items'     => $cart_items,
				'total'          => STM_LMS_Helpers::display_price( $total ),
				'user'           => isset( $order_meta['user_id'] )
					? STM_LMS_User::get_current_user( $order_meta['user_id'] )
					: null,
			)
		);
	}

	//Get pagination for orders
	public static function get_pagination() {
		if ( ! isset( $_POST['total_pages'] ) || ! isset( $_POST['current_page'] ) ) {
			wp_send_json_error( 'Invalid data', 400 );
		}

		$total_pages  = intval( $_POST['total_pages'] );
		$current_page = intval( $_POST['current_page'] );

		ob_start();

		STM_LMS_Templates::show_lms_template(
			'components/pagination',
			array(
				'max_visible_pages' => 3,
				'total_pages'       => $total_pages,
				'current_page'      => $current_page,
				'dark_mode'         => false,
				'is_queryable'      => false,
				'done_indicator'    => false,
				'is_hidden'         => false,
			)
		);
		$pagination_html = ob_get_clean();

		wp_send_json_success(
			array(
				'pagination' => $pagination_html,
			)
		);
	}

	//Get bundle for orders
	public static function get_bundle_courses_count( $bundle ) {
		$bundle_ids = get_post_meta( (int) $bundle, 'stm_lms_bundle_ids', true );
		return ! empty( $bundle_ids ) ? ( is_array( $bundle_ids ) ? count( $bundle_ids ) : count( explode( ',', $bundle_ids ) ) ) : 0;
	}

	public static function get_instructor_order_info( $order_id = '' ) {
		$current_user_id = get_current_user_id();

		if ( empty( $order_id ) || ! is_user_logged_in()
			|| ( ! current_user_can( 'manage_options' ) && ! STM_LMS_Instructor::is_instructor() ) ) {
			die;
		}

		global $wpdb;

		$cart_items = array();
		$total      = 0;

		if ( STM_LMS_Cart::woocommerce_checkout_enabled() && function_exists( 'wc_get_order' ) ) {
			$courses = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT
						p.ID AS course_id,
						p.post_author,
						pm.meta_value AS product_id,
						( SELECT meta_value 
						FROM {$wpdb->prefix}woocommerce_order_itemmeta 
						WHERE order_item_id = oim.order_item_id 
								AND meta_key = '_line_total'
						LIMIT 1
						) AS price
					FROM {$wpdb->prefix}posts p
					INNER JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.ID
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.meta_value = pm.meta_value
					WHERE pm.meta_key = 'stm_lms_product_id'
					AND p.post_type IN ('stm-courses', 'stm-course-bundles')
					AND p.post_author = %d
					AND oim.meta_key = '_product_id'
					AND oim.order_item_id IN (
						SELECT order_item_id
						FROM {$wpdb->prefix}woocommerce_order_items oi
						WHERE oi.order_id = %d
					)
					",
					$current_user_id,
					$order_id
				)
			);
		} else {
			$courses = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT
						oi.object_id AS course_id,
						p.post_author,
						oi.price
					FROM {$wpdb->prefix}stm_lms_order_items oi
					INNER JOIN {$wpdb->prefix}posts p ON oi.object_id = p.ID
					WHERE oi.order_id = %d
					AND p.post_author = %d
					",
					$order_id,
					$current_user_id
				)
			);
		}

		foreach ( $courses as $course ) {
			$terms      = stm_lms_get_terms_array( $course->course_id, 'stm_lms_course_taxonomy', 'name' );
			$price      = $course->price;
			$bundle_ids = get_post_meta( $course->course_id, 'stm_lms_bundle_ids', true );

			$cart_items[ $course->course_id ] = array(
				'thumbnail_id'    => get_post_thumbnail_id( $course->course_id ),
				'title'           => get_the_title( $course->course_id ),
				'link'            => get_the_permalink( $course->course_id ),
				'image'           => get_the_post_thumbnail( $course->course_id, 'img-300-225' ),
				'image_full'      => get_the_post_thumbnail( $course->course_id, 'full' ),
				'placeholder'     => STM_LMS_URL . '/assets/img/image_not_found.png',
				'price'           => $price,
				'bundle'          => is_array( $bundle_ids ) ? count( $bundle_ids ) : 0,
				'terms'           => ! empty( $terms ) ? $terms : wp_list_pluck( get_the_terms( $course->course_id, 'product_cat' ), 'name' ),
				'price_formatted' => STM_LMS_Helpers::display_price( $price ),
			);

			$total += $price;
		}

		$order_meta = apply_filters( 'stm_lms_order_details', array(), $order_id );
		if ( empty( $order_meta ) ) {
			$order_meta = STM_LMS_Helpers::parse_meta_field( $order_id );
		}

		$i18n     = self::translates();
		$timezone = get_option( 'gmt_offset' );
		$diff     = ( ! empty( $timezone ) ) ? $timezone * 60 * 60 : 0;
		$diff     = apply_filters( 'stm_lms_gmt_offset', $diff );
		$tax      = isset( $order_meta['items'] ) ? array_sum( array_column( $order_meta['items'], 'tax' ) ) : 0;

		return array_merge(
			$order_meta,
			$i18n,
			array(
				'id'             => $order_id,
				'date'           => $order_meta['date'] ?? '',
				'date_formatted' => isset( $order_meta['date'] )
					? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $order_meta['date'] + $diff )
					: '',
				'cart_items'     => $cart_items,
				'total'          => STM_LMS_Helpers::display_price( $total + $tax ),
				'user'           => isset( $order_meta['user_id'] )
					? STM_LMS_User::get_current_user( $order_meta['user_id'] )
					: null,
			)
		);
	}

	/**
	 * @param $data [user_id, cart_items, payment_code, _order_total, _order_currency]
	 * @param bool $return
	 *
	 * @return int|WP_Error
	 */
	public static function create_order( $data, $return = false ) {
		if ( ! is_user_logged_in() || empty( $data['user_id'] ) ) {
			die;
		}

		$order_info = array(
			'user_id'         => $data['user_id'],
			'items'           => $data['cart_items'],
			'date'            => time(),
			'status'          => 'pending',
			'payment_code'    => $data['payment_code'],
			'order_key'       => uniqid( $data['user_id'] . time() ),
			'_order_total'    => $data['_order_total'],
			'_order_currency' => $data['_order_currency'],
		);

		$order_post = array(
			'post_type'   => 'stm-orders',
			'post_title'  => wp_strip_all_tags( $order_info['order_key'] ),
			'post_status' => 'publish',
		);

		$order_id = wp_insert_post( $order_post );

		foreach ( $order_info as $meta_key => $meta_value ) {
			update_post_meta( $order_id, $meta_key, $meta_value );
		}

		if ( $return ) {
			return $order_id;
		}
	}

	public static function translates() {
		return array(
			'i18n' => array(
				'order_key'    => esc_html__( 'Order key', 'masterstudy-lms-learning-management-system' ),
				'date'         => esc_html__( 'Date', 'masterstudy-lms-learning-management-system' ),
				'status'       => esc_html__( 'Status', 'masterstudy-lms-learning-management-system' ),
				'pending'      => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system' ),
				'processing'   => esc_html__( 'Processing', 'masterstudy-lms-learning-management-system' ),
				'failed'       => esc_html__( 'Failed', 'masterstudy-lms-learning-management-system' ),
				'on-hold'      => esc_html__( 'On hold', 'masterstudy-lms-learning-management-system' ),
				'refunded'     => esc_html__( 'Refunded', 'masterstudy-lms-learning-management-system' ),
				'completed'    => esc_html__( 'Completed', 'masterstudy-lms-learning-management-system' ),
				'cancelled'    => esc_html__( 'Cancelled', 'masterstudy-lms-learning-management-system' ),
				'user'         => esc_html__( 'User', 'masterstudy-lms-learning-management-system' ),
				'order_items'  => esc_html__( 'Order items', 'masterstudy-lms-learning-management-system' ),
				'course_name'  => esc_html__( 'Course name', 'masterstudy-lms-learning-management-system' ),
				'course_price' => esc_html__( 'Course price', 'masterstudy-lms-learning-management-system' ),
				'total'        => esc_html__( 'Total', 'masterstudy-lms-learning-management-system' ),
				'bundle'       => esc_html__( 'Courses in bundle', 'masterstudy-lms-learning-management-system' ),
				'enterprise'   => esc_html__( 'for group', 'masterstudy-lms-learning-management-system' ),
			),
		);
	}

	public static function save_order( $post_id ) {
		if ( ! is_user_logged_in() || 'stm-orders' !== get_post_type( $post_id ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$status          = sanitize_text_field( $_POST['order_status'] ?? '' );
		$user_id         = get_post_meta( $post_id, 'user_id', true );
		$previous_status = get_post_meta( $post_id, 'status', true );

		/*If status changed*/
		if ( $previous_status !== $status ) {
			if ( 'completed' === $status && ! empty( $user_id ) ) {
				update_post_meta( $post_id, 'status', $status );
				self::accept_order( $user_id, $post_id );
			} elseif ( 'cancelled' === $status && ! empty( $user_id ) ) {
				update_post_meta( $post_id, 'status', $status );
				self::remove_order( $user_id, $post_id );
			} else {
				update_post_meta( $post_id, 'status', $status );
			}
		}
	}

	public static function accept_order( $user_id, $order_id = '' ) {
		$accept_order = apply_filters( 'stm_lms_accept_order', true );
		$cart_items   = stm_lms_get_cart_items( $user_id, apply_filters( 'stm_lms_cart_items_fields', array( 'item_id', 'price' ) ) );

		if ( ! empty( $order_id ) ) {
			$cart_items = get_post_meta( $order_id, 'items', true );
		}

		if ( $accept_order ) {
			foreach ( $cart_items as $cart_item ) {
				STM_LMS_Course::add_user_course( $cart_item['item_id'], $user_id, 0, 0 );
				STM_LMS_Course::add_student( $cart_item['item_id'] );
			}

			/*Delete Cart*/
			stm_lms_get_delete_cart_items( $user_id );
		}

		do_action( 'stm_lms_order_accepted', $user_id, $cart_items );
	}

	public static function remove_order( $user_id, $order_id ) {
		$cart_items = get_post_meta( $order_id, 'items', true );

		foreach ( $cart_items as $cart_item ) {
			stm_lms_get_delete_user_course( $user_id, $cart_item['item_id'] );
			STM_LMS_Course::remove_student( $cart_item['item_id'] );
			do_action( 'stm_lms_order_remove', $cart_item['item_id'], $cart_item, $user_id );
		}

		/*Delete Cart*/
		stm_lms_get_delete_cart_items( $user_id );
	}

	public static function user_orders() {
		check_ajax_referer( 'user_orders', 'nonce' );

		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			die;
		}

		$user_id = $user['id'];
		$posts   = array();
		$pp      = get_option( 'posts_per_page' );
		$offset  = intval( $_GET['offset'] ?? 0 ) * $pp;

		$user_orders = apply_filters( 'stm_lms_user_orders', array(), $user_id, $pp, $offset );

		if ( empty( $user_orders ) ) {
			$args = array(
				'post_type'      => 'stm-orders',
				'posts_per_page' => $pp,
				'post_status'    => 'publish',
				'offset'         => $offset,
				'meta_query'     => array(
					array(
						'key'     => 'user_id',
						'compare' => '=',
						'value'   => $user_id,
					),
				),
			);

			$q     = new WP_Query( $args );
			$total = $q->found_posts;

			if ( $q->have_posts() ) {
				while ( $q->have_posts() ) {
					$q->the_post();
					$posts[] = self::get_order_info( get_the_ID() );
				}

				wp_reset_postdata();
			}
		} else {
			extract( $user_orders );
		}

		wp_send_json(
			array(
				'pages'        => ceil( $total / $pp ),
				'current_page' => $offset + 1,
				'total_posts'  => $total,
				'posts'        => $posts,
				'total'        => $total <= $offset + $pp,
			)
		);
	}

	public static function column_names( $columns ) {
		unset( $columns['cb'] );
		unset( $columns['title'] );

		$lms_id = array(
			'cb'           => '<input type="checkbox" />',
			'lms_id'       => esc_html__( 'Order', 'masterstudy-lms-learning-management-system' ),
			'order_key'    => esc_html__( 'Order Key', 'masterstudy-lms-learning-management-system' ),
			'order_status' => esc_html__( 'Order Status', 'masterstudy-lms-learning-management-system' ),
		);

		return array_merge( $lms_id, $columns );
	}

	public static function column_fields( $column, $post_id ) {
		$edit_link  = get_edit_post_link( $post_id );
		$title      = get_the_title( $post_id );
		$order_meta = STM_LMS_Helpers::parse_meta_field( $post_id );
		$user       = STM_LMS_User::get_current_user( $order_meta['user_id'] );

		switch ( $column ) {
			case 'lms_id':
				$user_login = $user['login'];
				echo wp_kses_post( "<a class='row-title' href='{$edit_link}'>#{$post_id} {$user_login}</a>" );
				break;
			case 'order_key':
				echo wp_kses_post( $title );
				break;
			case 'order_status':
				$status = isset( $order_meta['status'] ) ? $order_meta['status'] : '';
				echo wp_kses_post( "<span class='stm_lms_status stm_lms_status_{$status}'>{$status}</span>" );
				break;
		}
	}

	public static function has_purchased_courses( $user_id, $course_id ) {
		$is_bought      = false;
		$is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();

		if ( $is_woocommerce && class_exists( 'STM_LMS_Woocommerce' ) ) {
			$is_bought = STM_LMS_Woocommerce::has_course_been_purchased( $user_id, $course_id );
		} else {
			$query = new WP_Query(
				array(
					'post_type'   => 'stm-orders',
					'post_status' => 'publish',
					'meta_query'  => array(
						'relation' => 'AND',
						array(
							'key'     => 'user_id',
							'compare' => '=',
							'value'   => $user_id,
						),
						array(
							'key'     => 'status',
							'compare' => '=',
							'value'   => 'completed',
						),
						array(
							'key'     => 'items',
							'compare' => 'LIKE',
							'value'   => $course_id,
						),
					),
				)
			);

			if ( $query->found_posts > 0 ) {
				$is_bought = true;
			}
		}

		return $is_bought;
	}

	public static function is_purchased_by_enterprise( $course, $user_id ) {
		$in_enterprise = false;

		if ( isset( $course['enterprise_id'] ) && class_exists( 'STM_LMS_Enterprise_Courses' ) ) {
			$group_users = STM_LMS_Enterprise_Courses::get_group_users( $course['enterprise_id'] );
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			$in_enterprise = ( isset( $group_users ) && is_array( $group_users ) ) ? in_array( $user_id, $group_users ) : false;
		}

		return $in_enterprise;
	}
}
