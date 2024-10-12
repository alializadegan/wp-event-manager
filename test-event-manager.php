<?php
class Event_Manager_Test extends WP_UnitTestCase {
    public function test_event_creation() {
        $event_id = wp_insert_post( array(
            'post_title' => 'Test Event',
            'post_type' => 'event',
            'post_status' => 'publish',
        ) );

        $this->assertNotNull( $event_id );
        $this->assertEquals( 'event', get_post_type( $event_id ) );
    }

    public function test_event_rsvp() {
        $event_id = wp_insert_post( array(
            'post_title' => 'Test Event',
            'post_type' => 'event',
            'post_status' => 'publish',
        ) );

        update_post_meta( $event_id, '_event_rsvp', 5 );
        $this->assertEquals( 5, get_post_meta( $event_id, '_event_rsvp', true ) );

        // Simulate an RSVP
        $_POST['rsvp'] = true;
        $_POST['event_id'] = $event_id;
        em_handle_rsvp();

        $this->assertEquals( 6, get_post_meta( $event_id, '_event_rsvp', true ) );
    }

    public function test_event_filtering() {
        $event_type = wp_insert_term( 'Conference', 'event_type' );

        $post_id = wp_insert_post( array(
            'post_title' => 'Conference Event',
            'post_type' => 'event',
            'post_status' => 'publish',
        ) );

        wp_set_object_terms( $post_id, $event_type['term_id'], 'event_type' );

        // Simulate GET request for filtering
        $_GET['event-type'] = 'conference';
        $_GET['event-date-from'] = '2024-01-01';
        $_GET['event-date-to'] = '2024-12-31';

        ob_start();
        em_event_filtering_shortcode();
        $output = ob_get_clean();

        // Check if the event appears in the filtered output
        $this->assertContains( 'Conference Event', $output );
    }
}
