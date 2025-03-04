<?php

namespace MasterStudy\Lms\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Enums\LessonType;
use MasterStudy\Lms\Plugin\Addons;

final class GetSettingsController {
	public function __invoke(): \WP_REST_Response {
		$timezones = apply_filters( 'masterstudy_lms_timezones', array() );
		$options   = apply_filters(
			'masterstudy_lms_course_options',
			array(
				'max_upload_size'                => wp_max_upload_size(),
				'is_instructor'                  => \STM_LMS_Instructor::has_instructor_role(),
				'create_category_allowed'        => \STM_LMS_Options::get_option( 'course_allow_new_categories', false ),
				'course_allow_basic_info'        => \STM_LMS_Options::get_option( 'course_allow_basic_info', false ),
				'course_allow_requirements_info' => \STM_LMS_Options::get_option( 'course_allow_requirements_info', false ),
				'course_allow_intended_audience' => \STM_LMS_Options::get_option( 'course_allow_intended_audience', false ),
				'question_category_allowed'      => \STM_LMS_Options::get_option( 'course_allow_new_question_categories', false ),
				'course_premoderation'           => \STM_LMS_Options::get_option( 'course_premoderation', false ),
				'course_page_urls'               => STM_LMS_URL . '/assets/img/course/',
				'currency_symbol'                => \STM_LMS_Options::get_option( 'currency_symbol', '$' ),
				'quiz_attempts'                  => \STM_LMS_Options::get_option( 'quiz_attempts', false ),
				'grades_table'                   => \STM_LMS_Options::get_option( 'grades_table', stm_lms_settings_grades_default_values() ),
				'presto_player_allowed'          => apply_filters( 'ms_plugin_presto_player_allowed', false ),
			)
		);

		$lesson_types = apply_filters( 'masterstudy_lms_lesson_types', array_map( 'strval', LessonType::cases() ) );

		return new \WP_REST_Response(
			array(
				'addons'              => Addons::enabled_addons(),
				'plugins'             => array(
					'lms_pro'       => \STM_LMS_Helpers::is_pro(),
					'lms_pro_plus'  => \STM_LMS_Helpers::is_pro_plus(),
					'presto_player' => defined( 'PRESTO_PLAYER_PLUGIN_FILE' ),
					'vdocipher'     => defined( 'VDOCIPHER_PLUGIN_VERSION' ),
					'pmpro'         => defined( 'PMPRO_VERSION' ),
					'eroom'         => defined( 'STM_ZOOM_VERSION' ),
				),
				'options'             => $options,
				'urls'                => array(
					'courses'         => home_url( \STM_LMS_Options::courses_page_slug() ),
					'user_account'    => \STM_LMS_User::user_page_url(),
					'dashboard_posts' => admin_url( 'edit.php?post_type=' ),
					'addons'          => \STM_LMS_Helpers::is_pro()
						? admin_url( 'admin.php?page=stm-addons' )
						: admin_url( 'admin.php?page=stm-lms-go-pro' ),
					'plugins'         => admin_url( 'plugins.php' ),
					'settings'        => admin_url( 'admin.php?page=stm-lms-settings' ),
				),
				'lesson_types'        => $lesson_types,
				'video_sources'       => apply_filters( 'masterstudy_lms_lesson_video_sources', array() ),
				'audio_sources'       => apply_filters( 'masterstudy_lms_lesson_audio_sources', array() ),
				'presto_player_posts' => apply_filters( 'ms_plugin_presto_player_posts', array() ),
				'timezones'           => array_map(
					function ( $label, $id ) {
						return array(
							'id'    => $id,
							'label' => $label,
						);
					},
					$timezones,
					array_keys( $timezones ),
				),
				'current_user_id'     => get_current_user_id(),
			)
		);
	}
}
