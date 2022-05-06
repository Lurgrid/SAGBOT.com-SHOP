<?php
    function token($path, $nindex) {
        $log = false;
        if (!isset($_COOKIE["token"], $_COOKIE["user"])){
            return $log;
        }
        if (preg_match("/^.{0,128}$/", $_COOKIE["user"]) != 1 || !filter_var($_COOKIE["user"], FILTER_VALIDATE_EMAIL)){
            return $log;
        }
        $token = $_COOKIE["token"];
        include ".mysql.php";
        try {
            $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
        } catch (Exception $e){
            return $log;
        }
        if (mysqli_connect_error() != null){
            return $log;
        }
        $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'account'");
        if (mysqli_num_rows($res) == 0){
            mysqli_close($connexion);
            return $log;
        }
        $res = mysqli_query($connexion, "SELECT * FROM account WHERE email = '{$_COOKIE["user"]}';");
        if (mysqli_num_rows($res) == 0){
            setcookie('token', null, -1); 
            setcookie('user', null, -1); 
            mysqli_close($connexion);
            return $log;
        }

        $log = mysqli_fetch_array($res);
        $mdp = base64_decode($log["token"]);
        $keyfile = $path."/.private.key";
        $pvtkey = openssl_pkey_get_private(file_get_contents($keyfile));
        openssl_private_decrypt($mdp, $mdp, $pvtkey);

        $tokenpass = base64_decode($token);
        openssl_private_decrypt($tokenpass, $tokenpass, $pvtkey);
        $tokenpass = explode(":", $tokenpass);
        $time = array_pop($tokenpass);
        $tokenpass = implode(":", $tokenpass);

        $mdp = explode(":", $mdp);
        array_pop($mdp);
        $mdp = implode(":", $mdp);

        $url = explode("/", $_SERVER['PHP_SELF']);
        for ($i = 0; $i < $nindex; $i++){
            array_pop($url);
        }
        $url =implode("/", $url);
        if ($url == null){
            $url = "/";
        }

        if ($mdp != $tokenpass	|| time() > ($time + 3600) ){
            $options = array (
                'expires' => 1,
                'path' => $url,
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => !empty($_SERVER["HTTPS"]),
                'httponly' => true,
                'samesite' => 'Strict'
            );
            setcookie('token', null, $options); 
            setcookie('user', null, $options); 
            mysqli_close($connexion);
            return false;
        }

        $keyfile = $path."/.public.key";
        $pubkey = openssl_pkey_get_public(file_get_contents($keyfile));
        openssl_public_encrypt($tokenpass.":".time(), $token, $pubkey);
        $token = base64_encode($token);

        $res = mysqli_query($connexion, "UPDATE account SET token = '{$token}'  WHERE token = '{$log["token"]}';");
        if ($res == false){
            $token = $log["token"];
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
        setcookie("user",$log["email"], $options);

        mysqli_close($connexion);

        return $log;
    }
?>