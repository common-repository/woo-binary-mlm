jQuery(document).ready(function(){
        jQuery("#letscms_reg_url").click(function(){
            if(jQuery('#letscms_wp_reg').checked==true){
            jQuery("#letscms_reg_url").removeAttr("readonly");
        }});
         
    });
function CheckBoxChanged(checkbox)
{
    if (checkbox.checked == true) {
        jQuery("#letscms_reg_url").removeAttr("readonly");
    }
    else
    {
        jQuery("#letscms_reg_url").attr("readonly","readonly");
    }
}
function show1()
{
    if (document.getElementById('letscms_reg_url').value == '')
    {
        alert("Please Fill The URL");
        document.getElementById('letscms_reg_url').focus();
        return false;
    }
}



jQuery('#letscms_confirm_password').on('keyup', function () {
    if (jQuery(this).val() == jQuery('#letscms_password').val()) 
        jQuery('#message').html('Password matching').css('color', 'green');
    else 
        jQuery('#message').html('Password not matching').css('color', 'red');
});

// jQuery('#letscms_confirm_password').on('keyup', function () {
//     if (jQuery(this).val() == jQuery('#letscms_password').val()) {
//         jQuery('#message1').html('Password matching').css('color', 'green');
//     } else jQuery('#message1').html('Password not matching').css('color', 'red');
// });
var $=jQuery.noConflict();
function checkUserNameAvailability(urlpath,str)
{ 
	jQuery.ajax({url: urlpath+'?action=username'+'&q='+str, success: function(result){
            $('#err_username').html('');
        	$('#err_username').html(result);
    }});  
}

function checkEmailAvailability(urlpath,str)
{ 
    jQuery.ajax({url: urlpath+'?action=email'+'&q='+str, success: function(result){
            $('#check_email').html('');
            $('#check_email').html(result);
    }});  
}

function checkReferrerAvailability(urlpath,str)
{
	jQuery.ajax({url: urlpath+'?action=sponsor'+'&q='+str, success: function(result){
        $('#check_referrer').html('');
    	$('#check_referrer').html(result);
    }});
}

function checkSponsor(urlpath,str){ 
		jQuery.ajax({url: urlpath+'?action=bmw_sponsor'+'&name='+str, success: function(result){
                $('#bmw_checksponsor').html('');
	        	$('#bmw_checksponsor').html(result);
	    	}});
}

function savingPayoutPoints(urlpath){
		jQuery.ajax({url: urlpath+'?action=savepoints', success: function(result){
                $('#savePoints').html('');
	        	$('#savePoints').html(result);
	    	}});
}

function savingPayoutMoney(urlpath){
	jQuery.ajax({url: urlpath+'?action=savemoney', success: function(result){
            $('#saveMoney').html('');
        	$('#saveMoney').html(result);
            window.location.reload();
            // window.XMLHttpRequestUpload
    	}});
}


jQuery(document).ready(function(){
		jQuery("#test").hover(function()
		{
	  		jQuery('#test2').css("background-color", "blue");
	  	}, function()
        {
            jQuery("#test2").css("background-color", "green");
        });
});

jQuery(document).ready(function(){
	jQuery("#letscms_email").click(function(){
		
	});
});


