<?php

if(isset($_POST))
{
    $uName = $_POST['uName'];
    $uPass = $_POST['uPass'];
    $stay = 'off';
    if(array_key_exists($_POST['stay']))
    {
        $stay = $_POST['stay'];
    }

    print_r($_SESSION);
}
?>