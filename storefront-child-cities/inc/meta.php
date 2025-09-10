<?php
/**
 * Метабоксы координат для Cities
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавляем метабокс "Coordinates" для CPT city
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'city_coords',
        __('City Coordinates', 'storefront-child-cities'),
        'storefront_child_cities_coords_metabox_cb',
        'city',
        'normal',
        'default'
    );
});

/**
 * @param WP_Post $post
 */
function storefront_child_cities_coords_metabox_cb($post)
{
    wp_nonce_field('save_city_coords', 'city_coords_nonce');

    $lat = get_post_meta($post->ID, '_city_latitude', true);
    $lon = get_post_meta($post->ID, '_city_longitude', true);
    ?>
    <p>
        <label
            for="city_latitude"><strong><?php esc_html_e('Latitude', 'storefront-child-cities'); ?></strong></label><br />
        <input type="text" id="city_latitude" name="city_latitude" value="<?php echo esc_attr($lat); ?>"
            placeholder="e.g. 51.1694" style="width: 240px;" />
    </p>
    <p>
        <label
            for="city_longitude"><strong><?php esc_html_e('Longitude', 'storefront-child-cities'); ?></strong></label><br />
        <input type="text" id="city_longitude" name="city_longitude" value="<?php echo esc_attr($lon); ?>"
            placeholder="e.g. 71.4491" style="width: 240px;" />
    </p>
    <p style="opacity:.8">
        <?php esc_html_e('Введите широту и долготу города. Значения будут использованы для получения текущей температуры.', 'storefront-child-cities'); ?>
    </p>
    <?php
}

add_action('save_post_city', function ($post_id, $post, $update) {

    if (!isset($_POST['city_coords_nonce']) || !wp_verify_nonce($_POST['city_coords_nonce'], 'save_city_coords')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $lat = isset($_POST['city_latitude']) ? trim((string) $_POST['city_latitude']) : '';
    $lon = isset($_POST['city_longitude']) ? trim((string) $_POST['city_longitude']) : '';

    $lat = ($lat !== '') ? (string) floatval(str_replace(',', '.', $lat)) : '';
    $lon = ($lon !== '') ? (string) floatval(str_replace(',', '.', $lon)) : '';

    update_post_meta($post_id, '_city_latitude', $lat);
    update_post_meta($post_id, '_city_longitude', $lon);

    delete_transient('owm_temp_' . $post_id);

}, 10, 3);
