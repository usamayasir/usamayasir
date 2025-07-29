<?php
/**
 * Provides the user interface for the email writer page.
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
						<h2 class="hndle"><span><?php _e( 'Generate Email', 'smartcommerce-ai' ); ?></span></h2>
						<div class="inside">
							<form id="email-generator-form">
								<p>
									<label for="email_type"><?php _e( 'Email Type', 'smartcommerce-ai' ); ?></label>
									<select id="email_type" name="email_type" class="widefat">
										<option value="Abandoned Cart"><?php _e( 'Abandoned Cart', 'smartcommerce-ai' ); ?></option>
										<option value="Promotion"><?php _e( 'Promotion', 'smartcommerce-ai' ); ?></option>
										<option value="Thank You"><?php _e( 'Thank You', 'smartcommerce-ai' ); ?></option>
										<option value="Follow-up"><?php _e( 'Follow-up', 'smartcommerce-ai' ); ?></option>
									</select>
								</p>
								<p>
									<label for="target_product"><?php _e( 'Target Product', 'smartcommerce-ai' ); ?></label>
									<select id="target_product" name="target_product" class="wc-product-search widefat" style="width: 100%;"></select>
								</p>
								<p>
									<label for="custom_message"><?php _e( 'Custom Message', 'smartcommerce-ai' ); ?></label>
									<textarea id="custom_message" name="custom_message" rows="5" class="widefat"></textarea>
								</p>
								<p>
									<button type="submit" class="button button-primary"><?php _e( 'Generate Email', 'smartcommerce-ai' ); ?></button>
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h2 class="hndle"><span><?php _e( 'Generated Email', 'smartcommerce-ai' ); ?></span></h2>
						<div class="inside">
							<div id="generated-email" style="display:none;">
								<div class="generated-content-field">
									<h3><?php _e( 'Subject', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-subject" rows="1" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-subject"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<div class="generated-content-field">
									<h3><?php _e( 'Body', 'smartcommerce-ai' ); ?></h3>
									<textarea id="generated-body" rows="10" class="widefat"></textarea>
									<button class="button copy-button" data-target="generated-body"><?php _e( 'Copy', 'smartcommerce-ai' ); ?></button>
								</div>
								<p>
									<button id="export-txt" class="button"><?php _e( 'Export as .txt', 'smartcommerce-ai' ); ?></button>
								</p>
							</div>
							<p id="no-email-yet"><?php _e( 'Generate an email to see it here.', 'smartcommerce-ai' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {
		$('#email-generator-form').on('submit', function(e) {
			e.preventDefault();
			var form = $(this);
			form.find('button[type="submit"]').prop('disabled', true);
			$('#no-email-yet').hide();
			$('#generated-email').hide();
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
