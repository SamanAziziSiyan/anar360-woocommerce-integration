<?php
$is_activated = awca_check_activation_state();
add_action('admin_menu', 'awca_create_plugin_menu');

function awca_create_plugin_menu()
{
    // add_menu_page(
    //     __('داشبورد انار', 'awca-360'),
    //     __('داشبورد انار', 'awca-360'),
    //     'manage_options',
    //     'awca-dashboard-menu',
    //     'awca_dashboard_menu_content',
    //     'dashicons-admin-generic',
    //     10
    // );

    add_menu_page(
        // 'awca-dashboard-menu',
        __('پیکربندی انار', 'awca-360'),
        __('پیکربندی انار', 'awca-360'),
        'manage_options',
        'awca-configuration-menu',
        'awca_configuration_menu_content',
        ANAR_WC_API_PLUGIN_URL . 'admin/images/icon.png',
        10
    );
    // add_submenu_page(
    //     'awca-dashboard-menu',
    //     __('سفارشات انار', 'awca-360'),
    //     __('سفارشات انار', 'awca-360'),
    //     'manage_options',
    //     'awca-orders-menu',
    //     'awca_orders_menu_content'
    // );
    // add_submenu_page(
    //     'awca-dashboard-menu',
    //     __('تنظیمات انار', 'awca-360'),
    //     __('تنظیمات انار', 'awca-360'),
    //     'manage_options',
    //     'awca-settings-menu',
    //     'awca_settings_menu_content'
    // );
    // add_submenu_page(
    //     'awca-dashboard-menu',
    //     __('مالی انار', 'awca-360'),
    //     __('مالی انار', 'awca-360'),
    //     'manage_options',
    //     'awca-financial-menu',
    //     'awca_financial_menu_content'
    // );
}

function awca_dashboard_menu_content()
{
    include ANAR_WC_API_ADMIN . 'partials/menu-contents/general-menus/dashboard-menu-content.php';
}

function awca_configuration_menu_content()
{
    include ANAR_WC_API_ADMIN . 'partials/menu-contents/general-menus/configuration-menu-content.php';
}

function awca_settings_menu_content()
{
    global $is_activated;
    if (!$is_activated) {
        wp_redirect('?page=awca-configuration-menu');
        exit;
    } else {
        include ANAR_WC_API_ADMIN . 'partials/menu-contents/general-menus/settings-menu-content.php';
    }
}
function awca_financial_menu_content()
{
    global $is_activated;

    if (!$is_activated) {
        wp_redirect('?page=awca-configuration-menu');
        exit;
    } else {
        include ANAR_WC_API_ADMIN . 'partials/menu-contents/general-menus/financial-menu-content.php';
    }
}
function awca_orders_menu_content()
{
    global $is_activated;
    if (!$is_activated) {
        wp_redirect('?page=awca-configuration-menu');
        exit;
    } else {
        include ANAR_WC_API_ADMIN . 'partials/menu-contents/general-menus/orders-menu-content.php';
    }
}
