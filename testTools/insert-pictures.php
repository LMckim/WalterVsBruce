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


// insert images
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

// setup and insert comments
$ids = array();
$sql = "SELECT `id` FROM `images`";
$result  = $conn->query($sql);
while($row = $result->fetch_array(MYSQLI_NUM))
{
    $id[] = $row;
}
$comments = file($root.'/testTools/imageComments.txt');
shuffle($comments);
foreach($id as $key => $num)
{   
    foreach($comments as $comment){
        $user = 'anon'.array_rand($id);
        $comment = trim($comment);
        $sql = "INSERT INTO `comments`(`img_id`,`user`,`comment`) 
        VALUES('$num[0]','$user','$comment')";
        $conn->query($sql);
    }

}




$timeFin = time();
$totalTime = $timeFin - $timeInit;
print($count.' pictures succesfully added'."\n");
print('operation took '.$totalTime."s \n");
?>