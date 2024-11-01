
function checkInputField($value)
{
	if($value=="")
		return true;
	else
		return false;
}

function confirmPassword($pass, $confirm)
{
	if($confirm != $pass)
		return true;
	else
		return false;
}

function confirmEmail($email, $confirm)
{
	if($confirm != $email)
		return true;
	else 
		return false;
}

function allowspace(char,id)
{
	var iChars = " ";
	for (var i = 0; i < char.length; i++)
	{
    	if (iChars.indexOf(char.charAt(i)) != -1)
		{
    		document.getElementById("allow_"+id).innerHTML = "Space is not allowed in "+ id+"."; 
			document.getElementById(id).value='';
			document.getElementById(id).focus();
    		return false;
        }
    }
	return true;
}

function checkname(char,id)
{
	var iChars = "~`!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
	for (var i = 0; i < char.length; i++)
	{
    	if (iChars.indexOf(char.charAt(i)) != -1)
		{
    		document.getElementById("check_"+id).innerHTML = "Special characters are not allowed in "+ id+"."; 
			document.getElementById(id).value='';
			document.getElementById(id).focus();
    		return false;
        }
    }
	return true;
}

function numeric(x, id)
{
        if(isNaN(x)|| x.indexOf(" ")!=-1)
		{
              alert("You have entered wrong "+id+" number.");
			  document.getElementById(id).focus();
			  return false; 
		}
}

function isSpclChar(char,id)
{
	var iChars = "~`!@#$%^&*()+=-[]\\\';,. /{}|\":<>?";
	for (var i = 0; i < char.length; i++)
	{
    	if (iChars.indexOf(char.charAt(i)) != -1) 
		{
    		alert ("Special characters are not allowed in "+ id+".");
			document.getElementById(id).value='';
			document.getElementById(id).focus();
    		return false;
        }
    }
	return true;
}


function allowspace(char,id)
{
	var iChars = "~`!@#$%^&*()+=[]\\\';,./{}|\":<>?";
	for (var i = 0; i < char.length; i++)
	{
    	if (iChars.indexOf(char.charAt(i)) != -1) 
		{
    		alert ("Special characters are not allowed in "+ id+".");
			document.getElementById(id).value='';
			document.getElementById(id).focus();
    		return false;
        }
    }
	return true;
}

