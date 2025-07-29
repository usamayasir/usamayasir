<?php
/**
 * Provides the user interface for the settings page.
 *
 * @package    SmartCommerce_AI
 * @subpackage SmartCommerce_AI/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Save settings
if ( isset( $_POST['submit'] ) ) {
	$options = get_option( 'smartcommerce_ai_options', array() );
	$options['api_key'] = sanitize_text_field( $_POST['api_key'] );
	$options['default_model'] = sanitize_text_field( $_POST['default_model'] );
	update_option( 'smartcommerce_ai_options', $options );
	echo '<div class="updated"><p>Settings saved.</p></div>';
}

$options = get_option( 'smartcommerce_ai_options', array() );
$api_key = isset( $options['api_key'] ) ? $options['api_key'] : '';
$default_model = isset( $options['default_model'] ) ? $options['default_model'] : 'gpt-3.5-turbo';
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form method="post" action="">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'OpenAI API Key', 'smartcommerce-ai' ); ?></th>
					<td>
						<input type="text" name="api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text">
						<p class="description"><?php _e( 'Enter your OpenAI API key.', 'smartcommerce-ai' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Default Model', 'smartcommerce-ai' ); ?></th>
					<td>
						<select name="default_model">
							<option value="gpt-3.5-turbo" <?php selected( $default_model, 'gpt-3.5-turbo' ); ?>>GPT-3.5 Turbo</option>
							<option value="gpt-4" <?php selected( $default_model, 'gpt-4' ); ?>>GPT-4</option>
						</select>
						<p class="description"><?php _e( 'Select the default GPT model to use.', 'smartcommerce-ai' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
