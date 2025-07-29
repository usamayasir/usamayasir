<?php
/**
 * Chat logger.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_Chat_Logger {

	public static function create_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'smartcommerce_ai_chat_logs';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			user_id bigint(20) UNSIGNED,
			session_id varchar(255) NOT NULL,
			user_message text NOT NULL,
			ai_response text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public static function log_interaction( $user_message, $ai_response ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';

		$wpdb->insert(
			$table_name,
			array(
				'time'         => current_time( 'mysql' ),
				'user_id'      => get_current_user_id(),
				'session_id'   => self::get_session_id(),
				'user_message' => $user_message,
				'ai_response'  => $ai_response,
			)
		);
	}

	private static function get_session_id() {
		if ( ! session_id() ) {
			session_start();
		}
		return session_id();
	}

	public static function get_chat_logs( $args = array() ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';

		$defaults = array(
			'number'  => 20,
			'offset'  => 0,
			'orderby' => 'id',
			'order'   => 'DESC',
			'user_id' => '',
			'date_query' => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$sql = "SELECT * FROM $table_name";

		$where = array();

		if ( ! empty( $args['user_id'] ) ) {
			$where[] = $wpdb->prepare( 'user_id = %d', $args['user_id'] );
		}

		if ( ! empty( $args['date_query'] ) ) {
			$date_query = new WP_Date_Query( $args['date_query'], 'time' );
			$where[] = $date_query->get_sql();
		}

		if ( ! empty( $where ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $where );
		}

		$sql .= $wpdb->prepare( " ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d", $args['offset'], $args['number'] );

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	public static function count_chat_logs( $args = array() ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';

		$sql = "SELECT COUNT(*) FROM $table_name";

		$where = array();

		if ( ! empty( $args['user_id'] ) ) {
			$where[] = $wpdb->prepare( 'user_id = %d', $args['user_id'] );
		}

		if ( ! empty( $args['date_query'] ) ) {
			$date_query = new WP_Date_Query( $args['date_query'], 'time' );
			$where[] = $date_query->get_sql();
		}

		if ( ! empty( $where ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $where );
		}

		return $wpdb->get_var( $sql );
	}

	public static function delete_chat_log( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';
		$wpdb->delete( $table_name, array( 'id' => $id ), array( '%d' ) );
	}
}

// Shortcode for user chat history
add_shortcode( 'smartcommerce_ai_myhistory', 'smartcommerce_ai_myhistory_shortcode' );

function smartcommerce_ai_myhistory_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '<p>' . __( 'You must be logged in to view your chat history.', 'smartcommerce-ai' ) . '</p>';
	}

	ob_start();
	require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'templates/user-history.php';
	return ob_get_clean();
}
