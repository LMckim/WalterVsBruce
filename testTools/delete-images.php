<?php


include_once('../assets/php/helpers/directoryTools.php');
$root = getFilePath(__FILE__);


include_once($root.'/../assets/php/helpers/directoryTools.php');
include_once($root.'/../config.php');

$dir1 = $root.'/../images/thumbs';
$dir2 = $root.'/../images/upload';
$dir3 = $root.'/../images/processed';

$thumbs = parseDirectory_forFiles($dir1);
$imgs = parseDirectory_forFiles($dir2);
$processed = parseDirectory_forFiles($dir3);

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
foreach($processed as $img)
{
    unlink($dir3 .'/'. $img);
}
print('all images files removed, database rebuilt'."\n");
?>