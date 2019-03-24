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
function parseDirectory_forFolders($path)
{
    $dir = scandir($path);
    $dir = array_diff($dir,array('.','..'));
    foreach($dir as $key => $item)
    {
        if(is_file($path .'/'.$item))
        {
            unset($dir[$key]);
        }

    }
    $dir = array_values($dir);
    return $dir;
}
?>