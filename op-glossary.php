<?php
/*
Plugin Name: OP Glossary Plugin
Description: Creates a glossary with custom post type. This is in its initial phase of development.
Author: Outpace
Author URI: https://pratik-shrestha.com.np
Version: 1.00.11
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Plugin Constants
define( 'OP_GLOSSARY_VERSION', '1.00.11' );
define( 'OP_GLOSSARY_PATH', plugin_dir_path( __FILE__ ) );
define( 'OP_GLOSSARY_URL', plugin_dir_url( __FILE__ ) );

/**
 * Register the glossary post type and flush rewrite rules on activation.
 */
function op_glossary_activate() {
    op_glossary_register_post_type();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'op_glossary_activate' );

/**
 * Flush rewrite rules on deactivation.
 */
function op_glossary_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'op_glossary_deactivate' );

/**
 * The function registers a custom post type called "Glossary Term" with specific labels, supports for
 * title and editor, and a custom slug for the rewrite.
 */
function op_glossary_register_post_type() {
    $labels = array(
        'name'               => esc_html__( 'Glossary Terms', 'op-glossary' ),
        'singular_name'      => esc_html__( 'Glossary Term', 'op-glossary' ),
        'add_new'            => esc_html__( 'Add New', 'op-glossary' ),
        'add_new_item'       => esc_html__( 'Add New Glossary Term', 'op-glossary' ),
        'edit_item'          => esc_html__( 'Edit Glossary Term', 'op-glossary' ),
        'new_item'           => esc_html__( 'New Glossary Term', 'op-glossary' ),
        'view_item'          => esc_html__( 'View Glossary Term', 'op-glossary' ),
        'search_items'       => esc_html__( 'Search Glossary Terms', 'op-glossary' ),
        'not_found'          => esc_html__( 'No glossary terms found', 'op-glossary' ),
        'not_found_in_trash' => esc_html__( 'No glossary terms found in Trash', 'op-glossary' ),
    );

    $args = array(
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => false,
        'show_in_rest' => true,
        'rewrite'      => array(
            'slug'       => 'glossary',
            'with_front' => false,
        ),
        'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
    );

    register_post_type( 'op_glossary_term', $args );

    // Custom Taxonomy Labels
    $taxonomy_labels = array(
        'name'              => esc_html__( 'Categories', 'op-glossary' ),
        'singular_name'     => esc_html__( 'Category', 'op-glossary' ),
        'search_items'      => esc_html__( 'Search Categories', 'op-glossary' ),
        'all_items'         => esc_html__( 'All Categories', 'op-glossary' ),
        'parent_item'       => esc_html__( 'Parent Category', 'op-glossary' ),
        'parent_item_colon' => esc_html__( 'Parent Category:', 'op-glossary' ),
        'edit_item'         => esc_html__( 'Edit Category', 'op-glossary' ),
        'update_item'       => esc_html__( 'Update Category', 'op-glossary' ),
        'add_new_item'      => esc_html__( 'Add New Category', 'op-glossary' ),
        'new_item_name'     => esc_html__( 'New Category Name', 'op-glossary' ),
        'menu_name'         => esc_html__( 'Categories', 'op-glossary' ),
    );

    // Custom Taxonomy Arguments
    $taxonomy_args = array(
        'hierarchical' => true,
        'labels'       => $taxonomy_labels,
        'show_ui'      => true,
        'show_in_rest' => true,
        'query_var'    => true,
        'rewrite'      => array( 'slug' => 'glossary-category' ),
    );

    // Register Custom Taxonomy
    register_taxonomy( 'op_glossary_category', array( 'op_glossary_term' ), $taxonomy_args );
}
add_action( 'init', 'op_glossary_register_post_type' );

/**
 * The function `op_glossary_enqueue_assets` registers and enqueues CSS and JS files for the plugin.
 */
function op_glossary_enqueue_assets() {
    $styles_file = 'assets/css/styles.css';
    $styles_path = OP_GLOSSARY_PATH . $styles_file;
    $styles_url  = OP_GLOSSARY_URL  . $styles_file;
    $styles_ver  = file_exists( $styles_path ) ? filemtime( $styles_path ) : OP_GLOSSARY_VERSION;

    wp_register_style( 'op-glossary-styles', $styles_url, array(), $styles_ver );

    $js_file = 'assets/js/glossary.js';
    $js_path = OP_GLOSSARY_PATH . $js_file;
    $js_url  = OP_GLOSSARY_URL  . $js_file;
    $js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : OP_GLOSSARY_VERSION;

    wp_register_script( 'op-glossary-js', $js_url, array( 'jquery' ), $js_ver, true );

    // Localize data for AJAX
    wp_localize_script( 'op-glossary-js', 'op_glossary_vars', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'op_glossary_search_nonce' ),
    ) );

    if ( is_singular( 'op_glossary_term' ) ) {
        wp_enqueue_style( 'op-glossary-styles' );
    }
}
add_action( 'wp_enqueue_scripts', 'op_glossary_enqueue_assets' );

