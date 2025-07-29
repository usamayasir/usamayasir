<?php
/**
 * Provides the user interface for the product generator page.
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
						<h2 class="hndle"><span><?php _e( 'Generate Product Content', 'smartcommerce-ai' ); ?></span></h2>
						<div class="inside">
							<form id="product-generator-form">
								<p>
									<label for="product_name"><?php _e( 'Product Name', 'smartcommerce-ai' ); ?></label>
									<input type="text" id="product_name" name="product_name" class="widefat" required>
								</p>
								<p>
									<label for="key_features"><?php _e( 'Key Features', 'smartcommerce-ai' ); ?></label>
									<textarea id="key_features" name="key_features" rows="5" class="widefat" required></textarea>
								</p>
								<p>
									<label for="tone"><?php _e( 'Tone', 'smartcommerce-ai' ); ?></label>
									<select id="tone" name="tone" class="widefat">
										<option value="Friendly"><?php _e( 'Friendly', 'smartcommerce-ai' ); ?></option>
										<option value="Professional"><?php _e( 'Professional', 'smartcommerce-ai' ); ?></option>
										<option value="SEO-focused"><?php _e( 'SEO-focused', 'smartcommerce-ai' ); ?></option>
									</select>
								</p>
								<p>
									<button type="submit" class="button button-primary"><?php _e( 'Generate Content', 'smartcommerce-ai' ); ?></button>
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h2 class="hndle"><span><?php _e( 'Generated Content', 'smartcommerce-ai' ); ?></span></h2>
						<div class="inside">
							<div id="generated-content" style="display:none;">
								<div class="generated-content-field">
									<h3><?php _e( 'Title', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-title" rows="1" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-title"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<div class="generated-content-field">
									<h3><?php _e( 'Short Description', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-short-description" rows="5" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-short-description"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<div class="generated-content-field">
									<h3><?php _e( 'Long Description', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-long-description" rows="10" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-long-description"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<p>
									<button id="insert-into-product" class="button button-primary"><?php _e( 'Insert into New Product', 'smartcommerce-ai' ); ?></button>
								</p>
							</div>
							<p id="no-content-yet"><?php _e( 'Generate content to see it here.', 'smartcommerce-ai' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {
		$('#product-generator-form').on('submit', function(e) {
			e.preventDefault();
			var form = $(this);
			form.find('button[type="submit"]').prop('disabled', true);
			$('#no-content-yet').hide();
			$('#generated-content').hide();
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
