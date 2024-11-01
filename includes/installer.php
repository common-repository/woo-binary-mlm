<?php
defined('ABSPATH') || exit;
/**
 * Letscms_BMW_Install Class.
 */
class Letscms_BMW_Install
{

    public static function install()
    {
        self::create_roles();
        self::create_tables();
        self::create_pages();
        self::save_options();
        // ALTER TABLE `wp_bmw_point_transaction` ADD `commission_amount` DOUBLE(15,3) NOT NULL DEFAULT '0.000' AFTER `status`, ADD `commission_points` DOUBLE(15,3) NOT NULL DEFAULT '0.000' AFTER `commission_amount`;
    }

    /*================================Create Roles=======================================*/

    public static function create_roles()
    {
        global $wp_roles;

        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles(); // 
        }
        add_role('bmw_user', 'BMW Users', array());
    }

    /*================================Create Tables=======================================*/
    private static function create_tables()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $tables = self::get_schema();
        foreach ($tables as $table) {
            dbDelta($table);
        }
    }

    private static function get_schema()
    {
        global $wpdb;
        $collate = '';

        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bmw_users (
						id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						user_id BIGINT NOT NULL,
						user_key VARCHAR( 15 ) NOT NULL,
						parent_key VARCHAR( 15 ) NOT NULL,
						sponsor_key VARCHAR( 15 ) NOT NULL,
						leg ENUM(  '1',  '0' ) NOT NULL,
						payment_status ENUM('0','1','2') NOT NULL,
						qualification_point INT(11) NOT NULL,
						left_point float NOT NULL,
						right_point float NOT NULL,
						own_point float NOT NULL,
						created_at datetime NOT NULL,
						paid_at datetime NOT NULL
					) $collate;";
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bmw_leftleg(
						id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						user_key VARCHAR( 15 ) NOT NULL,
						parent_key VARCHAR( 15 ) NOT NULL,
						sponsor_key VARCHAR( 15 ) NOT NULL,
						comm_status int( 11 ) NOT NULL DEFAULT '0'
					) $collate;";
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bmw_rightleg(
						id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						user_key VARCHAR( 15 ) NOT NULL,
						parent_key VARCHAR( 15 ) NOT NULL,
                        sponsor_key VARCHAR( 15 ) NOT NULL,
						comm_status int( 11 ) NOT NULL DEFAULT '0'
					) $collate;";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bmw_point_transaction(
						id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						user_key VARCHAR( 15 ) NOT NULL,						 
						payout_id INT(15) NOT NULL,
						date date NOT NULL,
						status enum('0','1') NOT NULL,
                        commission_amount double(15,3) NOT NULL DEFAULT '0',
                        commission_points double(10,3) NOT NULL DEFAULT '0',
                        childs longtext NOT NULL
					) $collate;";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bmw_payout(	
						id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						userid BIGINT  NOT NULL,
						date date NOT NULL,						 
						units int(25) NOT NULL,
						commission_amount double(10,2) DEFAULT '0'
					) $collate;";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bmw_payout_master(	
						id int(10) unsigned NOT NULL auto_increment PRIMARY KEY,
						date date NOT NULL
					) $collate;";

        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bmw_pv_detail(
						id int(11) unsigned NOT NULL auto_increment PRIMARY KEY,
						order_id int(11) NOT NULL UNIQUE KEY,
						user_id int(11) NULL,
						total_amount int(11) NOT NULL,
						total_point int(11) NOT NULL,
						status int(11) NOT NULL DEFAULT '0'

					) $collate;";

        return $tables;
    }

    /*================================Create Pages=======================================*/
    private static function create_pages()
    {
        global $wpdb;
        $bmwpages = array(
            'my-networks-page' => array(
                'name' => 'my-networks-page',
                'title' => __('My Networks', 'bmw'),
                'tag' => '[bmw_network]',
                'option' => 'my_networks_url'
            ),

            'my-downlines' => array(
                'name' => 'my-downlines',
                'title' => __('My Downlines', 'bmw'),
                'tag' => '[bmw_downlines]',
                'option' => 'my_downlines_url'
            ),

            'registration-page' => array(
                'name' => 'registration-page',
                'title' => __('Registration', 'bmw'),
                'tag' => '[bmw_registration]',
                'option' => 'registration_url'
            ),
            'join-networks-page' => array(
                'name' => 'join-networks-page',
                'title' => __('Join Network', 'bmw'),
                'tag' => '[bmw_join_network]',
                'option' => 'join_networks_url'
            )
        );
        // $newmlmpages = false;
        // $menu = wp_get_nav_menu_object('primary');
        // if (empty($menu)) {
        //     wp_update_nav_menu_object(0, array('menu-name' => 'primary'));
        // }
        // $menu = wp_get_nav_menu_object('primary');

        // $args = array(
        //     "post_type" => "nav_menu_item",
        //     "name" => 'BMW',
        //     "title" => 'Binary MLM WooCommerce'
        // );

        // $query = new WP_Query($args);

        // if(empty($query->posts)){
        //  	$parent_id=wp_update_nav_menu_item($menu->term_id, 0, array(
        // 			'menu-item-title' =>  __('Binary MLM WooCommerce'),
        // 			'menu-item-classes' => 'bmw',
        // 			'menu-item-url' => '#',
        // 			'menu-item-status' => 'publish',
        // 			'menu-item-type' => 'custom',
        // 	 		)
        // 	 );

        //  	update_post_meta( $parent_id, 'menu_item_bmw','Binary MLM WooCommerce');
        // }
        //  else {
        //  	$parent_id=$query->posts[0]->ID;
        //  }

        //create the pages

        foreach ($bmwpages as $key => $page) {
            // $page_id=get_page_by_title( $page['title'], OBJECT, 'page');

            // if(empty($page_id)){
            $pageid = self::bmw_create_page($page['title'], $page['tag'], $page['name'], $page['option']);
            // 	wp_update_nav_menu_item($menu->term_id,0, array(
            // 	'menu-item-object-id' => $pageid,
            // 	'menu-item-object' => 'page',
            // 	'menu-item-title' =>  $page['title'],
            // 	'menu-item-classes' => 'bmw',
            // 	'menu-item-status' => 'publish',
            // 	'menu-item-type' => 'post_type',
            // 	'menu-item-parent-id' => $parent_id,
            // ));
            // }
        }
    }

    public static function bmw_create_page($page_title = '', $page_content = '', $page_name = '', $page_option = '')
    {
        global $wpdb;
        $page_data = array(
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_title'     => $page_title,
            'post_content'   => $page_content,
            'post_name'        =>    $page_name,
        );

        $page_id   = wp_insert_post($page_data);
        // $newmlmpages = true;
        update_option($page_option, get_permalink($page_id));
        update_post_meta($page_id, 'bmw_page_title', $page_title);
        if ($page_title != 'Registration' and $page_title != 'Join Network') {
            update_post_meta($page_id, 'is_mlm_page', true);
        }
        return $page_id;
    }




    /*================================Save Default Values=======================================*/
    private static function save_options()
    {
        $general = array('letscms_purchase_reg' => '1', 'letscms_affiliate_url' => 'registration-page/');
        update_option('bmw_general_settings', $general);

        $mapping = array('letscms_woocommerce_payment' => 'wc-completed');
        update_option('bmw_mapping_settings', $mapping);

        $eligibility = array('bmw_personalpoint' => '100', 'bmw_directreferrer' => '2', 'bmw_leftreferrer' => '1', 'bmw_rightreferrer' => '1', 'bmw_minpoint' => '');
        update_option('bmw_eligibility_settings', $eligibility);

        $payout = array('bmw_pair1' => '100', 'bmw_pair2' => '100', 'bmw_initialunits' => '2', 'bmw_initialrate' => '1000', 'bmw_furtheramount' => '500', 'bmw_servicecharges' => '100', 'bmw_tds' => '2', 'bmw_capamount' => '200000');
        update_option('bmw_payout_settings', $payout);
    }




    /*=============================Deactivate the plugin====================================*/

    public static function deactivate()
    {
        global $wpdb;

        $mlmPages = array('My Networks', 'My Downlines', 'Registration', 'Join Network');


        //delete post from wp_posts and wp_postmeta table
        foreach ($mlmPages as $value) {
            $post_id = $wpdb->get_var("SELECT id from {$wpdb->prefix}posts where post_title = '$value'");
            wp_delete_post($post_id, true);
        }

        foreach ($mlmPages as $value) {
            $results = $wpdb->get_results("SELECT p.id from {$wpdb->prefix}posts as p join {$wpdb->prefix}postmeta as pm on p.id=pm.post_id where pm.meta_key='bmw_page_title' AND pm.meta_value = '$value'");
            foreach ($results as $result) {
                wp_delete_post($result->id, true);
            }
        }

        $results = $wpdb->get_results("SELECT p.id from {$wpdb->prefix}posts as p join {$wpdb->prefix}postmeta as pm on p.id=pm.post_id where pm.meta_key='menu_item_bmw' AND pm.meta_value = 'Binary MLM WooCommerce'");
        foreach ($results as $result) {
            wp_delete_post($result->id, true);
        }


        self::drop_tables();

        $wp_roles = new WP_Roles();
        $wp_roles->remove_role("bmw_user");
    }
    public static function get_tables()
    {
        global $wpdb;

        $tables = array(
            "{$wpdb->prefix}bmw_users",
            "{$wpdb->prefix}bmw_leftleg",
            "{$wpdb->prefix}bmw_rightleg",
            "{$wpdb->prefix}bmw_payout",
            "{$wpdb->prefix}bmw_payout_master",
            "{$wpdb->prefix}bmw_point_transaction",
            "{$wpdb->prefix}bmw_pv_detail",
        );
        return $tables;
    }
    public static function drop_tables()
    {
        global $wpdb;

        $tables = self::get_tables();

        foreach ($tables as $table) {
            //echo $table.'<br>';

            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }
    }
}
