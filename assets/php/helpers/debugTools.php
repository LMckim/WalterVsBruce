<?php
function t($msg)
{
    if(is_array($msg))
    {
        print_r($msg);
        exit();
    }
    print($msg);
    exit();
}
function tr($msg)
{
    if(is_array($msg))
    {
        print_r($msg);
    }
    print($msg."\n");
}

?>