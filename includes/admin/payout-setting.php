<?php
class Bmw_Payout {
	public function bmw_setting_payout(){ 
	$message= '';

 
	if(isset($_REQUEST['bmw_payout_settings']))
	{
		$bmw_pair1 			= sanitize_text_field($_REQUEST['bmw_pair1']);
		$bmw_pair2 			= sanitize_text_field($_REQUEST['bmw_pair2']);
		$bmw_initialrate 	= sanitize_text_field($_REQUEST['bmw_initialrate']);
		$bmw_initialunits 	= sanitize_text_field($_REQUEST['bmw_initialunits']);
		$bmw_furtheramount	= sanitize_text_field($_REQUEST['bmw_furtheramount']);
		
	
		if($bmw_pair1 !='' && $bmw_pair2 !='' && $bmw_initialrate !='' && $bmw_initialunits !='' && $bmw_furtheramount!='') 
		{
			/*check for the numeric values*/
			if( is_numeric($bmw_pair1) && is_numeric($bmw_pair2) && is_numeric($bmw_initialrate) && is_numeric($bmw_initialunits))
			{
				$post_data=array();
		          foreach($_POST as $key=>$value){
		            $post_data[$key]=sanitize_text_field($value);
		          }

				update_option('bmw_payout_settings', $post_data);
				$url = get_bloginfo('url')."/wp-admin/admin.php?page=dashboard-page&tab=bonus";
				$message = __("Your Payout settings has been successfully updated.",'bmw');
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
	$settings = get_option('bmw_payout_settings');

?>

<div class='wrap'>
	<div id="icon-options-general" class="icon32"></div>
	
	<div id="payout-form">
	<form name="frm" id="bmw_frm" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<?php wp_nonce_field(); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php echo __('Initial Pair Ratio','bmw');?></th>
				<td><input type="number" name="bmw_pair1" maxlength="6" value="<?php echo $settings['bmw_pair1']?>" class="small-text"/> : <input type="number" name="bmw_pair2" maxlength="6" value="<?php echo $settings['bmw_pair2']?>" class="small-text" />
			  	</td>
			</tr>
			<tr>
				<th align="left"><?php echo __('Initial Units Amount','bmw');?></th>
				<td><input type="number" class="all-optionst" name="bmw_initialrate" size="11" maxlength="6" value="<?php echo$settings['bmw_initialrate']?>" />&nbsp;&nbsp;<?php echo __('for first','bmw');?>&nbsp;&nbsp;<input type="number" name="bmw_initialunits" size="11" class="small-text" maxlength="5" value="<?php echo $settings['bmw_initialunits']?>" class="smallinput"/>&nbsp;&nbsp;<?php echo __('Units','bmw');?><br>
				<p id="tagline-description" class="description">Amount to be paid for initial units.</p>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Further Units Amount','bmw');?></th>
				<td><input type="number" name="bmw_furtheramount" maxlength="6" value="<?php echo $settings['bmw_furtheramount']?>" class="regular-text" /><br>
				<p id="tagline-description" class="description">Amount to be paid after initial units.</p>
				</td>
			</tr>
			
			
			<tr>
				<td colspan="2" class="text-center">
				<input type="submit" name="bmw_payout_settings" id="bmw_payout_settings" value="<?php echo __('Update Options', 'bmw')?>" class='button-primary'></td>
			</tr>
	  </table>
	</form>
	</div>
</div>

<?php
}

}