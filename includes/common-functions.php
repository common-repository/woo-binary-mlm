<?php
trait Letscms_BMW_CommonClass
{
    function letscms_check_user($id = '')
    {
        global $wpdb, $current_user;
        if (!is_user_logged_in()) {
            echo $url = site_url('wp-login.php');
            echo "<script>window.location='$url'</script>";
            exit;
        }
        if ($id == '') {
            $user_id = $current_user->ID;
        } else {
            $user_id = $id;
        }
        $user_meta = get_userdata($user_id);
        if (is_user_logged_in()) {
            $user_roles = $user_meta->roles;
            if (!empty($user_roles) && is_array($user_roles) && in_array('bmw_user', $user_roles)) {
                return true;
            } else {    ?>
                <div class="container">
                    <p>You are not a <b>MLM user</b>. To access this page , you must first register as MLM user . <br>Please contact the system admin at<?php echo get_option('admin_email') ?> to report this problem.</p>
                </div>
<?php die;
            }
        }
    }

    function getUserNameById($id)
    {
        global $wpdb;
        $name = '';
        $name .= ucfirst(strtolower(get_user_meta($id, 'first_name', true))) . ' ';
        $name .= ucfirst(strtolower(get_user_meta($id, 'last_name', true)));
        return $name;
    }
    function getUserNameByKey($user_key)
    {
        global $wpdb;
        $name = '';
        $id = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}bmw_users WHERE user_key='" . $user_key . "'");
        $sql = "SELECT user_login FROM {$wpdb->prefix}users WHERE ID='" . $id . "'";
        $username = $wpdb->get_var($sql);

