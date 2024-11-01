<?php
class Bmw_MyPayout
{
    use Letscms_BMW_CommonClass;
    public function bmw_member_mypayout($request)
    {
        global $wpdb;
        $userId = $request['user_id'];
        if (isset($userId)) {
            $sql = "SELECT DATE_FORMAT(`date`,'%d %b %Y') as payout_date,
						payout_id, units,commission_amount,bonus_amount,tds, service_charge
					FROM {$wpdb->prefix}bmw_payout
					WHERE userid = '" . $userId . "' 
					ORDER BY id desc";

            $results = $wpdb->get_results($sql);
            $i = 1;
            $data = array();
            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $data[$i]['payout_id'] = $row->payout_id;
                    $data[$i]['payout_date'] = $row->payout_date;
                    $data[$i]['units'] = $row->units;
                    $commission = $row->commission_amount;
                    $bonus = $row->bonus_amount;
                    $serviceCharge = $row->service_charge;
                    $tds = $row->tds;

                    $data[$i]['commission_amount'] = $commission;
                    $data[$i]['bonus_amount'] = $bonus;
                    $data[$i]['service_charge'] = $serviceCharge;
                    $data[$i]['tds'] = $tds;
                    $data[$i]['payableAmount'] = $commission + $bonus - $serviceCharge - $tds;
                    $i++;
                }
            } else {

                $data[$i]['payableAmount'] = '';

                $data[$i]['payout_id'] = '';
                $data[$i]['payout_date'] = __('No Payout Available', 'bmw');
                $data[$i]['units'] = '';
                $data[$i]['commission_amount'] = '';
                $data[$i]['bonus_amount'] = '';
                $data[$i]['service_charge'] = '';
                $data[$i]['tds'] = '';
                $data[$i]['payableAmount'] = '';
            }


?>
            <div class="clear"></div>
            <div class='wrap'>
                <!--------------------------------------------------------------------------
  My Payout Detail
---------------------------------------------------------------------------->
                <table id="data_table">
                    <thead>
                        <tr>
                            <th colspan="8">MyPayout Details</th>
                        </tr>
                        <tr>
                            <th><?php echo __('Payout Id', 'bmw'); ?></th>
                            <th><?php echo __('Date', 'bmw'); ?></th>
                            <th><?php echo __('Unit(s)', 'bmw'); ?></th>
                            <th><?php echo __('Commission', 'bmw'); ?></th>
                            <th><?php echo __('Bonus', 'bmw'); ?></th>
                            <th><?php echo __('Service Charges', 'bmw'); ?></th>
                            <th><?php echo __('TDS', 'bmw'); ?></th>
                            <th><?php echo __('Payable Amount', 'bmw'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $member) : ?>
                            <tr class="gradeX">
                                <td class="text-center"><?php echo $member['payout_id'] ?></td>
                                <td><?php echo $member['payout_date']  ?></td>
                                <td><?php echo $member['units'] ?></td>
                                <td><?php echo $member['commission_amount'] ?></td>
                                <td class="center"><?php echo $member['bonus_amount'] ?></td>
                                <td class="center"><?php echo $member['service_charge'] ?></td>
                                <td class="center"><?php echo $member['tds'] ?></td>
                                <td class="center"><?php echo $member['payableAmount'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
            </div>
<?php
        }
    }
}
