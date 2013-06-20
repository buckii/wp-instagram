<?php
/**
 * Default template for WP Instagram sidebar widgets
 *
 * @package WP Instagram
 * @author Buckeye Interactive
 */

global $wp_instagram;
$media = $wp_instagram->api->getUserMedia();

?>

<div class="wp-instagram-photos">

<?php if ( $media->data ) : ?>
  <ul class="wp-instagram-photos">
  <?php foreach ( $media->data as $photo ) : ?>

    <li>
      <a href="<?php echo $photo->link; ?>" title="<?php echo esc_attr( $photo->caption->text ); ?>" rel="canonical">
        <img src="<?php echo $photo->images->low_resolution->url; ?>" alt="" />
      </a>
    </li>

    <?php endforeach; ?>
  </ul>
<?php endif; ?>

</div><!-- .wp-instagram-photos -->