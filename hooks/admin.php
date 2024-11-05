<?php
add_action('admin_enqueue_scripts', 'itc_d365_load_admin_scripts');

function itc_d365_load_admin_scripts() {
    // Check the current screen
    $screen = get_current_screen();
    // Load the script only on the specified screen(s)
    if ( $screen->id == 'toplevel_page_itc_dynamics_365_options') {
        wp_enqueue_script('itc-d365-forms', ITC_D365_URL . 'assets/js/form.js' , array('jquery'), '1.0', true);
    }
}
