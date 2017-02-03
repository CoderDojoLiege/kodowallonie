<?php
/**
* Plugin Name: Kodo Subsites List Generator
* Description: Generates a list with all existing subsites.
* Version: 1.0
* Author: Justine Lejeune
*/

/**
* Creates a shortcode for displaying a list of all existing subsites.
*/

function kodo_subsites_list_shortcode1() {
  ob_start();
  $subsites = get_sites();
  echo '<div class="kodo-subsites effects clearfix">';
  foreach( $subsites as $subsite ) {
    $subsite_id = get_object_vars($subsite)["blog_id"];
    if( $subsite_id != 1 ) {
      $subsite_name = get_blog_details($subsite_id)->blogname;
      echo '<div class="logo-dojo">' . get_custom_logo($subsite_id) . '</div>';
    }
  }
  echo '</div>';
}

add_shortcode( 'kodo-subsites-shortcode', 'kodo_subsites_list_shortcode1' );

?>
