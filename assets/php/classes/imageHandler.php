<?php
// classes for handling image verification as well as placement into the database
// and re-building indexing of images as well as image sizes
class imageStore
{
    function handleImage($img,$dir,$title,$conn)
    {
        //debugging
        //shell_exec('php testTools/delete-images.php');
        //debugging
        $path;
        $title = $title;
        $date;
        $ratio;
        $orientation;
        $latLong = array();

        if($this->checkDuplicate($img['name'],$dir) == FALSE)
        {
            return 'image already exists';
        } 
        if($this->storeImage($img,$dir,$path) == FALSE)
        {
            return 'could not store image';
        }
        // at this point stop using tmp file and use stored file
        $meta = exif_read_data($path);
        $date = $this->getDate($meta);
        $ratio = $this->getRatio($meta);
        $orientation = $this->checkOrientation($meta);
        $this->fixOrientation($path,$orientation);
        $latLong = $this->getLatLong($meta);
        if($this->createThumb($img['name'],$dir,$path,$ratio,$orientation) == FALSE)
        {
            return "could not convert image";
        }
        // insert image info into database
        $sql = "INSERT INTO `images` (`title`,`path`,`latitude`,`longitude`,`date`)".
                "VALUES ('$title','$path','$latLong[0]','$latLong[1]','$date')";
        $result = $conn->query($sql);
        if($result != 1)
        {
            return "error inserting image into database";
        }
        return TRUE;
        
    }
    private function checkDuplicate($name,$dir)
    {
        $files = parseDirectory_forFiles($dir);
        if(in_array($name,$files))
        {
            return FALSE;
        }
        return TRUE;
    }
    private function storeImage($img,$dir,&$path)
    {
        $path = $dir .'/'. $img['name'];
        if(move_uploaded_file($img['tmp_name'],$path))
        {
            return TRUE;
        }
        return FALSE;
    }
    private function getDate($meta)
    {
        $date = new DateTime($meta['DateTime']);
        return $date->format('l,F d \a\t h:i a');
    }
    private function getRatio($meta)
    {
        $height = $meta['COMPUTED']['Height'];
        $width = $meta['COMPUTED']['Width'];
        if($height > $width){return $width/$height;}
        else{return $height/$width;}
    }
    private function checkOrientation($meta)
    {
        return $meta['Orientation'];
    }
    private function fixOrientation($img,$orientation)
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
    private function getLatLong($meta)
    {
        $latRef = $meta['GPSLatitudeRef'];
        $longRef = $meta['GPSLongitudeRef'];
        $latInfo = $meta['GPSLatitude'];
        $longInfo = $meta['GPSLongitude'];

        $lat_degrees = count($latInfo) > 0 ? $this->gps2Num($latInfo[0]) : 0;
        $lat_minutes = count($latInfo) > 1 ? $this->gps2Num($latInfo[1]) : 0;
        $lat_seconds = count($latInfo) > 2 ? $this->gps2Num($latInfo[2]) : 0;
        
        $lon_degrees = count($longInfo) > 0 ? $this->gps2Num($longInfo[0]) : 0;
        $lon_minutes = count($longInfo) > 1 ? $this->gps2Num($longInfo[1]) : 0;
        $lon_seconds = count($longInfo) > 2 ? $this->gps2Num($longInfo[2]) : 0;
        
        $lat_direction = ($latRef== 'W' || $latRef == 'S') ? -1 : 1;
        $lon_direction = ($longRef == 'W' || $longRef == 'S') ? -1 : 1;
        
        $lat = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
        $long = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

        $latLong = array($lat,$long);

        return $latLong;
    }
    private function gps2Num($coordPart)
    {
        $parts = explode('/', $coordPart);
        if(count($parts) <= 0)
        return 0;
        if(count($parts) == 1)
        return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
    private function createThumb($name,$dir,$imagePath,$ratio,$orientation)
    {   
        // thumbnail dimensions
        $w = 300;
        $h = 200;
        if($this->resizeImage($name,$dir,$imagePath,$w,$h,$ratio,$orientation))
        {
            return TRUE;
        }
        return FALSE;

    }

    private function resizeImage($name,$dir,$path,$w,$h,$ratio,$orientation)
    {
        $thumbDir = $dir .'/../thumbs';

        $image = imagecreatefromjpeg($path);
        $imageW = imagesx($image);
        $imageH = imagesy($image);

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
}
class imageVerify
{
    public function verify($img)
    {
        if($this->verifyIsImage($img) == FALSE)
        {
            return "this file is not an image";
        }
        if($this->verifySize($img) == FALSE)
        {
            return "image is too large";
        }
        if($this->verifyType($img) == FALSE)
        {
            return "image is not of the proper type";
        }
        return TRUE;
    }
    private function verifyIsImage($img)
    {
        $size = getimagesize($img['tmp_name']);

        //checks that the images dimensions are returned as well as image type
        if((gettype($size[0]) == 'integer') && 
           (gettype($size[1]) == 'integer') &&
           (array_key_exists('mime',$size)))
        {
            return TRUE;
        }
        return FALSE;
    }
    private function verifySize($img)
    {
        if($img['size'] > 20000000)
        {
            return FALSE;
        }
        return TRUE;

    }
    private function verifyType($img)
    {
        $imgType = strtolower(pathinfo($img['name'],PATHINFO_EXTENSION));
        if($imgType == 'jpg' || $imgType == 'png' || 
            $imgType == 'jpeg' || $imgType == 'gif')
        {
            return TRUE;
        }
        return FALSE;
    }

}

?>