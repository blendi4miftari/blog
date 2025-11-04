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



    add_action( 'wp_enqueue_scripts', function() {
        // Only load on singular pages OR archive pages where you show the modal
        if ( ! comments_open() ) {
            return;
        }
    
        wp_enqueue_script(
            'ajax-comments',
            get_stylesheet_directory_uri() . '/assets/js/ajax-comments.js',
            array(),          // no jQuery needed – we use native fetch
            '1.1',
            true
        );
    
        wp_localize_script(
            'ajax-comments',
            'ajax_comments_params',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'ajax_comment_nonce' ),
            )
        );
    } );
    
    /* -------------------------------------------------
       ONE AJAX handler – works for logged-in & guests
       ------------------------------------------------- */
    add_action( 'wp_ajax_ajax_comment_submit', 'ajax_comment_submit_handler' );
    add_action( 'wp_ajax_nopriv_ajax_comment_submit', 'ajax_comment_submit_handler' );
    
   function ajax_comment_submit_handler() {
    // 1. Verify nonce (don't die)
    if ( ! check_ajax_referer( 'ajax_comment_nonce', 'nonce', false ) ) {
        wp_send_json_error( [ 'message' => 'Security check failed.' ] );
    }

    // 2. Get data
    $post_id = intval( $_POST['comment_post_ID'] ?? 0 );
    $parent  = intval( $_POST['comment_parent'] ?? 0 );
    $content = wp_kses_post( $_POST['comment'] ?? '' );
    

    if ( ! $post_id || empty( $content ) ) {
        wp_send_json_error( [ 'message' => 'Missing post ID or comment content.' ] );
    }

    // 3. Build comment data
    $commentdata = [
        'comment_post_ID'      => $post_id,
        'comment_parent'       => $parent,
        'comment_content'      => $content,
        'comment_author_IP'    => $_SERVER['REMOTE_ADDR'] ?? '',
        'comment_agent'        => $_SERVER['HTTP_USER_AGENT'] ?? '',
    ];

    if ( is_user_logged_in() ) {
        $commentdata['user_ID'] = get_current_user_id();
    } else {
        $author = sanitize_text_field( $_POST['author'] ?? '' );
        $email  = sanitize_email( $_POST['email'] ?? '' );
        if ( empty( $author ) || empty( $email ) ) {
            wp_send_json_error( [ 'message' => 'Name and email are required for guests.' ] );
        }
        $commentdata['comment_author']       = $author;
        $commentdata['comment_author_email'] = $email;
        $commentdata['comment_author_url']   = esc_url_raw( $_POST['url'] ?? '' );
    }

    // 4. Insert comment
    $comment_id = wp_new_comment( $commentdata );

    if ( is_wp_error( $comment_id ) ) {
        wp_send_json_error( [ 'message' => $comment_id->get_error_message() ] );
    }

    $comment = get_comment( $comment_id );
    $is_approved = (int) $comment->comment_approved === 1;

    // 5. Render HTML – exact GeneratePress default (avatar 42px)
    ob_start();
    $GLOBALS['comment'] = $comment; // Required for functions like comment_text()
    ?>
    <li <?php comment_class( '', $comment ); ?> id="comment-<?php comment_ID(); ?>">
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <footer class="comment-meta">
                <div class="comment-author vcard">
                    <?php echo get_avatar( $comment, 42 ); // GeneratePress avatar size ?>
                    <?php printf( '<b class="fn">%s</b> <span class="says"></span>', get_comment_author_link( $comment ) ); ?>
                </div>
                <div class="comment-metadata">
                    <a class="text-sm" href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
                        <?php printf( '%1$s at %2$s |', get_comment_date( '', $comment ), get_comment_time() ); ?>
                    </a>
                    <?php edit_comment_link( __( 'Edit' ), ' <span class="edit-link text-sm">', '</span>' ); ?>
                </div>
            </footer>

            <?php if ( '0' == $comment->comment_approved ) : ?>
                <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
            <?php endif; ?>

            <div class="comment-content">
                <?php comment_text( $comment ); ?>

                <div class="reply text-sm">
                <?php
                comment_reply_link( array_merge( [
                    'depth'     => 1,
                    'max_depth' => get_option( 'thread_comments_depth' ),
                    'reply_text' => __( 'Reply' ),
                ] ), $comment->comment_ID, $comment->comment_post_ID );
                ?>
            </div>
            </div>

            
        </article>
    </li>
    <?php
    $comment_html = ob_get_clean();

    // 6. Success – always JSON
    wp_send_json_success( [
        'comment_html'   => $comment_html,
        'comment_id'     => $comment->comment_ID,
        'comment_count'  => get_comments_number( $post_id ),
        'is_approved'    => $is_approved,
    ] );
}

// Hook it (add if missing)
add_action( 'wp_ajax_ajax_comment_submit', 'ajax_comment_submit_handler' );
add_action( 'wp_ajax_nopriv_ajax_comment_submit', 'ajax_comment_submit_handler' );