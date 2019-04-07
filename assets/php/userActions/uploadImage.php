<?php

include_once($root.'/assets/php/classes/imageHandler.php');
if(isset($_FILES))
{
    $tmpFileMeta = $_FILES['photo-info'];
}
if(isset($_POST))
{ 
    $title = $conn->real_escape_string($_POST['image-title']);

    // check title length
    if($title == '' || strlen($title) <= 1)
    {
        $sql = "SELECT COUNT(*) FROM `images`";
        $result = $conn->query($sql);
        if(mysqli_num_rows($result) <= 0)
        {
            // something to fail to if the image table doesnt exist
        }
        $result = mysqli_fetch_array($result);
        $title = "Bruce_Trail_".$result[0];
    }
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
$storeImg = new imageStore($imageDir,$conn);
$storeImg->setOriginalImage($tmpFileMeta);
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


?> 