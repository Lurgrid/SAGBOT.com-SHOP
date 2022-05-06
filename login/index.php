<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <link rel="shortcut icon" href="../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>Log in Page</title>
</head>

<body>
    
    <div id="banner">
        <a href="../" id="logo">lemauvaiscoin</a>
        <div id="findcreate">
            <a id="search" href="../find">
                <p>Search</p>
                <svg viewBox="0 0 1024 1024" role="img">
                    <path class="path1" d="M848.471 928l-263.059-263.059c-48.941 36.706-110.118 55.059-177.412 55.059-171.294 0-312-140.706-312-312s140.706-312 312-312c171.294 0 312 140.706 312 312 0 67.294-24.471 128.471-55.059 177.412l263.059 263.059-79.529 79.529zM189.623 408.078c0 121.364 97.091 218.455 218.455 218.455s218.455-97.091 218.455-218.455c0-121.364-103.159-218.455-218.455-218.455-121.364 0-218.455 97.091-218.455 218.455z"></path>
                </svg>
            </a>
        </div>
    </div>
    

    <div id="main">
        <?php
            $err = false;
            if (!isset($_POST["email"], $_POST["password"])){
                goto end;
            }
            $_POST["email"] = strtolower($_POST["email"]);
            include "../src/.mysql.php";
            try {
                $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
            } catch (Exception $e){
                $err = "Error during server connection";
                goto end;
            }
            if (mysqli_connect_error() != null){
                $err = "Internal Serveur error.";
                goto end;
            }
            $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'account'");
            if (mysqli_num_rows($res) == 0){
                mysqli_close($connexion);
                $err = "Unknow account";
                goto end;
            }
            $res = mysqli_query($connexion, "SELECT * FROM account WHERE email = '{$_POST["email"]}';");
            if (mysqli_num_rows($res) == 0){
                mysqli_close($connexion);
                $err = "Wrong Email/Password";
                goto end;
            }
            $account = mysqli_fetch_array($res);

            $mdp = base64_decode($account["token"]);
            $keyfile = realpath('../src/.private.key');
            $pvtkey = openssl_pkey_get_private(file_get_contents($keyfile));
            openssl_private_decrypt($mdp, $mdp, $pvtkey);

            $mdp = explode(":", $mdp);
            array_pop($mdp);
            $mdp = implode(":", $mdp);
            if ($mdp != $_POST["email"].":".$_POST["password"]){
                $err = "Wrong Email/Password."; 
                goto end;
            }

            $token;
            $keyfile = realpath('../src/.public.key');
            $pubkey = openssl_pkey_get_public(file_get_contents($keyfile));
            openssl_public_encrypt($_POST["email"].":".$_POST["password"].":".time(), $token, $pubkey);
            $token = base64_encode($token);

            $res = mysqli_query($connexion, "UPDATE account SET token = '{$token}'  WHERE token = '{$account["token"]}';");
            if ($res == false){
                $token = $account["token"];
            }

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
            setcookie("user",$_POST["email"], $options);
            $url = "http://".$_SERVER['HTTP_HOST'].$url;
            mysqli_close($connexion);
            header("Location: " . $url);
            exit();
            end:
        ?>
        <div id="boxsui">
            <form action="./" method="post">
                <?php 
                    if ($err != false){
                        echo "<p id=\"errmsg\">".$err."</p>";
                    }
                ?>
                <input name="email" type="email" placeholder="Email" required>
                <input name="password" type="password" placeholder="Password" required>
                <button type="submit">Sign In</button>
                <a href="./forgot/">Forgot Password</a>
            </form>
            <a id="su" href="../signup/">Sign Up</a>
        </div>
        
    </div>
</body>

</html>