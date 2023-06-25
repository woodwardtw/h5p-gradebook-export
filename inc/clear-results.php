<?php 

add_action('admin_menu', 'clear_h5p_results_table');

//erase data in h5p results table
function clear_h5p_results_table(){
    $user_id = get_current_user_id();
    if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permission to clear responses.')    );
    }
    if ( isset($_GET['action'] ) && $_GET['action'] == 'clear_h5p_results_table' && is_super_admin($user_id)) {

        global $wpdb;
        $table  = $wpdb->prefix . 'h5p_results';
        $delete = $wpdb->query("TRUNCATE TABLE $table");
    }
}

