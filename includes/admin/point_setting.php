<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class Bmw_Display_PV_Settings  extends WP_List_Table
{
    public function bmw_display_pv_set_page()
    {

        extract($_REQUEST);

        if (isset($_REQUEST['pid']) && isset($_REQUEST['product_points'])) {
            if (!get_post_meta($pid, 'product_points_' . $pid, TRUE)) {
                add_post_meta($pid, 'product_points_' . $pid, $_REQUEST['product_points']);
            } else {
                update_post_meta($pid, 'product_points_' . $pid, $_REQUEST['product_points']);
            }
        }

        echo '<div id="icon-themes" class="icon32"><br></div>';

        $this->prepare_items();
        $this->display();
    }

    function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular'  => 'id',     //singular name of the listed records
            'plural'    => 'id',     //plural name of the listed records
            'ajax'      => false     //does this table support ajax?
        ));
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'product_id':
            case 'product_name':
            case 'product_price':
            case 'pvset':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns()
    {
        $columns = array(
            'product_id'        => __('Product ID', 'bmw'),
            'product_name'      => __('Product Name ', 'bmw'),
            'product_price'         => __('Product Price', 'bmw'),
            'pvset'     => __('Points', 'bmw'),
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

        $sql = "SELECT p.id as pid,p.post_title as name,pm.meta_value as price FROM $wpdb->posts as p INNER JOIN $wpdb->postmeta as pm  ON p.id=pm.post_id WHERE p.post_type ='product' AND p.post_status='publish' AND pm.meta_key LIKE '_regular_price' ";
        $results = $wpdb->get_results($sql);



        $path = BMW_ABSPATH . '/admin/';
        $num = $wpdb->num_rows;
        if ($num > 0) {
            $i = 0;
            foreach ($results as $row) {

                $product_points = get_post_meta($row->pid, 'product_points_' . $row->pid, TRUE);

                $form = '<form action="" id="pv_form_' . $row->pid . '" method="POST">
                    <input type="hidden" value="' . $row->pid . '" name="pid">
                    <input type="text" name="product_points" id="product_points_' . $row->pid . '" value="' . $product_points . '">
                    <input type="submit"  value="' . __('Save', 'bmw') . '" id="update_' . $row->pid . '"></form>';
                $data[$i]['product_id'] = $row->pid;
                $data[$i]['product_name'] = $row->name;
                $data[$i]['product_price'] = $row->price;
                $data[$i]['pvset'] = $form;
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
