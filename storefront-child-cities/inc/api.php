<?php
/**
 * Работа с OpenWeather API, кэширование через Transients
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Получить текущую температуру для города по post_id (CPT city).
 * Результат кэшируется (по умолчанию 10 минут).
 *
 * @param int  $post_id
 * @param bool $force_refresh  Принудительно обновить кэш
 * @return array{ok:bool,temp:float|null,error:string|null}
 */
function storefront_child_cities_get_temperature(int $post_id, bool $force_refresh = false): array
{

    $lat = get_post_meta($post_id, '_city_latitude', true);
    $lon = get_post_meta($post_id, '_city_longitude', true);

    if ($lat === '' || $lon === '') {
        return ['ok' => false, 'temp' => null, 'error' => 'Missing coordinates'];
    }

    $cache_key = 'owm_temp_' . $post_id;
    if (!$force_refresh) {
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return ['ok' => true, 'temp' => (float) $cached, 'error' => null];
        }
    }

    $api_key = (string) OPENWEATHER_API_KEY;
    if ($api_key === '') {
        return ['ok' => false, 'temp' => null, 'error' => 'No API key'];
    }

    $url = add_query_arg([
        'lat' => $lat,
        'lon' => $lon,
        'appid' => $api_key,
        'units' => 'metric',
    ], 'https://api.openweathermap.org/data/2.5/weather');

    $response = wp_remote_get($url, [
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        return ['ok' => false, 'temp' => null, 'error' => $response->get_error_message()];
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        return ['ok' => false, 'temp' => null, 'error' => 'HTTP ' . $code];
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!is_array($data) || !isset($data['main']['temp'])) {
        return ['ok' => false, 'temp' => null, 'error' => 'Malformed API response'];
    }

    $temp = (float) $data['main']['temp'];

    // Кэш на 10 минут
    set_transient($cache_key, $temp, 10 * MINUTE_IN_SECONDS);

    return ['ok' => true, 'temp' => $temp, 'error' => null];
}
