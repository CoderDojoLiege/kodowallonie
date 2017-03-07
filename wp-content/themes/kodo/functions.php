<?php

function kodo_enqueue_scripts() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'global-style', get_stylesheet_directory_uri() . '/style.css' );
  $blog_id = get_current_blog_id();
  if( $blog_id == '1' )
    $child_style = '/css/kodo.css';
  elseif( $blog_id == '2' )
    $child_style = '/css/liege.css';
  elseif( $blog_id == '3' )
    $child_style = '/css/mons.css';
  elseif( $blog_id == '4' )
    $child_style = '/css/charleroi.css';
  elseif( $blog_id == '5' )
    $child_style = '/css/louvain.css';
  wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . $child_style );
  wp_enqueue_style( 'dashicons' );
  wp_enqueue_script( 'global-script', get_stylesheet_directory_uri() . '/global.js', array( 'jquery' ) );
}

add_action( 'wp_enqueue_scripts', 'kodo_enqueue_scripts' );

?>
