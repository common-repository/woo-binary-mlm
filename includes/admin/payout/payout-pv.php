<?php
class Bmw_Distribute_PV
{
    use Letscms_BMW_CommonClass;

    public function Payoutpv()
    {

        global $wpdb;
        $sql = "SELECT user_id, user_key, parent_key, left_point, right_point, own_point FROM {$wpdb->prefix}bmw_users";
        $results = $wpdb->get_results($sql);
        $data = array();
        $i = 1;
        $message = '';
        if ($wpdb->num_rows > 0) {
            foreach ($results as $val) {
                $creteria1 =  $this->checkUserPaymentStatus($val->user_id);
                $creteria2 =  $this->checkSponsoredLeftAndRight($val->user_key);
                $creteria3 =  $this->checkDirectSponsored($val->user_key);

                if ($creteria1 == "TRUE" &&  $creteria2 == "TRUE" &&  $creteria3 == "TRUE") {
                    $uid = $val->user_id;
                    $left_point = $val->left_point;
                    $right_point = $val->right_point;
                    $own_point = $val->own_point;

                    $currLeftPoint = $this->GetNowLeftPoint($left_point, $right_point, $own_point);
                    $currRightPoint = $this->GetNowRightPoint($left_point, $right_point, $own_point);
                    $currOwnPoint = $this->GetNowOwnPoint($left_point, $right_point, $own_point);

                    if ($left_point > $right_point) {
                        $credit_right = $own_point;
                        $credit_left = 0;
                    } else if ($right_point > $left_point) {
                        $credit_left = $own_point;
                        $credit_right = 0;
                    } elseif ($right_point = $left_point) {
                        $credit_left = 0;
                        $credit_right = 0;
                    } else {
                        $credit_left = 0;
                        $credit_right = 0;
                    }

                    $PayoutArr = $this->getUnit($currLeftPoint, $currRightPoint);
                    $unit = $PayoutArr['unit'];
                    $balLeft = $PayoutArr['leftbal'];
                    $balRight = $PayoutArr['rightbal'];
                    $balOwn = $currOwnPoint;

                    $debit_left = $currLeftPoint - $balLeft;
                    $debit_right = $currRightPoint - $balRight;
                    $debit_own = $currOwnPoint - $balOwn;

                    $data[$i]['userId'] = $val->user_id;
                    $data[$i]['FirstName'] = get_user_meta($val->user_id, 'first_name', true);
                    $data[$i]['LastName'] = get_user_meta($val->user_id, 'last_name', true);
                    $data[$i]['name'] = $data[$i]['FirstName'] . '&nbsp;' . $data[$i]['LastName'];
                    $data[$i]['userKey'] = $val->user_key;
                    $data[$i]['parentKey'] = $val->parent_key;
                    $data[$i]['left_point'] = $left_point;
                    $data[$i]['right_point'] = $right_point;
                    $data[$i]['own_point'] = $own_point;
                    $data[$i]['currLeftPoint'] = $currLeftPoint;
                    $data[$i]['currRightPoint'] = $currRightPoint;
                    $data[$i]['credit_left'] = $credit_left;
                    $data[$i]['credit_right'] = $credit_right;
                    $data[$i]['unit'] = $unit;
                    $data[$i]['balLeft'] =  $balLeft;
                    $data[$i]['balRight'] =  $balRight;
                    $data[$i]['balOwn'] =  $balOwn;
                    $data[$i]['debit_left'] =  $debit_left;
                    $data[$i]['debit_right'] = $debit_right;
                    $i++;
                }
            }
        }
        return $data;
    }



    public function distribute_points()
    {

        $action = '';
        $data = $this->Payoutpv();
        $adminajax = "'" . admin_url('admin-ajax.php') . "'";

?>
        <!--------------------------------------------------------------------
show the distribution of points
--------------------------------------------------------------------->
        <table id="data_table">
            <thead>
                <tr>
                    <th rowspan="2" scope="col"><?php echo __('S.No', 'bmw'); ?></th>
                    <th rowspan="2" scope="col"><?php echo __('Consultant Name', 'bmw'); ?></th>
                    <th rowspan="2" scope="col"><?php echo __('Units', 'bmw'); ?></th>
                    <th colspan="3" scope="col"><?php echo __('Points', 'bmw'); ?></th>
                    <th colspan="3" scope="col"><?php echo __('Current Points', 'bmw'); ?></th>
                    <th colspan="2" scope="col"><?php echo __('Debit Points', 'bmw'); ?></th>
                    <th colspan="3" scope="col"><?php echo __('Balance Points', 'bmw'); ?> </th>

                </tr>
                <tr>
                    <th scope="col"><?php echo __('Left', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Right', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Own', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Left', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Right', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Own', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Left', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Right', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Left', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Right', 'bmw'); ?></th>
                    <th scope="col"><?php echo __('Own', 'bmw'); ?></th>

                </tr>
            </thead>
            <?php
            $i = 1;
            $flag = 0;
            if (count($data) > 0) {
                foreach ($data as $val) {
                    if (isset($val['userKey']) && isset($val['parentKey']) && $val['unit'] > 0) {
                        $flag = 1; ?>
                        <tr>
                            <td style="width: 6%"><?php echo $i; ?></td>
                            <td><?php echo $val['name']; ?></td>
                            <td><?php echo $val['unit']; ?></td>
                            <td><?php echo $val['left_point']; ?></td>
                            <td><?php echo $val['right_point']; ?></td>
                            <td><?php echo $val['own_point']; ?></td>
                            <td><?php echo $val['currLeftPoint']; ?></td>
                            <td><?php echo $val['currRightPoint']; ?></td>
                            <td><?php echo $val['balOwn']; ?></td>
                            <td><?php echo $val['debit_left']; ?></td>
                            <td><?php echo $val['debit_right']; ?></td>
                            <td><?php echo $val['balLeft']; ?></td>
                            <td><?php echo $val['balRight']; ?></td>
                            <td><?php echo $val['balOwn']; ?></td>

                        </tr>
            <?php $i++;
                    } else {
                        echo "<tr><td></td><td>" . $val['name'] . "</td><td colspan='13'>" . __('This User is Not Eligible for Distribute Points', 'bmw') . " </td></tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='15'>" . __('There is no member found in network.', 'bmw') . "</td></tr>";
            }    ?>
            <tr>
                <td colspan='15'>
                    <button type="submit" name="distribute_pv" id="distribute_pv" class="button-primary" onclick="savingPayoutPoints(<?php echo $adminajax; ?>);" <?php echo (!empty($flag)) ? '' : 'disabled'; ?>><?php echo __('Distribute Point Value in the network', 'bmw'); ?>
                    </button>
                </td>
            </tr>
        </table>
        <div id="savePoints"></div>

<?php
        /**************| Close |**********************/
    }
}
