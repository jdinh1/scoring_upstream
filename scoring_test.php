<?php

/*  Jonathan's Upstream's scoring system using SHA1 hashing between a shared secret key and a random generated token

1.    Each time a request is made, a new token is generated and is sent back to the application.
2.    The application will concatenate the new token with the secret key and produce a SHA1 hash of the string.
3.    The string is sent back to the server via POST request
4.    Server check for validity of the hash, and update the score based on the game mode (easy, med, hard)
5.    Repeat step 1 until gameover is sent, session is destroyed then.

 */

// exit if no hash is seen
if (!isset($_POST['extra'])) {
    exit();
}

session_start();

// delete session if gameover
if (isset($_POST['gameover']) && $_POST['gameover'] == 1) {
    $_SESSION["token"] = generateRandomString(20);
    $msg = array(
        "err" => "0",
        "score" => isset($_SESSION['score']) ? $_SESSION['score'] : "0" ,
        "token" => isset($_SESSION['token']) ? $_SESSION['token'] : "",
        "msg" => "gameover"
    );
    echo json_encode($msg);

    session_unset();
    session_destroy();
    exit();
}

// Hash Validations
$data = $_POST['extra'];
$sharedSecretKey = "test1234";
$sha1_sharedSecretKey = sha1($sharedSecretKey);

if ($data === $sha1_sharedSecretKey) {
    $gamemode = $_POST['mode'];

    if (!isset($_POST['token']) && !isset($_SESSION["token"])) {
        $_SESSION['score'] = 0;
        $_SESSION["token"] = generateRandomString(20);
        $msg = array(
            "err" => "0",
            "score" => $_SESSION['score'],
            "mode" => $gamemode."",
            "token" => $_SESSION["token"],
            "msg" => "new session"
        );
    } else if (!isset($_POST['token'])) { // token not set
        $msg = array(
            "err" => "1",
            "score" => isset($_SESSION['score']) ? $_SESSION['score'] : "0",
            "mode" => $gamemode."",
            "token" => $_SESSION["token"],
            "msg" => "invalid token"
        );
    } else if (isset($_POST['token']) && !isset($_SESSION["token"])) { // token set but there is no session
        $_SESSION['score'] = 0;
        $_SESSION["token"] = generateRandomString(20);
        $msg = array(
            "err" => "0",
            "score" => $_SESSION['score'],
            "mode" => $gamemode."",
            "token" => $_SESSION["token"],
            "msg" => "invalid session 1"
        );
    } else if (isset($_POST['token']) && isset($_SESSION["token"]) && sha1($_SESSION["token"] . $sharedSecretKey) != $_POST['token'] ) { //token set but not matching with session token
        $_SESSION['score'] = 0;
        $_SESSION["token"] = generateRandomString(20);
        $msg = array(
            "err" => "1",
            "score" => $_SESSION['score'],
            "mode" => $gamemode."",
            "token" => $_SESSION["token"],
            "msg" => "invalid token 2"
        );
    } else if (isset($_POST['token']) && $_POST['token'] == sha1($_SESSION["token"] . $sharedSecretKey) && isset($_POST['mode']) && $_POST['mode'] != 0 ){ // everything is set correctly and tokens are matching
        // generate new token to prevent relay attack
        $_SESSION["token"] = generateRandomString(20);

        // scoring based on mode
        switch($_POST['mode']) {
            case 1:
                $_SESSION['score'] += 10;
                break;
            case 2:
                $_SESSION['score'] += 20;
                break;
            case 3:
                $_SESSION['score'] += 30;
                break;
        }

        $msg = array(
            "err" => "0",
            "score" => $_SESSION['score'],
            "mode" => $gamemode."",
            "token" => $_SESSION["token"],
            "msg" => "score updated"
        );
    }
}

if (isset($msg)) {
    echo json_encode($msg);
    exit();
}

function generateRandomString($length = 15) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}



?>
