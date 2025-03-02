<?php get_header(); ?>
<div class="container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <p><strong>Description:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'project_description', true)); ?></p>
        <p><strong>Start Date:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'project_start_date', true)); ?></p>
        <p><strong>End Date:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'project_end_date', true)); ?></p>
        <p><strong>URL:</strong> <a href="<?php echo esc_url(get_post_meta(get_the_ID(), 'project_url', true)); ?>"><?php echo esc_html(get_post_meta(get_the_ID(), 'project_url', true)); ?></a></p>
    <?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>