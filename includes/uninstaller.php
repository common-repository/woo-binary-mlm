<?php
class Letscms_BMW_Uninstall
{

    public function uninstall()
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

        // Delete options.
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%{$wpdb->prefix}bmw%';");

        // Delete users & usermeta.


        $this->drop_tables();

        $wp_roles = new WP_Roles();
        $wp_roles->remove_role("bmw_user");
        session_destroy();
    }
    /**
     * Return a list of Binary MLM tables. Used to make sure all MLM tables are dropped when uninstalling the plugin
     * in a single site or multi site environment.
     *
     * @return array MLM tables.
     */
    public function get_tables()
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

    /**
     * Drop Binary MLM tables.
     *
     * @return void
     */
    public function drop_tables()
    {
        global $wpdb;

        $tables = $this->get_tables();

        foreach ($tables as $table) {
            //echo $table.'<br>';

            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }
    }
}
