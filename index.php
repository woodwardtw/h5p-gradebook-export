<?php 
/*
Plugin Name: H5P Gradebook Export
Plugin URI:  https://github.com/
Description: To get your H5P gradebook out as an Excel document - [h5p-progress] shortcode for assignment pages
Version:     1.0
Author:      Tom Woodward
Author URI:  https://tomwoodward.us
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//load up front end js
add_action('wp_enqueue_scripts', 'h5p_gb_front_load_scripts');

function h5p_gb_front_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('h5p-gb-assignment-js', plugin_dir_url( __FILE__) . 'js/h5p_gb_assignment.js', $deps, $version, $in_footer); 
    //wp_enqueue_style( 'h5p-front-main-css', plugin_dir_url( __FILE__) . 'css/h5p-gb-front.css');
}


//load up admin styles
add_action('admin_enqueue_scripts', 'h5p_gb_css_and_js');

function h5p_gb_css_and_js($hook)
    {

    $current_screen = get_current_screen();
    if ( $current_screen->base == 'toplevel_page_hp5_gradebook') {
        wp_enqueue_style('h5p_gb_css', plugins_url('css/h5_gb.css',__FILE__ ));
        wp_enqueue_style('h5p_gb_datatables', plugins_url('css/jquery.dataTables.min.css', __FILE__));

        wp_enqueue_script('dataTables', plugins_url('js/jquery.dataTables.min.js', __FILE__), ['jquery'], false, true);
        wp_enqueue_script('dataTablesButtons', plugins_url('js/dataTables.buttons.min.js', __FILE__) , ['dataTables'], false, true);
        wp_enqueue_script('dataTablesJs', plugins_url('js/jszip.min.js', __FILE__), ['dataTables'], false, true);
        wp_enqueue_script('dataTablesFonts', plugins_url('js/vfs_fonts.js', __FILE__), ['dataTables'], false, true);
        wp_enqueue_script('dataTablesHTML5', plugins_url('js/buttons.html5.min.js', __FILE__), ['dataTables'], false, true);
        wp_enqueue_script('dataTablesPrint', plugins_url('js/buttons.print.min.js', __FILE__), ['dataTables'], false, true);
        wp_enqueue_script('ln_script', plugins_url('js/h5p_gb.js', __FILE__), ['dataTables'], false, true);
        }
    }


$h5p_gb_includes = array(
   '/admin-display.php',
   '/assignment-progress.php'
);

// Include files.
foreach ( $h5p_gb_includes as $file ) {
   require_once plugin_dir_path( __FILE__ ) . 'inc' . $file;
}



//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}



  //print("<pre>".print_r($a,true)."</pre>");
