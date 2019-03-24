<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/classes/imageHandler.php');
$tmp = $_FILES['photo-info'];
if(isset($_POST))
{
    $title = $_POST['image-title'];
}
// check image is legit
$verify = new imageVerify();
$check = $verify->verify($tmp);
if($check !== TRUE)
{
    $jsonReturn = array('status'=>'error','message'=>$check);
    unset($check);
    exit();
}
unset($check);

// handles storing, indexing and image size conversions
$storeImg = new imageStore();
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


?> 