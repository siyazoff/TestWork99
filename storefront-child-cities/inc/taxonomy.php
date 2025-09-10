<?php
/**
 * Регистрация таксономии "Countries" для CPT city
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {

    $labels = [
        'name' => __('Countries', 'storefront-child-cities'),
        'singular_name' => __('Country', 'storefront-child-cities'),
        'search_items' => __('Search Countries', 'storefront-child-cities'),
        'all_items' => __('All Countries', 'storefront-child-cities'),
        'parent_item' => __('Parent Country', 'storefront-child-cities'),
        'parent_item_colon' => __('Parent Country:', 'storefront-child-cities'),
        'edit_item' => __('Edit Country', 'storefront-child-cities'),
        'update_item' => __('Update Country', 'storefront-child-cities'),
        'add_new_item' => __('Add New Country', 'storefront-child-cities'),
        'new_item_name' => __('New Country Name', 'storefront-child-cities'),
        'menu_name' => __('Countries', 'storefront-child-cities'),
    ];

    register_taxonomy('country', ['city'], [
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'countries'],
    ]);
});
