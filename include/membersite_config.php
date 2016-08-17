<?PHP
require_once("initialize.php");
require_once("fg_membersite.php");

$fgmembersite = new FGMembersite();

//Provide your site name here
$fgmembersite->SetWebsiteName(WEBSITE_NAME);

//Provide your admin name here
$fgmembersite->SetAdminName(ADMIN_NAME);

//Provide the email address where you want to get notifications
$fgmembersite->SetAdminEmail(ADMIN_EMAIL);

//Provide your database login details here:
//hostname, user name, password and database name
$fgmembersite->InitDB(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

//For better security. Get a random string from this link: http://tinyurl.com/randstr
$fgmembersite->SetRandomKey(RANDOM_KEY);


?>