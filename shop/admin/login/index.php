<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../../css/login.css">
    <link rel="shortcut icon" href="../../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>Log in Page</title>
</head>

<body>
    <!--- Top Barre Website --->
    <div id="banner">
        <a href="../" id="logo">lemauvaiscoin</a>
    </div>
    <!--- Top Barre Website --->

    <div id="main">
        <?php
            $err = false;
            if (!isset($_POST["password"])){
                goto end;
            }

            $string = file_get_contents("../../src/.admin_config.json");
            if ($string === false) {
                $err = "Internal Serveur error.";
                goto end;
            }
    
            $json = json_decode($string, true);
            if ($json === null) {
                $err = "Internal Serveur error.";
                goto end;
            }

            if ($json["password"] != $_POST["password"]){
                $err = "Wrong Password."; 
                goto end;
            }

            $token;
            $keyfile = realpath('../../src/.public.key');
            $pubkey = openssl_pkey_get_public(file_get_contents($keyfile));
            openssl_public_encrypt($_POST["password"].":".time(), $token, $pubkey);
            $token = base64_encode($token);

            $url = explode("/", $_SERVER['PHP_SELF']);
            array_pop($url);
            array_pop($url);
            $url =implode("/", $url);
            if ($url == null){
                $url = "/";
            }
            $options = array (
                'expires' => 0,
                'path' => $url,
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => !empty($_SERVER["HTTPS"]),
                'httponly' => true,
                'samesite' => 'Strict'
                );
            setcookie("token",$token, $options);
            $url = "http://".$_SERVER['HTTP_HOST'].$url;
            header("Location: " . $url);
            exit();
            end:
        ?>

        <!--- Box loggin Website --->
        <div id="boxsui">
            <form action="./" method="post">
                <?php 
                    if ($err != false){
                        echo "<p id=\"errmsg\">".$err."</p>";
                    }
                ?>
                <input name="password" type="password" placeholder="Password" required>
                <button type="submit">Sign In</button>
            </form>
        </div>
        <!--- Box loggin Website --->
    </div>
</body>

</html>