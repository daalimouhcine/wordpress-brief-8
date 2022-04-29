<?php

namespace WPForms\Forms;

/**
 * Form preview.
 *
 * @since 1.5.1
 */
class Preview {

	/**
	 * Form data.
	 *
	 * @since 1.5.1
	 *
	 * @var array
	 */
	public $form_data;

	/**
	 * Constructor.
	 *
	 * @since 1.5.1
	 */
	public function __construct() {

		if ( ! $this->is_preview_page() ) {
			return;
		}

		$this->hooks();
	}

	/**
	 * Check if current page request meets requirements for form preview page.
	 *
	 * @since 1.5.1
	 *
	 * @return bool
	 */
	public function is_preview_page() {

		// Only proceed for the form preview page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['wpforms_form_preview'] ) ) {
			return false;
		}

		// Check for logged-in user with correct capabilities.
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = absint( $_GET['wpforms_form_preview'] );

		if ( ! wpforms_current_user_can( 'view_form_single', $form_id ) ) {
			return false;
		}

		// Fetch form details.
		$this->form_data = wpforms()->get( 'form' )->get( $form_id, [ 'content_only' => true ] );

		// Check valid form was found.
		if ( empty( $this->form_data ) || empty( $this->form_data['id'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Hooks.
	 *
	 * @since 1.5.1
	 */
	public function hooks() {

		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
		add_filter( 'the_title', [ $this, 'the_title' ], 100, 1 );
		add_filter( 'the_content', [ $this, 'the_content' ], 999 );
		add_filter( 'get_the_excerpt', [ $this, 'the_content' ], 999 );
		add_filter( 'home_template_hierarchy', [ $this, 'force_page_template_hierarchy' ] );
		add_filter( 'frontpage_template_hierarchy', [ $this, 'force_page_template_hierarchy' ] );
		add_filter( 'post_thumbnail_html', '__return_empty_string' );
	}

	/**
	 * Modify query, limit to one post.
	 *
	 * @since 1.5.1
	 * @since 1.7.0 Added `page_id`, `post_type` and `post__in` query variables.
	 *
	 * @param \WP_Query $query The WP_Query instance.
	 */
	public function pre_get_posts( $query ) {

		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$query->set( 'page_id', '' );
		$query->set( 'post_type', 'wpforms' );
		$query->set( 'post__in', empty( $this->form_data['id'] ) ? [] : [ (int) $this->form_data['id'] ] );
		$query->set( 'posts_per_page', 1 );
	}

	/**
	 * Customize form preview page title.
	 *
	 * @since 1.5.1
	 *
	 * @param string $title Page title.
	 *
	 * @return string
	 */
	public function the_title( $title ) {

		if ( in_the_loop() ) {
			$title = sprintf( /* translators: %s - form title. */
				esc_html__( '%s Preview', 'wpforms-lite' ),
				! empty( $this->form_data['settings']['form_title'] ) ? sanitize_text_field( $this->form_data['settings']['form_title'] ) : esc_html__( 'Form', 'wpforms-lite' )
			);
		}

		return $title;
	}

	/**
	 * Customize form preview page content.
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	public function the_content() {

		if ( ! isset( $this->form_data['id'] ) ) {
			return '';
		}

		if ( ! wpforms_current_user_can( 'view_form_single', $this->form_data['id'] ) ) {
			return '';
		}

		$links = [];

		if ( wpforms_current_user_can( 'edit_form_single', $this->form_data['id'] ) ) {
			$links[] = [
				'url'  => esc_url(
					add_query_arg(
						[
							'page'    => 'wpforms-builder',
							'view'    => 'fields',
							'form_id' => absint( $this->form_data['id'] ),
						],
				 		admin_url( 'admin.php' )
					)
				),
				'text' => esc_html__( 'Edit Form', 'wpforms-lite' ),
			];
		}

		if ( wpforms()->is_pro() && wpforms_current_user_can( 'view_entries_form_single', $this->form_data['id'] ) ) {
			$links[] = [
				'url'  => esc_url(
					add_query_arg(
						[
							'page'    => 'wpforms-entries',
							'view'    => 'list',
							'form_id' => absint( $this->form_data['id'] ),
						],
						admin_url( 'admin.php' )
					)
				),
				'text' => esc_html__( 'View Entries', 'wpforms-lite' ),
			];
		}

		if ( ! empty( $_GET['new_window'] ) ) { // phpcs:ignore
			$links[] = [
				'url'  => 'javascript:window.close();',
				'text' => esc_html__( 'Close this window', 'wpforms-lite' ),
			];
		}

		$content  = '<p>';
		$content .= esc_html__( 'This is a preview of the latest saved revision of your form. If this preview does not match your form, save your changes and then refresh this page. This form preview is not publicly accessible.', 'wpforms-lite' );

		if ( ! empty( $links ) ) {
			$content .= '<br>';
			$content .= '<span class="wpforms-preview-notice-links">';

			foreach ( $links as $key => $link ) {
				$content .= '<a href="' . $link['url'] . '">' . $link['text'] . '</a>';
				$l        = array_keys( $links );

				if ( end( $l ) !== $key ) {
					$content .= ' <span style="display:inline-block;margin:0 6px;opacity: 0.5">|</span> ';
				}
			}

			$content .= '</span>';
		}
		$content .= '</p>';

		$content .= '<p>';
		$content .= sprintf(
			wp_kses(
				/* translators: %s - WPForms doc link. */
				__( 'For form testing tips, check out our <a href="%s" target="_blank" rel="noopener noreferrer">complete guide!</a>', 'wpforms-lite' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
						'rel'    => [],
					],
				]
			),
			'https://wpforms.com/docs/how-to-properly-test-your-wordpress-forms-before-launching-checklist/'
		);
		$content .= '</p>';

		$content .= do_shortcode( '[wpforms id="' . absint( $this->form_data['id'] ) . '"]' );

		return $content;
	}

	/**
	 * Force page template types.
	 *
	 * @since 1.7.2
	 *
	 * @param array $templates A list of template candidates, in descending order of priority.
	 *
	 * @return array
	 */
	public function force_page_template_hierarchy( $templates ) {

		return [ 'page.php', 'single.php', 'index.php' ];
	}

    /**
     * Force page template types.
     *
     * @since 1.5.1
     * @deprecated 1.7.2
     *
     * @return string
     */
    public function template_include() {

        _deprecated_function( __METHOD__, '1.7.2 of WPForms plugin' );

        return locate_template( [ 'page.php', 'single.php', 'index.php' ] );
    }
}
