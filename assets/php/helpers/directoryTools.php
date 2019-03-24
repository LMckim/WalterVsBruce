<?php
// retrieves just files from a directory
function parseDirectory_forFiles($path)
{
    $dir = scandir($path);
    foreach($dir as $key => $item)
    {
        if(is_dir($path .'/'.$item))
        {
            unset($dir[$key]);
        }

    }
    $dir = array_values($dir);
    return $dir;
}
?>