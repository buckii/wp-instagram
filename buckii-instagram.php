<?php
/**
 * Plugin Name: Buckii Instagram
 * Plugin URI:
 * Description: Developer-oriented plugin to handle authenticating a site with the Instagram API via oAuth 2.0
 * Version: 1.0
 * Author: Buckeye Interactive
 * Author URI: http://www.buckeyeinteractive.com
 * License: GPL2
 *
 * @package Buckii Instagram
 * @author Buckeye Interactive
 */

require_once dirname( __FILE__ ) . '/widget.php';

class Bii_Instagram {

  /**
   * @var public $api An instance of the Instagram class
   */
  public $api;

  /**
   * @var protected $options The bii_instagram options array from wp_options
   */
  protected $options;

  /** Current plugin version */
  const PLUGIN_VERSION = '1.0';

  /** Path to settings page relative to wp-admin/ */
  const SETTINGS_PAGE = 'options-general.php?page=bii-instagram';

  /** Name for our record in wp_options */
  const WP_OPTIONS_NAME = 'bii_instagram';

  /**
   * Class constructor
   * @return void
   * @since 1.0
   */
  public function __construct() {
    // Create an instance of the Instagram class for $this->api
    $this->api = $this->authenticate();

    // Register admin menus
    if ( is_admin() ) {
      add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
      add_action( 'admin_init', array( &$this, 'page_init' ) );
      add_filter( sprintf( 'plugin_action_links_%s', plugin_basename( __FILE__ ) ), array( &$this, 'add_settings_link_to_plugins_page' ) );
    }

    // Load internationalization
    load_plugin_textdomain( 'bii-instagram', null, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }

  /**
   * Setup/installation at plugin activation
   * @return void
   * @uses is_wp_error()
   */
  public static function activate() {
    if ( is_wp_error( self::load_instagram_class() ) ) {
      deactivate_plugins( plugin_basename( __FILE__ ) );
      $error_msg = __( 'Unable to load <code>Instagram</code> PHP class, most likely due to the git submodule not being present. Please view the README file for solutions. In the meantime the plugin has been deactivated.', 'bii-instagram' );
      $error_msg .= sprintf( '<br><br><a href="%s">%s</a>', admin_url( 'plugins.php' ), __( 'Return to the plugins page', 'bii-instagram' ) );
      wp_die( $error_msg );
      return;
    }
    return true;
  }

  /**
   * Register the theme options page within WordPress
   * @return void
   * @uses add_options_page()
   * @since 1.0
   */
  public function add_options_page() {
    add_options_page(
      __( 'Instagram Settings', 'bii-instagram' ),
      __( 'Instagram', 'bii-instagram' ),
      'manage_options',
      'bii-instagram',
      array( &$this, 'create_options_page' )
    );
  }

  /**
   * Add a settings link to the plugins page
   * @param array $links Links to output
   * @return array
   */
  public function add_settings_link_to_plugins_page( $links ) {
    array_unshift( $links, sprintf( '<a href="%s">%s</a>', self::SETTINGS_PAGE, __( 'Settings', 'bii-instagram' ) ) );
    return $links;
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
    printf( '<h2>%s</h2>', __( 'Instagram Settings', 'bii-instagram' ) );
    print '<form method="post" action="options.php">';
    settings_fields( 'bii_instagram' );
    do_settings_sections( 'bii-instagram' );
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
    register_setting( 'bii_instagram', 'bii_instagram' );

    add_settings_section(
      'bii_instagram_oauth',
      __( 'Authentication', 'bii-instagram' ),
      array( &$this, 'print_oauth_section_info' ),
      'bii-instagram'
    );
    add_settings_field(
      'bii_instagram_client_id',
      __( 'Client ID', 'bii-instagram' ),
      array( &$this, 'create_textfield' ),
      'bii-instagram',
      'bii_instagram_oauth',
      array( 'code' => true, 'name' => 'client_id' )
    );
    add_settings_field(
      'bii_instagram_client_secret',
      __( 'Client Secret', 'bii-instagram' ),
      array( &$this, 'create_textfield' ),
      'bii-instagram',
      'bii_instagram_oauth',
      array( 'code' => true, 'name' => 'client_secret', 'type' => 'password' )
    );
  }

  /**
   * Create an input[type="text"] element
   *
   * Accepted keys in $args:
   * code (bool) Apply pre-formatted code styling?
   * default (str) The default value
   * name (str) The key within the bii_instagram theme option
   * type (str) Input type (default: 'text')
   *
   * @param array $args
   * @return void
   * @since 1.0
   */
  public function create_textfield( $args=array() ) {
    $settings = get_option( 'bii_instagram' );
    $classes = ( isset( $args['code'] ) && $args['code'] ? 'regular-text code' : '' );
    $type = ( isset( $args['type'] ) && $args['type'] ? $args['type'] : 'text' );
    $default = ( isset( $args['default'] ) ? $args['default'] : '' );
    printf( '<input name="bii_instagram[%s]" type="%s" value="%s" class="%s" />', $args['name'], $type, ( isset( $settings[ $args['name'] ] ) ? $settings[ $args['name'] ] : $default ), $classes );
  }

  /**
   * Get an option from $this->options
   * @param str $key The option to retrieve
   * @return mixed
   * @uses get_option
   * @since 1.0
   */
  public function get_option( $key='' ) {
    // Load our options array
    if ( ! $this->options ) {
      $this->options = get_option( self::WP_OPTIONS_NAME );
    }
    return ( isset( $this->options[ $key ] ) ? $this->options[ $key ] : '' );
  }

  /**
   * Print instructions before the fields for the bii_instagram_oauth settings section
   * @return void
   * @since 1.0
   */
  public function print_oauth_section_info() {
    printf( '<p>%s</p>', __( 'Set your Instagram API credentials here. You can generate them by <a href="http://instagram.com/developer/clients/register/" target="_blank">creating a new client</a>.', 'bii-instagram' ) );
    printf( '<p>%s</p>', sprintf( __( 'Your OAuth <code>redirect_uri</code> attribute should be: %s', 'bii-instagram' ), '<br /><kbd>' . admin_url( self::SETTINGS_PAGE ) . '</kbd>' ) );
    if ( $this->get_option( 'client_id' ) ) {
      printf( '<p><a href="%s" class="button">%s</a></p>', $this->api->getLoginUrl(), __( 'Authenticate with Instagram', 'bii-instagram' ) );
    }
  }

  /**
   * Uninstall the plugin
   * @return void
   * @uses delete_option()
   * @since 1.0
   */
  public static function uninstall() {
    delete_option( 'bii_instagram' );
  }

  /**
   * Load Christian's Instagram class
   * @return mixed True if loaded, WP_Error object if something goes wrong
   * @since 1.0
   */
  protected function load_instagram_class() {
    $path = dirname( __FILE__ ) . '/lib/Instagram-PHP-API/instagram.class.php';
    if ( file_exists( $path ) ) {
      require_once $path;
      return true;
    }
    return new WP_Error( 'dependencies_missing', __( 'Unable to load Instagram class', 'bii-instagram' ) );
  }

  /**
   * Create and return a new instance of the Instagram class
   * @return Instagram object
   * @since 1.0
   */
  protected function authenticate() {
    $this->load_instagram_class();
    $instagram = new Instagram( array(
      'apiKey' => $this->get_option( 'client_id' ),
      'apiSecret' => $this->get_option( 'client_secret' ),
      'apiCallback' => admin_url( self::SETTINGS_PAGE )
    ) );

    if ( $token = $this->get_option( 'oauth_token' ) ) {
      $instagram->setAccessToken( $token );

    } elseif ( isset( $_GET['code'] ) ) {
      // Clear $this->options so we have to reload them
      $this->options = null;
      $options = get_option( self::WP_OPTIONS_NAME );
      $options['oauth_token'] = $instagram->getOAuthToken( $_GET['code'], true );
      update_option( self::WP_OPTIONS_NAME, $options );
    }
    return $instagram;
  }

}

/**
 * Bootstrap the plugin
 * @global $bii_instagram
 * @return void
 * @since 1.0
 */
function bii_instagram_init() {
  global $bii_instagram;
  $bii_instagram = new Bii_Instagram;
}
add_action( 'init', 'bii_instagram_init' );

/**
 * Register activation and uninstallation hooks
 */
register_activation_hook( __FILE__, array( 'Bii_Instagram', 'activate' ) );
register_uninstall_hook( __FILE__, array( 'Bii_Instagram', 'uninstall' ) );