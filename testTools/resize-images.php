<?php

$dir = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'../images'),array('..','.'));
array_splice($dir,sizeof($dir)-1);
foreach($dir as $image)
{
    $path = $_SERVER['DOCUMENT_ROOT'].'../images/'.$image;
    $resizedImg = resizeImage($path,$image);

}
function resizeImage($path,$fileName)
{

    $source_image = imagecreatefromjpeg($path);
    $source_imagex = imagesx($source_image);
    $source_imagey = imagesy($source_image);

    $dest_imagex = 300;
    $dest_imagey = 200;
    $dest_image = imagecreatetruecolor($dest_imagex, $dest_imagey);

    imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $dest_imagex, 
                       $dest_imagey, $source_imagex, $source_imagey);

    imagejpeg($dest_image,'../images/thumbs/'.$fileName,80);

}
?>