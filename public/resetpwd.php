<?PHP  require_once("./controllers/resetpwd_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reset Password</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>

<div class="container">
<div class="box-container registartionForm">
<form id="form-submission" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]). "?code=".urlencode($token);?>" method="POST" accept-charset="utf-8" class="form-horizontal">
<fieldset >
<legend>Reset Password</legend>

	<?php echo (isset($message) && $message != "") ? "<div class='".$divClass."'>".$message."</div>" : "" ; ?>
	
	<div id="errorBox-js" class="errorBox-js"></div>
    		
	<?php  echo csrf_token_tag(); ?>
	
	<div class="form-group">
	    <label for="email" class="col-sm-4 control-label">Email:</label>
	    <div class="col-sm-8">
		  <input type='text' name='email' id='email' value='<?php echo $fgmembersite->SafeDisplay('email') ?>' maxlength="64" required placeholder="Email address" class="form-control"/>
	    </div>
	</div>

	<div class="form-group">
	    <label for='password' class="col-sm-4 control-label">Password:</label>
		<div class="col-sm-8">
	    <input type='password' name='password' id='password' maxlength="64" required placeholder="Password" class="form-control"/>
		</div>
	</div>
	
	<div class="form-group">
	    <label for='passwordConfirm' class="col-sm-4 control-label">Confirm Password:</label>
		<div class="col-sm-8">
	    <input type='password' name='passwordConfirm' id='passwordConfirm' maxlength="50" required placeholder="Confirm Password" class="form-control"/>
		<span id="passChecker">&#x2718;</span>
		</div>
	</div>

	<div class="form-group">
	    <div class="col-sm-12">
 		    <a href="#" id="submitButton"  class="btn btn-primary btn-block">RESET</a>
   		</div>
   </div>
   
   <div class="form-group">
       <div class="col-sm-12">
   		<div class='short_explanation'><a href='reset-pwd-req.php'>Forgot Password?</a></div>
   	</div>
   </div>
		
</fieldset>
</form>
</div>
</div>

<!-- client-side Form Validation-->
<script type='text/javascript' src='js/jqueryLatest.js'></script>
<script type='text/javascript' src='js/form-submisson.js'></script>

</body>
</html>