        return $username;
    }
    function get_childs_user_by_payout_id($payout_id)
    {
        global $wpdb;
        $childs = [];
        $sql = "SELECT childs FROM {$wpdb->prefix}bmw_point_transaction WHERE payout_id = '" . $payout_id . "'";
        $results = $wpdb->get_var($sql);

        $users = unserialize($results);
        foreach ($users as $val) {

            $childs[] = $this->getUserNameByKey($val);
        }
        return $childs;
    }
    function checksponsorvalid($username)
    {
        global $wpdb;
        $id = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}users WHERE user_login='" . $username . "'");
        $user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmw_users WHERE user_id='" . $id . "'");
        if (!empty($user_key)) {
            return true;
        } else {
            return false;
        }
    }

    function getSponsoridByUserId($userid)
    {
        global $wpdb;
        if (isset($userid)) {
            $sql = "SELECT sponsor_key FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userid . "'";
            $sponsor_key = $wpdb->get_var($sql);
            $sql_user_id = "SELECT user_id FROM {$wpdb->prefix}bmw_users WHERE user_key = '" . $sponsor_key . "'";
            $sponsor_id = $wpdb->get_var($sql_user_id);
        }
        return $sponsor_id;
    }
    public function getInfoByUserId($userId)
    {
        global $wpdb;
        $results = [];
        if (!empty($userId)) {
            $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userId . "'");
        }
        return $results;
    }

    function getKeyByUserId($userid)
    {
        global $wpdb;
        if (isset($userid)) {
            $sql = "SELECT user_key FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userid . "'";
            $userKey = $wpdb->get_var($sql);
        }
        return $userKey;
    }

    function getUserNameByUserId($id)
    {
        global $wpdb;
        $sql = "SELECT user_login FROM $wpdb->users WHERE ID='" . $id . "'";
        $username = $wpdb->get_var($sql);
        return $username;
    }

    function getUserIdByKey($userKey)
    {
        global $wpdb;
        if (isset($userKey)) {
            $sql = "SELECT user_id FROM {$wpdb->prefix}bmw_users WHERE user_key = '" . $userKey . "'";
            $userId = $wpdb->get_var($sql);
            return $userId;
        }
        return false;
    }

    function checkInputField($value)
    {
        if ($value == "")
            return true;
        else
            return false;
    }

    function confirmPassword($pass, $confirm)
    {
        if ($confirm != $pass)
            return true;
        else
            return false;
    }

    function confirmEmail($email, $confirm)
    {
        if ($confirm != $email)
            return true;
        else
            return false;
    }

    /****************************To Register user*******************************/
    function letscms_generateKey()
    {
        $characters = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $keys = array();
        $length = 9;
        while (count($keys) < $length) {
            $x = mt_rand(0, count($characters) - 1);
            if (!in_array($x, $keys))
                $keys[] = $x;
        }
        $random_chars = '';
        foreach ($keys as $key) {
            $random_chars .= $characters[$key];
        }
        return $random_chars;
    }

    function checkKey($key)
    {
        global $wpdb;
        $query = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmw_users WHERE `user_key` = '" . $key . "'");
        if ($wpdb->num_rows < 1) {
            return false;
        }
        return true;
    }

    function checkallowed($key, $leg = NULL)
    {
        global $wpdb;
        $query = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}bmw_users WHERE leg = '" . $leg . "' AND parent_key = '" . $key . "'");
        $num = $wpdb->num_rows;
        return $num;
    }

    function GetUserInfoById($id)
    {
        global $wpdb;
        $sql = "SELECT ID,user_login,user_pass,user_email FROM `{$wpdb->users}` WHERE ID = '" . $id . "'";
        $results = $wpdb->get_results($sql);
        $userDetail = array();
        if ($results && $wpdb->num_rows > 0) {
            foreach ($results as $row) {
                $userId = $row->ID;
                $userDetail['id'] = $userId;
                $userDetail['userlogin'] = $row->user_login;
                $userDetail['email'] = $row->user_email;
                $userDetail['name'] = $this->getUserNameById($userId);
                $userDetail['userKey'] = $this->getKeyByUserId($userId);
            }
        }
        return $userDetail;
    }

    function getSponsorName($sponsor_key)
    {
        global $wpdb;
        $referrer = '';
        if (isset($sponsor_key)) {
            $sql = "SELECT `user_id` FROM {$wpdb->prefix}bmw_users WHERE `user_key` = '" . $sponsor_key . "'";
            $user_id = $wpdb->get_var($sql);
            if ($wpdb->num_rows == 1) {
                $sql = "SELECT user_login FROM $wpdb->users WHERE ID = '" . $user_id . "'";
                $referrer = $wpdb->get_var($sql);
            }
        }
        return $referrer;
    }

    function getReferrerByKey($userKey)
    {
        global $wpdb;
        $referrer = '';
        if (isset($userKey)) {
            $sql = "SELECT `user_id` FROM {$wpdb->prefix}bmw_users WHERE `user_key` = '" . $userKey . "'";
            $user_id = $wpdb->get_var($sql);
            if ($wpdb->num_rows == 1) {
                $referrer = $this->getUserNameById($user_id);
            }
        }
        return $referrer;
    }

    function get_current_user_key()
    {
        global $wpdb;
        $current_user = wp_get_current_user();
        $userId = $current_user->ID;
        $userKey = '';
        if (isset($userId) && $userId != '') {
            $result = $wpdb->get_var("SELECT `user_key` FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userId . "'");
            if ($result && $wpdb->num_rows > 0) {
                $userKey =  $result;
            }
        }
        return $userKey;
    }

    function getSponsorKeyBySponsorname($sponsorName)
    {
        global $wpdb;
        $sql = "SELECT ID FROM $wpdb->users WHERE user_login = '" . $sponsorName . "'";
        $ID = $wpdb->get_var($sql);

        if ($wpdb->num_rows == 1) {
            $userId = $ID;
        } else {
            $userId = '';
        }
        $sql1 = "SELECT user_key FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userId . "'";
        $result = $wpdb->get_var($sql1);

        if ($wpdb->num_rows == 1) {
            $user_key = $result;
            return $user_key;
        }
        return false;
    }


    function get_total_amount($arr)
    {
        $total_amount = 0;
        if (!empty($arr)) {
            $total_amount += $arr->commission_amount;
        }
        return $total_amount;
    }

    function MyPayoutDetails($userid)
    {
        // unit = points value
        global $wpdb;
        $sql = "SELECT DATE_FORMAT(`date`,'%d %b %Y') as payout_date, id, units, commission_amount FROM {$wpdb->prefix}bmw_payout WHERE userid = '" . $userid . "' ORDER BY id desc";
        $results = $wpdb->get_results($sql);
        $i = 1;
        $data = array();
        if ($wpdb->num_rows > 0) {
            foreach ($results as $row) {
                $data[$i]['payout_date'] = $row->payout_date;
                $data[$i]['payout_id'] = $row->id;
                $data[$i]['points'] = $row->units;
                // $commission = $row->commission_amount;
                $data[$i]['paidAmount'] = $row->commission_amount;
                $i++;
            }
        }
        return $data;
    }

    function checkValidParentKey($key)
    {
        global $wpdb;
        $sql = "SELECT user_id FROM {$wpdb->prefix}bmw_users WHERE user_key = '" . $key . "'";
        $query = $wpdb->get_var($sql);
        if ($wpdb->num_rows > 0)
            return true;
        else
            return false;
    }

    function checkValidSponsor($sponsorName)
    {
        global $wpdb;
        $sql = "SELECT ID FROM $wpdb->users WHERE user_login = '" . $sponsorName . "'";
        $results = $wpdb->get_var($sql);

        if ($wpdb->num_rows == 1) {

            $userId = $results;
            $sql1 = "SELECT user_key FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $userId . "'";
            $result1 = $wpdb->get_var($sql1);

            if ($wpdb->num_rows == 1) {
                return true;
            }
            return false;
        }
        return false;
    }

    function eligibility($user_key, $user_id)
    {

        global $wpdb;


        $eligibility_settings = get_option('bmw_eligibility_settings');
        $status = false;
        $min_personal_points = $wpdb->get_var("SELECT SUM(total_point) FROM {$wpdb->prefix}bmw_pv_detail WHERE user_id = '" . $user_id . "'");


        $total_no_of_personal_refer = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}bmw_users WHERE sponsor_key = '" . $user_key . "' AND payment_status='1'");

        $total_no_of_left_personal_refer = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}bmw_users WHERE sponsor_key = '" . $user_key . "' AND payment_status='1' AND leg='0'");

        $total_no_of_right_personal_refer = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}bmw_users WHERE sponsor_key = '" . $user_key . "' AND payment_status='1' AND leg='1'");

        $min_personal_points = isset($min_personal_points) ? $min_personal_points : 0;
        $total_no_of_personal_refer = isset($total_no_of_personal_refer) ? $total_no_of_personal_refer : 0;

        $total_no_of_left_personal_refer = isset($total_no_of_left_personal_refer) ? $total_no_of_left_personal_refer : 0;

        $total_no_of_right_personal_refer = isset($total_no_of_right_personal_refer) ? $total_no_of_right_personal_refer : 0;

        if (isset($min_personal_points) && $min_personal_points >= $eligibility_settings['bmw_personalpoint'] && isset($total_no_of_personal_refer) && $total_no_of_personal_refer >= $eligibility_settings['bmw_directreferrer'] && isset($total_no_of_left_personal_refer) && $total_no_of_left_personal_refer >= $eligibility_settings['bmw_leftreferrer'] && isset($total_no_of_right_personal_refer) && $total_no_of_right_personal_refer >= $eligibility_settings['bmw_rightreferrer']) {

            $status = true;
        } else {

            $status = false;
        }

        return $status;
    }
    function bmw_get_the_post_id_by_shortcode($shortcode)
    {
        global $wpdb;
        $sql = "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` IN('page','post') AND `post_content` LIKE '%" . $shortcode . "%' LIMIT 1";
        $page_id = $wpdb->get_var($sql);
        return apply_filters('bmw_get_the_post_id_by_shortcode', $page_id);
    }



    /************************************Payout functions**********************************
     **************************************************************************************/
    public function checkUserPaymentStatus($user_id)
    {
        global $wpdb;
        $return_val = 0;
        if (isset($user_id)) {
            $sql = "SELECT user_id, payment_status FROM {$wpdb->prefix}bmw_users WHERE user_id = '" . $user_id . "' ";
            $row = $wpdb->get_row($sql);

            if ($wpdb->num_rows > 0) {
                $payment_status = $row->payment_status;
                if ($payment_status == 2 || $payment_status == 1) {
                    $return_val = "TRUE";
                } else {
                    $return_val = "FALSE";
                }
            }
        }
        return $return_val;
    }

    /* Check the user Sponcered as per the settings left and right leg. */
    public function  checkSponsoredLeftAndRight($userKey)
    {
        global $wpdb;
        if (isset($userKey)) {
            $leftSide = 0;
            $rightSide = 0;
            $status = '';

            $sql = "SELECT user_id,user_key,sponsor_key,leg FROM {$wpdb->prefix}bmw_users WHERE sponsor_key = '" . $userKey . "' AND payment_status IN ('1','2')";
            $results = $wpdb->get_results($sql);

            if ($results && $wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $leg = $row->leg;

                    if ($leg == 0) {
                        $leftSide = $leftSide + 1;
                    } else if ($leg == 1) {
                        $rightSide = $rightSide + 1;
                    }
                }
                $referrerCriteria =  get_option('bmw_eligibility_settings', true);
                if (($leftSide >= $referrerCriteria['bmw_leftreferrer'] && $rightSide >= $referrerCriteria['bmw_rightreferrer']) || ($leftSide >= $referrerCriteria['bmw_rightreferrer'] &&  $rightSide >= $referrerCriteria['bmw_leftreferrer'])) {
                    $status = "TRUE";
                } else {
                    $status = "FALSE";
                }
            } else {
                $status = "FALSE";
            }
        }
        return $status;
    }

    /* Check the member has how many directly sponsored */
    public function checkDirectSponsored($userKey)
    {
        global $wpdb;
        if (isset($userKey)) {
            $totalCriteria = 0;
            $sql = "SELECT COUNT(user_key) AS total FROM {$wpdb->prefix}bmw_users 
					WHERE sponsor_key = '" . $userKey . "' AND payment_status IN ('1','2')";
            $count_member = $wpdb->get_var($sql);
            if ($count_member && $wpdb->num_rows > 0) {
                $total = $count_member;
                $referrerCriteria =  get_option('bmw_eligibility_settings', true);
                if ($total >= $referrerCriteria['bmw_directreferrer']) {
                    $totalCriteria = "TRUE";
                } else {
                    $totalCriteria = "FALSE";
                }
            }
        }
        return $totalCriteria;
    }

    public function GetNowRightPoint($leftPV, $rightPV, $ownPV)
    {
        $nowRightPV = 0;
        if ($leftPV > $rightPV) {
            $nowRightPV = $ownPV + $rightPV;
        } else if ($rightPV > $leftPV) {
            $nowRightPV = $rightPV;
        } else if ($leftPV = $rightPV) {
            $nowRightPV = $rightPV;
        }

        return $nowRightPV;
    }

    public function GetNowLeftPoint($leftPV, $rightPV, $ownPV)
    {

        if ($leftPV > $rightPV) {
            $nowLeftPV = $leftPV;
        } else if ($rightPV > $leftPV) {
            $nowLeftPV = $ownPV + $leftPV;
        } else if ($leftPV == $rightPV) {
            $nowLeftPV = $leftPV;
        }
        return $nowLeftPV;
    }

    public function GetNowOwnPoint($leftPV, $rightPV, $ownPV)
    {

        if ($leftPV > $rightPV) {
            $nowOwnPV = 0;
        } else if ($rightPV > $leftPV) {
            $nowOwnPV = 0;
        } else if ($leftPV == $rightPV) {
            $nowOwnPV = $ownPV;
        }
        return $nowOwnPV;
    }

    public function getUnit($leftcount, $rightcount)
    {
        $directCaseArr = $this->getUnitByDirectCase($leftcount, $rightcount);
        $oppisiteCaseArr = $this->getUnitByOppositeCase($leftcount, $rightcount);

        if ($directCaseArr['unit'] >= $oppisiteCaseArr['unit']) {
            $returnArr = $directCaseArr;
        } else {
            $returnArr = $oppisiteCaseArr;
        }
        return $returnArr;
    }

    public function getUnitByDirectCase($leftcount, $rightcount)
    {
        $pair = get_option('bmw_payout_settings');
        $pair1 = $pair['bmw_pair1'];
        $pair2 = $pair['bmw_pair2'];

        $leftunit = (int)($leftcount / $pair1);
        $rightunit = (int)($rightcount / $pair2);

        if ($leftunit <= $rightunit)
            $unit = $leftunit;
        else
            $unit = $rightunit;

        $leftbalance = $leftcount - ($unit * $pair1);
        $rightbalance = $rightcount - ($unit * $pair2);

        $array['leftbal'] = $leftbalance;
        $array['rightbal'] = $rightbalance;
        $array['unit'] = $unit;

        return $array;
    }

    public function getUnitByOppositeCase($leftcount, $rightcount)
    {
        $pair = get_option('bmw_payout_settings');
        $pair1 = $pair['bmw_pair2'];
        $pair2 = $pair['bmw_pair1'];

        $leftunit = (int)($leftcount / $pair1);
        $rightunit = (int)($rightcount / $pair2);

        if ($leftunit <= $rightunit)
            $unit = $leftunit;
        else
            $unit = $rightunit;

        $leftbalance = $leftcount - ($unit * $pair1);
        $rightbalance = $rightcount - ($unit * $pair2);

        $array['leftbal'] = $leftbalance;
        $array['rightbal'] = $rightbalance;
        $array['unit'] = $unit;

        return $array;
    }

    public function getComission($userid, $unit)
    {
        global $wpdb;
        $sql = "SELECT sum(units) FROM {$wpdb->prefix}bmw_payout WHERE userid = '" . $userid . "' ";
        $pre_unit = $wpdb->get_var($sql);
        $totalUnit = $pre_unit + $unit;

        /*Rate Criteria */
        $payout = get_option('bmw_payout_settings', true);

        $initialrate      = $payout['bmw_initialrate'];
        $initialunits         = $payout['bmw_initialunits'];
        $furtheramount     = $payout['bmw_furtheramount'];

        if ($totalUnit < $initialunits) {
            $pay = $unit * $initialrate;
        } elseif ($totalUnit >= $initialunits) {
            if ($pre_unit <= $initialunits) {
                $pay1 = $initialunits - $pre_unit;
                $pay2 = $unit - $pay1;
                $pay = $pay1 * $initialrate + $pay2 * $furtheramount;
            } else {
                $pay = $unit * $furtheramount;
            }
        }

        return $pay;
    }

    public function getMembersByPayoutId($payoutId)
    {
        global $wpdb;
        $sql = "SELECT COUNT(id) AS totalMembers, SUM(commission_amount) AS commission FROM {$wpdb->prefix}bmw_payout
				WHERE payout_id=" . $payoutId . "";

        $results = $wpdb->get_results($sql);
        $num = $wpdb->num_rows;
        $total = 0;
        $returnArr = array();
        if ($results && $num > 0) {
            foreach ($results as $row) {
                $returnArr['members'] = $row->totalMembers;
                $returnArr['commission'] = $row->commission;
                $returnArr['totalAmount'] = $row->commission;
            }
        }
        return $returnArr;
    }
    public function getchildsMembersByPayoutId($payoutId)
    {
        global $wpdb;
        $no_childs = 0;
        $sql = "SELECT childs FROM {$wpdb->prefix}bmw_point_transaction WHERE payout_id=" . $payoutId . "";

        $results = $wpdb->get_var($sql);
        if (!empty($results)) {
            $childs = unserialize($results);
        }
        $no_childs = count($childs);
        return $no_childs;
    }



    /*==========================GENEALOGY FUNCTIONS================================*/
    public function MyNetwork($userKey, $level)
    {

        $levelArr = array();
        $levelArr[0] = $this->LevelZero($userKey);

        for ($i = 1, $j = 0; $i <= $level; $i++, $j++) {
            $levelArr[$i] = $this->BuildLevelArr($levelArr[$j]);
        }
        return $levelArr;
    }

    /*----------------------------------------------------------------------------------
	Top of the network
	------------------------------------------------------------------------------------*/
    public function LevelZero($userKey)
    {
        global $wpdb;
        if (isset($userKey)) {
            $sql = "SELECT user_id,user_key,parent_key,sponsor_key, DATE_FORMAT(`created_at`,'%d %M %Y') as creationDate, 
							payment_status, paid_at
					FROM {$wpdb->prefix}bmw_users 
					WHERE `user_key` = '" . $userKey . "'";
            $results = $wpdb->get_results($sql);

            $i = 0;
            $data = array();

            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $data[$i]['name'] = $this->getUserNameById($row->user_id);
                    $data[$i]['username'] = $this->getUserNameByUserId($row->user_id);
                    $data[$i]['leg'] = '';
                    $data[$i]['userKey'] = $row->user_key;
                    $data[$i]['parentKey'] = $row->parent_key;
                    $data[$i]['sponsorKey'] = $row->sponsor_key;
                    $data[$i]['payment_status'] = ($row->payment_status == 1 || $row->payment_status == 2) ? 'paid' : 'unpaid';
                    $data[$i]['created'] = $row->creationDate;

                    $i++;
                }
                $newArr = array($data);
                return $newArr;
            }
        }
    }

    /*----------------------------------------------------------------------------------
	Level others
	------------------------------------------------------------------------------------*/
    public function BuildLevelArr($levelArr)
    {
        global $wpdb;

        if (isset($levelArr) && count($levelArr) > 0) {
            $i = 0;
            $data = array();
            foreach ($levelArr as $details => $rows) {
                foreach ($rows as $row) {
                    if (isset($row['userKey']) && $row['userKey'] != '') {
                        $data[$i] = $this->getChildDetailByParent($row['userKey']);

                        $i++;
                    }
                }
            }
            return $data;
        }
    }

    public function getChildDetailByParent($key)
    {
        global $wpdb;
        if (isset($key)) {
            $sql =     "SELECT user_id,user_key,sponsor_key,leg,DATE_FORMAT(`created_at`,'%d %M %Y') as creationDate, 
							payment_status, paid_at
						FROM {$wpdb->prefix}bmw_users
						WHERE `parent_key` = '" . $key . "'
						ORDER BY leg desc";
            $results =     $wpdb->get_results($sql);

            //echo'<pre>';print_r($key);die;	
            $i = 0;
            $data = array();
            if ($wpdb->num_rows == 2) {
                foreach ($results as $row) {
                    $data[$i]['name'] = $this->getUserNameById($row->user_id);
                    $data[$i]['username'] = $this->getUserNameByUserId($row->user_id);
                    $data[$i]['userKey'] = $row->user_key;
                    $data[$i]['parentKey'] = $key;
                    $data[$i]['sponsorKey'] = $row->sponsor_key;
                    $data[$i]['payment_status'] = ($row->payment_status == 1 || $row->payment_status == 2) ? 'paid' : 'unpaid';
                    $data[$i]['created'] = $row->creationDate;
                    $data[$i]['leg'] = $row->leg;
                    $i++;
                }
            } else if ($wpdb->num_rows == 1) {
                foreach ($results as $row) {
                    $leg = $row->leg;

                    if ($leg == 0) {
                        $data[0]['name'] = $this->getUserNameById($row->user_id);
                        $data[0]['username'] = $this->getUserNameByUserId($row->user_id);
                        $data[0]['userKey'] = $row->user_key;
                        $data[0]['sponsorKey'] = $row->sponsor_key;
                        $data[0]['parentKey'] = $key;
                        $data[0]['payment_status'] = ($row->payment_status == 1 || $row->payment_status == 2) ? 'paid' : 'unpaid';
                        $data[0]['created'] = $row->creationDate;
                        $data[0]['leg'] = $row->leg;

                        $data[1]['name'] = '<a href="' . $this->addMemberLink($key, 'right') . '">Add</a>';
                        $data[1]['username'] = '';
                        $data[1]['userKey'] = '';
                        $data[1]['payment_status'] = 'unpaid';
                        $data[1]['parentKey'] = $key;
                        $data[1]['sponsorKey'] = '';
                        $data[1]['created'] = '';
                        $data[1]['leg'] = 1;
                    } else {

                        $data[0]['name'] = '<a href="' . $this->addMemberLink($key, 'left') . '">Add</a>';
                        $data[0]['username'] = '';
                        $data[0]['userKey'] = '';
                        $data[0]['parentKey'] = $key;
                        $data[0]['sponsorKey'] = '';
                        $data[0]['payment_status'] = 'unpaid';
                        $data[0]['created'] = '';
                        $data[0]['leg'] = 0;

                        $data[1]['name'] =  $this->getUserNameById($row->user_id);
                        $data[1]['username'] = $this->getUserNameByUserId($row->user_id);
                        $data[1]['userKey'] = $row->user_key;
                        $data[1]['parentKey'] = $key;
                        $data[1]['sponsorKey'] = $row->sponsor_key;
                        $data[1]['payment_status'] = ($row->payment_status == 1 || $row->payment_status == 2) ? 'paid' : 'unpaid';
                        $data[1]['created'] = $row->creationDate;
                        $data[1]['leg'] = $row->leg;
                    }
                }
            } else {


                $data[0]['name'] = '<a href="' . $this->addMemberLink($key, 'left') . '">Add</a>';
                $data[0]['username'] = '';
                $data[0]['userKey'] = '';
                $data[0]['parentKey'] = $key;
                $data[0]['sponsorKey'] = '';
                $data[0]['payment_status'] = '';
                $data[0]['created'] = '';
                $data[0]['leg'] = 0;

                $data[1]['name'] = '<a href="' . $this->addMemberLink($key, 'right') . '">Add</a>';
                $data[1]['username'] = '';
                $data[1]['userKey'] = '';
                $data[1]['parentKey'] = $key;
                $data[1]['sponsorKey'] = '';
                $data[1]['payment_status'] = '';
                $data[1]['created'] = '';
                $data[1]['leg'] = 1;
            }
            return $data;
        }
    }

    /*end of the class*/

    public function addMemberLink($parent, $leg)
    {
        if ($leg == 'right') {
            $leg = 1;
        } else {
            $leg = 0;
        }

        $reg_page_id = $this->bmw_get_the_post_id_by_shortcode('[bmw_registration]');
        $reg_page_link = '?page_id=' . $reg_page_id . '&k=' . $parent . '&l=' . $leg;

        return $reg_page_link;
    }

    public function bmw_register_mlm_user($user_id, $order_id)
    {
        global $wpdb;
        $settings = get_option('bmw_general_settings');
        if (isset($settings['letscms_purchase_reg']) && $settings['letscms_purchase_reg'] == 1) {
            $sponsor_name = get_post_meta($order_id, 'bmw_sponsor', true);
            $leg = get_post_meta($order_id, 'bmw_placement', true);
            wp_update_user(array('ID' => $user_id, 'role' => 'bmw_user'));
            //generate random numeric key for new user registration
            $user_key = $this->letscms_generateKey();
            //if generated key is already exist in the DB then re-generate key
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

            $sponsor = $this->getSponsorKeyBySponsorname($sponsor_name);
            $sp_key = $sponsor;
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
	                  '" . $user_id . "','" . $user_key . "', '" . $parent_key . "', '" . $sponsor . "', '" . $leg . "',
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
        } // end of if condition 
        else {
            return;
        }
    }


    function get_pair_commission($user_id, $user_key)
    {
        global $wpdb;
        $eligibility_settings = get_option('bmw_eligibility_settings');
        $payout_settings = get_option('bmw_payout_settings');
        // $getInfoByUserId         = $this->getInfoByUserId($user_id); //retrun the array.
        // print_r($getInfoByUserId);       

        $total_points = $wpdb->get_var("SELECT SUM(total_point) FROM {$wpdb->prefix}bmw_pv_detail WHERE user_id = '" . $user_id . "' and status='0'");
        $total_points = isset($total_points) ? $total_points : 0;
        do {
            $childs = array();
            $commission_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bmw_point_transaction WHERE user_key='" . $user_key . "'");

            $pair1 = $payout_settings['bmw_pair1'];
            $pair2 = $payout_settings['bmw_pair2'];

            $leftQuery = $wpdb->get_results("SELECT bh.user_key FROM {$wpdb->prefix}bmw_leftleg as bh JOIN {$wpdb->prefix}bmw_users as  u ON u.user_key=bh.user_key WHERE bh.parent_key='" . $user_key . "' AND bh.sponsor_key='" . $user_key . "'  AND bh.comm_status = '0' AND u.payment_status='1' ORDER BY bh.id ASC LIMIT $pair1");

            $rightQuery = $wpdb->get_results("SELECT bh.user_key FROM {$wpdb->prefix}bmw_rightleg as bh JOIN {$wpdb->prefix}bmw_users as  u ON u.user_key=bh.user_key  WHERE bh.parent_key='" . $user_key . "' AND bh.sponsor_key='" . $user_key . "'  AND bh.comm_status = '0' AND u.payment_status='1' ORDER BY bh.id ASC LIMIT $pair2");

            if (COUNT($leftQuery) < $pair1 || COUNT($rightQuery) < $pair2) {

                $leftQuery = $wpdb->get_results("SELECT bh.user_key FROM {$wpdb->prefix}bmw_leftleg as bh JOIN {$wpdb->prefix}bmw_users as  u ON u.user_key=bh.user_key WHERE bh.parent_key='" . $user_key . "' AND bh.sponsor_key='" . $user_key . "'  AND bh.comm_status = '0' AND u.payment_status='1' ORDER BY bh.id ASC LIMIT $pair2");

                $rightQuery = $wpdb->get_results("SELECT bh.user_key FROM {$wpdb->prefix}bmw_rightleg as bh JOIN {$wpdb->prefix}bmw_users as  u ON u.user_key=bh.user_key  WHERE bh.parent_key='" . $user_key . "' AND bh.sponsor_key='" . $user_key . "'  AND bh.comm_status = '0' AND u.payment_status='1' ORDER BY bh.id ASC LIMIT $pair1");
            }

            if (COUNT($leftQuery) >= $pair1 && COUNT($rightQuery) >= $pair2) {
                foreach ($rightQuery as $RQ) {
                    $wpdb->query("UPDATE {$wpdb->prefix}bmw_rightleg  SET comm_status = '1' WHERE parent_key = '" . $user_key . "'  AND user_key='" . $RQ->user_key . "' AND sponsor_key='" . $user_key . "'  LIMIT 1 ");
                    $childs[] = $RQ->user_key;
                }

                foreach ($leftQuery as $LQ) {
                    $wpdb->query(" UPDATE {$wpdb->prefix}bmw_leftleg  SET comm_status = '1' WHERE parent_key = '$user_key'  AND user_key='" . $LQ->user_key . "' AND sponsor_key='" . $user_key . "' LIMIT 1");
                    $childs[] = $LQ->user_key;
                }

                if ($commission_count == 0 || $commission_count < $payout_settings['bmw_initialunits']) {

                    $commission_amount = $payout_settings['bmw_initialrate'];
                } else {

                    $commission_amount = $payout_settings['bmw_furtheramount'];
                }
                if (isset($commission_amount) && !empty($commission_amount)) {
                    $wpdb->query("INSERT INTO {$wpdb->prefix}bmw_point_transaction  SET user_key='" . $user_key . "', childs='" . serialize($childs) . "', commission_amount='" . $commission_amount . "', status='0', commission_points='" . $total_points . "', date='" . date('Y-m-d') . "'");
                }
            } else if (COUNT($leftQuery) >= $pair2 && COUNT($rightQuery) >= $pair1) {
                foreach ($rightQuery as $RQ) {
                    $wpdb->query("UPDATE {$wpdb->prefix}bmw_rightleg  SET comm_status = '1' WHERE parent_key = '" . $user_key . "'  AND user_key='" . $RQ->user_key . "' AND sponsor_key='" . $user_key . "'  LIMIT 1 ");
                    $childs[] = $RQ->user_key;
                }

                foreach ($leftQuery as $LQ) {
                    $wpdb->query(" UPDATE {$wpdb->prefix}bmw_leftleg  SET comm_status = '1' WHERE parent_key = '$user_key'  AND user_key='" . $LQ->user_key . "' AND sponsor_key='" . $user_key . "' LIMIT 1");
                    $childs[] = $LQ->user_key;
                }


                if ($commission_count == 0 || $commission_count < $payout_settings['bmw_initialunits']) {
                    $commission_amount = $payout_settings['bmw_initialrate'];
                } else {
                    $commission_amount = $payout_settings['bmw_furtheramount'];
                }

                if (isset($commission_amount) && !empty($commission_amount)) {
                    $wpdb->query("INSERT INTO {$wpdb->prefix}bmw_point_transaction  SET user_key='" . $user_key . "', childs='" . serialize($childs) . "', amount='" . $commission_amount . "', status='0', commission_points='" . $total_points . "'");
                }
            }
        } while (COUNT($leftQuery) >= $pair1 && COUNT($rightQuery) >= $pair2);

        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmw_point_transaction WHERE user_key='" . $user_key . "' AND status='0'", ARRAY_A);
    }
}
