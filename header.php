<?php
/**
 * The template for displaying the header.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php generate_do_microdata( 'body' ); ?>>
<?php
/**
 * wp_body_open hook.
 *
 * @since 2.3
 */
do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.


/**
 * generate_before_header hook.
 *
 * @since 0.1
 *
 * @hooked generate_do_skip_to_content_link - 2
 * @hooked generate_top_bar - 5
 * @hooked generate_add_navigation_before_header - 5
 */
do_action( 'generate_before_header' );

/**
 * Custom Header Section
 */
?>
<header class="bg-gray-300 shadow-lg sticky top-0 z-10 py-6">
	<?php
	/**
	 * Header Container
	 * Flex layout to align logo and navigation horizontally.
	 * Items centered vertically with 1rem padding on x-axis.
	 */
	?>
	<div class="container mx-auto flex items-center px-4 mt-4">
		<?php
		/**
		 * Logo Section
		 * "Blog" text with text-xl and font-bold.
		 * Linked to home URL with no hover effects.
		 */
		?>
		<div class="text-xl font-bold">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-black no-underline text-2xl font-bold">
				<span class="text-xl font-bold">Blog</span>
			</a>
		</div>
		
		<?php
		/**
		 * Navigation Menu Section
		 * Flex layout with 1rem spacing between items.
		 * No decorations (no borders or underlines).
		 */
		?>
		<nav class="flex items-center pt-2 pl-10">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'flex space-x-4 list-none m-0 p-0', 
				'list_item_class' => 'text-md text-black font-semibold', 
				'link_class'      => 'text-lg text-black hover:text-white no-underline', 
				'fallback_cb'     => false,
			) );
			?>
		</nav>
	</div>
</header>
<?php

/**
 * generate_after_header hook.
 *
 * @since 0.1
 *
 * @hooked generate_featured_page_header - 10
 */
do_action( 'generate_after_header' );
?>

<div <?php generate_do_attr( 'page' ); ?>>
    <?php
    /**
     * generate_inside_site_container hook.
     *
     * @since 2.4
     */
    do_action( 'generate_inside_site_container' );
    ?>
    <div <?php generate_do_attr( 'site-content' ); ?>>
<?php
/**
 * generate_inside_container hook.
 *
 * @since 0.1
 */
do_action( 'generate_inside_container' );

