<?php
include_once($_SERVER['DOCUMENT_ROOT'].'../config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'../assets/php/classes/imageHandler.php');
include_once($_SERVER['DOCUMENT_ROOT'].'../assets/php/helpers/directoryTools.php');
$root = '/d_drive/Development/repos/WalterHikesBruce';
$dir = parseDirectory_forFiles($root.'/images/testImages');
foreach($dir as $file)
{
    if(pathinfo(basename($file),PATHINFO_EXTENSION) != 'jpg')
    {
        unset($file);
    }
}
array_values($dir);

$names = file('imageNames.txt');
$imageDir = $root. '/images/upload';

$storeImg = new imageStore();
foreach($dir as $key => $image)
{
    $title = $names[$key];
    $path = $root.'/images/testImages/'.$image;
    $meta = exif_read_data($path);
    $copy = copy($path,$root.'/images/upload/'.$image);

    $date = new DateTime($meta['DateTime']);
    $date = $date->format('d:j:o | g:i:s A');

    $ratio = getRatio($meta);
    $orientation = $meta['Orientation'];
    fixOrientation($path,$orientation);
    $latLong = getLatLong($meta);
    createThumb($image,$root.'/images/thumbs',$path,$w,$h,$ratio,$orientation);

    $sql = "INSERT INTO `images` (`title`,`path`,`latitude`,`longitude`,`date`)".
            "VALUES ('$title','$path','$latLong[0]','$latLong[1]','$date')";
    $result = $conn->query($sql);
    if($result != 1)
    {
        return "error inserting image into database";
    }
    if(array_key_exists(1,$argv))
    {
        if($key == $argv[1]-1)
        {
            break;
        }
    }
}
function getRatio($meta)
{
    $height = $meta['COMPUTED']['Height'];
    $width = $meta['COMPUTED']['Width'];
    if($height > $width){return $width/$height;}
    else{return $height/$width;}
}

function fixOrientation($img,$orientation)
{
    // 6 & 8 = portrait
    // 1 & 3 = landscape
    if($orientation == 6)
    {
        $src = imagecreatefromjpeg($img);
        $rot = imagerotate($src,-90,0);
        imagejpeg($rot,$img);
        imagedestroy($src);
        imagedestroy($rot);
    }
    if($orientation == 8)
    {
        $src = imagecreatefromjpeg($img);
        $rot = imagerotate($src,90,0);
        imagejpeg($rot,$img);
        imagedestroy($src);
        imagedestroy($rot);
    }
}
function getLatLong($meta)
{
    if(!array_key_exists('GPSLatitudeRef',$meta))
    {
        return array(0,0);
    }
    $latRef = $meta['GPSLatitudeRef'];
    $longRef = $meta['GPSLongitudeRef'];
    $latInfo = $meta['GPSLatitude'];
    $longInfo = $meta['GPSLongitude'];

    $lat_degrees = count($latInfo) > 0 ? gps2Num($latInfo[0]) : 0;
    $lat_minutes = count($latInfo) > 1 ? gps2Num($latInfo[1]) : 0;
    $lat_seconds = count($latInfo) > 2 ? gps2Num($latInfo[2]) : 0;
    
    $lon_degrees = count($longInfo) > 0 ? gps2Num($longInfo[0]) : 0;
    $lon_minutes = count($longInfo) > 1 ? gps2Num($longInfo[1]) : 0;
    $lon_seconds = count($longInfo) > 2 ? gps2Num($longInfo[2]) : 0;
    
    $lat_direction = ($latRef== 'W' || $latRef == 'S') ? -1 : 1;
    $lon_direction = ($longRef == 'W' || $longRef == 'S') ? -1 : 1;
    
    $lat = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
    $long = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

    $latLong = array($lat,$long);

    return $latLong;
}
function createThumb($name,$dir,$imagePath,$ratio,$orientation)
{   
    // thumbnail dimensions
    $w = 300;
    $h = 300;
    if(resizeImage($name,$dir,$imagePath,$w,$h,$ratio,$orientation))
    {
        return TRUE;
    }
    return FALSE;

}
function resizeImage($name,$dir,$path,$w,$h,$ratio,$orientation)
{
    $thumbDir = $dir;

    $image = imagecreatefromjpeg($path);
    $imageW = imagesx($image);
    $imageH = imagesy($image);
    
    // fix to work for width instead of height
    $thumb = imagecreatetruecolor($w,$h);
    $dst_w = $w / 2 * $ratio;
    $wD = $w/2;
    $iD = $dst_w/2;
    $dst_Xpos = $wD-$iD;

    if($orientation == 6 || $orientation == 8)
    {
        imagecopyresampled($thumb, $image, $dst_Xpos, 0, 0, 0, $dst_w, 
                        $h, $imageW, $imageH);
    }else{
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $w, 
                            $h, $imageW, $imageH);
    }

    if(!imagejpeg($thumb,$thumbDir.'/'.$name,100))
    {
        return FALSE;
    }
    return TRUE;

}
function gps2Num($coordPart)
{
    $parts = explode('/', $coordPart);
    if(count($parts) <= 0)
    return 0;
    if(count($parts) == 1)
    return $parts[0];
    return floatval($parts[0]) / floatval($parts[1]);
}
?>
