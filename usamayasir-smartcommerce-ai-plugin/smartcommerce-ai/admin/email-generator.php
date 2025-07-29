<?php
/**
 * AI Email Writer for Marketing.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_Email_Generator {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'wp_ajax_smartcommerce_ai_generate_email', array( $this, 'generate_email' ) );
	}

	public function add_admin_menu() {
		$options = get_option( 'smartcommerce_ai_options' );
		if ( ! empty( $options['enable_email_writer'] ) ) {
			add_submenu_page(
				'woocommerce',
				__( 'AI Email Writer', 'smartcommerce-ai' ),
				__( 'AI Email Writer', 'smartcommerce-ai' ),
				'manage_woocommerce',
				'smartcommerce-ai-email-generator',
				array( $this, 'create_admin_page' )
			);
		}
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AI Email Writer', 'smartcommerce-ai' ); ?></h1>
			<form id="email-generator-form">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="email_type"><?php esc_html_e( 'Email Type', 'smartcommerce-ai' ); ?></label></th>
							<td>
								<select id="email_type" name="email_type">
									<option value="Abandoned Cart"><?php esc_html_e( 'Abandoned Cart', 'smartcommerce-ai' ); ?></option>
									<option value="Promotion"><?php esc_html_e( 'Promotion', 'smartcommerce-ai' ); ?></option>
									<option value="Thank You"><?php esc_html_e( 'Thank You', 'smartcommerce-ai' ); ?></option>
									<option value="Follow-up"><?php esc_html_e( 'Follow-up', 'smartcommerce-ai' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="target_product"><?php esc_html_e( 'Target Product', 'smartcommerce-ai' ); ?></label></th>
							<td>
								<select id="target_product" name="target_product" class="wc-product-search" style="width: 300px;"></select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="custom_message"><?php esc_html_e( 'Custom Message', 'smartcommerce-ai' ); ?></label></th>
							<td><textarea id="custom_message" name="custom_message" rows="5" class="large-text"></textarea></td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Generate Email', 'smartcommerce-ai' ); ?></button>
				</p>
			</form>
			<div id="generated-email" style="display:none;">
				<h2><?php esc_html_e( 'Generated Email', 'smartcommerce-ai' ); ?></h2>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'Subject', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-subject" rows="1" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-subject"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'Body', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-body" rows="10" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-body"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<p>
					<button id="export-txt" class="button"><?php esc_html_e( 'Export as .txt', 'smartcommerce-ai' ); ?></button>
				</p>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$('#email-generator-form').on('submit', function(e) {
					e.preventDefault();
					var form = $(this);
					form.find('button[type="submit"]').prop('disabled', true);
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'smartcommerce_ai_generate_email',
							nonce: '<?php echo wp_create_nonce( "smartcommerce_ai_generate_email_nonce" ); ?>',
							email_type: $('#email_type').val(),
							target_product: $('#target_product').val(),
							custom_message: $('#custom_message').val()
						},
						success: function(response) {
							if (response.success) {
								$('#generated-subject').val(response.data.subject);
								$('#generated-body').val(response.data.body);
								$('#generated-email').show();
							} else {
								alert(response.data.message);
							}
						},
						complete: function() {
							form.find('button[type="submit"]').prop('disabled', false);
						}
					});
				});

				$('.copy-button').on('click', function() {
					var target = $('#' + $(this).data('target'));
					target.select();
					document.execCommand('copy');
				});

				$('#export-txt').on('click', function() {
					var subject = $('#generated-subject').val();
					var body = $('#generated-body').val();
					var element = document.createElement('a');
					element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent("Subject: " + subject + "\n\n" + body));
					element.setAttribute('download', 'email.txt');
					element.style.display = 'none';
					document.body.appendChild(element);
					element.click();
					document.body.removeChild(element);
				});
			});
		</script>
		<?php
	}

	public function generate_email() {
		check_ajax_referer( 'smartcommerce_ai_generate_email_nonce', 'nonce' );

		$email_type     = sanitize_text_field( $_POST['email_type'] );
		$target_product = intval( $_POST['target_product'] );
		$custom_message = sanitize_textarea_field( $_POST['custom_message'] );

		$product = wc_get_product( $target_product );
		$product_name = $product ? $product->get_name() : '';

		$prompt = "Write a marketing email.\nEmail Type: $email_type\nProduct: $product_name\nCustom Message: $custom_message\n\nGenerate a subject line and email body, separated by '---'.";

		$response = SmartCommerce_AI_Functions::call_openai_api(
			array(
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		} else {
			$content = $response['choices'][0]['message']['content'];
			$parts   = explode( '---', $content );

			wp_send_json_success(
				array(
					'subject' => trim( $parts[0] ),
					'body'    => trim( $parts[1] ),
				)
			);
		}
	}
}

new SmartCommerce_AI_Email_Generator();
