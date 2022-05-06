<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../css/signup.css">
    <link rel="shortcut icon" href="../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>Sign up Page</title>
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
            $err = 0;
            $noinfo = false;
            if (isset($_POST["name"], $_POST["email"], $_POST["password"], $_POST["QS"], $_POST["res"])){
                if (preg_match("/[A-Za-z^\s][A-Za-z\s]{1,14}[A-Za-z^\s]/", $_POST["name"]) != 1){ unset($_POST["name"]);}
                if (preg_match("/^.{1,1024}$/", $_POST["QS"]) != 1){ unset($_POST["QS"]);}
                if (preg_match("/^.{1,512}$/", $_POST["res"]) != 1){ unset($_POST["res"]);}
                if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,128}$/", $_POST["password"]) != 1){ unset($_POST["password"]);}
                if (preg_match("/^.{0,128}$/", $_POST["email"]) != 1){ unset($_POST["email"]);}
                if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){ unset($_POST["email"]);}
                if (!isset($_POST["name"], $_POST["email"], $_POST["password"], $_POST["QS"], $_POST["res"])){
                    $err = "Bad Format";
                    goto fin;
                }
                

                include "../src/.mysql.php";
                try {
                    $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                } catch (Exception $e){
                    $err = "Error during server connection";
                    goto fin;
                }
                if (mysqli_connect_error() != null){
                    $err = "Internal Serveur error.";
                    mysqli_close($connexion);
                    goto fin;
                }
                $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'account'");
                if (mysqli_num_rows($res) == 0){
                    $account = "CREATE TABLE account(id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, token TEXT NOT NULL,name varchar(16) NOT NULL UNIQUE,email varchar(128) NOT NULL UNIQUE,qs varchar(1024) NOT NULL,res varchar(512) NOT NULL,img LONGTEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                    mysqli_query($connexion, $account);
                }
                $token;
                $keyfile = realpath('../src/.public.key');
                $pubkey = openssl_pkey_get_public(file_get_contents($keyfile));
                $_POST["email"] = strtolower($_POST["email"]);
                $_POST["res"] = strtolower($_POST["res"]);
                openssl_public_encrypt($_POST["email"].":".$_POST["password"].":".time(), $token, $pubkey);
                $token = base64_encode($token);

                try {
                    mysqli_query($connexion, "INSERT INTO account(token, name, email, QS, res) VALUES ('{$token}', '{$_POST["name"]}', '{$_POST["email"]}', '{$_POST["QS"]}', '{$_POST["res"]}')");
                }catch (Exception $e){
                    $msg = $e->getMessage();
                    if ($msg == "Duplicate entry '".$_POST["email"]."' for key 'email'"){
                        unset($_POST["email"]);
                        $err = "Account already exist.";
                    }else if ($msg == "Duplicate entry '".$_POST["name"]."' for key 'name'"){
                        unset($_POST["name"]);
                        $err = "This name is already taken.";
                    }else {
                        $err = $msg;
                    }
                    unset($_POST["password"]);
                    mysqli_close($connexion);
                    goto fin;
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
                fin:
            } else {
                $noinfo = true;
            }
        ?>
        <div id="boxsui">
            <?php

            ?>
            <form action="./" method="post">
                <?php 
                if ($err != 0){
                    echo "<p id=\"errmsg\">".$err."</p>";
                }
                ?>
                
                <label>Minimum 3 characters, maximum 16 character and no special character</label>
                <input name="name" type="text" placeholder="Name" pattern="[A-Za-z^\s][A-Za-z\s]{1,14}[A-Za-z^\s]" <?php 
                    if (isset($_POST["name"])){
                        $a = $_POST["name"];
                        echo "value = \"{$a}\"";
                    }else if (!$noinfo) {
                        echo "class=\"red\"";
                    }
                 ?>required>
                <input name="email" type="email" placeholder="Email" <?php
                    if (isset($_POST["email"])){
                        $a = $_POST["email"];
                        echo "value = \"{$a}\"";
                    }else if (!$noinfo) {
                        echo "class=\"red\"";
                    }
                 ?> required>
                <label>Minimum 8 characters, at least 1 letter, 1 number and 1 special character (@$!%*#?&)</label>
                <input name="password" type="password" placeholder="Password" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,64}$" required>
                <select name="QS" required>
                    <option>In what city were you born?</option>
                    <option>What is the name of your favorite pet?</option>
                    <option>What is the name of your first school?</option>
                    <option>What was your favorite food as a child?</option>
                    <option>Where did you meet your spouse?</option>
                </select>
                <input name="res" type="text" placeholder="Response"  <?php
                    if (isset($_POST["res"])){
                        $a = $_POST["res"];
                        echo "value = \"{$a}\"";
                    }else  if (!$noinfo) {
                        echo "class=\"red\"";
                    }
                 ?>required>
                <button type="submit">Sign Up</button>
            </form>
            <hr>
            <p>Already have an account?</p>
            <a id="si" href="../login/">Sign In</a>
        </div>
    </div>
</body>

</html>