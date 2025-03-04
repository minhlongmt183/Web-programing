<?php

namespace stmLms\Classes\Vendor;

use MasterStudy\Lms\Database\CurriculumMaterial;
use MasterStudy\Lms\Database\CurriculumSection;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use MasterStudy\Lms\Repositories\CurriculumSectionRepository;
use MasterStudy\Lms\Utility\CourseGrade;

abstract class LmsUpdateCallbacks {

	/**
	 * Add uf_new_messages column to Conversations table.
	 * Rename new_messages column to ut_new_messages in Conversations table.
	 */
	public static function lms_chat_columns(): void {
		global $wpdb;

		$table_name = stm_lms_user_conversation_name( $wpdb );

		if ( ! $wpdb->get_var( sprintf( "SHOW COLUMNS FROM `%s` LIKE 'uf_new_messages';", $table_name ) ) ) { // phpcs:ignore
			$wpdb->query( sprintf( "ALTER TABLE `%s` ADD `uf_new_messages` INT NOT NULL, CHANGE `new_messages` `ut_new_messages` INT;", $table_name ) ); // phpcs:ignore
		}
	}

	/**
	 * Delete page routes config transient to reset them and autosave new routes
	 */
	public static function lms_page_routes(): void {
		delete_transient( 'stm_lms_routes_pages_transient' );
		delete_transient( 'stm_lms_routes_pages_config_transient' );

		flush_rewrite_rules( true );
	}

	public static function lms_admin_notification_transient(): void {
		$data = array(
			'show_time'   => DAY_IN_SECONDS * 3 + time(),
			'step'        => 0,
			'prev_action' => '',
		);
		set_transient( 'stm_masterstudy-lms-learning-management-system_notice_setting', $data );
	}

	public static function lms_add_lesson_video_sources(): void {
		$lessons = get_posts(
			array(
				'post_type'      => 'stm-lessons',
				'posts_per_page' => -1,
			)
		);

		foreach ( $lessons as $lesson ) {
			$lesson_type       = get_post_meta( $lesson->ID, 'type', true );
			$lesson_poster     = get_post_meta( $lesson->ID, 'lesson_video_poster', true );
			$lesson_video      = get_post_meta( $lesson->ID, 'lesson_video', true );
			$lesson_video_url  = get_post_meta( $lesson->ID, 'lesson_video_url', true );
			$lesson_video_type = get_post_meta( $lesson->ID, 'video_type', true );

			if ( 'text' === $lesson_type || 'slide' === $lesson_type ) {
				if ( ! empty( $lesson_video_url ) ) {
					$lesson_type = 'video';
					update_post_meta( $lesson->ID, 'type', 'video' );
				}
			}

			if ( 'video' === $lesson_type ) {
				if ( ! empty( $lesson_video ) ) {
					update_post_meta( $lesson->ID, 'video_type', 'html' );
				} elseif ( ! empty( $lesson_video_url ) ) {
					$youtube_pos = strpos( $lesson_video_url, 'youtube' );
					$vimeo_pos   = strpos( $lesson_video_url, 'vimeo' );

					if ( false !== $youtube_pos ) {
						update_post_meta( $lesson->ID, 'video_type', 'youtube' );
						update_post_meta( $lesson->ID, 'lesson_youtube_url', $lesson_video_url );
					} elseif ( false !== $vimeo_pos ) {
						update_post_meta( $lesson->ID, 'video_type', 'vimeo' );
						update_post_meta( $lesson->ID, 'lesson_vimeo_url', $lesson_video_url );
					} elseif ( ! empty( $lesson_poster ) ) {
						update_post_meta( $lesson->ID, 'lesson_ext_link_url', $lesson_video_url );
						if ( empty( $lesson_video_type ) ) {
							update_post_meta( $lesson->ID, 'video_type', 'ext_link' );
						}
					}
				}
			} elseif ( 'stream' === $lesson_type ) {
				$lesson_video_url = get_post_meta( $lesson->ID, 'lesson_video_url', true );
				update_post_meta( $lesson->ID, 'lesson_stream_url', $lesson_video_url );
			}
		}
	}

	public static function lms_create_curriculum_tables(): void {
		require_once STM_LMS_LIBRARY . '/db/tables/curriculum_sections.table.php';
		require_once STM_LMS_LIBRARY . '/db/tables/curriculum_materials.table.php';

		stm_lms_curriculum_sections();
		stm_lms_curriculum_materials();
	}

