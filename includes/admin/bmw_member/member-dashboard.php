<?php
class Bmw_Dashboard
{
    use Letscms_BMW_CommonClass;

    /*----------------------------------------------------------------------------------
	Total Business (PV)
	------------------------------------------------------------------------------------*/
    public function TotalBusiness($userid)
    {
        global $wpdb;
        $userKey = $this->getKeyByUserId($userid);
        if (isset($userKey)) {
            $sql = "SELECT sum(credit_left) as credit_left , sum(credit_right) as credit_right FROM {$wpdb->prefix}bmw_point_transaction WHERE parent_key = '" . $userKey . "'";

            $total = array();
            $results = $wpdb->get_results($sql);
            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $total['left'] = $row->credit_left;
                    $total['right'] = $row->credit_right;
                    if ($row->credit_left == null) {
                        $total['left'] = 0;
                    }
                    if ($row->credit_right == null) {
                        $total['right'] = 0;
                    }

                    $total['total'] = $row->credit_left + $row->credit_right;
                }
            } else {
                $total['left'] = 0;
                $total['right'] = 0;
                $total['total'] = 0;
            }
            //echo "<pre>";print_r($total); exit; 
            return $total;
        }
    }

    /*----------------------------------------------------------------------------------
	My Left Leg Members
	------------------------------------------------------------------------------------*/
    public function MyTop5LeftLegMember($userid)
    {
        global $wpdb;
        $userKey = $this->getKeyByUserId($userid);
        if (isset($userKey)) {
            $sql = "SELECT user_key FROM {$wpdb->prefix}bmw_leftleg WHERE parent_key = '" . $userKey . "' ORDER BY id LIMIT 0,5";
            $results = $wpdb->get_results($sql);
            $MyLeftArr = array();
            $i = 1;
            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $MyLeftArr[$i]['sno'] = $i;
                    $userKey = $row->user_key;
                    $userId = $this->getUserIdByKey($userKey);
                    $userDetail = $this->GetUserInfoById($userId);
                    if ($userDetail['payment_status'] == 0) {
                        $payment_status = "<span style='color:red;'>Unpaid</span>";
                    } else {
                        $payment_status = "<span style='color:green;'>Paid</span>";
                    }

                    $MyLeftArr[$i]['name'] = $userDetail['name'];
                    $MyLeftArr[$i]['payment_status'] = $payment_status;
                    $i++;
                }
            } else {
                $MyLeftArr[$i]['name'] = __('No Consultant Found', 'bmw');
                $MyLeftArr[$i]['payment_status'] = '';
            }
            //echo "<pre>";print_r($MyLeftArr); exit; 
            return $MyLeftArr;
        }
    }

    /*----------------------------------------------------------------------------------
	My Right Leg Members
	------------------------------------------------------------------------------------*/
    public function MyTop5RightLegMember($userid)
    {
        global $wpdb;
        $userKey = $this->getKeyByUserId($userid);
        if (isset($userKey)) {

            $sql = "SELECT user_key FROM {$wpdb->prefix}bmw_rightleg WHERE parent_key = '" . $userKey . "' ORDER BY id LIMIT 0,5";
            $results = $wpdb->get_results($sql);
            $MyRightArr = array();
            $i = 1;
            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $MyRightArr[$i]['sno'] = $i;
                    $userKey = $row->user_key;
                    $userId = $this->getUserIdByKey($userKey);
                    $userDetail = $this->GetUserInfoById($userId);
                    if ($userDetail['payment_status'] == 0) {
                        $payment_status = "<span style='color:red;'>Unpaid</span>";
                    } else {
                        $payment_status = "<span style='color:green;'>Paid</span>";
                    }
                    $MyRightArr[$i]['name'] = $userDetail['name'];
                    $MyRightArr[$i]['payment_status'] = $payment_status;
                    $i++;
                }
            } else {
                $MyRightArr[$i]['name'] = __('No Consultant Found', 'bmw');
                $MyRightArr[$i]['payment_status'] = '';
            }
            //echo "<pre>";print_r($MyRightArr); exit; 
            return $MyRightArr;
        }
    }

    /*----------------------------------------------------------------------------------
	My Personal Sales 
	------------------------------------------------------------------------------------*/
    public function MyTop5PersonalSales($userid)
    {
        global $wpdb;
        $userKey = $this->getKeyByUserId($userid);
        if (isset($userKey)) {
            $sql = "SELECT user_id, payment_status FROM {$wpdb->prefix}bmw_users WHERE sponsor_key = '" . $userKey . "' ORDER BY created_at, user_id LIMIT 0,5";
            $results = $wpdb->get_results($sql);
            $i = 1;
            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $userDetail = $this->GetUserInfoById($row->user_id);
                    if ($userDetail['payment_status'] == 0) {
                        $payment_status = "<span style='color:red;'>Unpaid</span>";
                    } else {
                        $payment_status = "<span style='color:green;'>Paid</span>";
                    }
                    $data[$i]['name'] = $userDetail['name'];
                    $data[$i]['payment_status'] = $payment_status;
                    $i++;
                }
            } else {
                $data[$i]['name'] = __('No Consultant Found', 'bmw');
                $data[$i]['payment_status'] = '';
            }
            //echo "<pre>";print_r($MyLeftArr); exit; 
            return $data;
        }
    }

    /*----------------------------------------------------------------------------------
	Payout Details
	------------------------------------------------------------------------------------*/
    public function MyTop5PayoutDetails($userid)
    {
        global $wpdb;
        if (isset($userid)) {
            $sql = "SELECT DATE_FORMAT(`date`,'%d %b %Y') as payout_date,
						payout_id, units,commission_amount,affiliate_commission,bonus_amount,tds, service_charge
					FROM {$wpdb->prefix}bmw_payout
					WHERE userid = '" . $userid . "' 
					ORDER BY id desc
					LIMIT 0,5";
            $results = $wpdb->get_results($sql);
            $i = 1;
            $data = array();
            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $data[$i]['payout_date'] = $row->payout_date;
                    $data[$i]['payout_id'] = $row->payout_id;
                    $data[$i]['pair'] = $row->units;
                    $commission = $row->commission_amount;
                    $aff_comm = $row->affiliate_commission;
                    $bonus = $row->bonus_amount;
                    $serviceCharge = $row->service_charge;
                    $tds = $row->tds;
                    $data[$i]['paidAmount'] = $commission + $aff_comm + $bonus - $serviceCharge - $tds;
                    $i++;
                }
            } else {
                $data[$i]['payout_date'] = __('No Payout Available', 'bmw');
                $data[$i]['paidAmount'] = '';
            }
            return $data;
        }
    }

    /*----------------------------------------------------------------------------------
	My Right Leg Total 
	------------------------------------------------------------------------------------*/
    public function MyRightLegMemberTotal($userid)
    {
        global $wpdb;
        $userKey = $this->getKeyByUserId($userid);
        if (isset($userKey)) {
            $sql = "SELECT count(id) FROM {$wpdb->prefix}bmw_rightleg WHERE parent_key = '" . $userKey . "'";
            $rightlegtotal = $wpdb->get_var($sql);
            if ($wpdb->num_rows > 0) {
                $myRight['total'] = $rightlegtotal;
            } else {
                $myRight['total'] = 0;
            }
            return $myRight;
        }
    }

    /*----------------------------------------------------------------------------------
	My Left Leg Total 
	------------------------------------------------------------------------------------*/
    public function MyLeftLegMemberTotal($userid)
    {
        global $wpdb;
        $userKey = $this->getKeyByUserId($userid);
        if (isset($userKey)) {
            $sql = "SELECT count(id) FROM {$wpdb->prefix}bmw_leftleg WHERE parent_key = '" . $userKey . "'";
            $leftlegtotal = $wpdb->get_var($sql);
            if ($wpdb->num_rows > 0) {
                $myLeft['total'] = $leftlegtotal;
            } else {
                $myLeft['total'] = 0;
            }
            return $myLeft;
        }
    }

    /*----------------------------------------------------------------------------------
	My Personal Sales Total 	
	------------------------------------------------------------------------------------*/
    public function MyPersonalSalesTotal($userid)
    {
        global $wpdb;
        $userKey = $this->getKeyByUserId($userid);
        if (isset($userKey)) {
            $sql_count = "SELECT COUNT(id) FROM {$wpdb->prefix}bmw_users WHERE sponsor_key = '" . $userKey . "'";
            $personalsales = $wpdb->get_var($sql_count);
            if ($wpdb->num_rows > 0) {
                $MyPSTotal['total'] = $personalsales;
            } else {
                $MyPSTotal['total'] = '0';
            }
            return $MyPSTotal;
        }
    }


    public function bmw_member_dashboard($request)
    {
        $userId = $request['user_id'];
        $userDetail     = $this->GetUserInfoById($userId);
        $totalPoints    = $this->TotalBusiness($userId);
        $myLeftArr        = $this->MyTop5LeftLegMember($userId);
        $myRightArr        = $this->MyTop5RightLegMember($userId);
        $myPerSalesArr    = $this->MyTop5PersonalSales($userId);
        $payoutArr        = $this->MyTop5PayoutDetails($userId);
        $myRightTotal    = $this->MyRightLegMemberTotal($userId);
        $myLeftTotal    = $this->MyLeftLegMemberTotal($userId);
        $myPerSalesTotal = $this->MyPersonalSalesTotal($userId);
?>
        <div class="clear"></div>
        <div class='wrap'>
            <!-- 
	------------------------------------------------------------------------ 
	Personal Detail Page 
	-------------------------------------------------------------------------- 
-->
            <div class="row">
                <div class="col-6">
                    <table id="data_table">
                        <thead>
                            <tr>
                                <th colspan="2">Personal Details</th>
                            </tr>
                            <tr>
                                <th><?php echo __('Title', 'bmw'); ?></th>
                                <th><?php echo __('Details', 'bmw'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo __('User ID', 'bmw'); ?></td>
                                <td><?php echo $userDetail['id'] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo __('User Key', 'bmw'); ?></td>
                                <td><?php echo $userDetail['userKey'] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo __('Name', 'bmw'); ?></td>
                                <td><?php echo $userDetail['name'] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo __('Address', 'bmw'); ?></td>
                                <td><?php echo $userDetail['address1'] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo __('City', 'bmw'); ?></td>
                                <td><?php echo $userDetail['city'] ?></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table id="data_table">
                        <thead>
                            <tr>
                                <th colspan="2">Personal Sale </th>
                            </tr>
                            <tr>
                                <td>Total Members : <?php echo $myPerSalesTotal['total'] ?></td>
                                <td><a href="<?php echo admin_url('admin.php?page=dashboard-page&bmw-setting=member-info&tab=my-direct&user_id=' . $userDetail['id']) ?>"><?php echo __('View All Users', 'bmw'); ?></a></td>
                            </tr>
                            <tr>
                                <td class="head0"><?php echo __('Name', 'bmw'); ?></td>
                                <td class="head0"><?php echo __('Status', 'bmw'); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myPerSalesArr as $myPerSales) :
                            ?>
                                <tr>
                                    <td><?php echo $myPerSales['name']; ?></td>
                                    <td><?php echo $myPerSales['payment_status']; ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
        <div class="clear"></div>

        <div class='wrap'>
            <div class="row">
                <div class="col-6">
                    <table id="data_table">
                        <thead>
                            <tr>
                                <th colspan="3">Total Point Values</th>
                            </tr>
                            <tr>
                                <td class="head1"><?php echo __('Left', 'bmw'); ?></th>
                                <td class="head1"><?php echo __('Right', 'bmw'); ?></th>
                                <td class="head1"><?php echo __('Total', 'bmw'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $totalPoints['left'] ?></td>
                                <td><?php echo $totalPoints['right'] ?></td>
                                <td><?php echo $totalPoints['total'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table id="data_table">
                        <thead>
                            <tr>
                                <th colspan="3">Payout Details</th>
                            </tr>
                            <?php if (count($payoutArr) > 2) {  ?>
                                <tr>
                                    <th colspan="2"><a href="<?php echo admin_url('admin.php?page=dashboard-page&bmw-setting=member-info&tab=my-payout&user_id=' . $userDetail['id']) ?>"><?php echo __('View All Payout', 'bmw'); ?></a></th>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td class="head0"><?php echo __('Date', 'bmw'); ?></td>
                                <td class="head0"><?php echo __('Amount', 'bmw'); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payoutArr as $payout) : ?>
                                <tr>
                                    <td><?php echo $payout['payout_date'] ?></td>
                                    <td><?php echo $payout['paidAmount'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <div class="clear"></div>


        <div class='wrap'>
            <div class="row">
                <div class="col-6">
                    <table id="data_table">
                        <thead>
                            <tr>
                                <th colspan="3">My Left Leg</th>
                            </tr>
                            <tr>
                                <td>Total Members : <?php echo $myLeftTotal['total'] ?></td>
                                <td><a href="<?php echo admin_url('admin.php?page=dashboard-page&bmw-setting=member-info&tab=my-left&user_id=' . $userDetail['id']) ?>"><?php echo __('View All Users', 'bmw'); ?></a></td>
                            </tr>
                            <tr>
                                <td class="head0"><?php echo __('Name', 'bmw'); ?></th>
                                <td class="head0"><?php echo __('Status', 'bmw'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myLeftArr as $myleft) :
                            ?>

                                <tr>
                                    <td><?php echo $myleft['name'] ?></td>
                                    <td><?php echo $myleft['payment_status'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>
                </div>
                <div class="col-6">
                    <table id="data_table">
                        <thead>
                            <tr>
                                <th colspan="3">My Right Leg</th>
                            </tr>
                            <tr>
                                <td>Total Members : <?php echo $myRightTotal['total'] ?></td>
                                <td><a href="<?php echo admin_url('admin.php?page=dashboard-page&bmw-setting=member-info&tab=my-right&user_id=' . $userDetail['id']) ?>"><?php echo __('View All Users', 'bmw'); ?></a></td>
                            </tr>
                            <tr>
                                <td class="head0"><?php echo __('Name', 'bmw'); ?></td>
                                <td class="head0"><?php echo __('Status', 'bmw'); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myRightArr as $myright) :
                            ?>
                                <tr>
                                    <td><?php echo $myright['name'] ?></td>
                                    <td><?php echo $myright['payment_status'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <div class="clear"></div>


        <div class="clear"></div>
<?php

    }
}
