<?php
require_once "acf-content.php";

// Disable normal editors in WP to use the ACF flexible content instead
add_action('init', 'blank_acf_fc_init_remove_support', 100);
function blank_acf_fc_init_remove_support()
{
    remove_post_type_support('page', 'editor');
}

add_action('after_setup_theme', 'blank_acf_fc_setup');
function blank_acf_fc_setup()
{
    load_theme_textdomain('blank_acf_fc', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form', 'navigation-widgets'));
    add_theme_support('woocommerce');
    global $content_width;
    if (!isset($content_width)) {
        $content_width = 1920;
    }
    register_nav_menus(array('main-menu' => esc_html__('Main Menu', 'blank_acf_fc')));
}
add_action('wp_enqueue_scripts', 'blank_acf_fc_enqueue');
function blank_acf_fc_enqueue()
{
    wp_enqueue_style('blank_acf_fc-style', get_stylesheet_uri());
    wp_enqueue_style('blank_acf_fc-style-site', get_stylesheet_directory_uri() . '/site.css');
    wp_enqueue_style('blank_acf_fc-script-site', get_stylesheet_directory_uri() . '/site.js');
}
add_filter('the_title', 'blank_acf_fc_title');
function blank_acf_fc_title($title)
{
    if ($title == '') {
        return esc_html('...');
    } else {
        return wp_kses_post($title);
    }
}
function blank_acf_fc_schema_type()
{
    $schema = 'https://schema.org/';
    if (is_single()) {
        $type = "Article";
    } elseif (is_author()) {
        $type = 'ProfilePage';
    } elseif (is_search()) {
        $type = 'SearchResultsPage';
    } else {
        $type = 'WebPage';
    }
    echo 'itemscope itemtype="' . esc_url($schema) . esc_attr($type) . '"';
}
add_filter('nav_menu_link_attributes', 'blank_acf_fc_schema_url', 10);
function blank_acf_fc_schema_url($atts)
{
    $atts['itemprop'] = 'url';
    return $atts;
}
if (!function_exists('blank_acf_fc_wp_body_open')) {
    function blank_acf_fc_wp_body_open()
    {
        do_action('wp_body_open');
    }
}
add_action('wp_body_open', 'blank_acf_fc_skip_link', 5);
function blank_acf_fc_skip_link()
{
    echo '<a href="#content" class="skip-link screen-reader-text">' . esc_html__('Skip to the content', 'blank_acf_fc') . '</a>';
}
add_filter('the_content_more_link', 'blank_acf_fc_read_more_link');
function blank_acf_fc_read_more_link()
{
    if (!is_admin()) {
        return ' <a href="' . esc_url(get_permalink()) . '" class="more-link">' . sprintf(__('...%s', 'blank_acf_fc'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}
add_filter('excerpt_more', 'blank_acf_fc_excerpt_read_more_link');
function blank_acf_fc_excerpt_read_more_link($more)
{
    if (!is_admin()) {
        global $post;
        return ' <a href="' . esc_url(get_permalink($post->ID)) . '" class="more-link">' . sprintf(__('...%s', 'blank_acf_fc'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}
add_filter('big_image_size_threshold', '__return_false');
add_filter('intermediate_image_sizes_advanced', 'blank_acf_fc_image_insert_override');
function blank_acf_fc_image_insert_override($sizes)
{
    unset($sizes['medium_large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
}
add_action('widgets_init', 'blank_acf_fc_widgets_init');
function blank_acf_fc_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar Widget Area', 'blank_acf_fc'),
        'id' => 'primary-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('wp_head', 'blank_acf_fc_pingback_header');
function blank_acf_fc_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s" />' . "\n", esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('comment_form_before', 'blank_acf_fc_enqueue_comment_reply_script');
function blank_acf_fc_enqueue_comment_reply_script()
{
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
function blank_acf_fc_custom_pings($comment)
{
?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url(comment_author_link()); ?></li>
<?php
}
add_filter('get_comments_number', 'blank_acf_fc_comment_count', 0);
function blank_acf_fc_comment_count($count)
{
    if (!is_admin()) {
        global $id;
        $get_comments = get_comments('status=approve&post_id=' . $id);
        $comments_by_type = separate_comments($get_comments);
        return count($comments_by_type['comment']);
    } else {
        return $count;
    }
}

/**
 * Disable the emoji's
 */
function blank_acf_fc_disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remove from TinyMCE
    add_filter('tiny_mce_plugins', 'blank_acf_fc_disable_emojis_tinymce');
}
add_action('init', 'blank_acf_fc_disable_emojis');

/**
 * Filter out the tinymce emoji plugin.
 */
function blank_acf_fc_disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}
