<?php
/**
 * Plugin Name: Event Manager
 * Description: A custom plugin to manage events.
 * Version: 1.0.0
 * Author: Ali Alizadegan
 * Text Domain: event-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue CSS Styles
function em_enqueue_styles() {
    wp_enqueue_style( 'em-styles', plugin_dir_url( __FILE__ ) . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'em_enqueue_styles' );


// Register Custom Post Type and Taxonomies
function em_register_event_post_type() {
    $labels = array(
        'name' => 'Events',
        'singular_name' => 'Event',
        'menu_name' => 'Events',
        'all_items' => 'All Events',
        'add_new_item' => 'Add New Event',
        'edit_item' => 'Edit Event',
        'view_item' => 'View Event',
    );

    $args = array(
        'label' => 'Event',
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array( 'slug' => 'events' ),
    );

    register_post_type( 'event', $args );

    register_taxonomy( 'event_type', 'event', array(
        'label' => 'Event Type',
        'rewrite' => array( 'slug' => 'event-type' ),
        'hierarchical' => true,
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'em_register_event_post_type' );

// Add Meta Boxes
function em_add_custom_meta_boxes() {
    add_meta_box(
        'em_event_details',
        'Event Details',
        'em_event_meta_box_callback',
        'event',
        'normal',
        'high'
    );

    add_meta_box(
        'em_event_rsvp',
        'RSVP',
        'em_event_rsvp_meta_box_callback',
        'event',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'em_add_custom_meta_boxes' );

// Meta Box Callback
function em_event_meta_box_callback( $post ) {
    wp_nonce_field( 'em_save_event_meta', 'em_event_meta_nonce' );
    
    $event_date = get_post_meta( $post->ID, '_event_date', true );
    $event_location = get_post_meta( $post->ID, '_event_location', true );

    echo '<label for="event_date">Event Date:</label>';
    echo '<input type="date" id="event_date" name="event_date" value="' . esc_attr( $event_date ) . '" />';

    echo '<label for="event_location">Event Location:</label>';
    echo '<input type="text" id="event_location" name="event_location" value="' . esc_attr( $event_location ) . '" />';
}

function em_event_rsvp_meta_box_callback( $post ) {
    wp_nonce_field( 'em_save_event_rsvp_meta', 'em_event_rsvp_nonce' );

    $rsvp = get_post_meta( $post->ID, '_event_rsvp', true );
    echo '<label for="event_rsvp">RSVP Count:</label>';
    echo '<input type="number" id="event_rsvp" name="event_rsvp" value="' . esc_attr( $rsvp ) . '" />';
}

// Save Meta Box Data
function em_save_event_meta( $post_id ) {
    if ( ! isset( $_POST['em_event_meta_nonce'] ) || ! wp_verify_nonce( $_POST['em_event_meta_nonce'], 'em_save_event_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['event_date'] ) ) {
        update_post_meta( $post_id, '_event_date', sanitize_text_field( $_POST['event_date'] ) );
    }

    if ( isset( $_POST['event_location'] ) ) {
        update_post_meta( $post_id, '_event_location', sanitize_text_field( $_POST['event_location'] ) );
    }

    if ( isset( $_POST['event_rsvp'] ) ) {
        update_post_meta( $post_id, '_event_rsvp', intval( $_POST['event_rsvp'] ) );
    }
}
add_action( 'save_post', 'em_save_event_meta' );

// Front-End Search and Filtering
function em_event_filtering_shortcode() {
    ob_start(); ?>
    <form method="get" id="event-filter">
        <label for="event-type">Event Type:</label>
        <?php
        $event_types = get_terms( array(
            'taxonomy' => 'event_type',
            'hide_empty' => true,
        ));
        ?>
        <select name="event-type" id="event-type">
            <option value="">Select Event Type</option>
            <?php foreach ( $event_types as $type ) : ?>
                <option value="<?php echo esc_attr( $type->slug ); ?>"><?php echo esc_html( $type->name ); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="event-date-from">From:</label>
        <input type="date" name="event-date-from" id="event-date-from" />

        <label for="event-date-to">To:</label>
        <input type="date" name="event-date-to" id="event-date-to" />

        <input type="submit" value="Filter Events" />
    </form>

    <?php
    // Query Events
    $args = array( 'post_type' => 'event', 'posts_per_page' => -1 );

    if ( isset( $_GET['event-type']) && !empty($_GET['event-type']) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'event_type',
                'field' => 'slug',
                'terms' => sanitize_text_field( $_GET['event-type'] ),
            ),
        );
    }

    if ( isset( $_GET['event-date-from']) && !empty($_GET['event-date-from']) ) {
        $args['meta_query'][] = array(
            'key' => '_event_date',
            'value' => sanitize_text_field( $_GET['event-date-from'] ),
            'compare' => '>=',
            'type' => 'DATE',
        );
    }

    if ( isset( $_GET['event-date-to']) && !empty($_GET['event-date-to']) ) {
        $args['meta_query'][] = array(
            'key' => '_event_date',
            'value' => sanitize_text_field( $_GET['event-date-to'] ),
            'compare' => '<=',
            'type' => 'DATE',
        );
    }

    $events = new WP_Query( $args );

    if ( $events->have_posts() ) {
        echo '<ul>';
        while ( $events->have_posts() ) {
            $events->the_post();
            echo '<li>' . get_the_title() . ' - ' . get_post_meta( get_the_ID(), '_event_date', true ) . '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p>No events found.</p>';
    }

    return ob_get_clean();
}
add_shortcode( 'event_filter', 'em_event_filtering_shortcode' );

// RSVP Form and Handling
function em_rsvp_form_shortcode( $atts ) {
    if ( ! is_single() || get_post_type() !== 'event' ) {
        return;
    }

    $event_id = get_the_ID();
    $rsvp_count = get_post_meta( $event_id, '_event_rsvp', true );

    ob_start(); ?>
    <form method="post" class="rsvp-form">
        <input type="hidden" name="event_id" value="<?php echo esc_attr( $event_id ); ?>">
        <input type="submit" name="rsvp" value="RSVP">
    </form>
    <p>Current RSVP Count: <?php echo esc_html( $rsvp_count ); ?></p>
    <?php
    return ob_get_clean();
}
add_shortcode( 'event_rsvp', 'em_rsvp_form_shortcode' );

function em_handle_rsvp() {
    if ( isset( $_POST['rsvp'] ) && isset( $_POST['event_id'] ) ) {
        $event_id = intval( $_POST['event_id'] );
        $rsvp_count = get_post_meta( $event_id, '_event_rsvp', true );
        $rsvp_count = $rsvp_count ? $rsvp_count + 1 : 1;

        update_post_meta( $event_id, '_event_rsvp', $rsvp_count );

        // Optionally, send a notification to the event organizer/admin
    }
}
add_action( 'init', 'em_handle_rsvp' );


function filter_events_by_meta( $query ) {
    if ( is_post_type_archive( 'event' ) && !is_admin() && $query->is_main_query() ) {
        // Check for event type filter
        if ( isset( $_GET['event-type'] ) && !empty( $_GET['event-type'] ) ) {
            $tax_query = array(
                array(
                    'taxonomy' => 'event_type',
                    'field'    => 'slug',
                    'terms'    => $_GET['event-type'],
                ),
            );
            $query->set( 'tax_query', $tax_query );
        }

        // Check for date range filter
        if ( isset( $_GET['event-date-from'] ) && !empty( $_GET['event-date-from'] ) ) {
            $meta_query = array(
                array(
                    'key'     => 'event_date',
                    'value'   => $_GET['event-date-from'],
                    'compare' => '>=',
                    'type'    => 'DATE',
                ),
            );
            $query->set( 'meta_query', $meta_query );
        }
        if ( isset( $_GET['event-date-to'] ) && !empty( $_GET['event-date-to'] ) ) {
            $meta_query[] = array(
                'key'     => 'event_date',
                'value'   => $_GET['event-date-to'],
                'compare' => '<=',
                'type'    => 'DATE',
            );
            $query->set( 'meta_query', $meta_query );
        }
    }
}
add_action( 'pre_get_posts', 'filter_events_by_meta' );

function event_manager_load_textdomain() {
    load_plugin_textdomain( 'event-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'event_manager_load_textdomain' );

