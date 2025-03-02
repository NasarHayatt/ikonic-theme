<?php
/*
 Template Name: Blog
*/
get_header(); ?>
<div class="container">
    <h1>Blog</h1>
    <?php
    $args = array('post_type' => 'post', 'posts_per_page' => 5);
    $blog_query = new WP_Query($args);
    if ($blog_query->have_posts()) : while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
        <h2><?php the_title(); ?></h2>
        <?php the_excerpt(); ?>
    <?php endwhile; wp_reset_postdata(); endif; ?>
</div>
<?php get_footer(); ?>