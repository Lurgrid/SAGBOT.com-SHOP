<?php
    include_once "../src/.token.php";
    $log = token(realpath("../src/"), 2);
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../css/ads.css">
    <link rel="shortcut icon" href="../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>Ads Information</title>
</head>

<body>
    <!--- Top Barre Website --->
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
        <?php
            if ($log != false){
                echo "<a id=\"account\" href=\"../account/\">{$log['name']}";
                if ($log["img"] == NULL){
                    echo "<img id=\"pp\" alt=\"pp\" src=\"../src/img.jpg\">";
                }else {
                    echo "<img id=\"pp\" alt=\"pp\" src=\"{$log["img"]}\">";
                }
                echo "</a>";
            }else{
                echo "<a id=\"sui\" href=\"../login/\">Sign Up / Sign In</a>";
            }
        ?>
    </div>
    <!--- Top Barre Website --->

    <div id="main">
        <?php
            $err = false;
            if (!isset($_GET["id"]) || intval($_GET["id"]) <= 0){
                $err = "Unknow Classified Ad";
                goto end;
            }
            try {
                include_once "../src/.mysql.php";
                $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
            } catch (Exception $e){
                $err = "Error during server connection";
                goto end;
            }
            $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'classified'");
            if (mysqli_num_rows($res) == 0){
                $err = "Unknow Classified Ad";
                goto end;
            }
            $res = mysqli_query($connexion, "SELECT * FROM classified WHERE id = '{$_GET["id"]}';");
            if (mysqli_num_rows($res) == 0){
                $err = "Unknow Classified Ad";
                goto end; 
            }
            $ad = mysqli_fetch_array($res);

            $res = mysqli_query($connexion, "SELECT name FROM account WHERE id = '{$ad["user"]}';");
            if (mysqli_num_rows($res) != 0){
                $user = mysqli_fetch_array($res)["name"];
            }else {
                $user = "Unknow User";
            }

            end:
        ?>
        <div id="box"> 
            <?php
                if ($err != false){
                    echo "<p id=\"errmsg\">{$err}<p>";
                    ?>
                    <a href="../" id="return">Return To Home</a>
                    <?php
                    return;
                }
            ?>
            <img id="adspic" alt="ad picture" src="<?php echo ($ad["img"] == null)?"../src/clasads.jpg":$ad["img"];?>">
            <hr>
            <p id="adsname"><?php echo $ad["name"] ?><p>
            <p id="adsdesc"><?php 
                $ad["description"] = explode("\n", $ad["description"]);
                foreach ($ad["description"] as $desc){
                    echo $desc;
                    echo "<br>";
                }
             ?><p>
            <p id="adsprice"><?php 
                switch ($ad["AF"]){
                    case "Buy": echo "It asks for";
                    break;
                    case "Sell": echo "Buy it for ";
                    break;
                    default : echo "?? ";
                    break;
                }
            ?> <?php echo $ad["amount"] ?>€</p>
            <p class="title">Information<p>
            <ul id="adsinfo">
                <li>Category: <?php echo $ad["category"] ?></li>
                <li>Phone: <?php echo $ad["tel"] ?></li>
                <li>Email: <?php echo $ad["email"] ?></li>
                <li>City: <?php echo $ad["city"] ?></li>
                <li>Address: <?php echo $ad["address"] ?></li>
                <li>User Name: <?php echo $user ?></li>
            </ul>
            <p id="adstime">Publish: <?php echo date("Y F d H:i:s", $ad["time"])?></p>
        </div>
    </div>

</body>

</html>