<?php
/**
 * Base class for all post types.
 */

namespace Project6\PostTypes;

abstract class PostType
{
  /**
   * Register the post type and related taxonomy.
   */
  abstract public function register();

  /**
   * Identify yourself.
   */
  abstract public function label();


  /**
   * Define shortcodes
   *
   * Example array:
   * [
   *  'team_listing', [$this, 'teamListing']
   * ]
   *
   *
   * @return array Shortcode definition with name and function definition.
   */
  public function shortcodes()
  {
    return [];
  }

  /**
   * Define image sizes.
   *
   * Example array:
   * [
   *  'person-lg' => [
   *    'width' => 460,
   *    'height' => 460,
   *    'crop' => true
   *  ]
   * ]
   *
   * @see https://developer.wordpress.org/reference/functions/add_image_size
   *
   * @return array Image sizes with add_image_size parameters.
   */
  public function imageSizes()
  {
    return [];
  }
}
