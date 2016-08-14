<?php

require_once "../include/membersite_config.php";

if(isset($_GET['code']))
{
   if($fgmembersite->ConfirmUser($_GET['code']))
   {
        $fgmembersite->RedirectToURL("thank-you-regd.html");
   }
}

?>