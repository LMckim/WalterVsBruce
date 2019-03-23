<?php
$uName =  strtolower($_POST['uName']);
$uPass = $_POST['uPass'];
$stay = 'off';
if(array_key_exists('stay',$_POST))
{
    $stay = $_POST['stay'];
}
$sql = "SELECT * FROM `users` WHERE `uName`='$uName'";
$result = $conn->query($sql);
if(mysqli_num_rows($result))
{
    $result = $result->fetch_array(MYSQLI_ASSOC);
    include($_SERVER['DOCUMENT_ROOT'].'/assets/php/security/PBKDF2.php');
    $sec = new PasswordStorage();
    if($sec->verify_password($uPass,$result['uPass']))
    {
        session_start();
        $_SESSION['u_id'] = $result['u_id'];
    }else{
        $jsonReturn = array('status'=>'error',
                            'message'=>'incorrect username or password');
    }

}else{
    $jsonReturn = array('status'=>'error',
                        'message'=>'incorrect username or password');
}

?>