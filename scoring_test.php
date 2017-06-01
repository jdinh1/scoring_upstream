<?php

// exit if no hash is seen
if (!isset($_POST['extra'])) {
    exit();
}

$data = $_POST['extra'];

// checking if shared key is correct
$sharedSecretKey = "test1234";
$sha1_sharedSecretKey = sha1($sharedSecretKey);

if ($data === $sha1_sharedSecretKey) {
echo "hi";
    if (!isset($_SESSION["token"])) {
        echo "hi2";

        session_start();
        #$_SESSION["token"] = random_str(20);
        echo random_str(20);
    } else {
    echo "hi3";
        if (isset($_POST['token']) && $_POST['token'] === sha1($_SESSION["token"])) {


        }

    }


}

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}


?>
