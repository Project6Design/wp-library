<?php

namespace Project6;

use Project6\PostTypes\PostType;
use Timber\Site as BaseSite;

abstract class Site extends BaseSite
{
  protected $postTypes = [];

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Initialize the site.
   */
  public function init()
  {
    if (function_exists('acf_add_options_page')) {
      acf_add_options_page();
    }

    add_filter('timber_context', [$this, 'addToContext']);
    add_filter('get_twig', [$this, 'addToTwig']);
    add_filter('login_headerurl', [$this, 'logoUrl']);
    add_filter('default_hidden_meta_boxes', [$this, 'hideMetaBox'], 10, 2);

    add_action('init', [$this, 'removeRoles']);
    add_action('after_setup_theme', [$this, 'themeSupports']);
    add_action('login_head', [$this, 'loginStylesheet']);

    $this->registerPostTypes();
    $this->registerRoutes();
  }


  /**
   * Add a new post type to the site
   * @param PostType $postType
   */
  public function addPostType(PostType $postType)
  {
    array_push($this->postTypes, $postType);
  }

  /**
   * Register the post types within Wordpress
   */
  public function registerPostTypes()
  {
    if (count($this->postTypes)) {
      foreach ($this->postTypes as $postType) {
        // Create the post type and related taxonomies.
        add_action('init', [$postType, 'register']);
      }
    }
  }

  /**
   * Register post type shortcodes and custom.
   */
  public function registerShortCodes()
  {
    // Custom shortcodes.


    // Post type short codes.
  }

  /**
   * Register post type image size and custom.
   */
  public function registerImageSizes()
  {

  }

  /**
   * Register post type routes and custom.
   */
  public function registerRoutes()
  {
    // Handle exposed filter form submits.
    \Routes::map('forms/exposed-filters', function () {
      $filters = [];

      $valid_fields = [
        'filter_name'
      ];

      foreach ($_REQUEST as $name => $value) {
        if (!empty($value) && in_array($name, $valid_fields)) {
          if (is_array($value)) {
            $filters[$name] = implode('+', $value);
          }
          else {
            $filters[$name] = $value;
          }
        }
      }

      $redirect = $_REQUEST['redirect'];

      if ($filters) {
        $redirect .= '?' . http_build_query($filters);
      };

      // In case there are no files or the user doesn't have access.
      wp_redirect($redirect);
      exit();
    });
  }

  /**
   * Timber hook. Add more items to the global context
   * @param type $context
   */
  public function addToContext($context)
  {
    // $main_menu = new \TimberMenu(2);
    // $footer_menu = new \TimberMenu(3);

    // $context['menu']['main'] = $main_menu->get_items();
    // $context['menu']['footer'] = $footer_menu->get_items();

    // $context['menu']['mobile'] = array_merge($main_menu->get_items(), $footer_menu->get_items());

    // Add ACF options.
    $context['options'] = get_fields('options');

    $context['site'] = $this;
    $context['site']->live = isset($_SERVER['PANTHEON_ENVIRONMENT']) && $_SERVER['PANTHEON_ENVIRONMENT'] === 'live';

    return $context;
  }

  /**
   * Timber hook. Extend Twig functionality.
   * @param type $twig
   */
  public function addToTwig($twig)
  {

    $twig->addExtension( new \Twig_Extension_StringLoader() );

    // Convert all internal absolute links into relative links.
    $twig->addFilter('relative_links', new \Twig_SimpleFilter('relative_links', array($this, 'relativeLinks')));

    return $twig;
  }

  /**
   * Remove unused roles from Wordpress.
   */
  public function removeRoles()
  {
    if (get_role('author')) {
      remove_role('author');
    }

    if (get_role('contributor')) {
      remove_role('contributor');
    }

    if (get_role('subscriber')) {
      remove_role('subscriber');
    }

    if (get_role('wpseo_manager')) {
      remove_role('wpseo_manager');
    }

    if (get_role('wpseo_editor')) {
      remove_role('wpseo_editor');
    }
  }

  /**
   * Hide meta boxes in the backend.
   * @param type $hidden
   * @param type $screen
   * @return type
   */
  public function hideMetaBox($hidden, $screen)
  {
    $post_types = [
      'person'
    ];

    $hidden = [];

    if ( ('post' == $screen->base) && in_array($screen->id, $post_types) ){
      //lets hide everything
      $hidden = [
          'person_rolediv'
      ];
    }

    return $hidden;
  }

  public function relativeLinks( $text ) {
    $targets = [
      'https://live-SITE.pantheonsite.io',
      'https://test-SITE.pantheonsite.io',
      'https://dev-SITE.pantheonsite.io',
      'https://SITE.lndo.site'
    ];

    $text = str_replace($targets, '', $text);

    return $text;
  }

  public function themeSupports() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support( 'title-tag' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support(
      'html5', array(
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
      )
    );

    /*
     * Enable support for Post Formats.
     *
     * See: https://codex.wordpress.org/Post_Formats
     */
    add_theme_support(
      'post-formats', array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'audio',
      )
    );

    add_theme_support( 'menus' );
  }

  public function loginStylesheet() {
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/css/site.css" />';
  }
}
