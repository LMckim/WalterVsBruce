<?php
// classes for handling image verification as well as placement into the database
// and re-building indexing of images as well as image sizes
class imageStore
{
    function handleImage($img,$dir)
    {
        if($this->checkDuplicate($img['name'],$dir) == FALSE)
        {
            return 'image already exists';
        } 
        // 6 & 8 = portrait
        // 1 & 3 = landscape
        $orientation = $this->checkOrientation($img);
        $path = '';
        if($this->storeImage($img,$dir,$path) == FALSE)
        {
            return 'could not store image';
        }
        //$this->fixOrientation($path,$orientation);
        if($this->createThumb($img['name'],$dir,$path) == FALSE)
        {
            return "could not convert image";
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
    private function checkOrientation($img)
    {
        $meta = exif_read_data($img['tmp_name']);
        return $meta['Orientation'];
    }
    private function fixOrientation(&$img,$orientation)
    {
        if($orientation == 6)
        {
            $src = imagecreatefromjpeg($img);
            $rot = imagerotate($src,90,0);
            imagejpeg($rot);
            imagedestroy($src);
            imagedestroy($rot);
        }
        if($orientation == 8)
        {
            $src = imagecreatefromjpeg($img);
            $rot = imagerotate($src,-90,0);
            imagejpeg($rot);
            imagedestroy($src);
            imagedestroy($rot);
        }
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
    private function createThumb($name,$dir,$orignalImagePath)
    {   
        // thumbnail dimensions
        $w = 300;
        $h = 200;
        if($this->resizeImage($name,$dir,$orignalImagePath,$w,$h))
        {
            return TRUE;
        }
        return FALSE;

    }

    private function resizeImage($name,$dir,$path,$w,$h)
    {
        $thumbDir = $dir .'/../thumbs';

        $image = imagecreatefromjpeg($path);
        $imageW = imagesx($image);
        $imageH = imagesy($image);

        $thumb = imagecreatetruecolor($w,$h);

        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $w, 
                            $h, $imageW, $imageH);

        if(!imagejpeg($thumb,$thumbDir.'/'.$name,80))
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