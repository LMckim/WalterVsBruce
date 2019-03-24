<?php
$root = '/d_drive/Development/repos/WalterHikesBruce';
include_once($root.'/assets/php/helpers/directoryTools.php');

$dir1 = $root.'/images/thumbs';
$dir2 = $root.'/images/upload';

$thumbs = parseDirectory_forFiles($dir1);
$imgs = parseDirectory_forFiles($dir2);

foreach($thumbs as $img)
{
    unlink($dir1 .'/'. $img);
}
foreach($imgs as $img)
{
    unlink($dir2 .'/'. $img);
}
print('all images files removed'."\n");
?>