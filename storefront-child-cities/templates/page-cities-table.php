<?php
/**
 * Template Name: Cities Table
 * Description: Таблица стран/городов/температуры с поиском (AJAX) и $wpdb.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Начальная выборка таблицы — напрямую из БД через $wpdb (без поиска)
global $wpdb;

$sql = "
	SELECT p.ID AS city_id, p.post_title AS city_name, t.name AS country_name
	FROM {$wpdb->posts} p
	LEFT JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
	LEFT JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'country'
	LEFT JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
	WHERE p.post_type = 'city' AND p.post_status = 'publish'
	ORDER BY t.name ASC, p.post_title ASC
	LIMIT 200
";

$rows = $wpdb->get_results($sql, ARRAY_A);
?>

<div class="storefront-child-cities-table container" style="max-width:980px;margin:40px auto;">
    <h1><?php echo esc_html(get_the_title()); ?></h1>

    <!-- Custom action hook: before table -->
    <?php do_action('cities_table_before'); ?>

    <div style="margin:16px 0;">
        <label for="cities-search-input"
            style="display:block;font-weight:600;margin-bottom:6px;"><?php esc_html_e('Search cities', 'storefront-child-cities'); ?></label>
        <input type="search" id="cities-search-input"
            placeholder="<?php esc_attr_e('Type city name...', 'storefront-child-cities'); ?>"
            style="width:100%;max-width:420px;padding:8px 10px;">
    </div>

    <div class="table-wrap" style="overflow:auto;">
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th><?php esc_html_e('Country', 'storefront-child-cities'); ?></th>
                    <th><?php esc_html_e('City', 'storefront-child-cities'); ?></th>
                    <th><?php esc_html_e('Temperature', 'storefront-child-cities'); ?></th>
                </tr>
            </thead>
            <tbody id="cities-table-body">
                <?php
                if ($rows) {
                    foreach ($rows as $r) {
                        $city_id = (int) ($r['city_id'] ?? 0);
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
                    echo '<tr><td colspan="3"><em>' . esc_html__('No data', 'storefront-child-cities') . '</em></td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Custom action hook: after table -->
    <?php do_action('cities_table_after'); ?>

</div>

<?php
get_footer();
