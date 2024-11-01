<?php
class Bmw_action_ajax
{
    use Letscms_BMW_CommonClass;

    public function __construct()
    {
        include_once(ABSPATH . 'wp-includes/user.php');
        add_action('wp_enqueue_scripts', array($this, 'bmw_enqueue_script'));

        add_action('wp_ajax_username', array($this, 'checkUserName_action'));
        add_action('wp_ajax_nopriv_username', array($this, 'checkUserName_action'));
        add_action('wp_ajax_email', array($this, 'checkEmail_action'));
        add_action('wp_ajax_nopriv_email', array($this, 'checkEmail_action'));
        add_action('wp_ajax_sponsor', array($this, 'checkUserName_action'));
        add_action('wp_ajax_nopriv_sponsor', array($this, 'checkUserName_action'));
        add_action('wp_ajax_bmw_sponsor', array($this, 'check_bmw_sponsor'));
        add_action('wp_ajax_nopriv_bmw_sponsor', array($this, 'check_bmw_sponsor'));
        add_action('wp_ajax_savepoints', array($this, 'bmw_distribute_points'));
        add_action('wp_ajax_savemoney', array($this, 'bmw_distribute_money'));
    }

    public function bmw_enqueue_script()
    {
        wp_enqueue_script('bmw-ajax', BMW_URL . '/assets/js/ajax.js', array(), false, true);
        wp_enqueue_script('bmw-form-validation', BMW_URL . '/assets/js/form-validation.js', array(), false, true);
    }

    public function checkUserName_action()
    {
        global $wpdb;
        $action = sanitize_text_field($_REQUEST['action']);
        $invalid_usernames = array('admin');
        if ($action == 'username') {
            $username = sanitize_text_field($_REQUEST['q']);
            $username = sanitize_user($username);
            if (!validate_username($username) || in_array($username, $invalid_usernames) || empty($_REQUEST['q'])) {
                echo "<span class='errormsg' style='color:red;'>" . __('Username is invalid', 'bmw') . "</span>";
            } else if (username_exists($username)) {
                echo "<span class='errormsg' style='color:red;'>" . __('Sorry! The specified username is not available for registration', 'bmw') . "</span>";
            } else {
                if (!preg_match("/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{5,20})$/", $username))
                    echo "<span class='msg' style='color:red;'>" . __('User name must be 5 character, at least one number and alphabet ', 'bmw') . "</span>";
                else {
                    echo "<span class='msg' style='color:green;'>" . __('Congratulations! The username is available', 'bmw') . "</span>";
                }
            }
        }

        if ($action == 'sponsor') {
            $sponsorName = sanitize_text_field($_REQUEST['q']);
            if (username_exists($sponsorName)) {
                $sql = "SELECT ID FROM $wpdb->users WHERE user_login = '" . $sponsorName . "'";
                $ID = $wpdb->get_var($sql);
                if ($wpdb->num_rows == 1) {
                    $userId = $ID;
                    $sql1 = "SELECT user_key FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userId . "'";
                    $result1 = $wpdb->get_var($sql1);
                    if ($wpdb->num_rows == 1) {
                        echo "<span class='msg' style='color:green;'>" . __('Sponosr is Valid', 'bmw') . "</span>";
                    } else {
                        echo "<span class='errormsg' style='color:red;'>" . __('Sorry ! Sponosr is invalid', 'bmw') . "</span>";
                    }
                }
            } else {
                echo "<span class='errormsg' style='color:red;'>" . __('Sorry ! Sponosr is invalid', 'bmw') . "</span>";
            }
        }
        die();
    }

    public function checkEmail_action()
    {
        $email = sanitize_email($_REQUEST['q']);
        if (!is_email($email))
            echo "<span class='errormsg' style='color:red;'>" . __('Sorry! E-mail address is invalid.', 'bmw') . "</span>";
        if (email_exists($email))
            echo "<span class='errormsg' style='color:red;'>" . __('Sorry! The specified email is already exist.', 'bmw') . "</span>";

        die();
    }

    public function check_bmw_sponsor()
    {
        global $wpdb;
        $sponsorName = sanitize_text_field($_REQUEST['name']);
        if (username_exists($sponsorName)) {
            $sql = "SELECT ID FROM $wpdb->users WHERE user_login = '" . $sponsorName . "'";
            $ID = $wpdb->get_var($sql);
            if ($wpdb->num_rows == 1) {
                $userId = $ID;
                $sql1 = "SELECT user_key FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userId . "'";
                $result1 = $wpdb->get_var($sql1);
                if ($wpdb->num_rows == 1) {
                    echo "<span class='msg' style='color:green;'>" . __('Sponosr is Valid', 'bmw') . "</span>";
                } else {
                    echo "<span class='errormsg' style='color:red;'>" . __('Sorry ! Sponosr is invalid', 'bmw') . "</span>";
                }
            }
        } else {
            echo "<span class='errormsg' style='color:red;'>" . __('Sorry ! Sponosr is invalid', 'bmw') . "</span>";
        }
        die();
    }

