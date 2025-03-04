<?php

class Masterstudy_Templates_System_Status {
	public static $notification = false;

	private static $srMySql         = '5.6';
	private static $srPHPv          = '7.4';
	private static $srPostMaxSize   = '64 MB';
	private static $srMemoryLimit   = '256 MB';
	private static $srTimeLimit     = '300';
	private static $srMaxInputVars  = '2004';
	private static $srMaxUploadSize = '64 MB';

	private static function map_wp_env() {
		$mapWPEnv = array(
			'home_url'   => array(
				'title'     => 'Home Url',
				'recommend' => '',
				'system'    => get_home_url(),
			),
			'site_url'   => array(
				'title'     => 'Site Url',
				'recommend' => '',
				'system'    => get_site_url(),
			),
			'wp_version' => array(
				'title'     => 'WP Version',
				'recommend' => self::get_upgrade_version(),
				'system'    => self::get_compare_wp_version(),
			),
			'mulyisite'  => array(
				'title'     => 'WP Multisite',
				'recommend' => '',
				'system'    => ( is_multisite() ) ? '<span class="ms-lms-icon-check"></span>On' : 'Off',
			),
			'wp_debug'   => array(
				'title'     => 'WP Debug',
				'recommend' => '',
				'system'    => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '<span class="ms-lms-icon-check"></span>On' : 'Off',
			),
			'lang'       => array(
				'title'     => 'Language',
				'recommend' => '',
				'system'    => get_locale(),
			),
		);

		return $mapWPEnv;
	}

	private static function map_server_env() {
		$mapServerEnv = array(
			'mysql_version'      => array(
				'title'     => 'MySQL Version',
				'recommend' => self::$srMySql,
				'system'    => self::get_mysql_version(),
			),
			'php_version'        => array(
				'title'     => 'PHP Version',
				'recommend' => self::$srPHPv,
				'system'    => self::get_php_version(),
			),
			'php_post_max_size'  => array(
				'title'     => 'PHP Post Max Size',
				'recommend' => self::$srPostMaxSize,
				'system'    => self::get_post_max_size(),
			),
			'php_memory_limit'   => array(
				'title'     => 'PHP Memory Limit',
				'recommend' => self::$srMemoryLimit,
				'system'    => self::get_memory_limit(),
			),
			'max_execution_time' => array(
				'title'     => 'PHP Time Limit',
				'recommend' => self::$srTimeLimit,
				'system'    => ( function_exists( 'ini_get' ) ) ? self::get_php_time_limit_system() : '',
			),
			'max_input_vars'     => array(
				'title'     => 'PHP Max Input Vars',
				'recommend' => self::$srMaxInputVars,
				'system'    => ( function_exists( 'ini_get' ) ) ? self::get_php_max_input_vars_system() : '',
			),
			'max_upload_size'    => array(
				'title'     => 'Max Upload Size',
				'recommend' => self::$srMaxUploadSize,
				'system'    => self::get_max_upload_size(),
			),
			'ziparchive'         => array(
				'title'     => 'ZipArchive',
				'recommend' => 'enabled',
				'system'    => class_exists( 'ZipArchive' ) ? '<span class="ms-lms-icon-check"></span>enabled' : '<span class="ss-error">ZipArchive is not installed on your server, but is required if you need to import demo content.</span>',
			),
			'wp_remote_get'      => array(
				'title'     => 'WP Remote Get',
				'recommend' => 'enabled',
				'system'    => self::get_wp_remote_get(),
			),
		);

		return $mapServerEnv;
	}

	public static function get_wp_env() {
		return self::map_wp_env();
	}

	public static function get_server_env() {
		return self::map_server_env();
	}

	private static function get_upgrade_version() {
		$updTrans = get_site_transient( 'update_core' );
		return ( ! empty( $updTrans ) ) ? $updTrans->updates[0]->current : '';
	}

	private static function get_compare_wp_version() {
		$new     = self::get_upgrade_version();
		$current = get_bloginfo( 'version' );

		if ( version_compare( $new, $current, '>' ) ) {
			self::$notification = true;
			return '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . $current . '</span>';
		} else {
			return '<span class="ms-lms-icon-check"></span>' . $current;
		}
	}

