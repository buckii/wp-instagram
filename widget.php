<?php
/**
 * Instagram widget
 *
 * @package Buckii Instagram
 * @author Buckeye Interactive
 */

class Bii_Instagram_Widget extends WP_Widget {

  /**
   * Widget constructor
   * @see WP_Widget::__construct()
   * @since 1.0
   */
  public function __construct() {
    parent::__construct( 'bii_instagram', __( 'Instagram Feed', 'bii-instagram' ),
      array( 'description' => 'Embed your photos from Instagram', 'bii-instagram' ),
      array( 'width' => 350 )
    );
  }

  /**
   * Front-end display of widget.
   * @param array $args Widget arguments.
   * @param array $instance Saved values from database.
   * @return void
   * @see WP_Widget::widget()
   * @since 1.0
   *
   * @todo Actual template loading, not just an include
   */
  public function widget( $args, $instance ) {
    global $bii_instagram;
    if ( ! $bii_instagram instanceof Bii_Instagram ) {
      $bii_instagram = new Bii_Instagram;
    }

    echo $args['before_widget'];
    include dirname( __FILE__ ) . '/templates/bii-instagram-widget.php';
    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   * @param array $instance Previously saved values from database.
   * @return void
   * @see WP_Widget::form()
   * @since 1.0
   */
  public function form( $instance ) {
    print '<p>';
    printf( '<label for="%s">%s</label>', $this->get_field_id( 'limit' ), __( 'Limit:', 'bii-instagram' ) );
    printf( '<input name="%s" id="%s" type="text" class="" value="%s" />', $this->get_field_name( 'limit' ), $this->get_field_id( 'limit' ), ( isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : '' ) );
    print '</p>';
  }

  /**
   * Sanitize widget form values as they are saved.
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   * @return array Updated safe values to be saved.
   * @see WP_Widget::update()
   * @since 1.0
   */
  public function update( $new_instance, $old_instance ) {
    return array(
      'limit' => intval( $new_instance['limit'] )
    );
  }

}

/**
 * Register our widget on widgets_init
 * @uses register_widget
 * @since 1.0
 */
function bii_instagram_register_widget() {
  register_widget( 'Bii_Instagram_Widget' );
}
add_action( 'widgets_init', 'bii_instagram_register_widget' );