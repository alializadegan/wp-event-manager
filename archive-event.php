<?php
get_header();
?>

<div class="event-filter">
    <form method="GET" action="<?php echo esc_url( home_url( '/events/' ) ); ?>">
        <label for="event-type">Event Type:</label>
        <select name="event-type" id="event-type">
            <option value="">All Types</option>
            <?php
            // Fetch and display all event types (taxonomy terms)
            $event_types = get_terms( array(
                'taxonomy' => 'event_type',
                'hide_empty' => false,
            ));
            foreach ( $event_types as $event_type ) {
                $selected = ( isset( $_GET['event-type'] ) && $_GET['event-type'] == $event_type->slug ) ? 'selected' : '';
                echo '<option value="' . esc_attr( $event_type->slug ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $event_type->name ) . '</option>';
            }
            ?>
        </select>

        <label for="event-date-from">From Date:</label>
        <input type="date" name="event-date-from" id="event-date-from" value="<?php echo isset( $_GET['event-date-from'] ) ? esc_attr( $_GET['event-date-from'] ) : ''; ?>">

        <label for="event-date-to">To Date:</label>
        <input type="date" name="event-date-to" id="event-date-to" value="<?php echo isset( $_GET['event-date-to'] ) ? esc_attr( $_GET['event-date-to'] ) : ''; ?>">

        <input type="submit" value="Filter Events">
    </form>
</div>

<div class="event-table-container">
    <table class="event-table">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Location</th>
                <th>RSVP</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    $event_date = get_post_meta( get_the_ID(), 'event_date', true );
                    $event_location = get_post_meta( get_the_ID(), 'event_location', true );
                    $event_rsvp = get_post_meta( get_the_ID(), 'event_rsvp_count', true );
            ?>
            <tr>
                <td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                <td class="event-date"><?php echo esc_html( $event_date ); ?></td>
                <td class="event-location"><?php echo esc_html( $event_location ); ?></td>
                <td class="event-rsvp"><?php echo esc_html( $event_rsvp ); ?> RSVPs</td>
            </tr>
            <?php
                endwhile;
            else :
            ?>
            <tr>
                <td colspan="4">No events found.</td>
            </tr>
            <?php
            endif;
            ?>
        </tbody>
    </table>
</div>

<?php
get_footer();
?>
