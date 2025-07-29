<?php
/**
 * AI Product Content Generator.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_Product_Generator {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'wp_ajax_smartcommerce_ai_generate_product_content', array( $this, 'generate_product_content' ) );
	}

	public function add_admin_menu() {
		$options = get_option( 'smartcommerce_ai_options' );
		if ( ! empty( $options['enable_product_generator'] ) ) {
			add_submenu_page(
				'edit.php?post_type=product',
				__( 'AI Product Generator', 'smartcommerce-ai' ),
				__( 'AI Product Generator', 'smartcommerce-ai' ),
				'manage_woocommerce',
				'smartcommerce-ai-product-generator',
				array( $this, 'create_admin_page' )
			);
		}
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AI Product Content Generator', 'smartcommerce-ai' ); ?></h1>
			<form id="product-generator-form">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="product_name"><?php esc_html_e( 'Product Name', 'smartcommerce-ai' ); ?></label></th>
							<td><input type="text" id="product_name" name="product_name" class="regular-text" required></td>
						</tr>
						<tr>
							<th scope="row"><label for="key_features"><?php esc_html_e( 'Key Features', 'smartcommerce-ai' ); ?></label></th>
							<td><textarea id="key_features" name="key_features" rows="5" class="large-text" required></textarea></td>
						</tr>
						<tr>
							<th scope="row"><label for="tone"><?php esc_html_e( 'Tone', 'smartcommerce-ai' ); ?></label></th>
							<td>
								<select id="tone" name="tone">
									<option value="Friendly"><?php esc_html_e( 'Friendly', 'smartcommerce-ai' ); ?></option>
									<option value="Professional"><?php esc_html_e( 'Professional', 'smartcommerce-ai' ); ?></option>
									<option value="SEO-focused"><?php esc_html_e( 'SEO-focused', 'smartcommerce-ai' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Generate Content', 'smartcommerce-ai' ); ?></button>
				</p>
			</form>
			<div id="generated-content" style="display:none;">
				<h2><?php esc_html_e( 'Generated Content', 'smartcommerce-ai' ); ?></h2>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'Title', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-title" rows="1" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-title"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'Short Description', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-short-description" rows="5" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-short-description"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'Long Description', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-long-description" rows="10" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-long-description"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<p>
					<button id="insert-into-product" class="button button-primary"><?php esc_html_e( 'Insert into New Product', 'smartcommerce-ai' ); ?></button>
				</p>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$('#product-generator-form').on('submit', function(e) {
					e.preventDefault();
					var form = $(this);
					form.find('button[type="submit"]').prop('disabled', true);
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'smartcommerce_ai_generate_product_content',
							nonce: '<?php echo wp_create_nonce( "smartcommerce_ai_generate_product_content_nonce" ); ?>',
							product_name: $('#product_name').val(),
							key_features: $('#key_features').val(),
							tone: $('#tone').val()
						},
						success: function(response) {
							if (response.success) {
								$('#generated-title').val(response.data.title);
								$('#generated-short-description').val(response.data.short_description);
								$('#generated-long-description').val(response.data.long_description);
								$('#generated-content').show();
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

				$('#insert-into-product').on('click', function() {
					var title = $('#generated-title').val();
					var shortDescription = $('#generated-short-description').val();
					var longDescription = $('#generated-long-description').val();
					var url = '<?php echo admin_url( "post-new.php?post_type=product" ); ?>';
					url += '&post_title=' + encodeURIComponent(title);
					url += '&post_excerpt=' + encodeURIComponent(shortDescription);
					url += '&content=' + encodeURIComponent(longDescription);
					window.location.href = url;
				});
			});
		</script>
		<?php
	}

	public function generate_product_content() {
		check_ajax_referer( 'smartcommerce_ai_generate_product_content_nonce', 'nonce' );

		$product_name = sanitize_text_field( $_POST['product_name'] );
		$key_features = sanitize_textarea_field( $_POST['key_features'] );
		$tone         = sanitize_text_field( $_POST['tone'] );

		$prompt = "Generate product content for a product named '$product_name' with the following key features: $key_features. The tone should be $tone. Provide a title, a short description, and a long description, separated by '---'.";

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
					'title'            => trim( $parts[0] ),
					'short_description' => trim( $parts[1] ),
					'long_description'  => trim( $parts[2] ),
				)
			);
		}
	}
}

new SmartCommerce_AI_Product_Generator();
