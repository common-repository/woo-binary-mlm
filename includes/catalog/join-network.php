<?php
class BMW_Join_Network
{
    use Letscms_BMW_CommonClass;
    public function view_join_network()
    {
        global $wpdb;
        $error = array();
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        if (!is_user_logged_in()) {
            echo $url = site_url('wp-login.php');
            echo "<script>window.location='$url'</script>";
            exit;
        }
        $user_meta = get_userdata($user_id);
        $user_roles = $user_meta->roles;

        if (!empty($user_roles) && in_array('bmw_user', $user_roles, true)) {
            $name = $current_user->display_name;
            echo "Hi <span style='color:green'>" . $name . "</span>, You are already MLM User";
            exit;
        }
        $adminajax = "'" . admin_url('admin-ajax.php') . "'";

        if (isset($_POST['submit'])) {
            $sponsor = sanitize_text_field($_POST['sponsor']);
            if (isset($_POST['leg'])) {
                $leg = sanitize_text_field($_POST['leg']);
            } else {
                $leg = '';
            }
            if (empty($sponsor)) {
                $error['sponsor_err'] = __('Sponsor could not be empty', 'bmw');
            }
            if (empty($leg)) {
                $error['leg_err'] = __('Position could not be empty', 'bmw');
            }

            if (isset($sponsor) && isset($leg) && empty($error)) {

                $user_key = $this->letscms_generateKey();
                //check if generated key already exist in network, then regenerate it
                do {
                    global $wpdb;
                    $sql = "SELECT COUNT(*) ck FROM {$wpdb->prefix}bmw_users WHERE `user_key` = '" . $user_key . "'";

                    $check = $wpdb->get_var($sql);

                    $flag = 1;
                    if ($check == 1) {
                        $user_key = $this->letscms_generateKey();
                        $flag = 0;
                    }
                } while ($flag == 0);

                $sponsor_key = $this->getSponsorKeyBySponsorname($sponsor);
                $sp_key = $sponsor_key;
                do {
                    $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmw_users WHERE parent_key = '" . $sp_key . "' AND 
                              leg = '" . $leg . "'";

                    $parentquery = $wpdb->get_row($sql);
                    $num = $wpdb->num_rows;
                    if ($num) {
                        $sp_key = $parentquery->user_key;
                    }
                } while ($num == 1);

                $parent_key = $sp_key;

                $insert = "INSERT INTO {$wpdb->prefix}bmw_users
                    (
                    user_id, user_key, parent_key, sponsor_key, leg, 
                    payment_status, qualification_point, left_point,right_point,own_point,
                    created_at,paid_at
                    ) 
                    VALUES
                    (
                      '" . $user_id . "','" . $user_key . "', '" . $parent_key . "', '" . $sponsor_key . "', '" . $leg . "',
                      '0','0','0','0','0','" . date('Y-m-d H:i:s') . "',''
                      
                    )";

                // if all data successfully inserted
                if ($wpdb->query($insert)) { //begin most inner if condition

                    //entry on left leg and Right leg
                    if ($leg == 0) {
                        $insert = "INSERT INTO {$wpdb->prefix}bmw_leftleg (`id`, `parent_key`,`user_key`) VALUES ('', '" . $parent_key . "','" . $user_key . "')";
                        $insert = $wpdb->query($insert);
                    } else if ($leg == 1) {
                        $insert = "INSERT INTO {$wpdb->prefix}bmw_rightleg (`id`, `parent_key`,`user_key`) VALUES ('', '" . $parent_key . "','" . $user_key . "')";
                        $insert = $wpdb->query($insert);
                    }

                    while ($parent_key != '0') {
                        $sql = "SELECT `parent_key`, `leg` FROM {$wpdb->prefix}bmw_users WHERE `user_key` = '" . $parent_key . "'";
                        $result = $wpdb->get_row($sql);
                        $num_rows = $wpdb->num_rows;

                        if ($num_rows) {
                            if ($result->parent_key != '0') {
                                if ($result->leg == 1) {
                                    $insert = "INSERT INTO {$wpdb->prefix}bmw_rightleg (`id`, `parent_key`,`user_key`) 
                      VALUES ('','" . $result->parent_key . "','" . $user_key . "')";
                                    $insert = $wpdb->query($insert);
                                } else {
                                    $insert = "INSERT INTO {$wpdb->prefix}bmw_leftleg (`id`, `parent_key`,`user_key`) 
                      VALUES ('','" . $result->parent_key . "','" . $user_key . "')";
                                    $insert = $wpdb->query($insert);
                                }
                            }
                            $parent_key = $result->parent_key;
                        } else {
                            $parent_key = '0';
                        }
                    }
                }
                wp_update_user(array('ID' => $current_user->ID, 'role' => 'bmw_user'));
                echo "<script>window.location=reload();</script>";
            } // end of if condition 
        }


?>

        <h2><?php echo __('Join Network', 'bmw'); ?></h2>
        <form name="frm" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

            <table class="table table-hover">
                <tr>
                    <td><?php echo __('Sponsor Name', 'bmw'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="sponsor" id="sponsor" value="<?php if (!empty($_POST['sponsor'])) {
                                                                                    __(htmlentities($_POST['sponsor']));
                                                                                } ?>" maxlength="20" size="37" onBlur="checkReferrerAvailability(<?php echo $adminajax; ?>,this.value);">
                        <div id="check_referrer" style="color:red;"><?php if (isset($error['sponsor_err']) && !empty($error['sponsor_err'])) {
                                                                        echo $error['sponsor_err'];
                                                                    } ?></div>
                    </td>
                </tr>

                <tr>
                    <td><?php echo __('Position', 'bmw'); ?> <span style="color:red;">*</span> :</td>
                    <?php
                    if (isset($_POST['leg']) && sanitize_text_field($_POST['leg']) == '0') {
                        $checked = 'checked';
                    } else if (isset($_GET['l']) && $_GET['l'] == '0') {
                        $checked = 'checked';
                        $disable_leg = 'disabled';
                    } else {
                        $checked = '';
                    }

                    if (isset($_POST['leg']) && sanitize_text_field($_POST['leg']) == '1') {
                        $checked1 = 'checked';
                    } else if (isset($_GET['l']) && $_GET['l'] == '1') {
                        $checked1 = 'checked';
                        $disable_leg = 'disabled';
                    } else {
                        $checked1 = '';
                    }
                    ?>
                    <td><?php echo __('Left', 'bmw') ?> <input id="left" type="radio" name="leg" value="0" <?php echo $checked; ?> <?php
                                                                                                                                    if (!empty($disable_leg)) {
                                                                                                                                        _e($disable_leg);
                                                                                                                                    }
                                                                                                                                    ?> />
                        <?php echo __('Right', 'bmw') ?><input id="right" type="radio" name="leg" value="1" <?php echo $checked1; ?> <?php
                                                                                                                                        if (!empty($disable_leg)) {
                                                                                                                                            _e($disable_leg);
                                                                                                                                        }
                                                                                                                                        ?> />
                        <div style="color: red"><?php if (isset($error['leg_err']) && !empty($error['leg_err'])) {
                                                    echo $error['leg_err'];
                                                } ?>
                    </td>
                </tr>


                <tr>
                    <td colspan="2">
                        <button class="btn btn-primary" name="submit" id="bmw_join_network" type="submit"><?php echo  __('Submit', 'bmw') ?> </button>
                </tr>
            </table>
        </form>
<?php
    }
}
