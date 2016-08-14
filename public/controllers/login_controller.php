<?php

session_start();
require_once("../include/membersite_config.php");
require_once '../include/csrf_token_functions.php';

if(isset($_POST['submitted']))
{
   if($fgmembersite->Login())
   {
	   $fgmembersite->RedirectToURL("login-home.php");
   }
}
?>