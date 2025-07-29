<?php
/**
 * Provides the user interface for the chatbot management page.
 *
 * @package    SmartCommerce_AI
 * @subpackage SmartCommerce_AI/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Save settings
if ( isset( $_POST['submit'] ) ) {
	$options = get_option( 'smartcommerce_ai_chatbot_options', array() );
	$options['enable_chatbot'] = isset( $_POST['enable_chatbot'] ) ? 1 : 0;
	$options['greeting_message'] = sanitize_text_field( $_POST['greeting_message'] );
	$options['max_tokens'] = intval( $_POST['max_tokens'] );
	$options['widget_color'] = sanitize_hex_color( $_POST['widget_color'] );
	$options['training_data'] = sanitize_textarea_field( $_POST['training_data'] );
	update_option( 'smartcommerce_ai_chatbot_options', $options );
	echo '<div class="updated"><p>Settings saved.</p></div>';
}

$options = get_option( 'smartcommerce_ai_chatbot_options', array() );
$enable_chatbot = isset( $options['enable_chatbot'] ) ? $options['enable_chatbot'] : 0;
$greeting_message = isset( $options['greeting_message'] ) ? $options['greeting_message'] : 'Hi! I’m your SmartCommerce AI assistant. How can I help you today?';
$max_tokens = isset( $options['max_tokens'] ) ? $options['max_tokens'] : 256;
$widget_color = isset( $options['widget_color'] ) ? $options['widget_color'] : '#0073aa';
$training_data = isset( $options['training_data'] ) ? $options['training_data'] : '';
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form method="post" action="">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'Enable Chatbot', 'smartcommerce-ai' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="enable_chatbot" value="1" <?php checked( $enable_chatbot, 1 ); ?>>
							<?php _e( 'Enable the chatbot on the frontend.', 'smartcommerce-ai' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Greeting Message', 'smartcommerce-ai' ); ?></th>
					<td>
						<input type="text" name="greeting_message" value="<?php echo esc_attr( $greeting_message ); ?>" class="regular-text">
						<p class="description"><?php _e( 'The first message the chatbot displays to the user.', 'smartcommerce-ai' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Max Tokens', 'smartcommerce-ai' ); ?></th>
					<td>
						<input type="number" name="max_tokens" value="<?php echo esc_attr( $max_tokens ); ?>" class="small-text">
						<p class="description"><?php _e( 'The maximum number of tokens to generate in the chatbot response.', 'smartcommerce-ai' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Widget Color', 'smartcommerce-ai' ); ?></th>
					<td>
						<input type="text" name="widget_color" value="<?php echo esc_attr( $widget_color ); ?>" class="wp-color-picker">
						<p class="description"><?php _e( 'The primary color of the chatbot widget.', 'smartcommerce-ai' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Training Data', 'smartcommerce-ai' ); ?></th>
					<td>
						<textarea name="training_data" rows="10" class="large-text"><?php echo esc_textarea( $training_data ); ?></textarea>
						<p class="description"><?php _e( 'Provide custom question and answer pairs to train the chatbot. Separate questions and answers with a pipe (|). Each pair should be on a new line. For example: "What is your return policy?|Our return policy is..."', 'smartcommerce-ai' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
<script>
	jQuery(document).ready(function($) {
		$('.wp-color-picker').wpColorPicker();
	});
</script>
