<?php get_header(); ?>
<div class="container">
    <h1>Welcome to the Theme</h1>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h2><?php the_title(); ?></h2>
        <?php the_content(); ?>
    <?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>