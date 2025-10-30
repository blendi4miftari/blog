<?php
/**
 * The template for displaying all pages
 * Custom page template for GeneratePress child theme with Tailwind CSS
 *
 * @package GeneratePress
 */

if (!defined('ABSPATH')) {
	exit;
}

get_header(); ?>

	<main id="main" class="site-main container mx-auto px-4 py-6">
		<?php
		while (have_posts()) : the_post();

			get_template_part('template-parts/content', 'page');

		endwhile;
		?>

	</main>

<?php
get_footer();