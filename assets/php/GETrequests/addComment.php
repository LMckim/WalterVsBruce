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

$sql = "SELECT `user`,`comment`,`time_commented` FROM `comments` WHERE `img_id`='$id'";
$result = $conn->query($sql);
$comments = array();
// organize comments by user
$userComments = array();
while($row = $result->fetch_array(MYSQLI_NUM))
{
    $userComments[] = array($row[0],$row[1],$row[2]);
}
$response['comments'] = $userComments;
print_r(json_encode($response));
exit;

?>