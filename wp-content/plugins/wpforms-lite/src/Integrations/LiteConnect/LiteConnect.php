<?php

namespace WPForms\Integrations\LiteConnect;

use WPForms\Integrations\IntegrationInterface;

/**
 * Class LiteConnect.
 *
 * @since 1.7.4
 */
abstract class LiteConnect implements IntegrationInterface {

	/**
	 * The slug that will be used to save the option of Lite Connect.
	 *
	 * @since 1.7.4
	 *
	 * @var string
	 */
	const SETTINGS_SLUG = 'lite-connect-enabled';

	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	public function allow_load() {

		return self::is_allowed();
	}

	/**
	 * Whether Lite Connect is allowed.
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	public static function is_allowed() {

		// Disable Lite Connect integration for local hosts.
		$allowed = ! self::is_local_not_debug();

		// phpcs:disable WPForms.PHP.ValidateHooks.InvalidHookName

		/**
		 * Determine whether Lite Connect integration is allowed to load.
		 *
		 * @since 1.7.4
		 *
		 * @param bool $is_allowed Is LiteConnect allowed? Value by default: true.
		 */
		return (bool) apply_filters( 'wpforms_integrations_lite_connect_is_allowed', $allowed );

		// phpcs:enable WPForms.PHP.ValidateHooks.InvalidHookName
	}

	/**
	 * Whether Lite Connect is enabled.
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	public static function is_enabled() {

		// phpcs:disable WPForms.PHP.ValidateHooks.InvalidHookName

		/**
		 * Determine whether LiteConnect is enabled on the WPForms > Settings admin page.
		 *
		 * @since 1.7.4
		 *
		 * @param bool $is_enabled Is LiteConnect enabled on WPForms > Settings page?
		 */
		return (bool) apply_filters( 'wpforms_integrations_lite_connect_is_enabled', wpforms_setting( self::SETTINGS_SLUG ) );

		// phpcs:enable WPForms.PHP.ValidateHooks.InvalidHookName
	}

	/**
	 * Load an integration.
	 *
	 * @since 1.7.4
	 */
	public function load() {

		$this->endpoints();
	}

	/**
	 * Whether Lite Connect is running locally and not in the debug mode.
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	private static function is_local_not_debug() {

		return ( ! defined( 'WPFORMS_DEBUG_LITE_CONNECT' ) ) && self::is_localhost();
	}

	/**
	 * Whether Lite Connect is running locally.
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	private static function is_localhost() {

		// Check for local IPs.
		$ip = wpforms_get_ip();

		if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
			return true;
		}

		// Check for local TLDs.
		if ( ! empty( $_SERVER['HTTP_HOST'] ) ) {
			$host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );

			$local_tlds = [
				'.local',
				'.invalid',
				'.example',
				'.test',
			];

			foreach ( $local_tlds as $tld ) {
				if ( preg_match( '/' . $tld . '$/', $host ) ) {
					return true;
				}
			}
		}

		// Return false if IP and TLD are not local.
		return false;
	}

	/**
	 * Provide responses to endpoint requests.
	 *
	 * @since 1.7.4
	 */
	private function endpoints() {

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$uri = filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$path = rtrim( wp_parse_url( $uri, PHP_URL_PATH ), '/' );

		if ( preg_match( '#/wpforms/auth/key/nonce$#', $path ) ) {
			$this->endpoint_key();
		}
	}

	/**
	 * Process endpoint for callback on generate_site_key().
	 *
	 * @since 1.7.4
	 */
	private function endpoint_key() {

		$json     = file_get_contents( 'php://input' );
		$response = json_decode( $json, true );

		if ( ! $response ) {
			$this->endpoint_die();
		}

		if ( isset( $response['error'] ) ) {
			$this->endpoint_die(
				'Lite Connect: unable to add the site to system',
				$response
			);
		}

		if ( ! isset( $response['key'], $response['id'], $response['nonce'] ) ) {
			$this->endpoint_die(
				'Lite Connect: unknown communication error',
				$response
			);
		}

		if ( ! wp_verify_nonce( $response['nonce'], API::KEY_NONCE_ACTION ) ) {
			$this->endpoint_die(
				'Lite Connect: nonce verification failed',
				$response
			);
		}

		unset( $response['nonce'] );

		$settings         = get_option( Integration::get_option_name(), [] );
		$settings['site'] = $response;

		update_option( Integration::get_option_name(), $settings );

		exit();
	}

	/**
	 * Finish the endpoint execution with wp_die().
	 *
	 * @since 1.7.4
	 *
	 * @param string $title    Log message title.
	 * @param array  $response Response.
	 *
	 * @noinspection ForgottenDebugOutputInspection
	 */
	private function endpoint_die( $title = '', $response = [] ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$this->log( $title, $response );

		// We call wp_die too early, before the query is run.
		// So, we should remove some filters to avoid having PHP notices in error log.
		remove_filter( 'wp_robots', 'wp_robots_noindex_embeds' );
		remove_filter( 'wp_robots', 'wp_robots_noindex_search' );

		wp_die(
			esc_html__( 'This is the Lite Connect endpoint page.', 'wpforms-lite' ),
			'Lite Connect endpoint',
			400
		);
	}

	/**
	 * Log message.
	 *
	 * @since 1.7.4
	 *
	 * @param string $title    Log message title.
	 * @param array  $response Response.
	 */
	private function log( $title = '', $response = [] ) {

		if ( ! $title ) {
			return;
		}

		wpforms_log(
			$title,
			[
				'response' => $response,
				'request'  => [
					'domain'      => isset( $response['domain'] ) ? $response['domain'] : '',
					'admin_email' => Integration::get_enabled_email(),
				],
			],
			[ 'type' => [ 'error' ] ]
		);
	}
}
