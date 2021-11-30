<?php
/**
 * Admin gradebook display
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/*
* make admin page
*/

add_action('admin_menu', 'h5p_gb_export_plugin_setup_menu');
 
function h5p_gb_export_plugin_setup_menu(){
    add_menu_page( 'H5P Gradebook', 'H5P Gradebook', 'see_grades', 'hp5_gradebook', 'h5p_gb_export_get_data' );
}
 


/*
* get data from mysql for gradebook display
*/

function h5p_gb_export_get_data(){
   global $wpdb;    
   $results = $wpdb->get_results( "
       SELECT {$wpdb->prefix}h5p_results.content_id,
              {$wpdb->prefix}h5p_results.user_id,
              {$wpdb->prefix}h5p_results.opened, 
              {$wpdb->prefix}h5p_results.finished, 
              {$wpdb->prefix}h5p_results.time, 
              {$wpdb->prefix}h5p_contents.title,
              {$wpdb->prefix}h5p_contents.id AS reliable_id, 
              {$wpdb->prefix}h5p_results.score, 
              {$wpdb->prefix}h5p_results.max_score,
              wp_users.id,
              wp_users.display_name
            FROM {$wpdb->prefix}h5p_results
            RIGHT JOIN {$wpdb->prefix}h5p_contents
            ON {$wpdb->prefix}h5p_contents.id = {$wpdb->prefix}h5p_results.content_id   
            LEFT JOIN wp_users
            ON wp_users.id = {$wpdb->prefix}h5p_results.user_id          
       ");
   
   $html = '';
   foreach ($results as $key => $result) {      
      // code...
      $content_id = $result->reliable_id;//h5p object id       
      $user_id = $result->user_id;
      $name = h5p_gb_name_fetcher($user_id);
      $title = $result->title;
      $tag = h5p_gb_tag_getter($content_id);;
      $score = $result->score;
      $max = $result->max_score;
      $opened = date(" d-m-Y, g:i a", $result->opened);
      $finished = date("d-m-Y, g:i a",$result->finished);
      $percent = 0;
      $time = $result->time;
      if($score != null){
         $percent = $score/$max * 100 . '%';
         $html .= "<tr><td>{$title}  - ${content_id}</td><td>{$tag}</td><td>{$name}</td><td>{$max}</td><td>{$score}</td><td>{$percent}</td><td>{$opened}</td><td>{$finished}</td></tr>";
      }
     
   }
    echo "<table id='h5p_grades' class='display nowrap'>
    <thead><tr><th>Title</th><th>Tags</th><th>Student</th><th>Max Pts</th><th>Score</th><th>%</th><th>Start</th><th>Finish</th></tr></thead><tbody>
            {$html}
            </tbody></table>";
}

add_shortcode( 'h5p-results', 'h5p_gb_export_get_data' ); //shortcode which we might not need/want any more


/*
* second mysql query to get the assignment tags 
*/

function h5p_gb_tag_getter($reliable_id){
   global $wpdb;    
   $results = $wpdb->get_results( "
       SELECT 
              {$wpdb->prefix}h5p_contents_tags.content_id,
              {$wpdb->prefix}h5p_contents_tags.tag_id,
              {$wpdb->prefix}h5p_tags.id, 
              {$wpdb->prefix}h5p_tags.name
            FROM {$wpdb->prefix}h5p_contents_tags           
            LEFT JOIN {$wpdb->prefix}h5p_tags
            ON {$wpdb->prefix}h5p_tags.id = {$wpdb->prefix}h5p_contents_tags.tag_id
            WHERE {$wpdb->prefix}h5p_contents_tags.content_id = $reliable_id
       ");
   $tags = array();
    foreach ($results as $key => $result) {  
      //var_dump($result);
      array_push($tags, $result->name);
    }
    $final_tags = implode(', ', $tags);
    return  $final_tags;
}


/*
* get student name, try to get first/last then fall back to display name
*/

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