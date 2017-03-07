<?php
/**
 * Plugin Name: Kodo Team Showcase
 * Description: Meet the Kodo teams.
 * Version: 1.0
 * Author: Justine Lejeune
 */

add_action( 'init', 'create_kodo_team_member' );

/**
 * Registers kodo_team_member custom post type.
 *
 * Sets public and has_archive to false to avoid direct templates for this post type.
 */
function create_kodo_team_member() {
  $labels = array(
    'name'               => 'Membres d\'équipe',
    'singular_name'      => 'Membre d\'équipe',
    'add_new'            => 'Ajouter un nouveau membre',
    'add_new_item'       => 'Ajouter un membre',
    'edit_item'          => 'Modifier un membre',
    'new_item'           => 'Nouveau membre',
    'view_item'          => 'Voir le membre',
    'search_items'       => 'Chercher un membre',
    'not_found'          => 'Pas de résultat',
    'not_found_in_trash' => 'Rien trouvé dans la corbeille',
    'parent_item_colon'  => ''
  );

  $args = array(
    'labels'              => $labels,
    'public'              => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'show_in_nav_menu'    => false,
    'has_archive'         => false,
    'show_ui'             => true,
    'query_var'           => true,
    'menu_icon'           => null,
    'rewrite'             => true,
    'capability_type'     => 'post',
    'hierarchical'        => false,
    'menu_position'       => null,
    'supports'            => array('title', 'thumbnail'),
    'menu_icon'           => 'dashicons-admin-users',
  );

  register_post_type( 'kodo_team_member' , $args );
}

/**
 * Removes unwanted boxes in custom post types (title and description).
 */
// function remove_unwanted_boxes($columns) {
//   remove_post_type_support( 'kodo_team_member', 'editor' );
// }
//
// add_action( 'admin_init', 'remove_unwanted_boxes' );

/**
 * Adds a box to the kodo Team Member edit screen.
 */
function add_kodo_team_member_metabox() {
  add_meta_box( 'kodo_team_member_meta', 'kodo Team Member Meta', 'kodo_team_member_meta_callback', 'kodo_team_member', 'normal', 'high' );
}

add_action( 'add_meta_boxes', 'add_kodo_team_member_metabox' );

/**
 * Prints the kodo Team Member box content.
 */
function kodo_team_member_meta_callback() {
  global $post;

  $firstname = get_post_meta($post->ID, 'member_firstname', true);
  $twitter   = get_post_meta($post->ID, 'member_twitter', true);
  $linkedin  = get_post_meta($post->ID, 'member_linkedin', true);
  $website   = get_post_meta($post->ID, 'member_website', true);

  echo '<input type="hidden" name="teammember_noncename" id="teammember_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
  echo '<p><label for="member_firstname">Prénom : </label>';
  echo '<input id="member_firstname" type="text" name="member_firstname" value="' . $firstname . '" /></p>';
  echo '<p><label for="member_twitter">Twitter : </label>';
  echo '<input id="member_twitter" type="text" name="member_twitter" value="' . $twitter . '" /></p>';
  echo '<p><label for="member_linkedin">LinkedIn : </label>';
  echo '<input id="member_linkedin" type="text" name="member_linkedin" value="' . $linkedin . '" /></p>';
  echo '<p><label for="member_website">Website : </label>';
  echo '<input id="member_website" type="text" name="member_website" value="' . $website . '" /></p>';
}

/**
 * Saves the kodo Team Member values.
 */
function save_kodo_team_member_meta($post_id, $post) {
  if( !isset( $_POST['teammember_noncename'] ) ) {
		return $post->ID;
	}

  if( !wp_verify_nonce( $_POST['teammember_noncename'], plugin_basename(__FILE__) ) ) {
    return $post->ID;
  }

  if( !current_user_can( 'edit_post', $post->ID ) ) {
    return $post->ID;
  }

  $team_member_meta['member_firstname'] = $_POST['member_firstname'];
  $team_member_meta['member_twitter']   = $_POST['member_twitter'];
  $team_member_meta['member_linkedin']  = $_POST['member_linkedin'];
  $team_member_meta['member_website']   = $_POST['member_website'];

  foreach( $team_member_meta as $key => $value ) {
    if( $post->post_type == "revision" ) {
      return;
    }

    if( get_post_meta($post->ID, $key, FALSE) ) {
      update_post_meta($post->ID, $key, $value);
    }
    else {
      add_post_meta($post->ID, $key, $value);
    }

    if( !$value ) {
      delete_post_meta($post->ID, $key);
    }
  }
}

