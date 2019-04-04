<?php

// sets document actual root regardless of machine
include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/helpers/directoryTools.php');
$root = getFilePath(__FILE__);

include_once($root.'/config.php');
include_once($root.'/assets/php/classes/pageBuild.php');
include_once($root.'/assets/php/helpers/debugTools.php');



$jsonReturn = array('status'=>'success','message'=>'all is well');

// get requests handling
if(isset($_GET))
{
    if(array_key_exists('getExpandedImage',$_GET))
    {
        include_once($root.'/assets/php/GETrequests/getExpandedImage.php');
    }elseif(array_key_exists('addComment',$_GET))
    {
        include_once($root.'/assets/php/GETrequests/addComment.php');
    }
}

// post requests handling
if(isset($_POST['login']))
{
    include_once($root.'/assets/php/userActions/login.php');
}elseif(isset($_POST['uploadImage']))
{
    include_once($root.'/assets/php/userActions/uploadImage.php');
}

$build = new pageBuild();

$attr = array();
if(isset($_SESSION['u_id'])){ $attr = array('loggedIn'); }
$page = $build->buildPage($imageDir,$attr,$conn);
print($page);

// send any json message to server after page sent
//if(isset($jsonReturn)){ print(json_encode($jsonReturn)); }
die();
?>