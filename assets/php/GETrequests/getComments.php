<?php

$title = strtolower($_GET['getComments']);
$sql = "SELECT `id` FROM `images` WHERE `title`='$title'";
$result = $conn->query($sql);
$id = $result->fetch_array(MYSQLI_ASSOC);
$id = $id['id'][0];
$sql = "SELECT `comment` FROM `comments` WHERE `img_id`='$id'";
$result = $conn->query($sql);
$comments = array();
while($row = $result->fetch_array(MYSQLI_NUM))
{
    $comments[] = $row;
}
print_r($comments);
exit();





?>