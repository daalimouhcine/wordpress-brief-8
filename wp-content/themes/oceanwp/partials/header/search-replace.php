<?php
/**
 * Site header search header replace
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Search attributes.
$item_search_attrs = apply_filters( 'oceanwp_attrs_search_bar', '' );

// Post type.
$post_type = get_theme_mod( 'ocean_menu_search_source', 'any' );

?>

<div id="searchform-header-replace" class="header-searchform-wrap clr" <?php echo $item_search_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-searchform">
		<span class="screen-reader-text"><?php oceanwp_theme_strings( 'owp-string-search-form-label' ) ?></span>
		<input aria-label="<?php oceanwp_theme_strings( 'owp-string-mobile-submit-search' ); ?>" type="search" name="s" autocomplete="off" value="" placeholder="<?php oceanwp_theme_strings( 'owp-string-header-replace-search-text', 'oceanwp' ); ?>" />
		<?php if ( 'any' !== $post_type ) { ?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
		<?php } ?>
		<?php do_action( 'wpml_add_language_form_field' ); ?>
	</form>
	<span id="searchform-header-replace-close" aria-label="<?php oceanwp_theme_strings( 'owp-string-close-search-form' ); ?>"><?php oceanwp_icon( 'close' ); ?></span>
</div><!-- #searchform-header-replace -->
