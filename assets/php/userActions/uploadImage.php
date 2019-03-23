<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/assets/php/classes/imageHandler.php');


$dir = $_SERVER['DOCUMENT_ROOT'].'/images/upload/';
$target = $dir . $_FILES['photo-info']['name'];

move_uploaded_file($_FILES['photo-info']['tmp_name'],$target);

?>