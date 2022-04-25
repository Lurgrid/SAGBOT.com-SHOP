<?php
    if (isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 41943040) {
        http_response_code(404);
        $url = explode("/", $_SERVER['PHP_SELF']);
        array_pop($url);
        array_pop($url);
        array_push($url, "error/?code=500");
        $url = implode("/", $url);
        header("Location: " . $url);
        exit();
    }
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
?>

<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../css/create.css">
    <title>LeMauvaisCoin</title>
</head>

<body>
    <!--------- Top Barre Website --------->
    <div id="banner">
        <a href="../" id="logo">lemauvaiscoin</a>
        <div id="findcreate">
            <a id="search" href="../find/">
                <p>Search</p>
                <svg viewBox="0 0 1024 1024" role="img">
                    <path class="path1" d="M848.471 928l-263.059-263.059c-48.941 36.706-110.118 55.059-177.412 55.059-171.294 0-312-140.706-312-312s140.706-312 312-312c171.294 0 312 140.706 312 312 0 67.294-24.471 128.471-55.059 177.412l263.059 263.059-79.529 79.529zM189.623 408.078c0 121.364 97.091 218.455 218.455 218.455s218.455-97.091 218.455-218.455c0-121.364-103.159-218.455-218.455-218.455-121.364 0-218.455 97.091-218.455 218.455z"></path>
                </svg>
            </a>
        </div>
        <?php
            echo "<a id=\"account\" href=\"../account/\">{$log['name']}";
            if ($log["img"] == NULL){
                echo "<img id=\"pp\" alt=\"pp\" src=\"../src/img.jpg\">";
            }else {
                echo "<img id=\"pp\" alt=\"pp\" src=\"{$log["img"]}\">";
            }
            echo "</a>";
        ?>
    </div>
    <!--------- Top Barre Website --------->

    <div id="main">
        <?php
            $err = 0;
            $email = true;
            $tel = true;
            $contact = true;
            $sucess = false;
            include "../src/.config.php";
            if (count($_POST) == 0){
                $noinfo = true;
            } else {
                $noinfo = false;
            }
            if (!isset($_POST["name"], $_POST["desc"], $_POST["AF"], $_POST["amont"], $_POST["categorie"], $_POST["city"], $_POST["address"])){
                goto end;
            }
            if (preg_match("/[A-Za-z0-9^\s][A-Za-z0-9\s]{1,63}[A-Za-z0-9^\s]/", $_POST["name"]) != 1){
                unset($_POST["name"]);
                $err = "Bad Format Classified Ad Name";
                goto end;
            }
            if (preg_match("/.{5,256}/", $_POST["desc"]) != 1){
                unset($_POST["desc"]);
                $err = "Bad Format Description";
                goto end;
            }
            if (preg_match("/.{1,128}/", $_POST["city"]) != 1){
                unset($_POST["city"]);
                $err = "Bad Format City";
                goto end;
            }
            if (preg_match("/.{1,256}/", $_POST["address"]) != 1){
                unset($_POST["address"]);
                $err = "Bad Format Address";
                goto end;
            }
            $list = ["Buy", "Sell"];
            if (!in_array($_POST["AF"], $list)){
                unset($_POST["AF"]);
                $err = "Bad Format Mod (Buy / Sell)";
                goto end;
            }
            if (preg_match("/^[0-9]{1,9}$/", $_POST["amont"]) != 1){
                unset($_POST["amont"]);
                $err = "Bad Format Amount";
                goto end;
            }
            if (!in_array($_POST["categorie"], $category)){
                unset($_POST["categorie"]);
                $err = "Bad Format Category";
                goto end;
            }
            if (isset($_POST["email"]) && $_POST["email"] != null){
                if (preg_match("/^.{0,128}$/", $_POST["email"]) != 1 || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                    unset($_POST["email"]);
                    $err = "Bad Email";
                    $email = false;
                    goto end;
                }
            }
            if (isset($_POST["tel"]) && $_POST["tel"] != null){
                if (preg_match("/^(0|\+33 )[1-9]([-. ]?[0-9]{2} ){3}([-. ]?[0-9]{2})$/", $_POST["tel"]) != 1){
                    unset($_POST["tel"]);
                    $err = "Bad Phone Number";
                    $tel = false;
                    goto end;
                }
            }
            if (!isset($_POST["email"], $_POST["tel"]) || ($_POST["tel"] == null && $_POST["email"] == null) ){
                $contact = false;
                $err = "One of the two required fields";
                goto end;
            }
            $base64 = null;
            if (isset($_FILES["img"])){
                if ($_FILES["img"]["size"] > 8000000){
                    $err = "This image is too heavy";
                    goto end;  
                }
                $typeallow = ["image/png","image/jpeg","image/jpg","image/gif"];
                if (!in_array($_FILES["img"]["type"], $typeallow) && $_FILES["img"]["size"] != 0){
                    $err = "Bad type file";
                    goto end;
                }
            }

            if ($_FILES["img"]["size"] != 0){
                $path = $_FILES["img"]["tmp_name"];
                $type = $_FILES["img"]['type'];
                $data = file_get_contents($path);
                $base64 = 'data:' . $type . ';base64,' . base64_encode($data);
            }

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
            $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'classified'");
            if (mysqli_num_rows($res) == 0){
                $account = "CREATE TABLE classified(id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,name varchar(64) NOT NULL, description varchar(256) NOT NULL, AF varchar(32) NOT NULL, amount varchar(9) NOT NULL, category varchar(128) NOT NULL, email varchar(128), tel varchar(16), city varchar(128), address varchar(256) ,time INT NOT NULL,user varchar(128) NOT NULL, img LONGTEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                mysqli_query($connexion, $account);
            }
            $res = mysqli_query($connexion, "SELECT time FROM classified WHERE user = '{$log["email"]}' ORDER BY time desc LIMIT 0,1");
            if (mysqli_num_rows($res) != 0 && time() == mysqli_fetch_array($res)[0] + 60*2){
                $err = "You can publish Ads every 2 minutes";
                mysqli_close($connexion);
                goto end;
            }
            $time = time();
            $_POST["email"] = strtolower($_POST["email"]);
            $request = "INSERT INTO classified (name, description, AF, amount, category, email, tel, city, address, time, user, img) VALUES (
            '{$_POST["name"]}',
            '{$_POST["desc"]}',
            '{$_POST["AF"]}',
            '{$_POST["amont"]}',
            '{$_POST["categorie"]}',
            '{$_POST["email"]}',
            '{$_POST["tel"]}',
            '{$_POST["city"]}',
            '{$_POST["address"]}',
            '{$time}',
            '{$log["id"]}',
            '{$base64}'
            )";
            $res = false;
            try {
                $res = mysqli_query($connexion, $request);
            } catch(Exception $e){
                $err = "DB error max_allowed_packet is too low.";
                goto end;
            }
            
            if ($res == false){
                mysql_close($connexion);
                $err = "Processing error.";
                goto end;
            }
            $sucess = true;

            end:
        ?>
        <div id="box">
            <?php
                if ($sucess){
                    ?>
                    <div id="returnbox">
                        <p id ="returnmess">Your classified ad has been published!</p>
                        <a href="../" id="return">Return To Home</a>
                    </div>
                    <?php
                    return;
                }
            ?>
            <form id="form" action="./" method="post" enctype="multipart/form-data">
                <?php

                if ($err != 0){
                    echo "<p id=\"errmsg\">".$err."</p>";
                }
                ?>
                
                <label>Minimum 3 characters, maximum 64 character and no special character <span class="imp">(*)</span></label>
                <input name="name" type="text" placeholder="Classified Ad Name" pattern="[A-Za-z0-9^\s][A-Za-z0-9\s]{1,63}[A-Za-z0-9^\s]" <?php
                    if (!$noinfo && isset($_POST["name"])){
                        echo "value=\"{$_POST["name"]}\"";
                    }
                    if (!$noinfo && !isset($_POST["name"])){
                        echo "class=\"red\"";
                    }
                ?>required>
                <label>Minimum 5 characters and maximum 256 character <span class="imp">(*)</span></label>
                <textarea form="form" name="desc" placeholder="Description" maxlength="256" minlength="5" <?php
                    if (!$noinfo && !isset($_POST["desc"])){
                        echo "class=\"red\"";
                    }
                ?>required><?php
                    if (!$noinfo && isset($_POST["desc"])){
                        echo $_POST["desc"];
                    }
                ?></textarea>
                <label>Minimum 0€ and Maximum 999 999 999€<span class="imp">(*)</span></label>
                <div id="sousblock">
                    <select name="AF" <?php
                        if (!$noinfo && !isset($_POST["AF"])){
                            echo "class=\"red\"";
                        }
                    ?>required>
                        <option label="Sell"<?php
                            if (!$noinfo && $_POST["AF"] == "Sell"){
                                echo "selected";
                            }  
                        ?>>Sell</option>
                        <option <?php
                            if (!$noinfo && $_POST["AF"] == "Buy"){
                                echo "selected";
                            }  
                        ?>>Buy</option>
                    </select>
                    <input name="amont" type="number" placeholder="333€" min="0" max="999999999"  <?php
                        if (!$noinfo && isset($_POST["amont"])){
                            echo "value=\"{$_POST["amont"]}\"";
                        }
                        if (!$noinfo && !isset($_POST["amont"])){
                            echo "class=\"red\"";
                        }
                    ?>required>
                </div>
                <label>One of the two required fields / Phone: 07 00 00 00 00 or +33 7 00 00 00 00 <span class="imp">(*)</span></label>
                <div id="sb">
                    <input type="email" name="email" placeholder="Email" <?php
                        if (!$noinfo && isset($_POST["email"])){
                            echo "value=\"{$_POST["email"]}\"";
                        }
                        if (!$email || !$contact){
                            echo "class=\"red\"";
                        }
                    ?>>
                    <input type="text" name="tel" placeholder="Phone Number" pattern="^(0|\+33 )[1-9]([-. ]?[0-9]{2} ){3}([-. ]?[0-9]{2})$" <?php
                        if (!$noinfo && isset($_POST["tel"])){
                            echo "value=\"{$_POST["tel"]}\"";
                        }
                        if (!$tel || !$contact){
                            echo "class=\"red\"";
                        }
                    ?>>
                </div>
                <label><span class="imp">(*)</span></label>
                <div id="sb2">
                    <input type="text" name="address" placeholder="Address" pattern="^.{0,256}$" <?php
                        if (!$noinfo && isset($_POST["address"])){
                            echo "value=\"{$_POST["address"]}\"";
                        }
                        if (!$noinfo && !isset($_POST["address"])){
                            echo "class=\"red\"";
                        }
                    ?>>
                    <input type="text" name="city" placeholder="City" pattern="^.{0,128}$" <?php
                        if (!$noinfo && isset($_POST["city"])){
                            echo "value=\"{$_POST["city"]}\"";
                        }
                        if (!$noinfo && !isset($_POST["city"])){
                            echo "class=\"red\"";
                        }
                    ?>>
                </div>
                <label>Category <span class="imp">(*)</span></label>
                <select name="categorie" <?php
                        if (!$noinfo && !isset($_POST["categorie"])){
                            echo "class=\"red\"";
                        }
                    ?>required>
                    <?php
                        foreach ($category as $cat){
                            echo "<option value=\"{$cat}\"";
                            if (!$noinfo && $_POST["categorie"] == $cat){
                                echo " selected";
                            }  
                            echo ">{$cat}</option>";
                        }
                    ?>                 
                </select>
                <label>No more than 8 Mo.</label>
                <label id="upload">
                    <input class="hidden" name="img" type="file" accept=".jpeg,.jpg,.png,.gif">
                    
                    <?php
                        if (count($_FILES) > 0 && $_FILES["img"]["name"] != null){
                            echo "Re-Upload Picture";
                        } else {
                            echo "Upload Picture";
                        }
                    ?>
                </label>
                
                <hr>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>