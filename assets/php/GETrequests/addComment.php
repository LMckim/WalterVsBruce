<?php
// handles putting the comment into the database as well as sanitizing it
extract($_GET);
$title = strtolower($title);
$sql = "SELECT `id` FROM `images` WHERE `title`='$title' LIMIT 1";
$result = $conn->query($sql);
$id = mysqli_fetch_array($result);
$id = $id['id'];

$User = $conn->real_escape_string($User);
$Comment = $conn->real_escape_string($Comment);
if(strlen($User) < 3 || strlen($Comment) < 3)
{
    exit;
}
// check if comment already exists, same username and text
$sql = "SELECT * FROM `comments` WHERE `user`='$User' AND `comment`='$Comment'";
$result = $conn->query($sql);
if(mysqli_num_rows($result) > 0)
{
    // comment is a duplicate
}
else{
    // comment is unique so insert it 
    $sql = "INSERT INTO `comments`(`img_id`,`user`,`comment`) 
            VALUES('$id','$User','$Comment')";
    $conn->query($sql);
}
// retrieve comments for picture
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