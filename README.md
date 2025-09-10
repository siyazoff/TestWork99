# TestWork99

Проект реализован как **дочерняя тема** WordPress на базе [Storefront](https://wordpress.org/themes/storefront/).  
Вся функциональность вынесена в отдельные модули в папке `/inc` для удобства поддержки и расширяемости.

### Основные компоненты:

- **CPT (Custom Post Type)**: `Cities`
- **Custom Taxonomy**: `Countries` (иерархическая)
- **Meta Box**: для хранения `latitude` и `longitude` у каждого города
- **Widget**: выводит выбранный город и температуру с OpenWeather API
- **Custom Page Template**: таблица стран/городов с AJAX-поиском
- **API-обёртка**: модуль для интеграции с OpenWeather
- **AJAX endpoint**: для поиска по городам и обновления таблицы
- **Custom hooks**: `cities_table_before` и `cities_table_after` для расширяемости

---
