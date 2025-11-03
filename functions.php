<?php
    function generatepress_theme_scripts() {

     wp_enqueue_style( 'output', get_stylesheet_directory_uri() . '/dist/output.css', array() );

    }   

    add_action( 'wp_enqueue_scripts', 'generatepress_theme_scripts' );


    add_filter( 'nav_menu_css_class', function($classes, $item, $args) {
        if (property_exists($args, 'list_item_class') && !empty($args->list_item_class)) {
            $classes[] = $args->list_item_class;
        }
        return $classes;
    }, 10, 3);

 
    add_filter( 'nav_menu_link_attributes', function($atts, $item, $args) {
        if (property_exists($args, 'link_class') && !empty($args->link_class)) {
            $atts['class'] = $args->link_class;
        }
        return $atts;
    }, 10, 3);

    

    function child_theme_scripts() {
        wp_enqueue_script(
            'modal-comments',
            get_stylesheet_directory_uri() . '/assets/js/modal.js',
            array(), 
            '1.0',
            true 
        );
    }
    add_action('wp_enqueue_scripts', 'child_theme_scripts');



     function enqueue_ajax_comment_script() {
       
        wp_enqueue_script(
            'ajax-comments',
            get_stylesheet_directory_uri() . '/assets/js/ajax-comments.js',
            array(), 
            '1.0',
            true
        );

        
        wp_localize_script('ajax-comments', 'ajax_comments_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('ajax_comment_nonce'),
        ));
    }
    add_action('wp_enqueue_scripts', 'enqueue_ajax_comment_script');


    function ajax_comment_submit() {
        check_ajax_referer('ajax_comment_nonce', 'nonce');

        $comment_data = array(
            'comment_post_ID' => intval($_POST['comment_post_ID']),
            'comment_parent'  => intval($_POST['comment_parent']),
            'comment' => wp_filter_post_kses($_POST['comment']),
            'user_id' => get_current_user_id(),
        );

        if(! is_user_logged_in()) {
            $comment_data['comment_author'] = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : 'Guest';
            $comment_data['comment_author_email'] = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        }

        $comment_id = wp_handle_comment_submission($comment_data);
        $comment_link = get_comment_link($comment);

        if (is_wp_error($comment_id)) {
            wp_send_json(array(
                'success' => false,
                'message' => $comment_id->get_error_message(),
                'comment_link' => $comment_link,
            ));
        }

        
        $comment = get_comment($comment_id);
        ob_start();
        ?>
        <li <?php comment_class('', $comment); ?> id="comment-<?php echo $comment->comment_ID; ?>">
            <article>
                <footer class="comment-meta">
                    <strong><?php echo get_comment_author($comment); ?></strong> says:
                </footer>
                <div class="comment-content">
                    <?php echo wpautop(esc_html($comment->comment_content)); ?>
                </div>
            </article>
        </li>
        <?php
        $comment_html = ob_get_clean();

        $count = get_comments_number($comment_data['comment_post_ID']);

        wp_send_json(array(
            'success' => true,
            'comment_html' => $comment_html,
            'comment_count' => $count,
            'comment_id' => $comment->comment_ID,
        ));
    }
    add_action('wp_ajax_ajax_comment_submit', 'ajax_comment_submit');         
    add_action('wp_ajax_nopriv_ajax_comment_submit', 'ajax_comment_submit');