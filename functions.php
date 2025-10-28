<?php

    function generatepress_child_enqueue_styles() {
        // Stilizimi i parentit me u load i pari
        wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

        wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style')); // eshte e n varte prej stilizimit t parentit n menyre qe me u load mas tij

        wp_enqueue_style('tailwindcss', get_stylesheet_directory_uri() . '/src/output.css', array('parent-style'), '4.0.0' );

    }

    add_action('wp_enqueue_scripts', 'generatepress_child_enqueue_styles');


