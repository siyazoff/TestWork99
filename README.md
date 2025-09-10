# TestWork99 — Техническая документация

## 1. Архитектура проекта

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

## 2. Структура файлов

storefront-child-cities/
├── style.css # Заголовок темы (Template: storefront)
├── functions.php # Подключение всех модулей, скриптов, ключа API
├── inc/
│ ├── cpt.php # Регистрация CPT "Cities"
│ ├── taxonomy.php # Регистрация таксономии "Countries"
│ ├── meta.php # Метабокс с latitude и longitude
│ ├── api.php # Работа с OpenWeather API + кэширование
│ ├── widget-cities-temp.php# Реализация WP_Widget для температуры
│ └── ajax.php # AJAX-поиск городов, SQL через $wpdb
├── assets/
│ └── js/
│ └── cities-search.js # JS для AJAX-поиска (debounce + jQuery)
└── templates/
└── page-cities-table.php # Шаблон страницы с таблицей
