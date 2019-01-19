<?php

namespace Project6\PostTypes;

use Project6\PostTypes\PostType;

class Person extends PostType {

  public function label()
  {
    return 'person';
  }

  public function register()
  {
    register_post_type('person',
      [
        'labels' => [
          'name' => __('Team'),
          'singular_name' => __('Person'),
          'add_new' => __('Add New Person'),
          'add_new_item' => __('Add Person'),
          'edit' => __('Edit'),
          'edit_item' => __('Edit Person'),
          'new_item' => __('New Person'),
          'view' => __('View Person'),
          'view_item' => __('View Person'),
          'search_items' => __('Search Team'),
          'not_found' => __('No team members found'),
          'not_found_in_trash' => __('No team members found in Trash')
        ],
        'public' => true,
        'hierarchical' => true,
        'has_archive' => false,
        'supports' => [
          'editor',
          'revisions',
          'author'
        ],
        'can_export' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-groups',
        'rewrite' => [
          'slug' => 'team',
          'with_front' => false,
          'hierarchical' => true
        ],
      ]
    );


    // add_filter('wp_insert_post_data', [$this, 'updatePersonTitle'], '99', 2);
  }

}
