<?php
/**
 * Chat logs admin page.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_Chat_Logs_Page {

	private $list_table;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_export' ) );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Chat Logs', 'smartcommerce-ai' ),
			__( 'Chat Logs', 'smartcommerce-ai' ),
			'manage_woocommerce',
			'smartcommerce-ai-chat-logs',
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {
		$this->list_table = new SmartCommerce_AI_Chat_Logs_List_Table();
		$this->list_table->prepare_items();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Chat Logs', 'smartcommerce-ai' ); ?></h1>
			<form method="post">
				<?php
				$this->list_table->search_box( 'search', 'search_id' );
				$this->list_table->display();
				?>
			</form>
		</div>
		<?php
	}

	public function handle_export() {
		if ( isset( $_GET['action'], $_GET['export_user'] ) && 'export_chat_logs' === $_GET['action'] ) {
			$user_id = intval( $_GET['export_user'] );
			$chat_logs = SmartCommerce_AI_Chat_Logger::get_chat_logs( array( 'user_id' => $user_id, 'number' => -1 ) );

			if ( ! empty( $_GET['format'] ) && 'csv' === $_GET['format'] ) {
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="chat-history-' . $user_id . '.csv"');
				$output = fopen('php://output', 'w');
				fputcsv($output, array('Date', 'User Message', 'AI Response'));
				foreach ( $chat_logs as $log ) {
					fputcsv($output, array($log['time'], $log['user_message'], $log['ai_response']));
				}
				exit;
			} else {
				header('Content-Type: text/plain');
				header('Content-Disposition: attachment; filename="chat-history-' . $user_id . '.txt"');
				foreach ( $chat_logs as $log ) {
					echo "Date: " . $log['time'] . "\n";
					echo "User: " . $log['user_message'] . "\n";
					echo "AI: " . $log['ai_response'] . "\n\n";
				}
				exit;
			}
		}
	}
}

new SmartCommerce_AI_Chat_Logs_Page();

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SmartCommerce_AI_Chat_Logs_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Chat Log', 'smartcommerce-ai' ),
				'plural'   => __( 'Chat Logs', 'smartcommerce-ai' ),
				'ajax'     => false,
			)
		);
	}

	public function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'time'         => __( 'Date', 'smartcommerce-ai' ),
			'user'         => __( 'User', 'smartcommerce-ai' ),
			'user_message' => __( 'User Message', 'smartcommerce-ai' ),
			'ai_response'  => __( 'AI Response', 'smartcommerce-ai' ),
		);
		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'time' => array( 'time', false ),
			'user' => array( 'user_id', false ),
		);
		return $sortable_columns;
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'time':
				return $item['time'];
			case 'user_message':
				return $item['user_message'];
			case 'ai_response':
				return $item['ai_response'];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_user( $item ) {
		$user = get_user_by( 'id', $item['user_id'] );
		if ( $user ) {
			return $user->display_name;
		}
		return __( 'Anonymous', 'smartcommerce-ai' ) . ' (' . substr( $item['session_id'], 0, 8 ) . '...)';
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="log[]" value="%s" />', $item['id']
		);
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete'
		);
		return $actions;
	}

	public function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			$log_ids = esc_sql( $_POST['log'] );
			foreach ( $log_ids as $log_id ) {
				SmartCommerce_AI_Chat_Logger::delete_chat_log( $log_id );
			}
		}
	}

	public function prepare_items() {
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'chat_logs_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = SmartCommerce_AI_Chat_Logger::count_chat_logs();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$args = array(
			'number'  => $per_page,
			'offset'  => ( $current_page - 1 ) * $per_page,
			'orderby' => isset( $_REQUEST['orderby'] ) ? sanitize_sql_orderby( $_REQUEST['orderby'] ) : 'id',
			'order'   => isset( $_REQUEST['order'] ) ? sanitize_key( $_REQUEST['order'] ) : 'DESC',
		);

		if ( ! empty( $_REQUEST['s'] ) ) {
			$user = get_user_by( 'login', sanitize_text_field( $_REQUEST['s'] ) );
			if ( $user ) {
				$args['user_id'] = $user->ID;
			}
		}

		$this->items = SmartCommerce_AI_Chat_Logger::get_chat_logs( $args );
	}
}
