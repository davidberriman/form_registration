<?PHP require_once("./controllers/change-pwd_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Change password</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>

<div class="container">
<div class="box-container registartionForm">
<form id='form-submission' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='POST' accept-charset='UTF-8' class="form-horizontal">
<fieldset >
<legend>Change Password</legend>

	<?php echo (isset($message) && $message != "") ? "<div class='errorBox'>".htmlspecialchars($message)."</div>" : "" ; ?>
	
	<div id="errorBox-js" class="errorBox-js"></div>

    <input type='hidden' name='submitted' id='submitted' value='1'/>
    <?php echo csrf_token_tag(); ?>
    	
	<div class="form-group">
	    <label for="oldpwd" class="col-sm-4 control-label">Current Password:</label>
	    <div class="col-sm-8">
		  <input type='password' name='oldpwd' id='oldpwd' maxlength="50" required placeholder="Old Password" class="form-control"/>
		  <br/>
		  <div class='short_explanation'><a href='reset-pwd-req.php'>Forgot Password?</a></div>
	    </div>
	</div>

	  
	<div class="form-group">
	    <label for="password" class="col-sm-4 control-label">New Password:</label>
	    <div class="col-sm-8">
		  <input type='password' autocomplete="off" name='password' id='password' maxlength="50" required placeholder="Password" class="form-control"/>
	    </div>
	 </div>
	
 	<div class="form-group">
 	    <label for="passwordConfirm" class="col-sm-4 control-label">Confirm Password:</label>
 	    <div class="col-sm-8">
 		  <input type='password' autocomplete="off" name='passwordConfirm' id='passwordConfirm' maxlength="50" required placeholder="Confirm Password" class="form-control"/>
		  <span id="passChecker">&#x2718;</span>
 	    </div>
 	 </div>
    
	<div class="form-group">
	    <div class="col-sm-12">            
    		<a href="#"  id="submitButton" class="btn btn-primary btn-block">CHANGE PASSWORD</a>
		</div>
	</div>

</fieldset>
</form>

<p><a href='login-home.php'>Home</a></p>
</div>
</div>

<script type='text/javascript' src='js/jqueryLatest.js'></script>
<script type='text/javascript' src='js/form-submisson.js'></script>
</body>
</html>