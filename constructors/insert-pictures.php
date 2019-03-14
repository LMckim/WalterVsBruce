<?php
include_once($_SERVER['DOCUMENT_ROOT'].'../config.php');

$dir = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'../images'),array('..','.'));

foreach($dir as $file)
{
    $file = $_SERVER['DOCUMENT_ROOT'] . '../images/' . $file;
    $meta = exif_read_data($file);
    $name = $meta['FileName'];
    // get time taken
    $time = getDateTime($meta);
    $latLong = getLatLong($meta);
    $lat = $conn->real_escape_string($latLong['Lat']);
    $long = $conn->real_escape_string($latLong['Long']);

    $query = "INSERT INTO `images` (`fileName`,`filePath`,`latitude`,`longitude`,`timeTaken`) VALUES ('$name','$file','$lat','$long','$time')";
    print($query . "\n");
    $conn->query($query);
}


function getDateTime($meta)
{
    if(array_key_exists('DateTimeOriginal',$meta))
    {
        $dateTime = $meta['DateTimeOriginal'];
    }else{
        $dateTime = date("Y:m:d H:i:s");
    }
    return $dateTime;
}
function getLatLong($meta)
{   
    $lat = '';
    $long = '';
    if(array_key_exists('GPSLatitudeRef',$meta))
    {
        //print_r($meta);
        $lat =
        removeSlash($meta['GPSLatitude'][0]) . '°' .
        removeSlash($meta['GPSLatitude'][1]) . '\''.
        insertPoint(removeSlash($meta['GPSLatitude'][2])) . '"' .
        $meta['GPSLatitudeRef'];
    
        $long = 
        removeSlash($meta['GPSLongitude'][0]) . '°' .
        removeSlash($meta['GPSLongitude'][1]) . '\''.
        insertPoint(removeSlash($meta['GPSLongitude'][2])) . '"' .
        $meta['GPSLongitudeRef'];
    }
    return $latLong = array('Lat'=>$lat,'Long'=>$long);
}





function removeSlash($string)
{
    $slash = strpos($string,'/');
    $string = substr($string,0,$slash);
    return $string;
}
function insertPoint($string)
{
    $string = substr_replace($string,'.',2,0);
    return $string;
}

?>
