<?PHP require_once("./controllers/access-controlled_controller.php"); ?>
<!DOCTYPE html>
<html lang='en-gb'>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>An Access Controlled Page</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="STYLESHEET" href="css/main.css" />
</head>
<body>

<div class="container">
	<div class="box-container registartionForm">
		<h2>This is an Access Controlled Page</h2>
		<p>This page can be accessed after logging in only. To make more access controlled pages, 
		copy paste the code between &lt;?php and ?&gt; to the page and name the page to be php.</p>
		<p>Logged in as: <strong> <?= $fgmembersite->UserFullName() ?> </strong></p>
		<hr/>
		<p><a href='login-home.php'>Home</a></p>
	</div>
</div>

</body>
</html>