var $=jQuery.noConflict();
$(document).ready(function(){

	$('#bmw_register').click(function(e){
	//e.preventDefault();
	var error = [];
	var rerror=[];
	rerror.push({name:'err_username'});
	rerror.push({name:'err_firstname'});
	rerror.push({name:'err_lastname'});
	rerror.push({name:'err_dob'});
	rerror.push({name:'err_city'});
	rerror.push({name:'err_state'});
	rerror.push({name:'err_postalcode'});
	rerror.push({name:'err_address'});
	rerror.push({name:'err_sponsor'});
	rerror.push({name:'err_password'});
	/*  User Name validate */	
	var username = $('#letscms_username').val();  
	var regex_username = /^[a-zA-Z0-9_]+$/;
	if(username=='') {
		error.push({name:'err_username',value:'Please enter your user name'}); 
	}
	/* Password validate */
	var password = $('#letscms_password').val();  
	var regex_pawword = /^[\w\!\@\#\$\%\&\*]{5,20}$/;
	if(password =='') {
		error.push({name:'err_password',value:'Please enter your password'});
	} else{
		if(!regex_pawword.test(password)) 
			error.push({name:'err_password',value:'Your password must be 5 digit & space not allow'});
	}
	/* Confirm Password validate  */
	var confirm_password = $('#letscms_confirm_password').val();  
	if(confirm_password =='') {
		error.push({name:'message',value:'Password not matching'});
	} 

	/* First Name validate */
	var firstname = $('#letscms_firstname').val();  
	var  regex_fistname= /^(?! )[A-Za-z ]{3,40}(?<! )$/;
	if(firstname == '') {
		error.push({name:'err_firstname',value:'Please enter your first name'});
	} else {
		if(!regex_fistname.test(firstname))
			error.push({name:'err_firstname',value:'Your name must be between 3 and 30 characters '});
	}

	/* Last Name validate  */
	var lastname = $('#letscms_lastname').val();  
	var  regex_lastname= /^(?! )[A-Za-z ]{3,40}$/;
	if(lastname=='') {
		error.push({name:'err_lastname',value:'Please enter your last name'});
	} else {
		if(!regex_lastname.test(lastname))
			error.push({name:'err_lastname',value:'Your Last name must be between 3 and 20 characters '});
	}

	/* Email validate */
	var email = $('#letscms_email').val();  
	var regex_email =/^[\w-\.]+@([\w-]+\.)+[a-z]{2,4}$/; 
	if(email=='') {
		error.push({name:'check_email',value:'Please enter your email'}); 
	}
	// else {
	// 	if(!regex_email.test(email))
	// 	error.push({name:'check_email',value:'Invalid Format'}); 

	// }
 
	/* Date of Birth validate  */	
	var dob = $('#letscms_dob').val();  
	var regex_dob =/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
	if(dob=='') {
		error.push({name:'err_dob',value:'Please enter your date of birth'});
	} else {
		if(!regex.test(dob))
			error.push({name:'err_dob',value:'Invalid Your Date of Birth'});
	       }
	
	var city = $('#letscms_city').val();  
	var regex_city = /^(?! )[A-Za-z ]{3,40}$/;  

	if(city=='')
	{
		error.push({name:'err_city',value:'Please enter your city'});
	} else {
		if(!regex_city.test(city))
		error.push({name:'err_city',value:'Invalid city name'});
	}

	var state = $('#letscms_state').val();
	var regex_state = /^(?! )[A-Za-z ]{3,40}$/;  
	if(state=='') {
		error.push({name:'err_state',value:'Please enter your state'});
	} else {
		if(!regex_state.test(state))
			error.push({name:'err_dob',value:'Invalid City'});
	}

	var postalcode = $('#letscms_postalcode').val();  
	var regex_postalcode = /^(?! )[A-Za-z]{3,40}$/;  

	if(postalcode=='')
	{
		error.push({name:'err_postalcode',value:'Please enter your postal code'});
	} else {
		if(!regex_postalcode.test(postalcode))
			error.push({name:'err_postalcode',value:'Invalid postal code'});
	}

	var address = $('#letscms_address1').val();  
	var regex_address = /^(?! )[A-Za-z]{3,40}$/;  

	if(address=='')
	{
		error.push({name:'err_address',value:'Please enter your address'});
	} else {
		if(!regex_address.test(address))
		error.push({name:'err_address',value:'Invalid address'});

	}

	var sponsor = $('#sponsor').val();  
	if(sponsor=='')
	{
		error.push({name:'err_sponsor',value:'Please enter your sponsor name'});
		//error.err_sponsor = "Please enter your sponsor name";
		 //document.getElementById("err_sponsor").innerHTML="Please enter your sponsor name";  
  			//return false;  
	}  
	

	$.each(rerror, function(index,value){
		$("#"+value.name).html('');
	});
   
   
   alert(error.length);

	if(error.length>0){ 
		alert("outerner");
		$.each(error, function (index,value) { 
	    	alert($("#"+value.name).html(''));
	    	alert($("#"+value.name).html(value.value));
		});
		e.preventDefault();
		return false;
		} else { 

		$('#bmw_signup').submit();
	}

	});

});


jQuery(document).ready(function(){
    jQuery("#user_acc_submit").click(function(e){
	    //e.preventDefault();
	   var error = [];
		var rerror=[];
		rerror.push({name:'err_account_holder_name'});
		rerror.push({name:'err_account_number'});
		rerror.push({name:'err_bank_name'});
		rerror.push({name:'err_ifsc_code'});
		rerror.push({name:'err_branch'});

		/* Null all id */
		  $.each(rerror, function(index,value){
			$("#"+value.name).html('');
		  });

		 /*  Account Holder Name Validate  */
	   var account_holder_name = jQuery("#account_holder_name").val();
	   var regex_account_holder_name =/^(?! )[A-Za-z ]{3,40}(?<! )$/;

		if(account_holder_name == '') {
			error.push({name:'err_account_holder_name',value:'Please enter account holder name'});
		} else {
		  	if(!regex_account_holder_name.test(account_holder_name))
		  		error.push({name:'err_account_holder_name',value:'Invalid account holder name'});
		  	//return true;
		} 
		   /*  Account Number Validate  */
		var account_number = jQuery("#account_number").val();
		var regex_account_number = /^[0-9]{9,40}$/;
		if(account_number == ''){
		 	error.push({name:'err_account_number',value:'Please enter account number'});
		} else {
		  	if(!regex_account_number.test(account_number))
			  	error.push({name:'err_account_number',value:'Invalid account number'});
		}

		   /* Bank Name Validate */
		var bank_name = jQuery("#bank_name").val();
		var regex_bank_name = /^(?! )[A-Za-z ]{5,40}(?<! )$/;
		if(bank_name == ''){
		  	error.push({name:'err_bank_name',value:'Please enter bank name'});
		} else {
		  	if(!regex_bank_name.test(bank_name))
		  		error.push({name:'err_bank_name',value:'Invalid Bank name'});
		}

		   /*  IFSC Code Validate   */
		var ifsc_code = jQuery("#ifsc_code").val();
		var regex_ifsc_code = /^(?=.*[0-9])(?=.*[A-Z])([A-Z0-9]{5,20})$/;
		if(ifsc_code == ''){
		  error.push({name:'err_ifsc_code',value:'Please enter IFSC code'});
		} else {
		  	if(!regex_ifsc_code.test(ifsc_code))
		  		error.push({name:'err_ifsc_code',value:'Invalid IFSC code'});
		}
		  /* Branch Validate */
		var branch = jQuery("#branch").val();
		var regex_branch = /^(?! )[A-Za-z0-9 ]{3,40}(?<! )$/;
		if(branch == ''){
		  error.push({name:'err_branch',value:'Please enter branch name'});
		} else {
		  	if(!regex_branch.test(branch))
			  error.push({name:'err_branch',value:'Invalid Branch name'});
		  } 
		if(error.length>0){
		 	$.each(error, function (index,value) {
		    	$("#"+value.name).html('');
		    	$("#"+value.name).html(value.value);
			});
			e.preventDefault();
			return false;
		} else {
			 // Swal.fire(
			 //   'Congratulations ',
			 //  	'<b>Dear</b> '+account_holder_name+' your account details has been updated',
			 //   'success'
			 // ) // its center pupup box
			 Swal.fire({
			   position: 'top-end',
			   type: 'success',
			   title: '<b>Dear</b> '+account_holder_name+' your account details has been updated',
			   showConfirmButton: true,
			   // timer: 1500
			 })// top right pupup box

			 jQuery('#user_acc_details_submit');
		}      
   })
});
