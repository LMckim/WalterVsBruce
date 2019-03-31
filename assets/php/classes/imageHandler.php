<?php
// classes for handling image verification as well as placement into the database
// and re-building indexing of images as well as image sizes
class imageStore
{
    // constants
    private $dbConnection;
    private $temporaryFileLocation;
    private $imageDirectory;
    // image data
    private $meta;
    private $newImageLocation;
    private $orientation;


    function __construct($dir,$conn)
    {
        $this->dbConnection = $conn;
        $this->imageDirectory = $dir;
    }
    public function processImage($title)
    {
        $title = $this->dbConnection->real_escape_string(strtolower(trim($title)));
        $this->meta = exif_read_data($this->newImageLocation);
        $path = trim($this->newImageLocation);
        $date = $this->getDate();
        $orientation = $this->checkOrientation();
        $latLong = $this->getLatLong();
        if($this->resizeImage() == FALSE)
        {
            return "could not process image : Thumbnail conversion failure...";
        }else{
            $sql = "INSERT INTO `images` (`title`,`path`,`latitude`,`longitude`,`date`)".
            "VALUES ('$title','$path','$latLong[0]','$latLong[1]','$date')";

            $result =  $this->dbConnection->query($sql);
            if($result != 1)
            {
                return "could not process image : Error inserting image into database";
            }
            return TRUE;

        }

    }
    public function setOriginalImage($path)
    {
        $this->temporaryFileLocation = $path;
    }
    public function checkDuplicate()
    {
        $files = parseDirectory_forFiles($this->imageDirectory);
        if(in_array($this->temporaryFileLocation['name'],$files))
        {
            return FALSE;
        }
        return TRUE;
    }
    public function moveImage()
    {
        $name =$this->temporaryFileLocation['name'];
        $tmpFile = $this->temporaryFileLocation['tmp_name'];

        if(move_uploaded_file($tmpFile,$this->imageDirectory.'/'.$name))
        {
            $this->newImageLocation = $this->imageDirectory.'/'.$name;
            return TRUE;
        }
        return FALSE;
    }
    public function copyImage($path)
    {   
        copy($this->temporaryFileLocation,$path);
        $this->newImageLocation = $path;
    }
    private function getDate()
    {
        if(array_key_exists('DateTime',$this->meta))
        {
            $date = new DateTime($this->meta['DateTime']);
            return $date->format('d.m.Y | g:i A');
        }elseif(array_key_exists('FileDateTime',$this->meta))
        {
            $date = new DateTime();
            $date->setTimestamp($this->meta['FileDateTime']);
            return $date->format('d.m.Y | g:i A');
        }
    }
    private function checkOrientation()
    {
        if(array_key_exists('Orientation',$this->meta))
        {
            $this->orientation = $this->meta['Orientation'];
            if($this->orientation == 6 || $this->orientation == 8 || $this->orientation == 3)
            {
                $this->fixOrientation($this->orientation);
            }
        }else{
            return 0;
        }
    }
    private function fixOrientation($orientation)
    {
        // 6 & 8 = portrait
        // 1 & 3 = landscape
        if($orientation == 6)
        {
            $src = imagecreatefromjpeg($this->newImageLocation);
            $rot = imagerotate($src,-90,0);
            imagejpeg($rot,$this->newImageLocation);
            imagedestroy($src);
            imagedestroy($rot);
        }
        if($orientation == 8)
        {
            $src = imagecreatefromjpeg($this->newImageLocation);
            $rot = imagerotate($src,90,0);
            imagejpeg($rot,$this->newImageLocation);
            imagedestroy($src);
            imagedestroy($rot);
        }
        if($orientation == 3)
        {
            $src = imagecreatefromjpeg($this->newImageLocation);
            $rot = imagerotate($src,180,0);
            imagejpeg($rot,$this->newImageLocation);
            imagedestroy($src);
            imagedestroy($rot);
        }
    }
    private function getLatLong()
    {
        if(array_key_exists('GPSLatitudeRef',$this->meta))
        {
            $latRef = $this->meta['GPSLatitudeRef'];
            $longRef = $this->meta['GPSLongitudeRef'];
            $latInfo = $this->meta['GPSLatitude'];
            $longInfo = $this->meta['GPSLongitude'];
    
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
        }else{
            return array(0,0);
        }

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
    private function resizeImage()
    {   
        // thumbnail dimensions
        $w = 300;
        $h = 300;
        if($this->resizeImageThumbnail($w,$h))
        {
            return TRUE;
        }
        return FALSE;

    }

    private function resizeImageThumbnail($w,$h)
    {
        $thumbDir = $this->imageDirectory .'/../thumbs';
        $ratio = $this->getRatio();

        $image = imagecreatefromjpeg($this->newImageLocation); // original image to copy from
        $imageW = imagesx($image);
        $imageH = imagesy($image);

        // fix to work for width instead of height
        
        $thumb = imagecreatetruecolor($w,$h); // create a blank image with desired dimensions

        if($imageW > $imageH)
        {
            $diff = $imageW - $imageH;
            $src_x = $diff/2;
            
            // new thumbnail, original image, new image x, new image y, old x, old y,
            imagecopyresampled($thumb, $image,0, 0, $src_x, 0, $w, 
                                $h, $imageH, $imageH);
    
            if(!imagejpeg($thumb,$thumbDir.'/'.$this->meta['FileName'],100))
            {
                return FALSE;
            }
            return TRUE;
        }else{
            $diff = $imageH - $imageW;
            $src_y = $diff/2;
            
            // new thumbnail, original image, new image x, new image y, old x, old y,
            imagecopyresampled($thumb, $image,0, 0, 0, $src_y, $w, 
                                $h, $imageW, $imageW);
    
            if(!imagejpeg($thumb,$thumbDir.'/'.$this->meta['FileName'],100))
            {
                return FALSE;
            }
            return TRUE;
        }


    }
    private function getRatio()
    {
        if(array_key_exists('COMPUTED',$this->meta))
        {
            $height = $this->meta['COMPUTED']['Height'];
            $width = $this->meta['COMPUTED']['Width'];
            if($height > $width){return $width/$height;}
            else{return $height/$width;}
        }else{
            return 0;
        }
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