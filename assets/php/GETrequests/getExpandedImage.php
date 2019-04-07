<?php
$repsonse = array();

$img_id = strtolower($_GET['getExpandedImage']);
$sql = "SELECT `id`,`path` FROM `images` WHERE `id`='$img_id'";
$result = $conn->query($sql);
$result = $result->fetch_array(MYSQLI_ASSOC);
// set variables from result
$id = $result['id'];
$path = $result['path'];
// handle image src
$imageSrc = '../../images/processed/'.pathinfo($path,PATHINFO_BASENAME);
$response['imageSrc'] = $imageSrc;
// handle comment retrieval
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
exit();





?>