	private static function get_memory_limit() {
		$memory = ini_get( 'memory_limit' );

		if ( ! $memory || -1 === $memory ) {
			$memory = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
		}

		if ( ! is_numeric( $memory ) ) {
			$memory = wp_convert_hr_to_bytes( $memory );
		}

		$memoryNum = intval( $memory );
		$memory    = size_format( $memory );

		return ( version_compare( $memoryNum, '268435456', '>=' ) ) ? '<span class="ms-lms-icon-check"></span>' . $memory : '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . $memory . '</span>';
	}

	private static function get_php_version() {
		$php_version = null;

		if ( defined( 'PHP_VERSION' ) ) {
			$php_version = str_replace( PHP_EXTRA_VERSION, '', PHP_VERSION );
		} elseif ( function_exists( 'phpversion' ) ) {
			$php_version = phpversion();
		}

		if ( version_compare( '7.4', $php_version, '>=' ) ) {
			self::$notification = true;
			$php_version        = '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . $php_version . '</span>';
		} else {
			$php_version = '<span class="ms-lms-icon-check"></span>' . $php_version;
		}

		return $php_version;
	}

	private static function get_mysql_version() {
		global $wpdb;

		return ( version_compare( $wpdb->db_version(), '5.6', '>=' ) ) ? '<span class="ms-lms-icon-check"></span>' . $wpdb->db_version() : '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . $wpdb->db_version() . '</span>';
	}

	private static function get_post_max_size() {
		$pms    = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
		$pmsNum = size_format( $pms );

		return ( function_exists( 'ini_get' ) && version_compare( $pms, '67108864', '>=' ) ) ? '<span class="ms-lms-icon-check"></span>' . $pmsNum : '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . $pmsNum . '</span>';
	}

	private static function get_php_max_input_vars_system() {
		$registered_navs  = get_nav_menu_locations();
		$menu_items_count = array( '0' => '0' );
		foreach ( $registered_navs as $handle => $registered_nav ) {
			$menu = wp_get_nav_menu_object( $registered_nav );
			if ( $menu ) {
				$menu_items_count[] = $menu->count;
			}
		}

		$max_input_vars = ini_get( 'max_input_vars' );

		if ( $max_input_vars < self::$srMaxInputVars ) {
			self::$notification = true;
			$inputVars          = '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . apply_filters( 'stm_theme_esc_variable', $max_input_vars ) . '</span>';
		} else {
			$inputVars = '<span class="ms-lms-icon-check"></span>' . $max_input_vars;
		}

		return $inputVars;
	}

	private static function get_max_upload_size() {
		$mus = size_format( wp_max_upload_size() );
		return ( version_compare( wp_max_upload_size(), '67108864', '>=' ) ) ? '<span class="ms-lms-icon-check"></span>' . $mus : '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . $mus . '</span>';
	}

	private static function get_php_time_limit_system() {
		$time_limit = ini_get( 'max_execution_time' );
		$limit      = '';

		if ( 300 > $time_limit && 0 !== $time_limit ) {
			self::$notification = true;
			$limit              = '<span class="ms-lms-icon-cancel"></span><span class="plug_bold">' . $time_limit . '</span>';
		} else {
			$limit = '<span class="ms-lms-icon-check"></span>' . $time_limit;
		}

		return $limit;
	}

	private static function get_wp_remote_get() {
		$response = wp_safe_remote_get(
			'https://build.envato.com/api/',
			array(
				'decompress' => false,
				'user-agent' => 'test-api',
			)
		);

		return ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) ? '<span class="ms-lms-icon-check"></span>enabled' : '<span class="ss-error">wp_remote_get() failed. Some theme features may not work. Please contact your hosting provider and make sure that https://build.envato.com/api/ is not blocked.</span>';
	}

	public static function set_notification_transient() {
		if ( self::$notification ) {
			set_transient( 'system_status_notification', 'isset' );
			return;
		}

		delete_transient( 'system_status_notification' );
	}
}
