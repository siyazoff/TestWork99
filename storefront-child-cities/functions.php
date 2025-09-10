<?php
/**
 * Storefront Child Cities — bootstrap
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * стили родителя и ребёнка.
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css', [], null);
    wp_enqueue_style('storefront-child-cities', get_stylesheet_uri(), ['storefront-style'], '1.0.0');
}, 20);

/**
 * ключ OpenWeather.
 */
if (!defined('OPENWEATHER_API_KEY')) {
    define('OPENWEATHER_API_KEY', 'f35aad4ce0e7cc4ba9ab0447f0b50ca9');
}

/**
 *  модули.
 */
require_once __DIR__ . '/inc/cpt.php';
require_once __DIR__ . '/inc/taxonomy.php';
require_once __DIR__ . '/inc/meta.php';
require_once __DIR__ . '/inc/api.php';
require_once __DIR__ . '/inc/widget-cities-temp.php';
require_once __DIR__ . '/inc/ajax.php';


add_action('wp_enqueue_scripts', function () {
    if (is_page_template('templates/page-cities-table.php')) {
        wp_enqueue_script(
            'cities-search',
            get_stylesheet_directory_uri() . '/assets/js/cities-search.js',
            ['jquery'],
            '1.0.0',
            true
        );
        wp_localize_script('cities-search', 'CitiesSearch', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cities_search_nonce'),
        ]);
    }
}, 30);



add_action('init', function () {
    error_log('OWM KEY: ' . OPENWEATHER_API_KEY);
});