	public static function lms_migrate_course_data(): void {
		$section_repository  = new CurriculumSectionRepository();
		$material_repository = new CurriculumMaterialRepository();

		$courses = get_posts(
			array(
				'post_type'      => 'stm-courses',
				'posts_per_page' => -1,
			)
		);

		foreach ( $courses as $course ) {
			// Course Files Migration
			self::migrate_file_materials( $course->ID, 'course' );

			// One Time Purchase Migration
			$not_single_sale = metadata_exists( 'post', $course->ID, 'not_single_sale' )
				? get_post_meta( $course->ID, 'not_single_sale', true )
				: false;
			update_post_meta( $course->ID, 'single_sale', '' === $not_single_sale ? 'on' : '' );

			// Curriculum Migration
			$curriculum       = get_post_meta( $course->ID, 'curriculum', true );
			$curriculum_items = explode( ',', $curriculum );
			$current_section  = false;
			$section_order    = 1;
			$material_order   = 1;

			if ( ! empty( $curriculum_items ) ) {
				foreach ( $curriculum_items as $curriculum_item ) {
					if ( is_numeric( $curriculum_item ) ) {
						if ( ! empty( $current_section->id ) ) {
							$current_material = ( new CurriculumMaterial() )->query()
								->where( 'post_id', $curriculum_item )
								->where( 'section_id', $current_section->id )
								->findOne();

							if ( ! $current_material ) {
								$material_repository->create(
									array(
										'post_id'    => $curriculum_item,
										'post_type'  => get_post_type( $curriculum_item ),
										'section_id' => $current_section->id,
										'order'      => $material_order,
									)
								);

								$material_order++;
							}
						}
					} else {
						$current_section = ( new CurriculumSection() )->query()
							->where( 'course_id', $course->ID )
							->where( 'title', urldecode( $curriculum_item ) )
							->findOne();

						if ( ! $current_section ) {
							$current_section = $section_repository->create(
								array(
									'title'     => urldecode( $curriculum_item ),
									'course_id' => $course->ID,
									'order'     => $section_order,
								)
							);

							$section_order++;
						}

						$material_order = 1;
					}
				}
			}
		}
	}

	public static function lms_instructor_role_add_capability(): void {
		$instructor_role = get_role( 'stm_lms_instructor' );

		$instructor_role->add_cap( 'list_users' );
	}

	public static function lms_migrate_lesson_data(): void {
		$lessons = get_posts(
			array(
				'post_type'      => 'stm-lessons',
				'posts_per_page' => -1,
			)
		);

		foreach ( $lessons as $lesson ) {
			// Change Slide Lesson Type
			$type = get_post_meta( $lesson->ID, 'type', true );
			if ( 'slide' === $type || empty( $type ) ) {
				update_post_meta( $lesson->ID, 'type', 'text' );
			}

			// Migrate Lesson Files
			self::migrate_file_materials( $lesson->ID, 'lesson' );
		}
	}