/**
 * Load a theme override or bundled template for single glossary terms.
 *
 * @param string $template The resolved template path.
 * @return string
 */
function op_glossary_single_template( $template ) {
    if ( ! is_singular( 'op_glossary_term' ) ) {
        return $template;
    }

    $theme_template = locate_template( 'op-glossary-single.php' );

    if ( $theme_template ) {
        return $theme_template;
    }

    $plugin_template = OP_GLOSSARY_PATH . 'templates/single-glossary-term.php';

    if ( file_exists( $plugin_template ) ) {
        return $plugin_template;
    }

    return $template;
}
add_filter( 'single_template', 'op_glossary_single_template' );

/**
 * The op_glossary_shortcode function is a PHP shortcode that generates a glossary with pagination
 * based on the first letter of the glossary terms.
 * 
 * @param array $atts Shortcode attributes.
 * @return string The HTML content.
 */
function op_glossary_shortcode( $atts ) {
    // Extract shortcode attributes
    $atts = shortcode_atts( array(
        'categories' => '',
        'limit'      => -1,
    ), $atts );

    // Convert comma-separated categories to an array
    $categories = ! empty( $atts['categories'] ) ? explode( ',', $atts['categories'] ) : array();
    $categories = array_map( 'trim', $categories );

    // Retrieve posts based on shortcode attributes
    $args = array(
        'post_type'      => 'op_glossary_term',
        'posts_per_page' => intval( $atts['limit'] ),
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true, // Performance optimization
    );

    if ( ! empty( $categories ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'op_glossary_category',
                'field'    => is_numeric( $categories[0] ) ? 'term_id' : 'slug',
                'terms'    => $categories,
            ),
        );
    }

    $selected_letter = isset( $_GET['op-glossary-pagination'] ) ? sanitize_text_field( wp_unslash( $_GET['op-glossary-pagination'] ) ) : 'A';
    
    if ( isset( $_GET['op-glossary-pagination'] ) ) {
        $args['starts_with'] = $selected_letter;
    }
    
    if ( isset( $_GET['op_glossary_search'] ) ) {
        $args['s'] = sanitize_text_field( wp_unslash( $_GET['op_glossary_search'] ) );
    }
    
    // Get first letters using optimized direct SQL query
    $first_letters_array = op_glossary_first_letters_of_titles( $args );

    // Scope the start-with filter only to the glossary query
    add_filter( 'posts_where', 'op_glossary_posts_where', 10, 2 );
    $query = new WP_Query( $args );
    remove_filter( 'posts_where', 'op_glossary_posts_where', 10 );

    wp_enqueue_style( 'op-glossary-styles' );
    wp_enqueue_script( 'op-glossary-js' );
    
    ob_start();
    // Template path: allow theme overrides
    $template_path = locate_template( 'op-glossary-template.php' );
    if ( ! $template_path ) {
        $template_path = OP_GLOSSARY_PATH . 'templates/shortcode-glossary.php';
    }

    if ( file_exists( $template_path ) ) {
        include $template_path;
    }

    return ob_get_clean();
}
add_shortcode( 'op-glossary', 'op_glossary_shortcode' );

/**
 * Render glossary items from query posts.
 *
 * @param WP_Query $items Query object.
 * @return string
 */
function op_glossary_render_term_list( $items ) {
    ob_start();

    if ( ! ( $items instanceof WP_Query ) || ! $items->have_posts() ) {
        echo '<p class="op-glossary-no-results">' . esc_html__( 'No glossary terms found.', 'op-glossary' ) . '</p>';

        return ob_get_clean();
    }

    echo '<dl class="op-glossary-list">';

    while ( $items->have_posts() ) {
        $items->the_post();
        echo '<div class="op-glossary-list__item">';
        echo '<dt><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></dt>';
        echo '<dd>' . wp_kses_post( get_the_content() ) . '</dd>';
        echo '</div>';
    }

    echo '</dl>';

    wp_reset_postdata();

    return ob_get_clean();
}

/**
 * Handle AJAX search requests.
 */
