<?php
/**
 * Assignment Progress
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


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
   } else {
      echo 'Please login.';
   }
  
}

function h5p_gb_mysql_progress($user_id, $h5p_ids){//$user_id, $assignment_ids
   global $wpdb;    
   $ids = implode(',',$h5p_ids);
   $results = $wpdb->get_results( "
       SELECT {$wpdb->prefix}h5p_results.user_id, {$wpdb->prefix}h5p_results.score, {$wpdb->prefix}h5p_results.score, {$wpdb->prefix}h5p_results.max_score, {$wpdb->prefix}h5p_results.content_id, {$wpdb->prefix}h5p_contents.title  
       FROM {$wpdb->prefix}h5p_results 
       RIGHT JOIN wp_49_h5p_contents
       ON {$wpdb->prefix}h5p_contents.id = {$wpdb->prefix}h5p_results.content_id 
       WHERE {$wpdb->prefix}h5p_results.user_id = {$user_id} AND {$wpdb->prefix}h5p_results.content_id IN ({$ids}) 
       ");
    //var_dump($results);
   $html = '';
   $css_array = array();
   foreach ($h5p_ids as $key => $h5p_id) {
      $html .= h5p_gb_id_matcher($h5p_id,$results);
      array_push($css_array,h5p_gb_id_css_wrapper($h5p_id,$results));
   }
   $count_ids = sizeof($h5p_ids);
   $count_results = sizeof($results);
   $css = implode(' ', array_filter($css_array));
   echo "<table>
            <caption>Progress on this assignment {$count_results} of {$count_ids}</caption>
            <tr><th>Title</th><th>Score</th><th>Max score</th></tr>
            {$html}
         </table> <style>{$css}</style>";
}

add_shortcode( 'h5p-progress', 'h5p_gb_assignment_progress' );

function h5p_gb_id_matcher($h5p_id,$results){
   foreach ($results as $key => $result) {
      // code...
      //var_dump($result);
      $title = $result->title . ' (id=' . $h5p_id . ')';
      $max_score = $result->max_score;
     
      if($h5p_id == $result->content_id){
          $score = $result->score;
         return "<tr class='taken'><td><a href='#h5p-iframe-{$h5p_id}'>{$title}</a></td><td>{$score}</td><td>{$max_score}</td></tr>";
      } 
   }
}

function h5p_gb_id_css_wrapper($h5p_id,$results){
   foreach ($results as $key => $result) { 
   $user_score = $result->score;
   $max_score = $result->max_score;    
      $css = '';
      if($h5p_id == $result->content_id){
         return "#h5p-iframe-{$h5p_id} {border-left: 12px solid green; border-top: 8px dashed green;}
                  #h5p-iframe-{$h5p_id}-holder:after {content: 'Current Score: {$user_score}/{$max_score}'; width: 100%; height: auto; text-align: center; display: block; border-left: 1px solid #424242;border-right: 1px solid #424242;border-bottom: 1px solid #424242;}";
      } 
   }
}