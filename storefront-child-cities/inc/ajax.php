<?php
/**
 * AJAX-поиск по городам, сбор таблицы через $wpdb
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Хэндлер AJAX — возвращаем HTML строк таблицы <tr>... для результата поиска.
 * Работает и для неавторизованных посетителей.
 */
add_action('wp_ajax_cities_search', 'storefront_child_cities_ajax_search');
add_action('wp_ajax_nopriv_cities_search', 'storefront_child_cities_ajax_search');

function storefront_child_cities_ajax_search()
{
    check_ajax_referer('cities_search_nonce', 'nonce');

    global $wpdb;

    $q = isset($_POST['q']) ? trim((string) $_POST['q']) : '';

    // Базовый SQL: city + связанный country (если есть)
    $sql = "
		SELECT p.ID AS city_id, p.post_title AS city_name, t.name AS country_name
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
		LEFT JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'country'
		LEFT JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
		WHERE p.post_type = 'city' AND p.post_status = 'publish'
	";

    $params = [];

    if ($q !== '') {
        // безопасный LIKE через prepare
        $like = '%' . $wpdb->esc_like($q) . '%';
        $sql .= " AND p.post_title LIKE %s ";
        $params[] = $like;
    }

    $sql .= " ORDER BY t.name ASC, p.post_title ASC LIMIT 200";

    $rows = ($params)
        ? $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A)
        : $wpdb->get_results($sql, ARRAY_A);

    // Собираем HTML строк таблицы
    ob_start();
    if ($rows) {
        foreach ($rows as $r) {
            $city_id = (int) $r['city_id'];
            $city_name = $r['city_name'] ?? '';
            $country_name = $r['country_name'] ?? '';

            $res = storefront_child_cities_get_temperature($city_id); // кэшировано

            echo '<tr>';
            echo '<td>' . esc_html($country_name ?: '—') . '</td>';
            echo '<td>' . esc_html($city_name) . '</td>';
            echo '<td>' . ($res['ok'] ? esc_html(number_format_i18n($res['temp'], 1) . ' °C') : '<span style="opacity:.7">—</span>') . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3"><em>' . esc_html__('No results', 'storefront-child-cities') . '</em></td></tr>';
    }

    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
}
