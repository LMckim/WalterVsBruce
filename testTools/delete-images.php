<?php
$conn = new mysqli('localhost','WalterVsBruce','BjsCiWtZgDIcsa84','WalterVsBruce');
$root = '/d_drive/Development/repos/WalterHikesBruce';
include_once($root.'/assets/php/helpers/directoryTools.php');

$dir1 = $root.'/images/thumbs';
$dir2 = $root.'/images/upload';

$thumbs = parseDirectory_forFiles($dir1);
$imgs = parseDirectory_forFiles($dir2);

// remove image references from DB then recreate foreign key
$sqlQuerys = array( "TRUNCATE `comments`",
                    "ALTER TABLE `comments` DROP FOREIGN KEY `image_relation`",
                    "TRUNCATE `images`",
                    "ALTER TABLE `comments` ADD CONSTRAINT `image_relation` FOREIGN KEY (`img_id`) REFERENCES `images`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
foreach($sqlQuerys as $query)
{
    $result = $conn->query($query);
    if($result != 1)
    {
        print('error: could not execute query: '.$query. 'exiting program...');
        exit();
    }
}
foreach($thumbs as $img)
{
    unlink($dir1 .'/'. $img);
}
foreach($imgs as $img)
{
    unlink($dir2 .'/'. $img);
}
print('all images files removed, database rebuilt'."\n");
?>