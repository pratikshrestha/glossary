<?php

/**
 * Shortcode Template: Glossary
 *
 * @var array    $atts
 * @var string   $selected_letter
 * @var array    $first_letters_array
 * @var WP_Query $query
 */

if (! defined('ABSPATH')) {
    exit;
}

$search_term  = isset($_GET['op_glossary_search']) ? sanitize_text_field(wp_unslash($_GET['op_glossary_search'])) : '';
$result_count = (int) $query->post_count;
$active_letter = isset($_GET['op-glossary-pagination']) ? sanitize_text_field(wp_unslash($_GET['op-glossary-pagination'])) : '';
?>
<div class="op-glossary">
    <div class="op-glossary__controls">
        <form class="op-glossary-search-form" role="search" aria-label="<?php esc_attr_e('Search terms', 'op-glossary'); ?>" method="get">
            <div class="op-glossary-search-form__header">
                <label class="op-glossary-search-form__label" for="op-glossary-search">
                    <?php esc_html_e('Search terms', 'op-glossary'); ?>
                </label>
            </div>

            <div class="op-glossary-search-form__field">
                <input
                    id="op-glossary-search"
                    type="text"
                    name="op_glossary_search"
                    class="op-glossary-search-form__input"
                    value="<?php echo esc_attr($search_term); ?>"
                    placeholder="<?php esc_attr_e('Search for a term or concept', 'op-glossary'); ?>" />

                <?php if (! empty($selected_letter)) : ?>
                    <input type="hidden" name="op-glossary-pagination" value="<?php echo esc_attr($selected_letter); ?>">
                <?php endif; ?>

                <button type="submit" class="op-glossary-search-form__button">
                    <?php esc_html_e('Search', 'op-glossary'); ?>
                </button>
            </div>
        </form>
    </div>

    <nav class="op-glossary-pagination">
        <div class="op-glossary-pagination__header">
            <span class="op-glossary-pagination__label"><?php esc_html_e('Browse by letter', 'op-glossary'); ?></span>
            <span class="op-glossary-pagination__current">
                <?php
                if ($search_term) {
                    esc_html_e('Filtered results', 'op-glossary');
                } elseif ($active_letter) {
                    printf(
                        esc_html__('Showing %s', 'op-glossary'),
                        esc_html($active_letter)
                    );
                } else {
                    esc_html_e('A to Z index', 'op-glossary');
                }
                ?>
            </span>
        </div>
        <ul class="op-glossary-pagination-list">
            <?php
            $all_query_args = array();

            if (! empty($search_term)) {
                $all_query_args['op_glossary_search'] = $search_term;
            }

            $all_url = ! empty($all_query_args)
                ? add_query_arg($all_query_args, get_permalink())
                : get_permalink();

            echo '<li class="' . esc_attr(empty($active_letter) ? 'active' : '') . '"><a class="op-glossary-pagination-list__all" href="' . esc_url($all_url) . '">' . esc_html__('All', 'op-glossary') . '</a></li>';

            foreach ($first_letters_array as $first_letter) {
                $active_class = ($first_letter === $selected_letter) ? 'active' : '';
                $query_args   = array('op-glossary-pagination' => $first_letter);

                // Preserve search if present
                if (! empty($search_term)) {
                    $query_args['op_glossary_search'] = $search_term;
                }

                $url = add_query_arg($query_args);

                echo '<li class="' . esc_attr($active_class) . '"><a href="' . esc_url($url) . '">' . esc_html($first_letter) . '</a></li>';
            }
            ?>
        </ul>
    </nav>

    <div class="op-glossary-results">
        <?php echo op_glossary_render_term_list($query); ?>
    </div>
</div>