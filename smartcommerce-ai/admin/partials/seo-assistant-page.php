<?php
/**
 * Provides the user interface for the SEO assistant page.
 *
 * @package    SmartCommerce_AI
 * @subpackage SmartCommerce_AI/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php _e( 'Generate SEO Content', 'smartcommerce-ai' ); ?></span></h2>
						<div class="inside">
							<form id="seo-generator-form">
								<p>
									<label for="post_id"><?php _e( 'Select a Product or Post', 'smartcommerce-ai' ); ?></label>
									<select id="post_id" name="post_id" class="post-search widefat" data-post-type="post,product" style="width: 100%;"></select>
								</p>
								<p>
									<button type="submit" class="button button-primary"><?php _e( 'Generate SEO Content', 'smartcommerce-ai' ); ?></button>
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h2 class="hndle"><span><?php _e( 'Generated SEO Content', 'smartcommerce-ai' ); ?></span></h2>
						<div class="inside">
							<div id="generated-seo" style="display:none;">
								<div class="generated-content-field">
									<h3><?php _e( 'SEO Title', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-seo-title" rows="1" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-seo-title"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<div class="generated-content-field">
									<h3><?php _e( 'Meta Description', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-meta-description" rows="3" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-meta-description"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<div class="generated-content-field">
									<h3><?php _e( 'Focus Keywords', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-focus-keywords" rows="1" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-focus-keywords"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<p>
									<button id="save-to-post" class="button button-primary"><?php _e( 'Save to Post', 'smartcommerce-ai' ); ?></button>
								</p>
							</div>
							<p id="no-seo-yet"><?php _e( 'Generate SEO content to see it here.', 'smartcommerce-ai' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
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
			$('#no-seo-yet').hide();
			$('#generated-seo').hide();
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
