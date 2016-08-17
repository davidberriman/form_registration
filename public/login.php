<?PHP require_once("./controllers/login_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>

<div id='container'>
<div class="box-container registartionForm">
<form id='form-submission' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' class="form-horizontal">
<fieldset >
<legend>Login</legend>

<input type='hidden' name='submitted' id='submitted' value='1'/>
<?php echo csrf_token_tag(); ?>

<?php $mess = $fgmembersite->GetErrorMessage(); echo (isset($mess) && $mess != "") ? "<div class='errorBox'>".$mess."</div>" : "" ?>

<div id="errorBox-js" class="errorBox-js"></div>

<div class="form-group">
	<div class="col-sm-12">
		<p>Not a member? Sign up <a href='register.php'>here</a></p>
	</div>
</div>

<hr/>

<div class="form-group">
    <label for="username" class="col-sm-3 control-label">Email:</label>
    <div class="col-sm-9">
	  <input type='text' name='username' id='username' value='<?php echo $fgmembersite->SafeDisplay('username') ?>' maxlength="64" required placeholder="Email address" class="form-control"/>
    </div>
</div>

<div class="form-group">
    <label for="password" class="col-sm-3 control-label">Password:</label>
    <div class="col-sm-9">
	  <input type='password' name='password' id='password'  maxlength="50" required placeholder="Password" class="form-control"/>
    </div>
</div>


<div class="form-group">
    <div class="col-sm-12">
	<input type='submit' name='Submit' value='Submit' id="submitButton"  class="btn btn-primary btn-block"/>
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