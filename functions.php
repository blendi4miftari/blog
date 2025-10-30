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
   