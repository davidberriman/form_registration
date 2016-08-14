<?PHP require_once("./controllers/confirmreg_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Confirm registration</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>

<div class="container">
<div class="box-container registartionForm">	
	
<form id='form-submission' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='GET' accept-charset='UTF-8' class="form-horizontal">
<fieldset>
<legend>Confirm registration</legend>
<p class="small-text">Please enter the confirmation code in the box below.</p>

<?php $mess = $fgmembersite->GetErrorMessage(); echo (isset($mess) && $mess != "") ? "<div class='errorBox'>".$mess."</div>" : "" ?>

<div id="errorBox-js" class="errorBox-js"></div>

<div class="form-group">
    <label for="code" class="col-sm-2 control-label">Code:</label>
    <div class="col-sm-10">
	  <input type='text' name='code' id='code' value='<?php echo $fgmembersite->SafeDisplay('code') ?>' maxlength="64" required placeholder="Confirmation code" class="form-control"/>
    </div>
</div>
  
<div class="form-group">
    <div class="col-sm-12">
    <input type='submit' name='Submit' value='Submit' id="submitButton" class="btn btn-primary btn-block" />
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