<?php
class Bmw_order
{
    use Letscms_BMW_CommonClass;

    public function totalPvForWoocommerce($order_id, $items)
    {
        global $wpdb;
        $total_pv = 0;
        foreach ($items as $item) {
            $product_name = $item['name'];
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $point_value = ((int)get_post_meta($product_id, 'product_points_' . $product_id, true));

            $total_pv += ($quantity * $point_value);
        }
        return $total_pv;
    }


    public function update_order($order_id, $order_customer_id, $order_total, $items)
    {
        global $wpdb, $woocommerce;
        $total_pv = $this->totalPvForWoocommerce($order_id, $items);
        $already_exist_record = $wpdb->get_row("SELECT * from {$wpdb->prefix}bmw_pv_detail where user_id='" . $order_customer_id . "' AND order_id='" . $order_id . "' AND status='0'");
        if (!empty($already_exist_record)) {
            $wpdb->query("UPDATE {$wpdb->prefix}bmw_pv_detail SET total_amount='" . $order_total . "', total_point='" . $total_pv . "' WHERE user_id='" . $order_customer_id . "' AND order_id='" . $order_id . "' AND status='0'");
        } else {
            $wpdb->query("INSERT INTO {$wpdb->prefix}bmw_pv_detail (order_id,user_id,total_amount,total_point) values ( $order_id, $order_customer_id, $order_total, $total_pv)");
        }
        // $sponsor_id = $this->getSponsoridByUserId($order_customer_id);

        $total_points = !empty($total_pv) ? $total_pv : 0;
        $getInfoByUserId         = $this->getInfoByUserId($order_customer_id); //retrun the array.
        // print_r($getInfoByUserId);

        if (!empty($getInfoByUserId)) {
            $payment_status = $getInfoByUserId->payment_status;
            $userId = $getInfoByUserId->user_id;

            if ($payment_status == '0') {
                $wpdb->query("UPDATE {$wpdb->prefix}bmw_users SET payment_status='1', paid_at = '" . date('Y-m-d H:i:s') . "' WHERE user_id='" . $userId . "'");
            }
        }
    }

    /*************| End of the Main Function |******************************************/



    public function updateUserOwnPV($userKey, $point)
    {
        global $wpdb;
        $sql = "UPDATE {$wpdb->prefix}bmw_users SET own_point = own_point + " . $point . " WHERE `user_key` = '" . $userKey . "'";
        $res = $wpdb->query($sql);
        if ($res) {
            return $res;
        }
    }

    public function updateUserQuaPV($user_id, $userKey, $point, $toalQpv, $qua_pv_criteria)
    {
        global $wpdb;
        if ($toalQpv < $qua_pv_criteria) {
            $res = $wpdb->query("UPDATE {$wpdb->prefix}bmw_users SET qualification_point = qualification_point + " . $point . ", payment_status = '1' WHERE `user_key` = '" . $userKey . "'");
        } else if ($toalQpv >= $qua_pv_criteria) {
            $extraPv = $toalQpv - $qua_pv_criteria;
            $debitpv = $qua_pv_criteria;
            $res = $wpdb->query("UPDATE {$wpdb->prefix}bmw_users SET qualification_point = '" . $debitpv . "', payment_status = '2' , own_point = 
			 + " . $extraPv . "
							WHERE `user_key` = '" . $userKey . "'");
        }
        return $res;
    }

    public function TransLogCreditPV($userid, $child_key, $lpv, $rpv)
    {
        global $wpdb;
        $sql = "SELECT `user_key`, parent_key, payment_status, qualification_point, left_point, right_point, own_point FROM {$wpdb->prefix}bmw_users WHERE `user_id` = '" . $userid . "'";
        $rsrow = $wpdb->get_row($sql);
        if ($rsrow && $wpdb->num_rows > 0) {
            $opening_left = $rsrow->left_point;
            $opening_right = $rsrow->right_point;
            $qualification_pv = $rsrow->qualification_point;
            $payment_status = $rsrow->payment_status;

            if ($qualification_pv == 100) {
                $opening_own = $rsrow->own_pv;
            } else {
                $opening_own = $qualification_pv;
            }
        }

        if ($lpv != 0) {
            $closing_left = $opening_left + $lpv;
            $closing_right = $opening_right;
            $closing_own = $opening_own;
        }

        if ($rpv != 0) {
            $closing_left = $opening_left;
            $closing_right = $opening_right + $rpv;
            $closing_own = $opening_own;
        }

        $addDate = date('Y-m-d H:i:s');
        if (isset($rsrow->user_key)) {
            $sql_tra = "INSERT INTO {$wpdb->prefix}bmw_point_transaction 
							(
								 `parent_key`,`user_key`, 
								 `opening_left`, `opening_right`, 
								`closing_left`, `closing_right`, 
								`credit_left` ,`credit_right`, `date`
							)								
							VALUES 
							(
								'" . $rsrow->user_key . "','" . $child_key . "',
								'" . $opening_left . "','" . $opening_right . "',
								'" . $closing_left . "','" . $closing_right . "',
								'" . $lpv . "','" . $rpv . "', '" . $addDate . "'
							)
						";
            $rs_tra = $wpdb->query($sql_tra);
        }
        return     $rs_tra;
    }

    public function distributePV($userid, $left_pv, $right_pv)
    {
        global $wpdb;
        $sql = "UPDATE {$wpdb->prefix}bmw_users SET  left_point = left_point + " . $left_pv . " , right_point = right_point + " . $right_pv . " WHERE `user_id` = '" . $userid . "'";

        $rs = $wpdb->query($sql);
        return $rs;
    }
    /*End of the Main Class */
}
