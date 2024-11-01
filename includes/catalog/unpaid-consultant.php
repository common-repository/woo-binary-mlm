<?php
class BMW_Unpaid_Consultant{
	use Letscms_BMW_CommonClass;
	public function UnpaidMembers($userKey)
	{ 
		if(isset($userKey))
		{
			$myLeft = $this->MyLeftLegMember($userKey); 
			$myRight = $this->MyRightLegMember($userKey);	
			
			if(count($myLeft)!=0 || count($myRight)!=0)
			{ 
				$consultant = array($myLeft, $myRight);
				return $consultant;
			
			}else{
				$data[0]['sno'] ='';
				$data[0]['name'] = __('No Consultant Found','bmw');
				$data[0]['userKey'] = '';
				$data[0]['referrer'] = '';
				$data[0]['payment_status']= '';
				$data[0]['leg']= '';
				$data[0]['creationDate']= '';
				$consultant = array($data);
				return $consultant;
			}	 
		}

	}

	public function view_unpaid_consultant(){
		global $current_user;
		
		$key = $this->get_current_user_key();
				$data			= $this->UnpaidMembers($key); 
				$totalLeft 		= $this->MyLeftLegMemberTotalUnpaid($key); 
				$totalRight 	= $this->MyRightLegMemberTotalUnpaid($key); 

?>
<div class="container">
	<h2><?php echo __('Unpaid Consultants Group Details','bmw');?></h2>
	<?php $this->letscms_check_user(); ?>
	<div class="row" >
		<div class="col-sm-4"><?php echo __('Left downlines : ','bmw');?><?php echo $totalLeft; ?></div>
		<div class="col-sm-4"><?php echo __('Right downlines : ','bmw');?><?php echo $totalRight; ?></div>
	</div>
	<div id="clear"></div>
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-hover">
			<thead>
			    <tr>
			      <th scope="col">#</th>
			      <th scope="col"><?php _e('Consultant Name','bmw'); ?></th>
			      <th scope="col"><?php _e('Consultant Key','bmw'); ?></th>
			      <th scope="col"><?php _e('Sponsor','bmw'); ?></th>
			      <th scope="col"><?php _e('Placement','bmw'); ?></th>
			      <th scope="col"><?php _e('Joining Date','bmw'); ?></th>
			    </tr>
			</thead>
			<tbody>
				<?php
					foreach ($data as $name => $details)
					{
							foreach ($details as $consultant) 
								{ 
				?>
				<tr>
					<td><?php echo $consultant['sno']; ?></td>
					<td><?php echo $consultant['name']; ?></td>
					<td><?php echo $consultant['userKey']; ?></td>
					<td><?php echo $consultant['referrer']; ?></td>
					<td><?php echo $consultant['leg']; ?></td>
					<td><?php echo $consultant['creationDate']; ?></td>
				</tr>
			<?php } } ?>
			</tbody>
			</table>
		</div>
	</div>
<?php
	}
}