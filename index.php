<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/classes/pageBuild.php');
$build = new pageBuild();

$imageDir = $_SERVER['DOCUMENT_ROOT']. '/images';

$page = $build->buildPage($imageDir);
print($page);
?>