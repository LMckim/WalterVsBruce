<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/classes/pageBuild.php');
$build = new pageBuild();


$page = $build->buildPage();
print($page);
?>