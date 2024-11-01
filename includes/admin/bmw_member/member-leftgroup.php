<?php
class Bmw_MyLeft{
	use Letscms_BMW_CommonClass;
	public function bmw_member_myleft_group($request){ 
		global $wpdb;
		if($request['user_id']){ 
			$userId = $request['user_id']; 
			$userKey = $this->getKeyByUserId($userId);
			
		} else{ 
			$userKey = $this->get_current_user_key();
		} 

		if(isset($userKey))
		{
			$sql = "SELECT user_key FROM {$wpdb->prefix}bmw_leftleg WHERE parent_key = '".$userKey."' ORDER BY id";
			$results = $wpdb->get_results($sql);				
			$data = array();
			$i=1;
			if($wpdb->num_rows>0)
			{
				foreach($results as $row)
				{
					$userKey = $row->user_key;
					$userId = $this->getUserIdByKey($userKey); 
					$userDetail = $this->GetUserInfoById($userId);	
					if($userDetail['payment_status']==0){
						$payment_status = "<span style='color:red;'>Unpaid</span>";
					} else {
						$payment_status = "<span style='color:green;'>Paid</span>";
					}
					$data[$i]['sno'] = $i;
                    $data[$i]['userlogin'] = $userDetail['userlogin'];
					$data[$i]['name'] = $userDetail['name'];
					$data[$i]['userKey'] = $userDetail['userKey'];
					$data[$i]['email'] = $userDetail['email'];
					$data[$i]['payment_status']= $payment_status;
				    $data[$i]['creationDate'] = $userDetail['creationDate'];
					$i++;
				}
					
			}else{
					$data[$i]['sno'] = '';
                    $data[$i]['userlogin'] = '';
					$data[$i]['name'] = __('No Consultant Found','bmw');
					$data[$i]['userKey'] = '';
					$data[$i]['email'] = '';
					$data[$i]['payment_status']= '';
					$data[$i]['creationDate'] = '';
			}
	}
	?>
<div class="clear"></div>
<div class='wrap'>
<!-- ------------------------------------------------------------------------
  Left Group Detail
-------------------------------------------------------------------------- -->
<table id="data_table">
	<thead>
		<tr>
			<th colspan="6"><?php echo __('Left Group Details','bmw');?></th>
		</tr>
		<tr>
			<th><?php echo __('User Name','bmw');?></th>
			<th><?php echo __('Name','bmw');?></th>
			<th><?php echo __('Member Key','bmw');?></th>
			<th><?php echo __('E-Mail','bmw');?></th>
			<th><?php echo __('Status','bmw');?></th>
			<th><?php echo __('Joining Date','bmw');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($data as $member) : ?>
		<tr>
			<td align="center"><?php echo $member['userlogin'] ?></td>
			<td><?php echo $member['name']  ?></td>
			<td><?php echo $member['userKey'] ?></td>
			<td><?php echo $member['email'] ?></td>
			<td class="center"><?php echo $member['payment_status'] ?></td>
			<td class="center"><?php echo $member['creationDate'] ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>

<?php
}
}