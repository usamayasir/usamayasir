<?php
/**
 * AI SEO Assistant.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_SEO_Generator {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'wp_ajax_smartcommerce_ai_generate_seo', array( $this, 'generate_seo' ) );
	}

	public function add_admin_menu() {
		$options = get_option( 'smartcommerce_ai_options' );
		if ( ! empty( $options['enable_seo_assistant'] ) ) {
			add_submenu_page(
				'tools.php',
				__( 'AI SEO Assistant', 'smartcommerce-ai' ),
				__( 'AI SEO Assistant', 'smartcommerce-ai' ),
				'manage_options',
				'smartcommerce-ai-seo-generator',
				array( $this, 'create_admin_page' )
			);
		}
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AI SEO Assistant', 'smartcommerce-ai' ); ?></h1>
			<form id="seo-generator-form">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="post_id"><?php esc_html_e( 'Select a Product or Post', 'smartcommerce-ai' ); ?></label></th>
							<td>
								<select id="post_id" name="post_id" class="post-search" data-post-type="post,product" style="width: 300px;"></select>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Generate SEO Content', 'smartcommerce-ai' ); ?></button>
				</p>
			</form>
			<div id="generated-seo" style="display:none;">
				<h2><?php esc_html_e( 'Generated SEO Content', 'smartcommerce-ai' ); ?></h2>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'SEO Title', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-seo-title" rows="1" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-seo-title"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'Meta Description', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-meta-description" rows="3" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-meta-description"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<div class="generated-content-field">
					<h3><?php esc_html_e( 'Focus Keywords', 'smartcommerce-ai' ); ?></h3>
					<textarea id="generated-focus-keywords" rows="1" class="large-text"></textarea>
					<button class="button copy-button" data-target="generated-focus-keywords"><?php esc_html_e( 'Copy', 'smartcommerce-ai' ); ?></button>
				</div>
				<p>
					<button id="save-to-post" class="button button-primary"><?php esc_html_e( 'Save to Post', 'smartcommerce-ai' ); ?></button>
				</p>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$('.post-search').select2({
					ajax: {
						url: ajaxurl,
						dataType: 'json',
						delay: 250,
						data: function(params) {
							return {
								q: params.term,
								action: 'woocommerce_json_search_posts_and_pages'
							};
						},
						processResults: function(data) {
							var terms = [];
							if (data) {
								$.each(data, function(id, text) {
									terms.push({
										id: id,
										text: text
									});
								});
							}
							return {
								results: terms
							};
						},
						cache: true
					},
					minimumInputLength: 2
				});

				$('#seo-generator-form').on('submit', function(e) {
					e.preventDefault();
					var form = $(this);
					form.find('button[type="submit"]').prop('disabled', true);
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'smartcommerce_ai_generate_seo',
							nonce: '<?php echo wp_create_nonce( "smartcommerce_ai_generate_seo_nonce" ); ?>',
							post_id: $('#post_id').val()
						},
						success: function(response) {
							if (response.success) {
								$('#generated-seo-title').val(response.data.seo_title);
								$('#generated-meta-description').val(response.data.meta_description);
								$('#generated-focus-keywords').val(response.data.focus_keywords);
								$('#generated-seo').show();
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

				$('#save-to-post').on('click', function() {
					var postId = $('#post_id').val();
					var seoTitle = $('#generated-seo-title').val();
					var metaDescription = $('#generated-meta-description').val();
					var focusKeywords = $('#generated-focus-keywords').val();

					// Yoast SEO
					if (typeof YoastSEO !== 'undefined') {
						update_post_meta(postId, '_yoast_wpseo_title', seoTitle);
						update_post_meta(postId, '_yoast_wpseo_metadesc', metaDescription);
						update_post_meta(postId, '_yoast_wpseo_focuskw', focusKeywords);
					}

					// Rank Math
					if (typeof rankMath !== 'undefined') {
						update_post_meta(postId, 'rank_math_title', seoTitle);
						update_post_meta(postId, 'rank_math_description', metaDescription);
						update_post_meta(postId, 'rank_math_focus_keyword', focusKeywords);
					}

					alert('SEO data saved!');
				});

				function update_post_meta(postId, metaKey, metaValue) {
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'update_post_meta',
							post_id: postId,
							meta_key: metaKey,
							meta_value: metaValue
						}
					});
				}
			});
		</script>
		<?php
	}

	public function generate_seo() {
		check_ajax_referer( 'smartcommerce_ai_generate_seo_nonce', 'nonce' );

		$post_id = intval( $_POST['post_id'] );
		$post    = get_post( $post_id );
		$content = $post->post_content;

		$prompt = "Generate SEO content for the following text:\n\n$content\n\nProvide an SEO title, a meta description, and focus keywords, separated by '---'.";

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
					'seo_title'        => trim( $parts[0] ),
					'meta_description' => trim( $parts[1] ),
					'focus_keywords'   => trim( $parts[2] ),
				)
			);
		}
	}
}

new SmartCommerce_AI_SEO_Generator();
