<?php
    include "../../src/.admin_token.php";
    $log = token(realpath("../../src/"), 2);
    if (!$log){
        $url = explode("/", $_SERVER['PHP_SELF']);
        array_pop($url);
        array_pop($url);
        array_push($url, "login/");
        $url = implode("/", $url);
        header("Location: " . $url);
        exit();
    }
    ob_start();
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../../css/account.css">
    <link rel="shortcut icon" href="../../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>Admin Account</title>
</head>

<body>
    
    <div id="banner">
        <a href="../" id="logo">lemauvaiscoin</a>
    </div>
    
    <div id = "main">
        <div class="block">
            <?php 
                $errpass = false;

                if (isset($_POST["npassword"], $_POST["password"])){
                    if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,64}$/", $_POST["password"]) != 1){
                        $errname = "Bad name format of Actual Password";
                        goto finpass;
                    }

                    if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,64}$/", $_POST["npassword"]) != 1){
                        $errname = "Bad name format of New Password";
                        goto finpass;
                    }

                    $string = file_get_contents("../../src/.admin_config.json");
                    if ($string === false) {
                        $err = "Internal Serveur error.";
                        goto finpass;
                    }
            
                    $json = json_decode($string, true);
                    if ($json === null) {
                        $err = "Internal Serveur error.";
                        goto finpass;
                    }
        
                    if ($json["password"] != $_POST["password"]){
                        $err = "Wrong Password."; 
                        goto finpass;
                    }

                    $keyfile = realpath("../../src/")."/.public.key";
                    $pubkey = openssl_pkey_get_public(file_get_contents($keyfile));
                    openssl_public_encrypt($_POST["npassword"].":".time(), $token, $pubkey);
                    $token = base64_encode($token);

                    $json["password"] = $_POST["npassword"];
                    if (($string = json_encode($json)) == false){
                        $err = "Internal Serveur error.";
                        goto finpass;
                    }
                    if (file_put_contents("../../src/.admin_config.json", $string) == false){
                        $err = "Internal Serveur error.";
                        goto finpass;
                    }

                    $url = explode("/", $_SERVER['PHP_SELF']);
                    array_pop($url);
                    array_pop($url);
                    array_push($url, "login/");
                    $url = implode("/", $url);
                    header("Location: " . $url);
                    exit();
                }

                finpass:

                if ($errpass != false){
                    echo "<p class=errmsg>{$errpass}</p>";
                }
            ?>

            <form class="sousblock password" action="./" method="post">
                <input type="password" placeholder="Actual Password" name="password" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,64}$" required>
                <label>Minimum 8 characters, at least 1 letter, 1 number and 1 special character</label>
                <input type="password" placeholder="New Password" name="npassword" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,64}$" required>
                <button class="button return" type="submit">Apply</button>
            </form>
        </div>
        <hr>

        <div class="block">
            <?php
                if (isset($_POST["discacc"])){ 
                    $url = explode("/", $_SERVER['PHP_SELF']);
                    array_pop($url);
                    array_pop($url);
                    $url = implode("/", $url);
                    if ($url == null){
                        $url = "/";
                    }
                    $options = array (
                        'expires' => 1,
                        'path' => $url,
                        'domain' => $_SERVER['HTTP_HOST'],
                        'secure' => !empty($_SERVER["HTTPS"]),
                        'httponly' => true,
                        'samesite' => 'Strict'
                        );
                    setcookie("token", null, $options); 
                    header("Location: " . $url."/login");
                    exit();
                }
            ?>
            <form class="sousblock discacc" action="./" method="post">
                <input class="hidden" name="discacc" type="checkbox" checked>
                <button class="button return" type="submit">Disconnect</button>
            </form>
        </div>
        
    </div>


</body>
<?php 
ob_end_flush();
?>

</html>