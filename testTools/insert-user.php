<?php
print('please enter username: ');
$uname = readline();
print('please enter password: ');
$upass = readline();
print('please confirm password: ');
$pass2 = readline();
if($upass != $pass2)
{
    print("passwords do not match, exiting now...\n");
    exit();
}else{
    $conn = new mysqli('localhost','WalterVsBruce','BjsCiWtZgDIcsa84','WalterVsBruce');
    include_once($_SERVER['DOCUMENT_ROOT'].'../assets/php/security/PBKDF2.php');  

    $sec = new PasswordStorage();
    $pass = $sec->create_hash($upass);
    $sql = "SELECT * FROM `users` WHERE `uName`='$uname'";
    $result = $conn->query($sql);
    if(mysqli_num_rows($result) != 0)
    {
        print("username taken already, exiting now...\n");
        exit();
    }else{
        $sql = "INSERT INTO `users` (`uName`,`uPass`,`stay`) VALUES('$uname','$pass','N')";
        if($conn->query($sql))
        {
            print("user successfully created, exiting now...\n");
            exit();
        }else{
            print("error creating new user, exiting now...\n");
            exit();
        }
    }
}

?>