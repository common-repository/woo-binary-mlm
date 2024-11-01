<?php
class BMW_Right_group{
	use Letscms_BMW_CommonClass;

	public function bmw_myright_group($userKey){
		global $wpdb;
		if(isset($userKey))
			{
				$sql = "SELECT user_key FROM {$wpdb->prefix}bmw_rightleg WHERE parent_key = '".$userKey."' ORDER BY id";
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
						$data[$i]['sno'] = $i;
	                    $data[$i]['userlogin'] = $userDetail['userlogin'];
						$data[$i]['name'] = $userDetail['name'];
						$data[$i]['userKey'] = $userDetail['userKey'];
						$data[$i]['email'] = $userDetail['email'];
						$data[$i]['referrer']= $userDetail['referrer'];
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
			return $data; 
	}

	public function view_rightgroup(){
		global $wpdb;
		$data = array();
		$key = $this->get_current_user_key();
		$user_id = $this->getUserIdByKey($key); 
		
		$data 	= $this->bmw_myright_group($key); 
	 	$totalArr 	= $this->MyDirectGroupTotal($key); 

?>
<div class="container">
	<h2><?php echo __('Right Group Details','bmw');?></h2>
	<?php $this->letscms_check_user(); ?>
	<div class="row" >
		<div class="col-sm-4"><?php echo __('Total Right downlines : ','bmw');?><?php echo $totalArr['right']; ?></div>
	</div>
	<div id="clear"></div>
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-hover">
				<thead>
				    <tr>
				      <th scope="col"><?php echo __('S No.','bmw');?></th>
				      <th scope="col"><?php echo __('Right Member','bmw');?></th>
				      <th scope="col"><?php echo __('User Key','bmw');?></th>
				      <th scope="col"><?php echo __('Email','bmw');?></th>
				      <th scope="col"><?php echo __('Sponsor','bmw');?></th>
				      <th scope="col"><?php echo __('Joining Date','bmw');?></th>
				    </tr>
				</thead>
				<tbody>
				<?php
				foreach($data as $consultant){
				?>
					<tr>
						<td><?php echo $consultant['sno']; ?></td>
						<td><?php echo $consultant['userlogin']; ?></td>
						<td><?php echo $consultant['userKey']; ?></td>
						<td><?php echo $consultant['email']; ?></td>
						<td><?php echo $consultant['referrer']; ?></td>
						<td><?php echo $consultant['creationDate']; ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
	</div>
	</div>
</div>
<?php
	}
}