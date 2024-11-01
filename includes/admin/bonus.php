<?php
class Bmw_Bonus {
	public function bmw_setting_bonus(){ 
		global $wpdb;
		$row_num=0;
		if($_POST){
			$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}bmw_bonus");
			foreach($_POST['bonus_setting'] as $row){
				$id = $row['id'];
				$units = $row['units'];
				$amount = $row['amount'];

				$insert_bonus = "INSERT INTO {$wpdb->prefix}bmw_bonus (units,amount,created_at,status)
				VALUES ( '".$units."', '".$amount."','".date('Y-m-d H:i:s')."','0')";
				$rs = $wpdb->query($insert_bonus); 
				
				if($row['id']){
					$update_bonus = "UPDATE {$wpdb->prefix}bmw_bonus SET units=$units , amount=$amount, lastupdate='".date('Y-m-d H:i:s')."' WHERE id='$id'";
					$wpdb->query($update_bonus);

				}

			}
		}

		$results=$wpdb->get_results("select * from {$wpdb->prefix}bmw_bonus");

	?>


		<form action="" id="bmw_frm" method="POST">
			<?php wp_nonce_field(); ?>
		<table id="data_table">
		  <thead> 
		  	<tr>
			<th scope="col"><?php echo __('S.No.','bmw');?></th>
			<th scope="col"><?php echo __('Units','bmw');?></th>
			<th scope="col"><?php echo __('Amount','bmw');?></th>
			<th scope="col"><?php echo __('Action','bmw');?></th>
			</tr>
		  </thead>
		  <tbody>
		  	<?php foreach($results as $result){?>
		    <tr id="bonus_row_<?php echo $row_num;?>">
		    	<td style="width:16%;">
		    		<input type="hidden" value="<?php echo $result->id;?>" name="bonus_setting[<?php echo $row_num;?>][id]"><?php echo $result->id;?>
		    	</td>

		    	<td>
		    		<input type="text"  value="<?php echo $result->units;?>" name="bonus_setting[<?php echo $row_num;?>][units]">
		    	</td>

		    	<td>
		    		<input type="text"  value="<?php echo $result->amount;?>" name="bonus_setting[<?php echo $row_num;?>][amount]">
		    	</td>
		    	<td>
		    		<input type="button" onclick="removeBonusRow(<?php echo $row_num;?>)" id="bmw_bonus_settings" value="<?php echo __('Remove', 'bmw')?>" class='button-secondary'>
		    		
		    	</td>
		    </tr>
		<?php $row_num++;} ?>
		  </tbody>

		  <tfoot>
		  	<tr><td colspan="4"><input type="button" onclick="addBonusRow()" id="bmw_bonus_settings" value="<?php echo __('Add', 'bmw')?>" class='button-secondary'></td></th></tr>
		  </tfoot>

		</table><br>
		<input type="submit" name="bmw_bonus_settings" id="bmw_bonus_settings" value="<?php echo __('Update Options', 'bmw')?>" class='button-primary'></td>
	</form>

	<script>
		$=jQuery.noConflict();
		var row_num='<?php echo $row_num;?>';
		function addBonusRow(){
			$('tbody').append('<tr id="bonus_row_'+row_num+'"><td style="width:6%;"><input type="hidden" name="bonus_setting['+row_num+'][id]"></td><td><input type="text" name="bonus_setting['+row_num+'][units]"></td><td><input type="text" name="bonus_setting['+row_num+'][amount]"></td><td><input type="button" value="<?php echo __('Remove', 'bmw')?>" onclick="removeBonusRow('+row_num+')" class="button-secondary" ></td></tr>');
			row_num++;
		}

		function removeBonusRow(row_num){
			$( '#bonus_row_'+row_num ).remove();
		}
	</script>
	<?php
	}
}
