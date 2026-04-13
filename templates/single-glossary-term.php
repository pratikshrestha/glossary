<?php

/**
 * Single Glossary Term Template
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();

        $previous_term = op_glossary_get_adjacent_term(get_the_ID(), 'previous');
        $next_term     = op_glossary_get_adjacent_term(get_the_ID(), 'next');
        $term_title    = get_the_title();
        // $term_excerpt  = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 28);
?>
        <style>
            .glossary-nav {
                min-width: 275px;
                max-width: 350px;
                width: fit-content;
            }
        </style>

        <main class="op-glossary op-glossary-single container py-4 py-lg-5">
            <article <?php post_class('op-glossary-single__article'); ?>>

                <header class="op-glossary-single__hero mb-5">
                    <nav aria-label="breadcrumb" class="op-glossary-breadcrumbs mb-4">
                        <ol class="breadcrumb m-0 p-0 bg-transparent flex-row d-flex flex-wrap list-unstyled" itemscope itemtype="https://schema.org/BreadcrumbList">
                            <li class="breadcrumb-item d-flex align-items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <a itemprop="item" href="<?php echo esc_url(home_url()); ?>" class="text-decoration-none text-muted">
                                    <span itemprop="name"><?php esc_html_e('Home', 'op-glossary'); ?></span>
                                </a>
                                <meta itemprop="position" content="1" />
                            </li>
                            <li class="breadcrumb-item d-flex align-items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <a itemprop="item" href="<?php echo esc_url(home_url('/glossary/')); ?>" class="text-decoration-none text-muted">
                                    <span itemprop="name"><?php esc_html_e('SEO Glossary', 'op-glossary'); ?></span>
                                </a>
                                <meta itemprop="position" content="2" />
                            </li>
                            <li class="breadcrumb-item d-flex align-items-center active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <span itemprop="item" href="<?php echo esc_url(get_permalink()); ?>" class="text-dark fw-medium">
                                    <span itemprop="name"><?php the_title(); ?></span>
                                </span>
                                <meta itemprop="position" content="3" />
                            </li>
                        </ol>
                    </nav>

                    <h1 class="op-glossary-single__title display-4 fw-bold py-4" style="color: var(--op-glossary-ink); letter-spacing: -0.03em;">
                        <?php the_title(); ?>
                    </h1>

                    <!-- <?php if ($term_excerpt) : ?> -->
                    <div class="op-glossary-single__intro p-4 p-lg-5 rounded-4 border-0 mb-4 mt-0" style="background: var(--op-glossary-surface-alt); box-shadow: var(--op-glossary-shadow);">
                        <p class="op-glossary-single__intro-label text-uppercase fw-bold mb-3" style="color: var(--op-glossary-accent); font-size: 13px; letter-spacing: 0.1em;">
                            <?php esc_html_e('Definition', 'op-glossary'); ?>
                        </p>
                        <p class="op-glossary-single__summary lead mb-0 fw-medium" style="color: var(--op-glossary-ink); font-size: clamp(1.1rem, 1rem + 0.5vw, 1.3rem); line-height: 1.6;">
                            <?php the_content(); ?>
                        </p>
                    </div>
                    <!-- <?php endif; ?> -->
                </header>

                <!-- <div class="op-glossary-single__content py-4" style="font-size: 1.1rem; line-height: 1.8; color: var(--op-glossary-text);">
                    <?php the_content(); ?>
                </div> -->

                <?php if ($previous_term || $next_term) : ?>
                    <hr class="mb-5 border-top border-2" style="border-color: var(--op-glossary-line); opacity: 1;">
                    <nav class="op-glossary-single__navigation row d-flex column-gap-5 justify-content-center justify-content-md-between row-gap-3 flex-wrap pt-0" aria-label="<?php esc_attr_e('Glossary term navigation', 'op-glossary'); ?>">

                        <?php if ($previous_term) : ?>
                            <a href="<?php echo esc_url(get_permalink($previous_term)); ?>" class="d-block h-100 text-decoration-none p-4 rounded-4 glossary-nav" style="background: var(--op-glossary-surface-alt); border: 1px solid var(--op-glossary-line); transition: all 0.2s ease;">
                                <span class="d-block text-uppercase fw-bold mb-2" style="color: var(--op-glossary-accent); font-size: 13px; letter-spacing: 0.1em;">
                                    &larr; <?php esc_html_e('Previous term', 'op-glossary'); ?>
                                </span>
                                <span class="d-block fw-bold " style="color: var(--op-glossary-ink); font-size: clamp(1.1rem, 1rem + 0.5vw, 1.3rem);">
                                    <?php echo esc_html(get_the_title($previous_term)); ?>
                                </span>
                            </a>
                        <?php endif; ?>
                        <?php if ($next_term) : ?>
                            <a href="<?php echo esc_url(get_permalink($next_term)); ?>" class="d-block h-100 text-decoration-none p-4 rounded-4 text-md-end glossary-nav" style="background: var(--op-glossary-surface-alt); border: 1px solid var(--op-glossary-line); transition: all 0.2s ease;">
                                <span class="d-block text-uppercase fw-bold mb-2" style="color: var(--op-glossary-accent); font-size: 13px; letter-spacing: 0.1em;">
                                    <?php esc_html_e('Next term', 'op-glossary'); ?> &rarr;
                                </span>
                                <span class="d-block fw-bold" style="color: var(--op-glossary-ink); font-size: clamp(1.1rem, 1rem + 0.5vw, 1.3rem);">
                                    <?php echo esc_html(get_the_title($next_term)); ?>
                                </span>
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </article>
        </main>
<?php
    endwhile;
endif;

get_footer();
