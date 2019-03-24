<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/classes/pageBuild.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/helpers/directoryTools.php');

$jsonReturn = array('status'=>'success','message'=>'all is well');
if(isset($_POST['login']))
{
    include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/userActions/login.php');
}elseif(isset($_POST['uploadImage']))
{
    include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/userActions/uploadImage.php');
}

$build = new pageBuild();

$attr = array();
if(isset($_SESSION['u_id'])){ $attr = array('loggedIn'); }
$page = $build->buildPage($imageDir,$attr);
print($page);

// send any json message to server after page sent
//if(isset($jsonReturn)){ print(json_encode($jsonReturn)); }
die();
?>