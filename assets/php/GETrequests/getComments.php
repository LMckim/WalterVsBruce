<?php

$titles = $_GET['getComments'];
foreach($titles as $title)
{
    $sql = "SELECT `id` FROM `images` WHERE `title`='$title'";
    $comments = $conn->query($sql);
    
}





?>