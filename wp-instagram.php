<?php
/**
 * Plugin Name: WP Instagram
 * Plugin URI:
 * Description: Developer-oriented plugin to handle authenticating a site with the Instagram API via oAuth 2.0
 * Version: 1.0
 * Author: Buckeye Interactive
 * Author URI: http://www.buckeyeinteractive.com
 * License: GPL2
 *
 * @package WP Instagram
 * @author Buckeye Interactive
 */

class WP_Instagram {

  /**
   * Class constructor
   * @return void
   * @since 1.0
   */
  public function __construct() {
    if ( is_admin() ) {
      add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
      add_action( 'admin_init', array( &$this, 'page_init' ) );
    }
  }

  /**
   * Register the theme options page within WordPress
   * @return void
   * @uses add_options_page()
   * @since 1.0
   */
  public function add_options_page() {
    add_options_page(
      __( 'Instagram Settings', 'wp-instagram' ),
      __( 'Instagram', 'wp-instagram' ),
      'manage_options',
      'wp-instagram',
      array( &$this, 'create_options_page' )
    );
  }

  /**
   * Generate the options page markup
   * @return void
   * @uses do_settings_sections()
   * @uses screen_icon()
   * @uses settings_fields()
   * @uses submit_button()
   * @since 1.0
   */
  public function create_options_page() {
    print '<div class="wrap">';
    screen_icon();
    printf( '<h2>%s</h2>', __( 'Instagram Settings', 'wp-instagram' ) );
    print '<form method="post" action="options.php">';
    settings_fields( 'wp_instagram' );
    do_settings_sections( 'wp-instagram' );
    submit_button();
    print '</form>';
    print '</div>';
  }

  /**
   * Initialize the options page
   * @return void
   * @uses add_settings_field()
   * @uses add_settings_section()
   * @uses register_setting()
   * @since 1.0
   */
  public function page_init() {
    register_setting( 'wp_instagram', 'wp_instagram' );

    add_settings_section(
      'wp_instagram_oauth',
      __( 'Authentication', 'wp-instagram' ),
      array( &$this, 'print_oauth_section_info' ),
      'wp-instagram'
    );
    add_settings_field(
      'wp_instagram_client_id',
      __( 'Client ID', 'wp-instagram' ),
      array( &$this, 'create_textfield' ),
      'wp-instagram',
      'wp_instagram_oauth',
      array( 'code' => true, 'name' => 'client_id' )
    );
    add_settings_field(
      'wp_instagram_client_secret',
      __( 'Client Secret', 'wp-instagram' ),
      array( &$this, 'create_textfield' ),
      'wp-instagram',
      'wp_instagram_oauth',
      array( 'code' => true, 'name' => 'client_secret', 'type' => 'password' )
    );
  }

  /**
   * Create an input[type="text"] element
   *
   * Accepted keys in $args:
   * code (bool) Apply pre-formatted code styling?
   * default (str) The default value
   * name (str) The key within the wp_instagram theme option
   * type (str) Input type (default: 'text')
   *
   * @param array $args
   * @return void
   * @since 1.0
   */
  public function create_textfield( $args=array() ) {
    $settings = get_option( 'wp_instagram' );
    $classes = ( isset( $args['code'] ) && $args['code'] ? 'regular-text code' : '' );
    $type = ( isset( $args['type'] ) && $args['type'] ? $args['type'] : 'text' );
    $default = ( isset( $args['default'] ) ? $args['default'] : '' );
    printf( '<input name="wp_instagram[%s]" type="%s" value="%s" class="%s" />', $args['name'], $type, ( isset( $settings[ $args['name'] ] ) ? $settings[ $args['name'] ] : $default ), $classes );
  }

  /**
   * Print instructions before the fields for the wp_instagram_oauth settings section
   * @return void
   * @since 1.0
   */
  public function print_oauth_section_info() {
    printf( '<p>%s</p>', __( 'Set your Instagram API credentials here. You can generate them by <a href="http://instagram.com/developer/clients/register/" target="_blank">creating a new client</a>.', 'wp-instagram' ) );
  }

}

/**
 * Bootstrap the plugin
 * @global $wp_instagram
 * @return void
 * @since 1.0
 */
function wp_instagram_init() {
  global $wp_instagram;
  $wp_instagram = new WP_Instagram;
}
add_action( 'init', 'wp_instagram_init' );