<?php
/**
 * Виджет: выбор города (CPT city) + текущая температура
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация виджета.
 */
add_action('widgets_init', function () {
    register_widget('Storefront_Child_Cities_Temp_Widget');
});

/**
 * Класс виджета
 */
class Storefront_Child_Cities_Temp_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'storefront_child_cities_temp_widget',
            __('City Temperature (Cities CPT)', 'storefront-child-cities'),
            ['description' => __('Показывает выбранный город и текущую температуру (OpenWeather).', 'storefront-child-cities')]
        );
    }

    /**
     * Вывод на фронте
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        $title = isset($instance['title']) ? $instance['title'] : '';
        $city_id = isset($instance['city_id']) ? (int) $instance['city_id'] : 0;

        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        if ($city_id > 0) {
            $city = get_post($city_id);
            if ($city && $city->post_type === 'city' && $city->post_status === 'publish') {
                $name = get_the_title($city_id);
                $res = storefront_child_cities_get_temperature($city_id);

                echo '<div class="city-temp-widget">';
                echo '<strong>' . esc_html($name) . '</strong><br/>';

                if ($res['ok']) {
                    // Экранируем и форматируем
                    echo '<span class="city-temp">' . esc_html(number_format_i18n($res['temp'], 1)) . '&nbsp;°C</span>';
                } else {
                    echo '<span class="city-temp-error" style="opacity:.7">' . esc_html__('Temperature unavailable', 'storefront-child-cities') . '</span>';
                }
                echo '</div>';
            } else {
                echo '<em>' . esc_html__('City not found', 'storefront-child-cities') . '</em>';
            }
        } else {
            echo '<em>' . esc_html__('City is not selected', 'storefront-child-cities') . '</em>';
        }

        echo $args['after_widget'];
    }

    /**
     * Настройки в админке
     */
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $city_id = isset($instance['city_id']) ? (int) $instance['city_id'] : 0;

        // Подгружаем список городов (только ID+title, оптимально)
        $cities = get_posts([
            'post_type' => 'city',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'orderby' => 'title',
            'order' => 'ASC',
            'fields' => 'ids',
        ]);

        $field_title = $this->get_field_id('title');
        $field_city = $this->get_field_id('city_id');
        $name_title = $this->get_field_name('title');
        $name_city = $this->get_field_name('city_id');

        ?>
        <p>
            <label
                for="<?php echo esc_attr($field_title); ?>"><?php esc_html_e('Title:', 'storefront-child-cities'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($field_title); ?>" name="<?php echo esc_attr($name_title); ?>"
                type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label
                for="<?php echo esc_attr($field_city); ?>"><?php esc_html_e('Select City:', 'storefront-child-cities'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($field_city); ?>" name="<?php echo esc_attr($name_city); ?>">
                <option value="0">—</option>
                <?php foreach ($cities as $cid): ?>
                    <option value="<?php echo (int) $cid; ?>" <?php selected($city_id, $cid); ?>>
                        <?php echo esc_html(get_the_title($cid)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    /**
     * Сохран настроек
     */
    public function update($new_instance, $old_instance)
    {
        return [
            'title' => sanitize_text_field($new_instance['title'] ?? ''),
            'city_id' => (int) ($new_instance['city_id'] ?? 0),
        ];
    }
}
