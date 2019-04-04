<?php
extract($_GET);
$title = strtolower($title);
$sql = "SELECT `id` FROM `images` WHERE `title`='$title' LIMIT 1";
$result = $conn->query($sql);
$id = mysqli_fetch_array($result);
$id = $id['id'];

$sql = "INSERT INTO `comments`(`img_id`,`user`,`comment`) 
        VALUES('$id','$User','$Comment')";
$conn->query($sql);
exit;

?>