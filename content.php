


<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white shadow-lg rounded-2xl overflow-hidden hover:shadow-2xl transition duration-300 border border-gray-100'); ?> itemtype="https://schema.org/CreativeWork" itemscope>

    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="block overflow-hidden group">
            <?php the_post_thumbnail('large', [
                'class' => 'w-full h-64 object-cover transform group-hover:scale-105 transition duration-300'
            ]); ?>
        </a>
    <?php endif; ?>

    <div class="inside-article p-6 md:p-8">
        <header class="entry-header mb-4">
            <?php if ( is_singular() ) : ?>
                <h1 class="entry-title text-3xl font-bold text-gray-800 mb-2">
                    <?php the_title(); ?>
                </h1>
            <?php else : ?>
                <h2 class="entry-title text-2xl font-semibold mb-2">
                    <a href="<?php the_permalink(); ?>" class="text-gray-800 hover:text-blue-600 transition-colors duration-200">
                        <?php the_title(); ?>
                    </a>
                </h2>
            <?php endif; ?>

            <div class="entry-meta flex items-center gap-3 text-sm text-gray-500">
                <span class="posted-on">
                    <?php echo get_the_date(); ?>
                </span>
                <span class="text-gray-400">•</span>
                <span class="byline">
                    By <?php the_author_posts_link(); ?>
                </span>
            </div>
        </header>

        <div class="entry-summary text-gray-600 leading-relaxed">
            <?php the_excerpt(); ?>
        </div>

        <footer class="entry-footer mt-6 flex justify-between items-center">

            <div class="flex flex-wrap gap-2">
                <?php
                $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    foreach ( $categories as $category ) {
                        echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="border no-underline text-black text-sm font-semibold px-4 py-2 rounded-lg hover:text-white hover:bg-gray-400 hover:border-white transition-colors duration-200">' . esc_html( $category->name ) . '</a>';
                    }
                }
                ?>
            </div>

        
            <div class="grid-2">
                <a href="<?php comments_link(); ?>"
                class="inline-block no-underline bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 hover:text-white transition-colors duration-200"
                data-post-id="<?php the_ID(); ?>"
                >
                <span class="comment-count pointer-events-none">
                    <?php
                        $comments_count = get_comments_number();
                        echo $comments_count == 1
                            ? '1 Comment'
                            : $comments_count . ' Comments';
                    ?>
                </span>
                </a>
                <button
                    class="open-comment-modal inline-block no-underline bg-gray-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200"
                    data-post-id="<?php the_ID(); ?>"
                    type="button"
                >
                    ✍🏻️
                </button>
            </div>
             

        </footer>
    </div>

        <div id="commentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 relative">
                
                <button id="closeModal" class="absolute top-3 right-3 w-8 h-8 m-0 p-0 bg-gray-700 text-white hover:text-gray-200 text-lg">x</button>

                <div id="commentFormContainer" class="text-gray-700">
                    <?php
                    comment_form();
                    ?>
                </div>
            </div>
         </div>

</article>