function op_glossary_search_ajax() {
    check_ajax_referer( 'op_glossary_search_nonce', 'nonce' );

    $search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
    $letter = isset( $_POST['letter'] ) ? sanitize_text_field( wp_unslash( $_POST['letter'] ) ) : '';

    $args = array(
        'post_type'      => 'op_glossary_term',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        's'              => $search,
        'no_found_rows'  => true,
    );

    if ( $letter ) {
        $args['starts_with'] = $letter;
    }

    add_filter( 'posts_where', 'op_glossary_posts_where', 10, 2 );
    $query = new WP_Query( $args );
    remove_filter( 'posts_where', 'op_glossary_posts_where', 10 );

    $html = op_glossary_render_term_list( $query );

    wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_op_glossary_search_ajax', 'op_glossary_search_ajax' );
add_action( 'wp_ajax_nopriv_op_glossary_search_ajax', 'op_glossary_search_ajax' );

/**
 * The function retrieves the first letters of the titles of all glossary terms in WordPress and
 * returns them as an array using optimized SQL and transient caching.
 * 
 * @param array $args The query arguments to filter the results.
 * @return array Optimized array of first letters.
 */
function op_glossary_first_letters_of_titles( $args ) {
    global $wpdb;

    // Build cache key based on tax_query and limit
    $cache_key = 'op_glos_ltrs_' . md5( wp_json_encode( $args['tax_query'] ?? '' ) );
    $results   = get_transient( $cache_key );

    if ( false !== $results ) {
        return $results;
    }

    $where = "post_type = 'op_glossary_term' AND post_status = 'publish'";

    // Handle taxonomy filter in SQL
    if ( ! empty( $args['tax_query'][0] ) ) {
        $tax    = $args['tax_query'][0];
        $terms  = (array) $tax['terms'];
        $field  = $tax['field'] === 'term_id' ? 'term_id' : 'slug';
        
        $placeholders = implode( ',', array_fill( 0, count( $terms ), '%s' ) );

        $where .= $wpdb->prepare(
            " AND {$wpdb->posts}.ID IN (
                SELECT object_id FROM {$wpdb->term_relationships}
                JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
                JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
                WHERE {$wpdb->term_taxonomy}.taxonomy = 'op_glossary_category'
                AND {$wpdb->terms}.{$field} IN ($placeholders)
            )",
            ...$terms
        );
    }

    $results = $wpdb->get_col(
        "SELECT DISTINCT UPPER(LEFT(post_title, 1)) as first_letter
         FROM {$wpdb->posts}
         WHERE {$where}
         ORDER BY first_letter ASC"
    );

    // Cache the result for 24 hours
    set_transient( $cache_key, $results, DAY_IN_SECONDS );

    return $results;
}

/**
 * Clear glossary transients when terms are updated.
 *
 * @param int $post_id The post ID being updated.
 */
function op_glossary_clear_transients( $post_id ) {
    if ( 'op_glossary_term' !== get_post_type( $post_id ) ) {
        return;
    }
    
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_op_glos_ltrs_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_op_glos_ltrs_%'" );
}
add_action( 'save_post', 'op_glossary_clear_transients' );
add_action( 'delete_post', 'op_glossary_clear_transients' );

/**
 * Modify the SQL WHERE clause for WordPress queries to search for posts with titles
 * that start with a specific value.
 *
 * @param string   $where The SQL WHERE clause.
 * @param WP_Query $query The current WP_Query instance.
 * @return string The modified SQL WHERE clause.
 */
function op_glossary_posts_where( $where, $query ) {
    global $wpdb;

    $starts_with = $query->get( 'starts_with' );

    if ( $starts_with ) {
        $where .= $wpdb->prepare(
            " AND LOWER($wpdb->posts.post_title) LIKE LOWER(%s)",
            $wpdb->esc_like( $starts_with ) . '%'
        );
    }

    return $where;
}

/**
 * Get the adjacent glossary term ordered alphabetically by title.
 *
 * @param int    $post_id   Current glossary term ID.
 * @param string $direction previous|next.
 * @return WP_Post|null
 */
function op_glossary_get_adjacent_term( $post_id, $direction = 'next' ) {
    global $wpdb;

    $current_post = get_post( $post_id );

    if ( ! $current_post || 'op_glossary_term' !== $current_post->post_type ) {
        return null;
    }

    $comparison = 'previous' === $direction ? '<' : '>';
    $order = 'previous' === $direction ? 'DESC' : 'ASC';
    $title = function_exists( 'mb_strtolower' )
        ? mb_strtolower( $current_post->post_title )
        : strtolower( $current_post->post_title );

    $query = $wpdb->prepare(
        "
        SELECT ID
        FROM {$wpdb->posts}
        WHERE post_type = %s
            AND post_status = 'publish'
            AND ID != %d
            AND (
                LOWER(post_title) {$comparison} %s
                OR ( LOWER(post_title) = %s AND ID {$comparison} %d )
            )
        ORDER BY LOWER(post_title) {$order}, ID {$order}
        LIMIT 1
        ",
        'op_glossary_term',
        $current_post->ID,
        $title,
        $title,
        $current_post->ID
    );

    $adjacent_id = $wpdb->get_var( $query );

    if ( ! $adjacent_id ) {
        return null;
    }

    return get_post( (int) $adjacent_id );
}

/**
 * Generates schema.org markup for single glossary page.
 */
function op_glossary_footer_schema() {
    $id = get_queried_object_id();

    if ( 'op_glossary_term' !== get_post_type( $id ) ) {
        return;
    }
    
    // Build schema data
    $schema_data = [
        "@context"    => "https://schema.org",
        "@type"       => "DefinedTerm",
        "name"        => get_the_title( $id ),
        "url"         => get_permalink( $id ),
        "description" => wp_strip_all_tags( get_post_field( 'post_content', $id ) ),
    ];

    // Add term set link if possible
    $schema_data['inDefinedTermSet'] = home_url( '/glossary/' );

    // Output the schema data as JSON within a <script> tag
    echo '<script type="application/ld+json">' . wp_json_encode( $schema_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>';
}
add_action( 'wp_footer', 'op_glossary_footer_schema', 10 );