    public function bmw_distribute_points()
    {

        $Distribute_PV = new Bmw_Distribute_PV();
        $data = $Distribute_PV->Payoutpv();
        /*------------------------------------------------------------------
Save the Calculated Money 
-------------------------------------------------------------------*/
        if (isset($_REQUEST['action'])) {
            $action = sanitize_text_field($_REQUEST['action']);

            if ($action == 'savepoints') {
                global $wpdb;
                /**************| Insert Data into  {$wpdb->prefix}bmw_payout_master table |**********************/
                $insert_pay_master = "INSERT INTO {$wpdb->prefix}bmw_payout_master (`date`)VALUES('" . date('Y-m-d') . "')";
                $wpdb->query($insert_pay_master);
                $pay_master_id = $wpdb->insert_id;

                if (!$pay_master_id) {
                    $message = __("Sorry,Payout Master Not Created. Please try again", 'bmw');
                }

                if (count($data) > 0) {
                    foreach ($data as $val) {

                        if (isset($val['userKey']) && isset($val['parentKey']) && $val['unit'] != 0 && $pay_master_id != '') {

                            /**************| Insert Data into  {$wpdb->prefix}bmw_point_transaction table |**********************/
                            $insert_transac =  "INSERT INTO {$wpdb->prefix}bmw_point_transaction
                (   
                  parent_key, user_key, 
                  opening_left,opening_right, 
                  closing_left,closing_right, 
                  debit_left,debit_right, 
                  credit_left, credit_right,
                  payout_id, date,status
                )
                VALUES 
                
                (
                  '" . $val['userKey'] . "','" . $val['userKey'] . "',
                  '" . $val['left_point'] . "','" . $val['right_point'] . "',
                  '" . $val['currLeftPoint'] . "','" . $val['currRightPoint'] . "','0','0',
                  '" . $val['credit_left'] . "', '" . $val['credit_right'] . "',
                  '0','" . date('Y-m-d') . "','0'
                  
                ),
                
                (
                  '" . $val['userKey'] . "','" . $val['userKey'] . "',
                  '" . $val['currLeftPoint'] . "','" . $val['currRightPoint'] . "',
                  '" . $val['balLeft'] . "','" . $val['balRight'] . "',
                  '" . $val['debit_left'] . "','" . $val['debit_right'] . "','0','0',
                  '" . $pay_master_id . "','" . date('Y-m-d') . "','0'
                  
                )";
                            $insert = $wpdb->query($insert_transac);

                            if ($insert) {
                                /**************| Update points in  {$wpdb->prefix}bmw_users table  |**********************/
                                $sql = "UPDATE {$wpdb->prefix}bmw_users 
                  SET 
                    left_point = '" . $val['balLeft'] . "',  
                    right_point = '" . $val['balRight'] . "',  
                    own_point = '" . $val['balOwn'] . "'
                  WHERE 
                    `user_id` = '" . $val['userId'] . "' AND 
                    `user_key` = '" . $val['userKey'] . "'";

                                $update = $wpdb->query($sql);


                                if ($update) {
                                    $message = __("Pay Cycle", 'bmw') . "(" . $pay_master_id . ")" . __("has been Calculated successfully. Now you can distribute money.", 'bmw');
                                } else {
                                    $message = __("Failed to update the points values of members.", 'bmw');
                                };
                            } else {
                                $message = __("Failed to insert the point transaction data.", 'bmw');
                            }
                        }
                    }
                } else {
                    $message = __("No member is eligible to get the Unit in the pay Cycle", 'bmw') . " (" . $pay_master_id . ").";
                }
                if ($message == '') {
                    $message = __("No member is eligible to get the Unit in the pay Cycle", 'bmw') . "(" . $pay_master_id . ").";
                }
                echo $message;
                die();
            }
        }
    }

    public function bmw_distribute_money()
    {
        global $wpdb;

        $Distribute_Money = new Bmw_Distribute_Money();
        // $data = $Distribute_Money->Payoutmoney($pid);
        $bmw_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmw_users WHERE payment_status='1'");
        /*------------------------------------------------------------------
        Saving the Calculated Money
        -------------------------------------------------------------------*/
        if (isset($_REQUEST['action'])) {
            $action = sanitize_text_field($_REQUEST['action']);
            if ($action == 'savemoney') {

                if (!empty($bmw_users)) {
                    foreach ($bmw_users as $values) {
                        if ($this->eligibility($values->user_key, $values->user_id)) {

                            $data = $Distribute_Money->Payoutmoney($values->user_key);
                            foreach ($data as $val) {
                                /******************| Insert Data into  payout Table |**********************/
                                if (isset($val['user_key']) && !empty($val['user_key']) && isset($val['commission_amount']) && !empty($val['commission_amount'])) {

                                    $insert_payout = "INSERT INTO {$wpdb->prefix}bmw_payout ( userid, date, units, commission_amount ) VALUES  ('" . $values->user_id . "', '" . date('Y-m-d') . "', '" . $val['commission_points'] . "', '" . $val['commission_amount'] . "')";

                                    $result_payout = $wpdb->query($insert_payout);
                                    $insert_id = $wpdb->insert_id;

                                    if ($result_payout) {
                                        $update_transac =  "UPDATE {$wpdb->prefix}bmw_point_transaction SET status = '1', payout_id='" . $insert_id . "' WHERE user_key = '" . $val['user_key'] . "' AND payout_id ='0'";
                                        $result_transac = $wpdb->query($update_transac);

                                        $wpdb->query("UPDATE {$wpdb->prefix}bmw_pv_detail SET status = '1' WHERE user_id = '" . $values->user_id . "'");

                                        if ($result_transac) {
                                            $message = __("Distribution run successfully", 'bmw');
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $message = __("No one is eligible to be paid the money in this pay cycle", 'bmw');
                }
            }
        }
        echo $message;
        die();
    }
}
