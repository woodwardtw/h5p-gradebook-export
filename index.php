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
      $opened = date(" d-m-Y, g:i a", $result->opened);
      $finished = date("d-m-Y, g:i a",$result->finished);
      $percent = 0;
      $time = $result->time;
      if($score){
         $percent = $score/$max * 100 . '%';
         $html .= "<tr><td>{$title}</td><td>$name</td><td>{$max}</td><td>{$score}</td><td>{$percent}</td><td>{$opened}</td><td>{$finished}</td></tr>";
      }
     
   }
    echo "<table>
    <tr><th>Title</th><th>Student</th><th>Max</th><th>Score</th><th>%</th><th>Start</th><th>Finish</th></tr>
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


function h5p_gb_assignment_progress(){
   if (is_user_logged_in() && get_current_user_id()){
       global $post;
      $user_id = get_current_user_id();
      $content = $post->post_content;
      $codes = preg_match_all( 
          '/' . get_shortcode_regex() . '/', 
          $content, 
          $matches, 
          PREG_SET_ORDER
      );
     // var_dump($matches);
      $h5p_ids = array();
      foreach ($matches as $key => $match) {
         // code...
        // var_dump($match[0]);
         //var_dump(strpos($match[0], '[h5p id=', 0 ));
         if(strpos($match[0], '[h5p id=', 0 ) === 0){
            //echo $match[0];
            preg_match('/h5p id="(\d+)/', $match[0], $h5p_id);
            $the_id = $h5p_id[1]; 
            array_push($h5p_ids, $the_id);
         }
      }
      //var_dump($user_id);//SELECT * FROM wp_49_h5p_results WHERE user_id = 164 AND content_id IN (3,5)
      //var_dump($h5p_ids);
      h5p_gb_mysql_progress($user_id, $h5p_ids);
   }
  
}

function h5p_gb_mysql_progress($user_id, $h5p_ids){//$user_id, $assignment_ids
   global $wpdb;    
   $ids = implode(',',$h5p_ids);
   $results = $wpdb->get_results( "
       SELECT {$wpdb->prefix}h5p_results.user_id, {$wpdb->prefix}h5p_results.score, {$wpdb->prefix}h5p_results.content_id, {$wpdb->prefix}h5p_contents.title  
       FROM {$wpdb->prefix}h5p_results 
       RIGHT JOIN wp_49_h5p_contents
       ON {$wpdb->prefix}h5p_contents.id = {$wpdb->prefix}h5p_results.content_id 
       WHERE {$wpdb->prefix}h5p_results.user_id = {$user_id} AND {$wpdb->prefix}h5p_results.content_id IN ({$ids}) 
       ");
    var_dump($results);
}


add_shortcode( 'h5p-progress', 'h5p_gb_assignment_progress' );


//SELECT wp_49_h5p_results.user_id, wp_49_h5p_results.score, wp_49_h5p_results.content_id, wp_49_h5p_contents.title 
// FROM wp_49_h5p_results 
// RIGHT JOIN wp_49_h5p_contents
// ON wp_49_h5p_contents.id = wp_49_h5p_results.content_id 
// WHERE wp_49_h5p_results.user_id = 164 AND wp_49_h5p_results.content_id IN (3,5)

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
