<?php
    include "../src/.token.php";
    $log = token(realpath("../src/"), 2);
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
    <link rel="stylesheet" type="text/css" href="../css/account.css">
    <link rel="shortcut icon" href="../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>My Account</title>
</head>

<body>
    
    <div id="banner">
        <a href="../" id="logo">lemauvaiscoin</a>
        <div id="findcreate">
            <form action="../create/">
                <button type="submit" class="btn">+</button>
            </form>
            <a id="search" href="../find/">
                <p>Search</p>
                <svg viewBox="0 0 1024 1024" role="img">
                    <path class="path1" d="M848.471 928l-263.059-263.059c-48.941 36.706-110.118 55.059-177.412 55.059-171.294 0-312-140.706-312-312s140.706-312 312-312c171.294 0 312 140.706 312 312 0 67.294-24.471 128.471-55.059 177.412l263.059 263.059-79.529 79.529zM189.623 408.078c0 121.364 97.091 218.455 218.455 218.455s218.455-97.091 218.455-218.455c0-121.364-103.159-218.455-218.455-218.455-121.364 0-218.455 97.091-218.455 218.455z"></path>
                </svg>
            </a>
        </div>
    </div>
    
    <div id = "main">
        <div class="block">
            <a id="myads" href="../myads/">My Classified Ad's</a>
        </div>
        <hr>
        <div class ="block">
            <?php
                $errpp = false;
                if (isset($_FILES["img"])){
                    if ($_FILES["img"]["error"] > 0){
                        $errpp = "Error during upload";
                        goto finpp;
                    }
                    if ($_FILES["img"]["size"] > 1000000){
                        $errpp = "This image is too heavy";
                        goto finpp;  
                    }
                    $typeallow = ["image/png","image/jpeg","image/gif"];
                    if (!in_array($_FILES["img"]["type"], $typeallow)){
                        $errpp = "Bad type file";
                        goto finpp;
                    }
                    $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                    if (mysqli_connect_error() != null){
                        $errpp = "Error during upload.";
                        goto finpp;
                    }
                    $path = $_FILES["img"]["tmp_name"];
                    $type = $_FILES["img"]['type'];
                    $data = file_get_contents($path);
                    $base64 = 'data:' . $type . ';base64,' . base64_encode($data);
                    $res = mysqli_query($connexion, "UPDATE account SET img = '{$base64}' WHERE id = '{$log["id"]}';");
                    if ($res == false){
                        mysql_close($connexion);
                        $errpp = "Error during Upload.";
                        goto finpp;
                    }
                    $log["img"] = $base64;
                }
                finpp:

                if ($errpp != false){
                    echo "<p class=errmsg>{$errpp}</p>";
                }

                if ($log["img"] == NULL){
                    ?>
                    <img id="pp" alt="pp" src="../src/img.jpg">
                    <?php
                }else {
                    echo "<img id=\"pp\" alt=\"pp\" src=\"{$log["img"]}\">";
                }
                
            ?>
            <p class="info">No more than 1 Mo</p>
            <form class="sousblock" action="./" method="post" enctype="multipart/form-data">
                <label id="upload">
                    <input class="hidden" name="img" type="file" accept=".jpeg,.png,.gif" required>
                    Upload
                </label>
                <button class="button" type="submit">Apply</button>
            </form>
        </div>
        <hr>
        <div class="block">
            <?php
                $errname = false;

                if (isset($_POST["name"])){
                    if (preg_match("/[A-Za-z^\s][A-Za-z\s]{1,14}[A-Za-z^\s]/", $_POST["name"]) != 1){
                        $errname = "Bad name format";
                        goto finname;
                    }
                    $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                    if (mysqli_connect_error() != null){
                        $errname = "Processing error";
                        goto finname;
                    }
                    $res = mysqli_query($connexion, "SELECT * FROM account WHERE name = '{$_POST["name"]}';");
                    if (mysqli_num_rows($res) != 0){
                        mysqli_close($connexion);
                        $errname = "This name is already taken.";
                        goto finname;
                    }
                    $res = mysqli_query($connexion, "UPDATE account SET name = '{$_POST["name"]}' WHERE id = '{$log["id"]}';");
                    if ($res == false){
                        mysql_close($connexion);
                        $errname = "Processing error.";
                        goto finname;
                    }
                    $log["name"] = $_POST["name"];
                }
                finname:

                if ($errname != false){
                    echo "<p class=errmsg>{$errname}</p>";
                }
                echo "<p class=\"info\">Actual name</p>";
                echo "<p id=\"name\">{$log["name"]}</p>";
            ?>
            <form class="sousblock" action="./" method="post">
                <input type="text" placeholder="New Name" pattern="[A-Za-z^\s][A-Za-z\s]{1,14}[A-Za-z^\s]" name="name" required>
                <button class="button" type="submit">Apply</button>
            </form>    
        </div>
        <hr>

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

                    $mdp = base64_decode($log["token"]);
                    $keyfile = realpath("../src/")."/.private.key";
                    $pvtkey = openssl_pkey_get_private(file_get_contents($keyfile));
                    openssl_private_decrypt($mdp, $mdp, $pvtkey);
            
                    $mdp = explode(":", $mdp);
                    array_pop($mdp);
                    $mdp = array_pop($mdp);
            
                    if ($mdp != $_POST["password"]){
                        $errpass = "Bad Password";
                        goto finpass;
                    }

                    $keyfile = realpath("../src/")."/.public.key";
                    $pubkey = openssl_pkey_get_public(file_get_contents($keyfile));
                    openssl_public_encrypt($log["email"].":".$_POST["npassword"].":".time(), $token, $pubkey);
                    $token = base64_encode($token);

                    $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                    if (mysqli_connect_error() != null){
                        $errpass = "Internal Server Error";
                        goto finpass;
                    }

                    $res = mysqli_query($connexion, "UPDATE account SET token = '{$token}'  WHERE id = '{$log["id"]}';");
                    if ($res == false){
                        mysqli_close($connexion);
                        $errpass = "Processing error. Retry later";
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
                if (isset($_POST["deleteacc"])){
                    $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                    if (mysqli_connect_error() != null){
                        $errpass = "Internal Server Error";
                        goto findelete;
                    }

                    $res = mysqli_query($connexion, "DELETE FROM account WHERE id = '{$log["id"]}';");
                    if ($res == false){
                        mysqli_close($connexion);
                        $errpass = "Processing error. Retry later";
                        goto findelete;
                    }

                    try {
                        mysqli_query($connexion, "DELETE FROM classified WHERE user = '{$log["id"]}';");
                    } catch(Exception $e) {  
                    }
                    $url = explode("/", $_SERVER['PHP_SELF']);
                    array_pop($url);
                    array_pop($url);
                    $url = implode("/", $url);
                    if ($url == null){
                        $url = "/";
                    }
                    header("Location: " . $url);
                    exit();
                }
                findelete:

                if ($errpass != false){
                    echo "<p class=errmsg>{$errpass}</p>";
                }
            ?>
            
            <form class="sousblock deleteacc" action="./" method="post">
                <label>This action will be irretrievable, so there will be no way to recover your account and your announcements published on the platform</label>
                <input class="hidden" name="deleteacc" type="checkbox" checked>
                <button class="button return" type="submit">Delete Account</button>
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
                    setcookie("user", null, $options);
                    header("Location: " . $url);
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