<?php
/**
 * User chat history template.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$user_id = get_current_user_id();
$chat_logs = SmartCommerce_AI_Chat_Logger::get_chat_logs( array( 'user_id' => $user_id ) );
?>

<div class="smartcommerce-ai-user-history">
	<h2><?php esc_html_e( 'Your Chat History', 'smartcommerce-ai' ); ?></h2>

	<?php if ( ! empty( $chat_logs ) ) : ?>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'smartcommerce-ai' ); ?></th>
					<th><?php esc_html_e( 'Your Message', 'smartcommerce-ai' ); ?></th>
					<th><?php esc_html_e( 'AI Response', 'smartcommerce-ai' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $chat_logs as $log ) : ?>
					<tr>
						<td><?php echo esc_html( $log['time'] ); ?></td>
						<td><?php echo esc_html( $log['user_message'] ); ?></td>
						<td><?php echo esc_html( $log['ai_response'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<a href="?smartcommerce_ai_export=txt" class="button"><?php esc_html_e( 'Export as TXT', 'smartcommerce-ai' ); ?></a>
		<a href="?smartcommerce_ai_export=csv" class="button"><?php esc_html_e( 'Export as CSV', 'smartcommerce-ai' ); ?></a>
	<?php else : ?>
		<p><?php esc_html_e( 'You have no chat history.', 'smartcommerce-ai' ); ?></p>
	<?php endif; ?>
</div>
<?php
// Handle export
if ( isset( $_GET['smartcommerce_ai_export'] ) ) {
	$format = sanitize_text_field( $_GET['smartcommerce_ai_export'] );
	$user_id = get_current_user_id();
	$chat_logs = SmartCommerce_AI_Chat_Logger::get_chat_logs( array( 'user_id' => $user_id, 'number' => -1 ) );

	if ( 'txt' === $format ) {
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="chat-history.txt"');
		foreach ( $chat_logs as $log ) {
			echo "Date: " . $log['time'] . "\n";
			echo "You: " . $log['user_message'] . "\n";
			echo "AI: " . $log['ai_response'] . "\n\n";
		}
		exit;
	}

	if ( 'csv' === $format ) {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="chat-history.csv"');
		$output = fopen('php://output', 'w');
		fputcsv($output, array('Date', 'User Message', 'AI Response'));
		foreach ( $chat_logs as $log ) {
			fputcsv($output, array($log['time'], $log['user_message'], $log['ai_response']));
		}
		exit;
	}
}
?>