	public static function migrate_file_materials( int $post_id, string $post_type ): void {
		$files_pack = get_post_meta( $post_id, "{$post_type}_files_pack", true );

		if ( ! empty( $files_pack ) && is_string( $files_pack ) ) {
			$files_pack = json_decode( $files_pack, true );
			$ids        = array();

			if ( $files_pack && is_array( $files_pack ) ) {
				foreach ( $files_pack as $file_pack ) {
					if ( empty( $file_pack[ "{$post_type}_files" ] ) ) {
						continue;
					}

					$files = json_decode( $file_pack[ "{$post_type}_files" ], true );

					if ( empty( $files['path'] ) || ! file_exists( $files['path'] ) ) {
						continue;
					}

					$file_type     = wp_check_filetype( basename( $files['path'] ) );
					$wp_upload_dir = wp_upload_dir();
					$attachment    = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $files['path'] ),
						'post_mime_type' => $file_type['type'],
						'post_title'     => ! empty( $file_pack[ "{$post_type}_files_label" ] )
							? $file_pack[ "{$post_type}_files_label" ]
							: esc_html__( 'Attached file', 'masterstudy-lms-learning-management-system' ),
						'post_status'    => 'inherit',
					);

					$attach_id = wp_insert_attachment( $attachment, $files['path'], $post_id );

					require_once ABSPATH . 'wp-admin/includes/image.php';

					$attach_data = wp_generate_attachment_metadata( $attach_id, $files['path'] );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					$ids[] = $attach_id;
				}

				update_post_meta( $post_id, "{$post_type}_files", wp_json_encode( array_filter( $ids ) ) );
			}
		}
	}

	public static function lms_remove_copyright_url() {
		$theme_settings      = get_option( 'stm_option' );
		$old_clients_content = 'MasterStudy Theme by Stylemix Themes';
		$dummy_copyright     = 'Copyright &copy; <a target="_blank" href="https://stylemixthemes.com/masterstudy/">MasterStudy</a> Theme for WordPress by <a target="_blank" href="https://www.stylemixthemes.com/">StylemixThemes</a>';
		$linked_text         = 'Created by <a target="_blank" href="https://wordpress.org/plugins/masterstudy-lms-learning-management-system/" style="text-decoration: none !important">MasterStudy</a> 2023. ';

		if ( $theme_settings['footer_copyright_text'] === $old_clients_content || $theme_settings['footer_copyright_text'] === $dummy_copyright ) {
			$theme_settings['footer_copyright_text'] = $linked_text;

			update_option( 'stm_option', $theme_settings, false );
		}
	}

	public static function lms_remove_stm_links_from_content() {
		$page_titles = array(
			'Contact Us',
			'Home',
		);
		$search      = 'stylemixthemes.com';
		foreach ( $page_titles as $title ) {
			$args        = array(
				'post_type'   => 'page',
				'title'       => $title,
				'post_status' => 'publish',
			);
			$page_object = current( get_posts( $args ) );

			if ( $page_object ) {
				$page_content = $page_object->post_content ?? '';
				if ( false !== strpos( $page_content, $search ) ) {
					$new_content = str_replace( $search, '/', $page_content );

					wp_update_post(
						array(
							'ID'           => $page_object->ID,
							'post_content' => $new_content,
						)
					);
				}
			}
		}
	}

	public static function lms_remove_url_from_widgets() {
		$widget_data          = get_option( 'widget_stm_text', array() );
		$searches             = 'stylemixthemes.com';
		$dummy_widget_content = 'Sed nec felis pellentesque';
		$lorem_widget_content = 'Lorem ipsum dolor sit amet';
		$linked_text          = 'Created by <a target="_blank" href="https://wordpress.org/plugins/masterstudy-lms-learning-management-system/">MasterStudy</a> 2023. ';

		if ( ! empty( $widget_data ) && is_array( $widget_data ) ) {
			foreach ( $widget_data as &$item ) {
				if ( isset( $item['text'] ) ) {
					$item['text'] = str_replace( $searches, '/', $item['text'] );
					if ( strpos( $item['text'], $lorem_widget_content ) === 0 || strpos( $item['text'], $dummy_widget_content ) === 0 ) {
						$item['text'] = $linked_text . $item['text'];
					}
				}
			}
			update_option( 'widget_stm_text', $widget_data );
		}
	}

	public static function lms_udemy_course_additional_info() {
		$courses = get_posts(
			array(
				'fields'         => 'ids',
				'post_type'      => 'stm-courses',
				'posts_per_page' => -1,
			)
		);

		foreach ( $courses as $course_id ) {
			if ( get_post_meta( $course_id, 'udemy_course_id', true ) ) {
				/* Needs translations from Pro version */
				update_post_meta( $course_id, 'price_info', esc_html__( '30-Day Money-Back Guarantee', 'masterstudy-lms-learning-management-system-pro' ) );
				update_post_meta( $course_id, 'access_duration', esc_html__( 'Full lifetime access', 'masterstudy-lms-learning-management-system-pro' ) );
				update_post_meta( $course_id, 'access_devices', esc_html__( 'Access on mobile and TV', 'masterstudy-lms-learning-management-system-pro' ) );
			}
		}
	}

	public static function lms_generate_required_pages() {
		if ( function_exists( 'stm_lms_autogenerate_pages' ) ) {
			stm_lms_autogenerate_pages();
		}
	}

	public static function lms_reset_page_routes() {
		( new \STM_LMS_Page_Router() )->reset_config();
	}

	public static function lms_flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	public static function lms_composite_index_to_user_lessons() {
		global $wpdb;
		// Create the composite index ( ix_user_course_lesson )
		// phpcs:ignore
		$wpdb->query( 'CREATE INDEX ix_user_course_lesson ON ' . stm_lms_user_lessons_name( $wpdb ) . ' (user_id, course_id, lesson_id)' );
	}

	public static function lms_composite_index_to_user_courses_table() {
		global $wpdb;
		// Create the composite indexes ( ix_user_course_current, ix_user_course_enterprice, ix_user_course_bundle, ix_user_course_start_time )
		$wpdb->query( 'CREATE INDEX ix_user_course_current ON ' . stm_lms_user_courses_name( $wpdb ) . ' (user_id, course_id, current_lesson_id)' ); // phpcs:ignore
		$wpdb->query( 'CREATE INDEX ix_user_course_enterprice ON ' . stm_lms_user_courses_name( $wpdb ) . ' (user_id, course_id, enterprise_id)' ); // phpcs:ignore
		$wpdb->query( 'CREATE INDEX ix_user_course_bundle ON ' . stm_lms_user_courses_name( $wpdb ) . ' (user_id, course_id, bundle_id)' ); // phpcs:ignore
		$wpdb->query( 'CREATE INDEX ix_user_course_start_time ON ' . stm_lms_user_courses_name( $wpdb ) . ' (user_id, course_id, start_time)' ); // phpcs:ignore
	}

	public static function lms_move_student_assignment_attachments() {
		$assignments = get_posts(
			array(
				'post_type'      => PostType::USER_ASSIGNMENT,
				'fields'         => 'ids',
				'posts_per_page' => -1,
			)
		);

		if ( ! empty( $assignments ) ) {
			foreach ( $assignments as $assignment_id ) {
				$attachments = get_posts(
					array(
						'post_type'      => 'attachment',
						'fields'         => 'ids',
						'post_parent'    => $assignment_id,
						'posts_per_page' => -1,
						'order'          => 'ASC',
					)
				);

				if ( ! empty( $attachments ) ) {
					update_post_meta( $assignment_id, 'student_attachments', array_unique( $attachments ) );
				}
			}
		}
	}

	public static function lms_replaced_auth_settings_values() {
		$settings = get_option( 'stm_lms_settings' );

		$settings['register_as_instructor']   = ! $settings['register_as_instructor'];
		$settings['instructor_premoderation'] = ! $settings['disable_instructor_premoderation'];

		update_option( 'stm_lms_settings', $settings );
	}

	public static function lms_set_default_certificate() {
		$default_certificate = get_option( 'stm_default_certificate', false );

		if ( ! $default_certificate ) {
			$certificates = get_posts(
				array(
					'post_type'      => PostType::CERTIFICATE,
					'posts_per_page' => -1,
					'meta_key'       => 'stm_category',
					'meta_value'     => 'entire_site',
				)
			);

			if ( ! empty( $certificates ) ) {
				foreach ( $certificates as $certificate ) {
					update_post_meta( $certificate->ID, 'stm_category', '' );
				}
				update_option( 'stm_default_certificate', $certificates[0]->ID );
			}
		}
	}

	public static function lms_replaced_single_course_style() {
		$settings = get_option( 'stm_lms_settings' );

		if ( 'udemy' === $settings['course_style'] ) {
			$settings['course_style'] = 'modern';
		}

		update_option( 'stm_lms_settings', $settings );
	}

	public static function lms_update_db_tables() {
		require_once STM_LMS_LIBRARY . '/db/tables.php';

		if ( function_exists( 'stm_lms_tables_update' ) ) {
			stm_lms_user_answers();
			stm_lms_user_cart();
			stm_lms_user_courses();
			stm_lms_user_lessons();
			stm_lms_user_quizzes();
			stm_lms_user_quizzes_times();

			if ( function_exists( 'stm_lms_scorm_table' ) ) {
				stm_lms_scorm_table();
			}

			// Update DB Version
			update_option( 'stm_lms_db_version', STM_LMS_DB_VERSION );
		}
	}

	public static function lms_update_grades() {
		require_once STM_LMS_LIBRARY . '/db/tables.php';

		if ( ! function_exists( 'stm_lms_tables_update' ) ) {
			return;
		}

		global $wpdb;

		// Database Tables
		$course_sections_table  = stm_lms_curriculum_sections_name( $wpdb );
		$course_materials_table = stm_lms_curriculum_materials_name( $wpdb );
		$user_courses_table     = stm_lms_user_courses_name( $wpdb );

		// Patch DB Tables
		stm_lms_user_courses();

		if ( \STM_LMS_Helpers::is_pro() && is_ms_lms_addon_enabled( 'assignments' ) ) {
			stm_lms_user_assignments_table();

			// Patch Assignments
			$user_assignments_table = stm_lms_user_assignments_name( $wpdb );
			$student_assignments    = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status != 'trash' ORDER BY post_modified ASC",
					PostType::USER_ASSIGNMENT
				)
			);

			if ( ! empty( $student_assignments ) ) {
				foreach ( $student_assignments as $student_assignment_id ) {
					$post_meta   = get_post_meta( $student_assignment_id );
					$post_status = get_post_status( $student_assignment_id );
					$status      = in_array( $post_status, array( 'pending', 'draft' ), true )
						? $post_status
						: $post_meta['status'][0] ?? '';
					$status      = empty( $status ) ? 'pending' : $status;
					$grade       = null;

					if ( 'pending' !== $status ) {
						$grade = 'passed' === $status ? 100 : 0;
					}

					// Update Assignment Status Post Meta for faster queries
					update_post_meta( $student_assignment_id, 'status', $status );

					// Check if the record already exists
					$student_id    = $post_meta['student_id'][0] ?? 0;
					$course_id     = $post_meta['course_id'][0] ?? 0;
					$assignment_id = $post_meta['assignment_id'][0] ?? 0;
					$record_exists = $wpdb->get_var(
						$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							"SELECT COUNT(*) FROM $user_assignments_table WHERE user_id = %d AND user_assignment_id = %d",
							$student_id,
							$student_assignment_id
						)
					);

					if ( 0 === (int) $record_exists ) {
						$wpdb->insert(
							$user_assignments_table,
							array(
								'user_id'            => $student_id,
								'course_id'          => $course_id,
								'assignment_id'      => $assignment_id,
								'user_assignment_id' => $student_assignment_id,
								'grade'              => $grade,
								'status'             => $status,
								'updated_at'         => strtotime( get_post_datetime( $student_assignment_id )->format( 'Y-m-d H:i:s' ) ),
							),
							array( '%d', '%d', '%d', '%d', is_null( $grade ) ? null : '%d', '%s' )
						);
					}
				}
			}
		}

		// Update DB Version
		update_option( 'stm_lms_db_version', STM_LMS_DB_VERSION );

		// Patch is_gradable column
		$course_ids = $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT DISTINCT cs.course_id FROM $course_sections_table AS cs JOIN $course_materials_table AS cm ON cs.id = cm.section_id WHERE cm.post_type IN(%s, %s)",
				PostType::QUIZ,
				PostType::ASSIGNMENT
			)
		);

		if ( ! empty( $course_ids ) ) {
			$placeholders = implode( ',', array_fill( 0, count( $course_ids ), '%d' ) );

			$wpdb->query(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
					"UPDATE $user_courses_table SET is_gradable = 1 WHERE course_id IN ($placeholders)",
					...$course_ids
				)
			);
		}

		if ( \STM_LMS_Helpers::is_pro_plus() ) {
			$user_courses = $wpdb->get_results(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT user_id, course_id, MAX(user_course_id) AS latest_id FROM $user_courses_table WHERE is_gradable = 1 GROUP BY user_id, course_id",
				ARRAY_A
			);

			foreach ( $user_courses as $user_course ) {
				CourseGrade::update_user_course_grade(
					(int) $user_course['user_id'],
					(int) $user_course['course_id'],
					(int) $user_course['latest_id']
				);
			}
		}
	}

	public static function lms_rewrite_profile_url_option() {
		$settings = get_option( 'stm_lms_settings' );

		if ( ! empty( $settings['user_url_profile'] ) ) {
			$settings['instructor_url_profile'] = $settings['user_url_profile'];
		}

		update_option( 'stm_lms_settings', $settings );
	}

	public static function lms_rename_lazyload_settings() {
		$settings = get_option( 'stm_lms_settings' );

		if ( ! empty( $settings['disable_lazyload'] ) ) {
			$settings['enable_lazyload'] = $settings['disable_lazyload'];
		}

		update_option( 'stm_lms_settings', $settings );
	}

	public static function lms_update_user_lesson_table() {
		require_once STM_LMS_LIBRARY . '/db/tables.php';

		if ( function_exists( 'stm_lms_tables_update' ) ) {
			stm_lms_user_lessons();
		}
	}
}
