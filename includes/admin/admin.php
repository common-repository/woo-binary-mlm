<?php
class BMW_Admin_Menu
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_script'));
    }
    public function enqueue_script()
    {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_register_script('bmw-admin-js', BMW_URL . '/assets/js/ajax.js', array(), false,  true);
        wp_enqueue_script('bmw-admin-js');

        wp_register_script('bmw-admin-bonus-js', BMW_URL . '/assets/js/bonus.js', array(), false,  true);
        wp_enqueue_script('bmw-admin-bonus-js');
    }

    public function bmw_admin_menupage()
    {

        $icon_url = BMW_URL . '/assets/images/mlm_tree.png';
        add_menu_page('Binary MLM Woocommerce', 'Binary MLM Woocommerce', 'administrator', 'dashboard-page', array($this, 'bmwShowDashboard'), $icon_url);
    }
    public function bmwShowDashboard()
    {
        global $pagenow;
        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'settings')
            $current = 'settings';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'point-value')
            $current = 'point-value';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'payout-reports')
            $current = 'payout-reports';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'member-info')
            $current = 'member-info';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'upgrade-pluging')
            $current = 'upgrade-pluging';
        else
            $current = 'settings';

        echo '<div class="wrap">';
        echo '<div id="icon-themes" class="icon32"></div>';
        echo '<h1>' . __('Binary MLM Woocommerce Settings', 'bmw') . '</h1>';

        echo '<h2 class="nav-tab-wrapper">';
        $bmw_settings = array('settings' => 'Settings', 'point-value' => 'Product Points', 'payout-reports' => 'Payout & Reports', 'member-info' => 'Members Info', 'upgrade-pluging' => 'Go to Pro');
        foreach ($bmw_settings as $setting => $name) {
            $class = ($setting == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=dashboard-page&bmw-setting=$setting'>" . __("$name", 'bmw') . "</a>";
        }
        echo '</h2>';

        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'settings') {
            $Display_MLM_Settings = new Bmw_Display_MLM_Settings();
            $Display_MLM_Settings->bmw_display_mlm_settings_page();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'point-value') {
            $Display_PV_Settings = new Bmw_Display_PV_Settings();
            $Display_PV_Settings->bmw_display_pv_set_page();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'payout-reports') {
            $Payout = new Bmw_Display_Payout();
            $Payout->display_bmw_page_view();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'member-info') {
            $Display_Member_Profile = new Bmw_Member_List_Table();
            $Display_Member_Profile->prepare_items();
            $Display_Member_Profile->display();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['bmw-setting']) && $_GET['bmw-setting'] == 'upgrade-pluging') {
            echo "<a href='https://www.mlmtrees.com/product/binary-mlm-ecommerce/' class='btn button button-primary m-auto d-block' style='width:20%; margin-top:15px !important'>Upgrade Plugin</a>";
        } else {
            $Display_MLM_Settings = new Bmw_Display_MLM_Settings();
            $Display_MLM_Settings->bmw_display_mlm_settings_page();
        }

        echo '</div>';
    }
}
/*   Add custom column in user pannel */
function bmw_add_custom_column_users($column)
{
    //unset($column);// remove add previous field & it's work on filter hook

    $column['account_holder_name'] = 'Account holder name';
    $column['account_number'] = 'Account number';
    $column['bank_name'] = 'Bank name';
    $column['branch'] = 'Branch';
    $column['ifsc_code'] = 'IFSC Code';
    return $column; // Add new field

}
add_filter('manage_users_columns', 'bmw_add_custom_column_users');


/* REMOVE PREVIOUS FIELD SELECTED */
function bmw_remove_custom_column_users($column_remove)
{
    unset($column_remove['posts']); // Remove one field
    unset($column_remove['role']);
    unset($column_remove['email']);
    return $column_remove;
}
add_action('manage_users_columns', 'bmw_remove_custom_column_users');

/* INSERT DATA IN OUR CUSTOM FIELD */
function bmp_add_custom_column_users_value($value, $column_name, $user_id)
{
    global $wpdb;
    switch ($column_name) {
        case 'account_holder_name': //column key
            return get_user_meta($user_id, 'account_holder_name', true); // meta key
        case 'account_number':
            return get_user_meta($user_id, 'account_number', true);
        case 'bank_name':
            return get_user_meta($user_id, 'bank_name', true);
        case 'ifsc_code':
            return get_user_meta($user_id, 'ifsc_code', true);
        case 'branch':
            get_user_meta($user_id, 'branch', true);
        default:
            return null;
    }
}

add_action('manage_users_custom_column',  'bmp_add_custom_column_users_value', 10, 3);
