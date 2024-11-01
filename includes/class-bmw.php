<?php

/**
 * woo-binary-mlm setup
 *
 * @package woo-binary-mlm
 */
defined('ABSPATH') || exit;
/**
 * Main Letscms_BMW Class
 *
 */
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
include_once BMW_ABSPATH . '/includes/common-functions.php';

final class Letscms_BMW
{

    use Letscms_BMW_CommonClass;

    protected static $_instance = null;
    /**
     * Main woo-binary-mlm Instance.
     *
     * Ensures only one instance of Letscms_BMW is loaded or can be loaded, only if WooCommerce is already activated.
     */
    public static function instance()
    {
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        } else {
            echo __("Before activating woo-binary-mlm, Please activate  WooCommerce plugin.", "bmw");
            exit;
        }
    }


    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
        if (!is_admin()) {
            $this->define_shortcodes();
        }
    }

    /**
     * Define BMW Constants.
     */
    public function define_constants()
    {

        //core constants
        $core_constants = array(
            'BMW_VERSION' => '1.0',
            'BMW_CORE_IMAGES_URL' => BMW_URL . "/assets/images",
            'BMW_CORE_IMAGES_PATH' => BMW_ABSPATH . '/assets/images',
            'BMW_CORE_JS_URL' => BMW_URL . '/assets/js',
            'BMW_CORE_JS_PATH' => BMW_ABSPATH . '/assets/js',
            'BMW_ADMIN_PATH' => BMW_ABSPATH . '/includes/admin',
        );

        foreach ($core_constants as $key => $value) {
            $this->define($key, $value);
        }
        do_action('bmw_constants');
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes()
    {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        require_once(ABSPATH . 'wp-includes/pluggable.php');

        //include_once BMW_ABSPATH . '/includes/form-validation.php';
        include_once BMW_ABSPATH . '/includes/installer.php';
        include_once BMW_ABSPATH . '/includes/ajax-function.php';
        include_once BMW_ABSPATH . '/includes/order.php';


        include_once BMW_ABSPATH . '/includes/admin/admin.php';
        include_once BMW_ABSPATH . '/includes/admin/settings.php';
        include_once BMW_ABSPATH . '/includes/admin/general-setting.php';
        include_once BMW_ABSPATH . '/includes/admin/mapping.php';
        include_once BMW_ABSPATH . '/includes/admin/eligibility.php';
        include_once BMW_ABSPATH . '/includes/admin/payout-setting.php';
        include_once BMW_ABSPATH . '/includes/admin/point_setting.php';

        include_once BMW_ABSPATH . '/includes/admin/manage-payout.php';
        include_once BMW_ABSPATH . '/includes/admin/payout/run-payout.php';
        include_once BMW_ABSPATH . '/includes/admin/payout/payout-pv.php';
        include_once BMW_ABSPATH . '/includes/admin/payout/payout-money.php';
        include_once BMW_ABSPATH . '/includes/admin/payout/payout-report.php';
        include_once BMW_ABSPATH . '/includes/admin/member-list-table.php';


        include_once BMW_ABSPATH . '/includes/catalog/join-network.php';
        include_once BMW_ABSPATH . '/includes/catalog/my-network.php';
        include_once BMW_ABSPATH . '/includes/catalog/mydownlines.php';
        include_once BMW_ABSPATH . '/includes/catalog/registration.php';
        include_once BMW_ABSPATH . '/includes/catalog/wc-register-form.php';



        include_once BMW_ABSPATH . '/includes/uninstaller.php';

        do_action('bmw_includes');
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0
     */
    private function init_hooks()
    {

        // Before setup
        do_action('bmw_pre_load');
        //load text domain
        if (!load_plugin_textdomain('bmw', false, '../languages/')) {
            load_plugin_textdomain('bmw', false, BMW_ABSPATH . '/language/');
        }
        $mapping = get_option('bmw_mapping_settings');
        if (isset($mapping)) {
            $order_status = 'wc-' === substr($mapping['letscms_woocommerce_payment'], 0, 3) ? substr($mapping['letscms_woocommerce_payment'], 3) : $mapping['letscms_woocommerce_payment'];
        }
        add_action('init', array($this, 'bmw_register_my_session'));
        add_action('init', array($this, 'bmw_add_admin_menu'));
        add_action('init', array($this, 'bmw_ajax_function_call'));
        add_action('admin_enqueue_scripts', array($this, 'bmw_admin_style'));
        add_action('wp_enqueue_scripts', array($this, 'bmw_enqueue_scripts'));

        register_activation_hook(BMW_PLUGIN_FILE, array('Letscms_BMW_Install', 'install'));
        /*To integrate with woocommerce*/
        add_action('woocommerce_order_status_' . $order_status, array($this, 'bmw_customer_order'));
        // add_action('woocommerce_after_checkout_billing_form', array($this, 'bmw_user_register'));
        // add_action('woocommerce_checkout_process', array($this, 'bmw_register_fields_validation'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'bmw_update_order_meta'), 10, 1);

        register_deactivation_hook(BMW_PLUGIN_FILE, array('Letscms_BMW_Install', 'deactivate'));
        register_uninstall_hook(BMW_PLUGIN_FILE, 'uninstall');
    }

    /* register session */
    public function bmw_register_my_session()
    {
        if (!isset($_SESSION))
            $_SESSION = null;
        if ((!is_array($_SESSION)) xor  (!$_SESSION))
            session_start();
    }

    public function bmw_add_admin_menu()
    {
        $Admin_Menu = new BMW_Admin_Menu();
        add_action('admin_menu', array($Admin_Menu, 'bmw_admin_menupage'));
    }

    public function bmw_admin_style()
    {
        wp_register_style('bmw_admin_css', BMW_URL . '/assets/css/admin/admin_style.css', false, false, 'all');
        wp_enqueue_style('bmw_admin_css');

        wp_register_style('bmw_bs_css', BMW_URL . '/assets/bootstrap/css/bootstrap.css', false, false, 'all');
        wp_enqueue_style('bmw_bs_css');
    }

    public function bmw_enqueue_scripts()
    {
        global $wpdb;

        wp_enqueue_script('jquery-ui-datepicker');

        $sql = "select * from $wpdb->postmeta where meta_key='bmw_page_title'";
        $posts = $wpdb->get_results($sql);

        foreach ($posts as $key => $value) {
            $post = $value->post_id;
            $post_title = get_the_title($post);
            if (is_page($post_title)) {
                wp_register_style('bmw_catalog_css', BMW_URL . '/assets/css/style.css', false, false, 'all');
                wp_enqueue_style('bmw_catalog_css');

                wp_register_style('bmw_bscatalog_css', BMW_URL . '/assets/bootstrap/css/bootstrap.css', false, false, 'all');
                wp_enqueue_style('bmw_bscatalog_css');

                wp_enqueue_script('bmw-bs-js', BMW_URL . '/assets/bootstrap/js/bootstrap.js', array(), false, true);

                wp_enqueue_script('bmw-ajax', BMW_URL . '/assets/js/ajax.js', array(), false, true);
                wp_enqueue_script('bmw-form-validation', BMW_URL . '/assets/js/form-validation.js', array(), false, true);
            }
        }
    }

    public function bmw_ajax_function_call()
    {
        $Action_ajax = new Bmw_action_ajax();
    }

    public function bmw_customer_order($order_id)
    {
        global $woocommerce, $post, $order;

        $order = wc_get_order($order_id);

        $order_data = $order->get_data();
        $items = $order->get_items();

        $order_id = $order_data['id'];
        $order_parent_id = $order_data['parent_id'];
        $order_status = $order_data['status'];
        $order_currency = $order_data['currency'];
        $order_customer_id = $order_data['customer_id'];

        $order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
        $order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');

        $order_total = $order_data['total'];
        $order_total_tax = $order_data['total_tax'];

        ## BILLING INFORMATION:
        $order_billing_first_name = $order_data['billing']['first_name'];
        $order_billing_last_name = $order_data['billing']['last_name'];

        $user_meta = get_userdata($order_customer_id);
        $user_roles = isset($user_meta->roles) ? $user_meta->roles : [];

        if (in_array('bmw_user', $user_roles, true)) {
            $Order = new Bmw_order();
            $Order->update_order($order_id, $order_customer_id, $order_total, $items);
        } else {
            if (!empty($order_customer_id) && !empty($order_id)) {
                $this->bmw_register_mlm_user($order_customer_id, $order_id);
                $Order = new Bmw_order();
                $Order->update_order($order_id, $order_customer_id, $order_total, $items);
            }
        }
    }

    // public function bmw_register_fields_validation()
    // {
    //     $createaccount_with_mlm = sanitize_text_field($_POST['createaccount_with_mlm']);
    //     if (!empty($createaccount_with_mlm)) {
    //         $sponsor = sanitize_text_field($_POST['bmw_sponsor']);
    //         if (empty($sponsor)) {
    //             wc_add_notice(__('Binary MLM Sponsor Could Not be Empty.'), 'error');
    //         }
    //         if (!($this->checksponsorvalid($sponsor))) {
    //             wc_add_notice(__('Binary MLM Sponsor is invalid.'), 'error');
    //         }
    //         $leg = sanitize_text_field($_POST['bmw_placement']);
    //         if ($leg == '') {
    //             wc_add_notice(__('Binary MLM Placement Could Not be Empty.'), 'error');
    //         }
    //     }
    // }

    // public function bmw_user_register()
    // {
    //     $bmw_woocommerce_register = new BMW_woocommerce_register();
    //     $bmw_woocommerce_register->bmw_register_form();
    // }

    public function bmw_update_order_meta($order_id)
    {
        if (!empty(sanitize_text_field($_POST['createaccount_with_mlm']))) {
            if (isset($_POST['bmw_sponsor']) && !empty($_POST['bmw_sponsor'])) {
                $sponsor = sanitize_text_field($_POST['bmw_sponsor']);
                $leg = sanitize_text_field($_POST['bmw_placement']);
                add_post_meta($order_id, 'bmw_placement', $leg);
                add_post_meta($order_id, 'bmw_sponsor', $sponsor);
            }
        }
    }

    /**
     * Define woo-binary-mlm Shortcodes.
     */
    private function define_shortcodes()
    {
        $BMW_Registration = new BMW_Registration;
        add_shortcode('bmw_registration', array($BMW_Registration, 'register_user'));

        $BMW_MyDownlines = new BMW_MyDownlines;
        add_shortcode('bmw_downlines', array($BMW_MyDownlines, 'view_mydownlines'));

        $BMW_Network = new BMW_Network;
        add_shortcode('bmw_network', array($BMW_Network, 'view_network_detail'));

        $BMW_Join_Network = new BMW_Join_Network;
        add_shortcode('bmw_join_network', array($BMW_Join_Network, 'view_join_network'));
    }
}
