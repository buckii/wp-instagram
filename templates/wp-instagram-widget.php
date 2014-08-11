<?php
/**
 * Default template for WP Instagram sidebar widgets
 *
 * @package Buckii Instagram
 * @author Buckeye Interactive
 */

global $bii_instagram;
$media = $bii_instagram->api->getUserMedia();

?>

<div class="bii-instagram-photos">

<?php if ( $media->data ) : ?>
  <ul class="bii-instagram-photos">
  <?php foreach ( $media->data as $photo ) : ?>

    <li>
      <a href="<?php echo $photo->link; ?>" title="<?php echo esc_attr( $photo->caption->text ); ?>" rel="canonical">
        <img src="<?php echo $photo->images->low_resolution->url; ?>" alt="" />
      </a>
    </li>

    <?php endforeach; ?>
  </ul>
<?php endif; ?>

</div><!-- .bii-instagram-photos -->