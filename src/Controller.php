<?php

namespace Project6;

use Project6\PostTypes\PostType;

abstract class Controller
{
  protected $postTypes = [];

  public function __construct()
  {
    add_filter('default_hidden_meta_boxes', [$this, 'hideMetaBox'], 10, 2);
    add_action('init', [$this, 'removeRoles']);
    add_action('admin_menu', [$this, 'hideDefaultPostType']);

    add_filter('timber_context', [$this, 'addToContext']);
    add_filter('get_twig', [$this, 'addToTwig']);

    // Add ACF options page.
    acf_add_options_page();
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

    // if ( ('post' == $screen->base) && in_array($screen->id, $post_types) ){
    //   //lets hide everything
    //   $hidden = [
    //       'person_rolediv'
    //   ];
    // }

    return $hidden;
  }

  public function hideDefaultPostType()
  {
    remove_menu_page('edit.php');
  }

  public function addToContext($context)
  {
    $main_menu = new \TimberMenu();

    $context['menu']['main'] = $main_menu->get_items();
    $context['menu']['mobile'] = $main_menu->get_items();

    // Add ACF options.
    $context['options'] = get_fields('options');

    $context['controller'] = $this;

    return $context;
  }

  public function environment()
  {
    $env = 'local';

    if (isset($_SERVER['PANTHEON_ENVIRONMENT'])) {
      $env = $_SERVER['PANTHEON_ENVIRONMENT'];
    }

    return $env;
  }

  public function addToTwig( $twig ) {

    $twig->addExtension( new \Twig_Extension_StringLoader() );

    // Convert all internal absolute links into relative links.
    $twig->addFilter('relative_links', new \Twig_SimpleFilter('relative_links', array($this, 'relativeLinks')));

    return $twig;
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
}
