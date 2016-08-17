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
	
	// show user a password strength indicatorx
	var passwordInput = $( "#password" );

	passwordInput.keyup(function() {
	   showPasswordStrengthInkdicator();
 	});

	passwordInput.focusout(function() {
	   showPasswordStrengthInkdicator();
 	});
	
	var passwordConfirmInput = $( "#passwordConfirm" );
	
	// call the displayPasswordVerification to 
	// show tick / cross to say if passwords
	// are the same
	passwordConfirmInput.keyup(function() {
	   displayPasswordVerification();
 	});

	passwordConfirmInput.focusout(function() {
	   displayPasswordVerification();
 	});
  
  	var passwordCheckerInput = $( "#passChecker" );
  
	// hide password confirm tick / cross until field gets focus
	passwordCheckerInput.css('display', 'none');

	// show password confirm cross when field gets focus
	passwordConfirmInput.focus(function(){
		passwordCheckerInput.css('display', 'block');
		passwordCheckerInput.css('color', 'red');  
	});

});


// -----------------------------------------
//  Show user how strong their password is
// -----------------------------------------
function showPasswordStrengthInkdicator(){
	var pass1 = $("#password").val();	
	var strengthCheck = passwordStrengthCheck(pass1);
		
	var indicator = $('#passwordStrengthIndicator');
	
	if(typeof pass1 == "undefined" || pass1 == ""){
		indicator.html("");
	}else{
		indicator.html("&nbsp;" + strengthCheck.text);
	}
	
	indicator.css('background-color', strengthCheck.backgroundColor);
	indicator.css('color', strengthCheck.textColor);
}


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
	
	var passChecker = $('#passChecker');
	
	if(valid){
		passChecker.html('&#x2714;');
		passChecker.css('color', 'green');
	}else{
	    passChecker.css('color', 'red');
	    passChecker.html('&#x2718;');
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
	
	var errorBox = $("#errorBox-js");
	errorBox.html(message);
	errorBox.css("display", "block");
		
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
	
	var errorBox = $("#errorBox-js");
	var formGroup = $(".form-group");
	
	errorBox.val("");
	errorBox.css("display", "none");
	formGroup.css( "border", "none" );
	formGroup.css( "padding", "0" );
}



// -----------------------------------------
// return colours for password indicator
// -----------------------------------------
function returnPasswordColour(type, score){
	
	if(type == "background"){
		var colors = new Array();
		colors[0] = "#ff0000";
		colors[1] = "#FB1919";
		colors[2] = "#ff5f5f";
		colors[3] = "#E5CA00";
		colors[4] = "#A7E500";
		colors[5] = "#4dcd00";
		return colors[score];
	}
	
	if(type == "text"){
		var textColor = new Array();
		textColor[0] = "#ffffff";
		textColor[1] = "#ffffff";
		textColor[2] = "#ffffff";
		textColor[3] = "#000000";
		textColor[4] = "#000000";
		textColor[5] = "#000000";
		return textColor[score];
	}	
}

// -----------------------------------------
// Check password stringth 
// -----------------------------------------
function passwordStrengthCheck(password)
{
	var score   = 0;

	if (password.length > 6) {score++;}

	if ( ( password.match(/[a-z]/) ) && 
	     ( password.match(/[A-Z]/) ) ) {score++;}

	if (password.match(/\d+/)){ score++;}

	if ( password.match(/[^a-z\d]+/) )	{score++};

	if (password.length > 12){ score++;}
	
	var desc='';
	if(password.length < 1){desc='';}
	else if(score<3){ desc = "WEAK"; }
	else if(score<4){ desc = "MEDIUM"; }
	else if(score==4){ desc= "GOOD"; }
	else if(score>4){ desc= "STRONG"; }
	
	var backgroundColor = returnPasswordColour("background", score);
	var textColor = returnPasswordColour("text", score);
	
	var passwordResult = { "backgroundColor" : backgroundColor, "text" : desc , "textColor" : textColor };
	
	return passwordResult;
}

