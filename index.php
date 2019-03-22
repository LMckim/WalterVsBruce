<?php
// dont need to add in the server just yet
include_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/classes/pageBuild.php');
$build = new pageBuild();

$loggedIn = 'no';
if(isset($_SESSION['u_id'])){ $loggedIn = 'yes'; }

$page = $build->buildPage($imageDir,$loggedIn);
print($page);
?>