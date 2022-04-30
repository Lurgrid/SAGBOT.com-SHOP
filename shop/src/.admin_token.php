<?php
    function token($path, $nindex) {
        if (!isset($_COOKIE["token"])){
            return false;
        }
        $token = $_COOKIE["token"];

        $string = file_get_contents($path."/.admin_config.json");
        if ($string === false) {
            return false;
        }

        $json = json_decode($string, true);
        if ($json === null) {
            return false;
        }

        $keyfile = $path."/.private.key";
        $pvtkey = openssl_pkey_get_private(file_get_contents($keyfile));

        $tokenpass = base64_decode($token);
        openssl_private_decrypt($tokenpass, $tokenpass, $pvtkey);
        $tokenpass = explode(":", $tokenpass);
        $time = array_pop($tokenpass);
        $tokenpass = $tokenpass[0];

        $url = explode("/", $_SERVER['PHP_SELF']);
        for ($i = 0; $i < $nindex; $i++){
            array_pop($url);
        }
        $url =implode("/", $url);
        if ($url == null){
            $url = "/";
        }

        if ($json["password"] != $tokenpass	|| time() > ($time + 5*60) ){
            $options = array (
                'expires' => 1,
                'path' => $url,
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => !empty($_SERVER["HTTPS"]),
                'httponly' => true,
                'samesite' => 'Strict'
            );
            setcookie('token', null, $options); 
            return false;
        }

        $keyfile = $path."/.public.key";
        $pubkey = openssl_pkey_get_public(file_get_contents($keyfile));
        openssl_public_encrypt($tokenpass.":".time(), $token, $pubkey);
        $token = base64_encode($token);

        $options = array (
            'expires' => 0,
            'path' => $url,
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => !empty($_SERVER["HTTPS"]),
            'httponly' => true,
            'samesite' => 'Strict'
            );
        setcookie("token",$token, $options);

        return true;
    }
?>