<?php /* This is part of the original theme, and was left here just for easy access - if you don't need anything from this, then just remove this block.
<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="header">
                <h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1> <?php edit_post_link(); ?>
            </header>
            <div class="entry-content" itemprop="mainContentOfPage">
                <?php if (has_post_thumbnail()) {
                    the_post_thumbnail('full', array('itemprop' => 'image'));
                } ?>
                <?php the_content(); ?>
                <div class="entry-links"><?php wp_link_pages(); ?></div>
            </div>
        </article>
        <?php if (comments_open() && !post_password_required()) {
            comments_template('', true);
        } ?>
<?php endwhile;
endif; ?>
<?php get_footer(); ?>
*/ ?>
<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="entry-content" itemprop="mainContentOfPage">
            <?php the_acf_content(); ?>
        </div>
<?php endwhile;
endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>