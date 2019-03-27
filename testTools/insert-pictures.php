<?php
$timeInit = time();
include_once('../assets/php/helpers/directoryTools.php');
$root = getFilePath(__FILE__).'/..';
include_once($root.'/assets/php/classes/imageHandler.php');
include_once($root.'/assets/php/helpers/debugTools.php');
include_once($root.'/config.php');

$dir = parseDirectory_forFiles($root.'/images/testImages');
shuffle($dir);
$names = file($root.'/testTools/imageNames.txt');
shuffle($names);



$storeImg = new imageStore($imageDir,$conn);
$count = 0;
foreach($dir as $key => $image)
{
    $title = $names[$key];
    $storeImg->setOriginalImage($root.'/images/testImages/'.$image);
    $storeImg->copyImage($root.'/images/upload/'.$image);
    $result = $storeImg->processImage($title);
    if($result != TRUE)
    {
        print('failed');
    }
    $count++;
    if($count == $argv[1])
    {
        break;
    }
}
$timeFin = time();
$totalTime = $timeFin - $timeInit;
print($count.' pictures succesfully added'."\n");
print('operation took '.$totalTime."s \n");
?>