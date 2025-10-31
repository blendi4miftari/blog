<?php
    function generatepress_theme_scripts() {

     wp_enqueue_style( 'output', get_stylesheet_directory_uri() . '/dist/output.css', array() );

    }   

    add_action( 'wp_enqueue_scripts', 'generatepress_theme_scripts' );

    // Add custom class to <li>
    add_filter( 'nav_menu_css_class', function($classes, $item, $args) {
        if (property_exists($args, 'list_item_class') && !empty($args->list_item_class)) {
            $classes[] = $args->list_item_class;
        }
        return $classes;
    }, 10, 3);

    // Add custom class to <a>
    add_filter( 'nav_menu_link_attributes', function($atts, $item, $args) {
        if (property_exists($args, 'link_class') && !empty($args->link_class)) {
            $atts['class'] = $args->link_class;
        }
        return $atts;
    }, 10, 3);

    add_filter( 'generate_parse_attr', function( $attributes, $context ) {
        if ( 'inside-article' === $context ) {
            $attributes['class'] .= ' bg-gray-700 p-8 shadow-lg'; // Replace with your Tailwind classes
        }
        return $attributes;
    }, 10, 2 );
   

    function custom_excerpt_more($more) {
        return ' <a class="inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 mt-3" href="' . get_permalink() . '">Read More →</a>';
    }
    add_filter('excerpt_more', 'custom_excerpt_more');

    function child_theme_scripts() {
        wp_enqueue_script(
            'modal-comments',
            get_stylesheet_directory_uri() . '/modal.js',
            array(), 
            '1.0',
            true 
        );
    }
    add_action('wp_enqueue_scripts', 'child_theme_scripts');
