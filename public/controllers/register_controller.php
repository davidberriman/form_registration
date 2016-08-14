<?php

session_start();
require_once("../include/membersite_config.php");
require_once '../include/csrf_token_functions.php';

if(csrf_token_is_valid() && csrf_token_is_recent() && isset($_POST['submitted'])) 
{

   if($fgmembersite->RegisterUser())
   {
        $fgmembersite->RedirectToURL("thank-you.html");
   }
}
?>