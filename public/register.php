<?PHP require_once("./controllers/register_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>

<div class="container">
<div class="box-container registartionForm">
<form id='form-submission' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' class="form-horizontal">
<fieldset>
<legend>Register</legend>

<input type='hidden' name='submitted' id='submitted' value='1'/>
<input type='text' class='hidnSpmTp' name='<?php echo $fgmembersite->GetSpamTrapInputName(); ?>' />
<?php echo csrf_token_tag(); ?>

<?php $mess = $fgmembersite->GetErrorMessage(); echo (isset($mess) && $mess != "") ? "<div class='errorBox'>".$mess."</div>" : "" ?>

<div id="errorBox-js" class="errorBox-js"></div>

<div class="form-group">
	<div class="col-sm-12">
		<p>Already a member? Login <a href='login.php'>here</a></p>
	</div>
</div>

<hr/>

<div class="form-group">
    <label for="forename" class="col-sm-4 control-label">Forename:</label>
    <div class="col-sm-8">
	  <input type='text' name='forename' id='forename' value='<?php echo $fgmembersite->SafeDisplay('forename') ?>' maxlength="64" required placeholder="Forname" class="form-control"/>
    </div>
  </div>

<div class="form-group">
    <label for='surename' class="col-sm-4 control-label">Surname: </label>
	<div class="col-sm-8">
    <input type='text' name='surename' id='surename' value='<?php echo $fgmembersite->SafeDisplay('surename') ?>' maxlength="64" required placeholder="Surname" class="form-control"/>
	</div>
</div>

<div class="form-group">
    <label for='email' class="col-sm-4 control-label">Email:</label>
	<div class="col-sm-8">
    <input type='text' name='email' id='email' value='<?php echo $fgmembersite->SafeDisplay('email') ?>' maxlength="64" required placeholder="Email address" class="form-control"/>
	</div>
</div>

<div class="form-group">
    <label for='password' class="col-sm-4 control-label">Password:</label>
	<div class="col-sm-8">
    <input type='password' name='password' id='password' maxlength="50" required placeholder="Password" class="form-control"/>
	</div>
</div>

<div class="form-group">
    <label for='passwordConfirm' class="col-sm-4 control-label">Password:</label>
 	<div class="col-sm-8">
    <input type='password' name='passwordConfirm' id='passwordConfirm' maxlength="50" required placeholder="Confirm Password" class="form-control"/>
	<span id="passChecker">&#x2718;</span>
	</div>
</div>

<div class="form-group">
    <label for='password' class="col-sm-4 control-label">Copy Digits:</label>
	<div class="col-sm-8">
	<img src="include/captcha.php" border="1" id="captcha" alt="CAPTCHA"><br />
    <input type='text' name='captcha' id='captchaText' maxlength="10" required  class="form-control"/>
	<p class="small-text">Copy the digits from the image above</p>
	</div>
</div>

<div class="form-group">
    <div class="col-sm-12">
    <input type='submit' name='Submit' value='Submit' id="submitButton"  class="btn btn-primary btn-block"/>
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