add_action( 'save_post', 'save_kodo_team_member_meta', 1, 2 );

/**
 * Creates a shortcode for displaying members pictures in a grid
 */

function kodo_team_members_shortcode1( $atts ) {
  ob_start();

  extract( shortcode_atts( array (
    'responsable' => '',
  ), $atts ) );

  $query = new WP_Query( array(
    'post_type'             => 'kodo_team_member',
    'posts_per_page'        => -1,
    'order'                 => 'ASC',
    'orderby'               => 'title',
  ) );

  // Re-orders the array with the group manager at the beginning.
  $ordered = array();
  foreach( $query->posts as $p ) {
    if( $p->post_name == $responsable ) {
      array_unshift( $ordered, $p );
    }
    else {
      array_push( $ordered, $p );
    }
  }
  $query->posts = $ordered;

  if ( $query->have_posts() ) { ?>
    <div class="effects clearfix">
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <div class="img" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <?php if ( has_post_thumbnail() ) {
        	   the_post_thumbnail( array( 180, 180 ) );
        	} else { ?>
        	   <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/default-avatar.png" alt="<?php the_title(); ?>" />
        	<?php } ?>
            <p class="team-name">
              <?php if( get_post_meta( get_the_ID(), 'member_firstname', true ) != "" ) {
                echo esc_html( get_post_meta( get_the_ID(), 'member_firstname', true ) );
              } ?>
              <?php echo ' '; ?>
              <?php the_title(); ?>
            </p>
            <p class="team-links">
            <?php if( get_post_meta( get_the_ID(), 'member_twitter', true ) != "" ) { ?>
              <a class="noline" href="<?php echo esc_html( get_post_meta( get_the_ID(), 'member_twitter', true ) ); ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a>
            <?php } if( get_post_meta( get_the_ID(), 'member_linkedin', true ) != "" ) { ?>
              <a class="noline" href="<?php echo esc_html( get_post_meta( get_the_ID(), 'member_linkedin', true ) ); ?>"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
            <?php } if( get_post_meta( get_the_ID(), 'member_website', true ) != "" ) { ?>
              <a class="noline" href="<?php echo esc_html( get_post_meta( get_the_ID(), 'member_website', true ) ); ?>"><i class="fa fa-link" aria-hidden="true"></i></a>
            <?php } ?>
          </p>
        </div>
      <?php endwhile;
      wp_reset_postdata(); ?>
    </div>
    <div style="clear:both;"></div>
  <?php $myvariable = ob_get_clean();

  return $myvariable;
  }
}

add_shortcode( 'kodo-team-shortcode', 'kodo_team_members_shortcode1' );

/**
 * Changes the default title label for the kodo_team_member custom post type.
 */

function kodo_team_member_custom_enter_title( $input ) {
  global $post_type;

  if( is_admin() && 'kodo_team_member' == $post_type )
    return __( 'Indiquez uniquement le nom de famille', 'kodo' );

  return $input;
}

add_filter( 'enter_title_here', 'kodo_team_member_custom_enter_title' );

/**
 * Adds overlay effect script.
 */
function add_overlay_effect_script() {
  wp_enqueue_style( 'hover-effect', plugins_url( '/style.css' , __FILE__ ) );
}
add_action( 'init', 'add_overlay_effect_script' );

/**
 * Adds a thumbnail column in the admin panel.
 */
function add_kodo_team_member_columns($columns) {
  $new = array();
  foreach($columns as $key => $title) {
    if( $key == 'date' )
      $new['firstname'] = 'Prénom';
    if( $key == 'date' )
      $new['thumbnail'] = 'Photo';
    $new[$key] = $title;
  }
  return $new;
}

add_filter('manage_kodo_team_member_posts_columns' , 'add_kodo_team_member_columns');

/**
 * Adds a thumbnail column in the kodo_team_member list in admin panel.
 */
function thumbnail_custom_columns($column_name, $id) {
  switch( $column_name ) {
    case 'thumbnail':
      if( has_post_thumbnail() ) {
        the_post_thumbnail( array( 80, 80 ) );
      } else { ?>
        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/default-avatar.png" height="80" width="80" />
      <?php }
      break;
    case 'firstname':
        echo get_post_meta( $id, 'member_firstname', true );
      break;
  }
}

add_action('manage_kodo_team_member_posts_custom_column', 'thumbnail_custom_columns', 10, 2);
?>
