<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var int $user_id
 * @var string $lesson_type
 * @var boolean $lesson_completed
 * @var boolean $dark_mode
 */

use MasterStudy\Lms\Plugin\PostType;

wp_enqueue_style( 'video.js' );
wp_enqueue_style( 'masterstudy-course-player-lesson' );
wp_enqueue_script( 'masterstudy-course-player-lesson' );

if ( function_exists( 'vc_asset_url' ) ) {
	wp_enqueue_style( 'stm_lms_wpb_front_css' );
}

global $post;

$post = get_post( $item_id );

if ( $post instanceof \WP_Post && PostType::LESSON === $post->post_type ) {
	setup_postdata( $post );
	?>
	<div class="masterstudy-course-player-lesson">
		<?php
		if ( 'video' === $lesson_type ) {
			STM_LMS_Templates::show_lms_template(
				'course-player/content/lesson/video',
				array(
					'id'               => $item_id,
					'user_id'          => $user_id,
					'course_id'        => $post_id,
					'lesson_completed' => $lesson_completed,
				),
			);
		}
		if ( 'audio' === $lesson_type ) {
			STM_LMS_Templates::show_lms_template(
				'course-player/content/lesson/audio',
				array(
					'item_id'          => $item_id,
					'user_id'          => $user_id,
					'course_id'        => $post_id,
					'lesson_completed' => $lesson_completed,
					'dark_mode'        => $dark_mode,
				)
			);
		}

		ob_start();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'the_content', $post->post_content );

		$content = ob_get_clean();
		$content = str_replace( '../../', site_url() . '/', $content );

		if ( STM_LMS_Helpers::is_pro_plus() && ( 'video' === $lesson_type || 'audio' === $lesson_type ) ) {
			$content = masterstudy_lms_wrap_timecode( $content );
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo stm_lms_filtered_output( $content );
		?>
	</div>
	<span class="masterstudy-course-player-lesson__submit-trigger"></span>
	<?php
	wp_reset_postdata();
}
