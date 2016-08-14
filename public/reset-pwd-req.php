<?PHP require_once("./controllers/reset-pwd-req_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reset Password Request</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>

<div class="container">
<div class="box-container registartionForm">
<form id='form-submission' action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method='post' accept-charset='UTF-8' class="form-horizontal">
<?php echo csrf_token_tag(); ?>

<fieldset >
<legend>Reset Password</legend>

<?php echo (isset($message) && $message != "") ? "<div class='".$divClass."'>".$message."</div>" : "" ?>

<div id="errorBox-js" class="errorBox-js"></div>

<?php if(!$linkSent) { ?>

<input type='hidden' name='submitted' id='submitted' value='1'/>

<div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>

<div class="form-group">
    <label for='email' class="col-sm-3 control-label">Email: </label>
	<div class="col-sm-9">
    <input type='text' name='email' id='email' value='<?php echo $fgmembersite->SafeDisplay('email') ?>' maxlength="64" required placeholder="Email" class="form-control"/>
	</div>
</div>

<div class="form-group">
    <div class="col-sm-12">
	<p class='small-text'>A link to reset your password will be sent to the email address</p>
    <input type='submit' name='Submit' value='Submit' id="submitButton" class="btn btn-primary btn-block" />
	</div>
</div>

<?php } ?>

</fieldset>
</form>

</div>
</div>

<!-- client-side Form Validation-->
<script type='text/javascript' src='js/jqueryLatest.js'></script>
<script type='text/javascript' src='js/form-submisson.js'></script>

</body>
</html>