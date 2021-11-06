<?php 
/*
Plugin Name: H5P Gradebook Export
Plugin URI:  https://github.com/
Description: To get your H5P gradebook out as an Excel document
Version:     1.0
Author:      Tom Woodward
Author URI:  http://tomwoodward.us
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// add_action('wp_enqueue_scripts', 'prefix_load_scripts');

// function prefix_load_scripts() {                           
//     $deps = array('jquery');
//     $version= '1.0'; 
//     $in_footer = true;    
//     wp_enqueue_script('prefix-main-js', plugin_dir_url( __FILE__) . 'js/prefix-main.js', $deps, $version, $in_footer); 
//     wp_enqueue_style( 'prefix-main-css', plugin_dir_url( __FILE__) . 'css/prefix-main.css');
// }

//******from h5p>admin>class-h5p-content-admin.php
// $this->content = $plugin->get_content($id);
//     if (!is_string($this->content)) {
//       $tags = $wpdb->get_results($wpdb->prepare(
//           "SELECT t.name
//              FROM {$wpdb->prefix}h5p_contents_tags ct
//              JOIN {$wpdb->prefix}h5p_tags t ON ct.tag_id = t.id
//             WHERE ct.content_id = %d",
//           $id
//       ));
//       $this->content['tags'] = '';
//       foreach ($tags as $tag) {
//         $this->content['tags'] .= ($this->content['tags'] !== '' ? ', ' : '') . $tag->name;
//       }
//     }
//   }

/*
things to look at 
https://github.com/cogdog/wp-posts2csv/blob/master/export-post-csv.php

SELECT wp_49_h5p_results.content_id,wp_49_h5p_results.user_id, wp_49_h5p_contents.title, wp_49_h5p_results.score, wp_49_h5p_results.max_score,wp_users.id, wp_users.display_name
FROM wp_49_h5p_results
RIGHT JOIN wp_49_h5p_contents
ON wp_49_h5p_contents.id = wp_49_h5p_results.content_id 
LEFT JOIN wp_users
ON wp_users.id = wp_49_h5p_results.user_id

*/


function h5p_gb_export_get_data(){
   global $wpdb;    
   $results = $wpdb->get_results( "
       SELECT {$wpdb->prefix}h5p_results.content_id,{$wpdb->prefix}h5p_results.user_id,{$wpdb->prefix}h5p_results.opened, {$wpdb->prefix}h5p_results.finished, {$wpdb->prefix}h5p_results.time, {$wpdb->prefix}h5p_contents.title, {$wpdb->prefix}h5p_results.score, {$wpdb->prefix}h5p_results.max_score,wp_users.id, wp_users.display_name
            FROM {$wpdb->prefix}h5p_results
            RIGHT JOIN {$wpdb->prefix}h5p_contents
            ON {$wpdb->prefix}h5p_contents.id = {$wpdb->prefix}h5p_results.content_id   
            LEFT JOIN wp_users
            ON wp_users.id = {$wpdb->prefix}h5p_results.user_id
       ");
   //var_dump($results);
   $html = '';
   foreach ($results as $key => $result) {
      // code...
      $user_id = $result->user_id;
      $name = h5p_gb_name_fetcher($user_id);
      $title = $result->title;
      $score = $result->score;
      $max = $result->max_score;
      $opened = date("F j, Y, g:i a", $result->opened);
      $finished = date("F j, Y, g:i a",$result->finished);
      $percent = 0;
      $time = $result->time;
      if($score){
         $percent = $score/$max * 100 . '%';
         $html .= "<tr><td>{$title}</td><td>$name</td><td>{$max}</td><td>{$score}</td><td>{$percent}</td><td>{$opened}</td><td>{$finished}</td><td>{$time}</td></tr>";
      }
     
   }
    echo "<table>
    <tr><th>Assignment</th><th>Student</th><th>Max score</th><th>Score</th><th>Percent</th><th>Opened</th><th>Finished</th><th>Time</th></tr>
            {$html}
            </table>";
}

add_shortcode( 'h5p-results', 'h5p_gb_export_get_data' );

function h5p_gb_name_fetcher($user_id){
   $user_info = $user_id ? new WP_User( $user_id ) : wp_get_current_user();

   if ( $user_info->first_name ) {

      if ( $user_info->last_name ) {
         return $user_info->last_name . ', ' . $user_info->first_name;
      }

      return $user_info->first_name;
   }

   return $user_info->display_name;

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
