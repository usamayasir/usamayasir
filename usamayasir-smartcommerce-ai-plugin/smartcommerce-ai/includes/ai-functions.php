<?php
/**
 * AI functions.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_Functions {

	/**
	 * Call the OpenAI API.
	 *
	 * @param array $messages The messages to send to the API.
	 * @return array The API response.
	 */
	public static function call_openai_api( $messages ) {
		$options = get_option( 'smartcommerce_ai_options' );
		$api_key = isset( $options['api_key'] ) ? $options['api_key'] : '';
		$model   = isset( $options['default_model'] ) ? $options['default_model'] : 'gpt-3.5-turbo';
		$max_tokens = isset( $options['max_tokens'] ) ? $options['max_tokens'] : 1024;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'api_key_missing', __( 'OpenAI API key is not set.', 'smartcommerce-ai' ) );
		}

		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				),
				'body'    => json_encode(
					array(
						'model'    => $model,
						'messages' => $messages,
						'max_tokens' => $max_tokens,
					)
				),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			return new WP_Error( 'openai_error', $data['error']['message'] );
		}

		return $data;
	}
}
