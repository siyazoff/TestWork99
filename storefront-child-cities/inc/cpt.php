<?php
/**
 * Регистрация кастомного типа записи "Cities"
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрируем CPT city
 * - оптимизировано: supports только title, REST включён
 */
add_action('init', function () {

    $labels = [
        'name' => __('Cities', 'storefront-child-cities'),
        'singular_name' => __('City', 'storefront-child-cities'),
        'add_new' => __('Add New City', 'storefront-child-cities'),
        'add_new_item' => __('Add New City', 'storefront-child-cities'),
        'edit_item' => __('Edit City', 'storefront-child-cities'),
        'new_item' => __('New City', 'storefront-child-cities'),
        'all_items' => __('All Cities', 'storefront-child-cities'),
        'view_item' => __('View City', 'storefront-child-cities'),
        'search_items' => __('Search Cities', 'storefront-child-cities'),
        'not_found' => __('No cities found', 'storefront-child-cities'),
        'not_found_in_trash' => __('No cities found in Trash', 'storefront-child-cities'),
        'menu_name' => __('Cities', 'storefront-child-cities'),
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'has_archive' => true,
        'hierarchical' => false,
        'rewrite' => ['slug' => 'cities'],
        'menu_icon' => 'dashicons-location',
        'supports' => ['title'],
    ];

    register_post_type('city', $args);
});
