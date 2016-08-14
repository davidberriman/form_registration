// -------------------------------------------------------------
// The files register.js & change-pwd.js are very similar
// so this is a generic file to use for both pages
// -------------------------------------------------------------
var g_errorMessages = [];
var g_errorIDs = [];

// -----------------------------------------
// Add events to the password input fields
// and prevent default on the form submit button
// -----------------------------------------
$( document ).ready(function() 
{	
    // prevent submit button so validation can be performed
   	$("#submitButton").on("click", function(event){
	   event.preventDefault();
       submitMe();
   	}); 

	// call the displayPasswordVerification to 
	// show tick / cross to say if passwords
	// are the same
	$( "#passwordConfirm" ).keyup(function() {
	   displayPasswordVerification();
 	});

	$( "#passwordConfirm" ).focusout(function() {
	   displayPasswordVerification();
 	});
  
	// hide password confirm tick / cross until field gets focus
	$('#passChecker').css('display', 'none');

	// show password confirm cross when field gets focus
	$('#passwordConfirm').focus(function(){
		$('#passChecker').css('display', 'block');
		$('#passChecker').css('color', 'red');  
	});

});



// -----------------------------------------
// check to see if passwords match then call
// isPasswordValid with the result
// -----------------------------------------
function displayPasswordVerification(){
	
	var pass1 = $("#password").val();
	var pass2 = $("#passwordConfirm").val();
	
	if(typeof pass1 == "undefined" || pass1 == "" || typeof pass2 == "undefined" || pass2 == ""){
		isPasswordValid(false);
		return;
	}
	
	if(pass1 == pass2){
		isPasswordValid(true);
	}else{
	    isPasswordValid(false);
	}
}


// -----------------------------------------
// show a red cross / green tick to say if
// the passwords match
// -----------------------------------------
function isPasswordValid(valid){
	
	if(valid){
		$('#passChecker').html('&#x2714;');
		$('#passChecker').css('color', 'green');
	}else{
	  $('#passChecker').css('color', 'red');
	  $('#passChecker').html('&#x2718;');
	}
}



// -----------------------------------------
// loop through object which contains 
// field : error message
// if field is found on the page then check
// that a value has been emtered
// -----------------------------------------
function checkMandatoryValues(object){

	var noErrors = true;
	// clear error arrays and start fresh each time
	g_errorMessages = [];
	g_errorIDs = [];
	g_errorMessages.push("<strong>There were errors in the form. Please correct the following:</strong><br/>");
	
	// loop through object and check each field has a value
	$.each(object, function( index, value ) {
		
	  	//console.log( index + ": " + value );
	  
	    // check to see if field are present - if they
	    // are then they are mandatory
	    if($( "#"+index ).length > 0){
	       // get field value
	   	   var fieldValue =  $( "#"+index ).val(); 
	   	   // check we have a forename otherwise error
	   	   if(typeof fieldValue == "undefined" || fieldValue == ""){
	   		  //showErroDiv(value, index);
	   		  noErrors = false;
			  // breaks outt of loop only and not function 
			  // so save boolean and pass that out
			  
			  g_errorMessages.push(value + "<br/>");
			  g_errorIDs.push(index);
			  //return noErrors; 
	   	   }
	    }
	  
	});
		
	return noErrors;
	
}

// -----------------------------------------
// used to override default behaviour on submit
// button in the registartion page - validates
// data before submitting the form
// -----------------------------------------
function submitMe(){
	
   // remove any previous error messages
   hideErroDiv();
   
   var mandValues = {  	"forename" : "Please enter your forename", 
   			   			"surename" : "Please enter your surname", 
   						"username" : "Please enter your email address", 
   						"email"    : "Please enter your email address",
   						"oldpwd"   : "Please enter your previous password",
   						"password" : "Please enter a password",
   						"passwordConfirm" : "Please confirm your password",
   						"code"     : "Please enter your confirmation code",
   						"terms"     : "Please accept the terms and conditions",
   						"captchaText" : "Please enter the captcha image letters"
			 		};
		
   // check all the mandatory values have been entered	 
   if(!checkMandatoryValues(mandValues)){
	  showErroDiv(g_errorMessages, g_errorIDs);
   	  return false;
   }
   
   // check both passwords match before submitting if we have a password
   // confirm field
   if($( "#password" ).length > 0 && $("#passwordConfirm" ).length > 0 ){
	   // check both passwords match before submitting
   	   if ($( "#password" ).val() != $( "#passwordConfirm" ).val()){
   		  showErroDiv('Your passwords are not the same', "password");
   		  return false;
   	   }
   }
   
   if($( "#terms" ).length > 0 && !$("#terms").is(':checked')){
	  showErroDiv('Please accept the terms and conditions', "terms");
	  return false;
   }
		
   // if we get this far then submit the form
   $("#form-submission").submit(); 
}


// -----------------------------------------
// shows an error box with error message
// -----------------------------------------
function showErroDiv(message, errorFields){
	$("#errorBox-js").html(message);
	$("#errorBox-js").css("display", "block");
		
	if(typeof errorFields != "undefined"){
		// loop through the errorFields array and
		// highlight each div in red 
		var fieldLength = errorFields.length;
		for(var i = 0; i < fieldLength; i++){
			$("#"+errorFields[i]).focus();
			$("#"+errorFields[i]).closest( ".form-group" ).css( "border", "solid 5px rgb(234, 101, 101)" );
			$("#"+errorFields[i]).closest( ".form-group" ).css( "padding", "5px" );
		}
		// move user to error box so they can see error information
		var x = jQuery("#errorBox-js").offset().top - 150;
		$('html,body').animate({scrollTop: x}, 300);
	}
}


// -----------------------------------------
// removes the error box 
// -----------------------------------------
function hideErroDiv(){
	$("#errorBox-js").val("");
	$("#errorBox-js").css("display", "none");
	$(".form-group").css( "border", "none" );
	$(".form-group").css( "padding", "0" );
}


