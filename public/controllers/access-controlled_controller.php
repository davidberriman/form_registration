<?php

require_once("../include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600))
{
	// last request was more than 10 minutes ago
	session_unset(); // unset $_SESSION variable for the run-time
	session_destroy(); // destroy session data in storage
	$fgmembersite->RedirectToURL("login.php");
	exit;
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if (!isset($_SESSION['CREATED']))
{
	$_SESSION['CREATED'] = time();
}
else
if (time() - $_SESSION['CREATED'] > 600)
{
	// session started more than 10 minutes ago
	session_regenerate_id(true); // change session ID for the current session and invalidate old session ID
	$_SESSION['CREATED'] = time(); // update creation time
}

?>
