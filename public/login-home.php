<?PHP require_once("./controllers/login_check_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home page</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>
	
<div class="container">
	<div class="box-container registartionForm">
		<h2>Home Page</h2>
		<p>Welcome back <strong><?= $fgmembersite->UserFullName(); ?>!</strong></p>
		<hr/>
		<ul>
			<li><a href='change-pwd.php'>Change password</a></li>
			<li><a href='access-controlled.php'>A sample 'members-only' page</a></li>
			<li><a href='logout.php'>Logout</a></li>
			<li><a href='delete-account.php'>Delete Account</a></li>
		</uk>
	</div>
</div>
</body>
</html>
