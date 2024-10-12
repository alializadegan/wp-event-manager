<?php get_header(); ?>
<div class="event-details">
    <h1><?php the_title(); ?></h1>
    <p>Date: <?php echo get_post_meta( get_the_ID(), '_event_date', true ); ?></p>
    <p>Location: <?php echo get_post_meta( get_the_ID(), '_event_location', true ); ?></p>
    <?php echo do_shortcode('[event_rsvp]'); ?>
</div>
<?php get_footer(); ?>
