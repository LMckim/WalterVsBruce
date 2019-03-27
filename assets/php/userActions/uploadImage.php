<?php

include_once($root.'/assets/php/classes/imageHandler.php');
if(isset($_FILES))
{
    $tmpFileMeta = $_FILES['photo-info'];
}
if(isset($_POST))
{ 
    $title = $_POST['image-title'];
}
// check image is legit
$verify = new imageVerify();
$check = $verify->verify($tmpFileMeta);
if($check !== TRUE)
{
    $jsonReturn = array('status'=>'error','message'=>$check);
    unset($check);
    exit();
}
unset($check);


// handles storing, indexing and image size conversions
// imageDir declared in config.php
$storeImg = new imageStore($tmpFileMeta,$imageDir,$conn);
// sanitize user input
if($storeImg->checkDuplicate() != TRUE)
{
    print('duplicate photo');
    exit();
}
$storeImg->moveImage();
$result = $storeImg->processImage($title); // title set above
if($result != TRUE)
{
    print('Failure : '. $result);
    $jsonReturn = array('status'=>'error','message'=>$storeImg);
    exit();
}
unset($storeImg);

/*
$dir = $imageDir;
$store = $storeImg->handleImage($tmp,$dir,$title,$conn);
if($store !== TRUE)
{
    print("\n" .$store. "\n");
    $jsonReturn = array('status'=>'error','message'=>$storeImg);
    unset($storeImg);
    exit();
}
unset($storeImg);
*/

?> 