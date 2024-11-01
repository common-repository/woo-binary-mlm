<?php
class Bmw_Eligibility {
	public function bmw_setting_eligibility(){ 
	$message= '';
	if(isset($_REQUEST['bmw_eligibility_settings']))
	{
		//echo "<pre>";print_r($_REQUEST);exit; 
		if(sanitize_text_field($_REQUEST['bmw_personalpoint'])!='' && sanitize_text_field($_REQUEST['bmw_leftreferrer'])!='' && sanitize_text_field($_REQUEST['bmw_rightreferrer'])!='' && sanitize_text_field($_REQUEST['bmw_directreferrer'])!='' )
		{
			$bmw_personalpoint = sanitize_text_field($_REQUEST['bmw_personalpoint']);
			$bmw_directreferrer = sanitize_text_field($_REQUEST['bmw_directreferrer']);
			$bmw_leftreferrer = sanitize_text_field($_REQUEST['bmw_leftreferrer']);
			$bmw_rightreferrer = sanitize_text_field($_REQUEST['bmw_rightreferrer']);
			//$bmw_minpoint = sanitize_text_field($_REQUEST['bmw_minpoint']);
			
			/* check for the numeric value */
			if( is_numeric($bmw_personalpoint) && is_numeric($bmw_directreferrer) && is_numeric($bmw_leftreferrer) && is_numeric($bmw_rightreferrer)  )
			{
					$post_data=array();
					foreach($_POST as $key=>$value){
						$post_data[$key]=sanitize_text_field($value);
					}
				
				update_option('bmw_eligibility_settings', $post_data);
				$url = get_bloginfo('url')."/wp-admin/admin.php?page=dashboard-page&tab=payout";
				$message = __("Your eligibility settings has been successfully updated.",'bmw');
				echo '<div class="updated settings-error notice is-dismissible"><p>'.$message.'</p></div>';
			}else{
				$error = __("You have not entered the numeric value in the criteria.",'bmw');
				echo '<div class="error settings-error notice is-dismissible"><p>'. $error .'</p></div>';
			}
		}else{
				$error = __("Please fill the complete information.",'bmw');
				echo '<div class="error settings-error notice is-dismissible"><p>'. $error .'</p></div>';
		}
	}
	$settings = get_option('bmw_eligibility_settings');
?>

<div class='wrap'>
	<div id="icon-options-general" class="icon32"></div>

	<div id="eligibility-form">
	<form name="frm" id="bmw_frm" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<?php wp_nonce_field(); ?>
		<table class="form-table">
			<tr>
				<th><?php echo __('Minimum Personal Point Value:','bmw');?> </th>
				<td><input type="number" class="all-options" name="bmw_personalpoint" value="<?php echo $settings['bmw_personalpoint']?>" /><br>
				<p id="tagline-description" class="description">Enter the value of qualification points to earn commission.</p>

				</td>
			</tr>
			
			<tr>
				<th><?php echo __('Total Direct Referrals:','bmw');?> </th>
				<td><input type="number" class="all-options" name="bmw_directreferrer" value="<?php echo $settings['bmw_directreferrer']?>" /><br>
				<p id="tagline-description" class="description">Members required to be referred directly.</p>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Left & Right ratio of Direct Referral:','bmw');?></th>
				<td><input type="number" class="small-text" name="bmw_leftreferrer" maxlength="6" value="<?php echo $settings['bmw_leftreferrer']?>" /> : <input type="number" class="small-text" name="bmw_rightreferrer"  maxlength="6" value="<?php echo $settings['bmw_rightreferrer']?>" /><br>
				<p id="tagline-description" class="description">Ratio of Left & Right members required to be referred directly.</p>
				</td>
			</tr>
			<!-- <tr>
				<th><?php echo __('Minimum Point Value of each referrer:','bmw');?> </th>
				<td><input type="number" class="all-options" size="10" name="bmw_minpoint" value="<?php echo $settings['bmw_minpoint']?>" /></td>
			</tr>  -->
		</table>

		<p class="submit">
		<input type="submit" name="bmw_eligibility_settings" id="bmw_eligibility_settings" value="<?php echo __('Update Options', 'bmw')?>" class='button-primary'>
		</p>
	</form>
	</div>
</div>
<?php
}

}