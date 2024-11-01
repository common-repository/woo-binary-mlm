<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class Bmw_Member_List_Table extends WP_List_Table
{
    use Letscms_BMW_CommonClass;
    function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular'  => 'id',     //singular name of the listed records
            'plural'    => 'id',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ));
    }
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'userid':
            case 'username':
            case 'name':
            case 'sponsorname':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }
    function get_columns()
    {
        $columns = array(
            'userid' => __('User Id', 'bmw'),
            'username'    => __('User Name', 'bmw'),
            'name'    => __('Full Name', 'bmw'),
            'sponsorname'    =>    __('Sponsor', 'bmw')

        );
        return $columns;
    }
    function get_sortable_columns()
    {
        $sortable_columns = array();
        return $sortable_columns;
    }
    function prepare_items()
    {
        global $wpdb;
        global $date_format;
        $per_page = 30;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $data = array();

        $sql = "SELECT * FROM {$wpdb->prefix}bmw_users ORDER BY id ASC";
        $results = $wpdb->get_results($sql);
        $data = array();
        $num = $wpdb->num_rows;
        if ($num > 0) {
            $i = 0;
            foreach ($results as $row) {
                $userInfoArr = $this->GetUserInfoById($row->user_id);
                $sponsorArr = $this->getSponsorName($row->sponsor_key);
                $data[$i]['username'] = isset($userInfoArr['userlogin']) ? $userInfoArr['userlogin'] : '';
                $data[$i]['userid'] = $row->user_id;
                $data[$i]['userKey']  = isset($userInfoArr['userKey']) ? $userInfoArr['userKey'] : '';
                $data[$i]['name'] =     isset($userInfoArr['name']) ? $userInfoArr['name'] : '';
                $data[$i]['sponsorname'] =     $sponsorArr;
                $i++;
            }
        }

        $data = $data;
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
}
