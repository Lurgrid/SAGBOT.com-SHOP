<?php
    include_once "../src/.token.php";
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

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../css/myclasads.css">
    <link rel="shortcut icon" href="../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>My Ads</title>
</head>

<body>
    
    <div id="banner">
        <a href="../" id="logo">lemauvaiscoin</a>
        <div id="findcreate">
            <form action="../create/">
                <button type="submit" class="btn">+</button>
            </form>
            <a id="search" href="../find">
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
    

    <div id="searchbanner">
        <div id="boxsui">
            <?php
                $errads = false;
                if (isset($_POST["id"], $_POST["mode"])){
                    $mode = ["edit", "delete"];
                    if (!in_array($_POST["mode"], $mode)){
                        $errads = "Invalid Mode";
                        goto start;
                    }
                    if (intval($_POST["id"]) < 1){
                        $errads = "Invalid ID";
                        goto start;
                    }
                    include_once "../src/.mysql.php";
                    try {
                        $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                    } catch (Exception $e){
                        $errads = "Error during server connection";
                        goto end;
                    }
                    $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'classified'");
                    if (mysqli_num_rows($res) == 0){
                        $errads = "Unknow Ad";
                        goto end;
                    }
                    $res = mysqli_query($connexion, "SELECT * FROM classified WHERE user = '{$log["id"]}' AND id = '{$_POST["id"]}'");
                    if (mysqli_num_rows($res) == 0){
                        $errads = "Unkown Ad";
                        goto already;
                    }

                    if ($_POST["mode"] == "delete"){
                        $res = mysqli_query($connexion, "DELETE FROM classified WHERE user = '{$log["id"]}' AND id = '{$_POST["id"]}'");
                        if (mysqli_errno($connexion)){
                            $errads = "Processing Error, Try later";
                            goto already;
                        }
                    }else {
                        $url = explode("/", $_SERVER['PHP_SELF']);
                        array_pop($url);
                        array_push($url, "edit/?id={$_POST["id"]}");
                        $url = implode("/", $url);
                        header("Location: " . $url);
                        exit();
                    }
                }
                start:
                include_once "../src/.mysql.php";
                try {
                    $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                } catch (Exception $e){
                    $err = "Error during server connection";
                    goto end;
                }
                already:
                $err = false;
                $ads = array();
                if (mysqli_connect_error() != null){
                    $err = "Error during server connection";
                    goto end;
                }
                $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'classified'");
                if (mysqli_num_rows($res) == 0){
                    mysqli_close($connexion);
                    goto end;
                }
                $query = array();
                if (isset($_GET["name"]) && $_GET["name"] != null ){
                    if (preg_match("/[A-Za-z0-9^\s][A-Za-z0-9\s]{0,63}[A-Za-z0-9^\s]/", $_GET["name"]) != 1){
                        unset($_GET["name"]);
                        $err = "Bad Format Classified Ad Name";
                        goto end;
                    }
                    $query["name"] = $_GET["name"];
                }

                if (isset($_GET["city"]) && $_GET["city"] != null ){
                    if (preg_match("/.{1,128}/", $_GET["city"]) != 1){
                        unset($_GET["city"]);
                        $err = "Bad Format City";
                        goto end;
                    }
                    $query["city"] = $_GET["city"];
                }

                if (isset($_GET["AF"]) && $_GET["AF"] != null ){
                    $list = ["Buy", "Sell"];
                    if (!in_array($_GET["AF"], $list)){
                        unset($_GET["AF"]);
                        $err = "Bad Format Mod (Buy / Sell)";
                        goto end;
                    }
                    $query["AF"] = $_GET["AF"];
                }
                if (isset($_GET["amount"]) && $_GET["amount"] != null ){
                    if (preg_match("/^[0-9]{1,9}$/", $_GET["amount"]) != 1){
                        unset($_GET["amount"]);
                        $err = "Bad Format Amount";
                        goto end;
                    }
                    $query["amount"] = $_GET["amount"];
                }
                include_once "../src/.config.php";
                if (isset($_GET["category"]) && $_GET["category"] != null ){
                    if (!in_array($_GET["category"], $category)){
                        unset($_GET["category"]);
                        $err = "Bad Format Category";
                        goto end;
                    }
                    $query["category"] = $_GET["category"];
                }
                if (isset($_GET["selector"]) && $_GET["selector"] != null ){
                    $selectors = [">","<"];
                    if (!in_array($_GET["selector"], $selectors)){
                        unset($_GET["category"]);
                        $err = "Bad Format Selector";
                        goto end;
                    }
                }
                $request = "SELECT * FROM classified WHERE user = {$log["id"]}";
                if (count($query) > 0){
                    foreach($query as $key => $value){
                        $request .= " AND ";
                        if ($key == "amount" && isset($_GET["selector"]) && $_GET["selector"] != null ){
                            $request .= "{$key} {$_GET["selector"]} '{$value}' ";
                        } else if ($key == "name" || $key == "city"){
                            $request .= "{$key} LIKE '%{$value}%' ";
                        } else {
                            $request .= "{$key} = '{$value}' ";
                        }
                    }
                }
                if (isset($_GET["page"]) && $_GET["page"] != null && preg_match("/[1-9]/", $_GET["page"]) == 1 && ($query["page"] = intval($_GET["page"])) > 0){
                }else {
                    $query["page"] = 1;
                }
                $page = ($query["page"] - 1) * 10;
                $request .= " ORDER BY id DESC LIMIT {$page},10";
                $res = mysqli_query($connexion, $request);
                $ads = array();
                while(($tmp = mysqli_fetch_array($res)) != null){
                    array_push($ads, $tmp);
                }
                mysqli_close($connexion);
                $url = explode("/", $_SERVER['PHP_SELF']);
                array_pop($url);
                $url = implode("/", $url);
                $url .= "?";
                foreach ($query as $key => $value){
                    if ($key != "page"){
                        $url .= "{$key}={$value}&";
                    }
                }

                end:
            ?>
            <form id="formsearch" action="./" method="get">
                <?php
                    if ($err != false){
                        echo "<p id=\"errmsg\">".$err."</p>";
                    }
                ?>               
                <div id="first">
                    <select name="AF">
                        <option value="">Sell/Buy</option>
                        <option <?php
                            if (isset($_GET["AF"]) && $_GET["AF"] == "Sell"){
                                echo "selected";
                            }  
                        ?>>Sell</option>
                        <option <?php
                            if (isset($_GET["AF"]) && $_GET["AF"] == "Buy"){
                                echo " selected";
                            }  
                        ?>>Buy</option>
                    </select>
                    <input name="name" type="text" placeholder="Your Ad's Name" pattern="[A-Za-z0-9^\s][A-Za-z0-9\s]{0,63}[A-Za-z0-9^\s]" <?php
                        if (isset($_GET["name"])){
                            echo "value=\"{$_GET["name"]}\"";
                        }
                    ?>>              
                    <button type="submit">Search</button>
                </div>
                <div id="second">
                    <input name="city" id="city" type="text" placeholder="City" pattern="^.{0,128}$" <?php
                        if (isset($_GET["city"])){
                            echo "value=\"{$_GET["city"]}\"";
                        }
                    ?>>
                    <select id="category" name="category">
                        <option value="">Category</option>
                        <?php
                            include_once "../src/.config.php";
                            foreach ($category as $cat){
                                echo "<option";
                                if (isset($_GET["category"]) && $_GET["category"] == $cat){
                                    echo " selected";
                                }  
                                echo ">{$cat}</option>";
                            }
                        ?>                 
                    </select>
                    <input name="amount" id="amont" type="number" min="0" max="999999999" placeholder="333€"   <?php
                        if (isset($_GET["amount"]) && $_GET["amount"] != null){
                            $val = intval($_GET["amount"], 10);
                            echo "value=\"{$val}\"";
                        }
                    ?>>
                    <select name="selector" id="selector">
                        <option value="">=</option>
                        <?php
                            $selectors = [">", "<"];
                            foreach ($selectors as $select){
                                echo "<option";
                                if (isset($_GET["selector"]) && $_GET["selector"] == $select){
                                    echo " selected";
                                }  
                                echo ">{$select}</option>";
                            }
                        ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div id="adsbanner">
        <div id="ads">
            <?php
                if ($errads != false){
                    echo "<p id=\"errmsg\">".$errads."</p>";
                }
                if (count($ads) == 0){
                    ?>
                    <p id="noting">Sorry, we have no results!</p>
                    <?php
                }else {
                    foreach ($ads as $key => $ad){
                        ?>
                            <div class="ad">
                                <img class="adspic" alt="ad picture" src="<?php echo ($ad["img"] == null)?"../src/clasads.jpg":$ad["img"];?>">
                                <div class="adssb">
                                    <div class="adsssb">
                                        <p class="adsname" ><a href="../ads?id=<?php echo $ad["id"] ?>"><?php echo $ad["name"]?></a></p>
                                        <ul>
                                            <li>Phone: <?php echo $ad["tel"] ?></li>
                                            <li>Email: <?php echo $ad["email"] ?></li>
                                            <li>City: <?php echo $ad["city"] ?></li>
                                            <li>Address: <?php echo $ad["address"] ?></li>
                                            <li><?php echo "<span>".$ad["AF"]."</span> ".$ad["amount"] ?>€</li>
                                            <li class="date"><?php echo date("Y F d H:i:s", $ad["time"])?></li>
                                        </ul>
                                    </div>
                                    <div class="form">
                                        <form class="edit" action="./" method="post">
                                            <input type="hidden" name="id" value="<?php echo $ad["id"] ?>">
                                            <input type="hidden" name="mode" value="edit">
                                            <button type="submit">Edit</button>
                                        </form>
                                        <form class="delete" action="./" method="post">
                                            <input type="hidden" name="id" value="<?php echo $ad["id"] ?>">
                                            <input type="hidden" name="mode" value="delete">
                                            <button type="submit">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                }
            ?>
            <?php
                if (count($ads) == 0 && isset($query["page"]) && $query["page"] > 1){
                    ?>
                    <hr>
                    <div id="np">
                        <p>
                            <a id="right" href="<?php echo $url."page=".( $query["page"] - 1 ) ?>">Previous</a>
                        </p>
                    </div>
                    <?php
                } else if (count($ads) != 0 && isset($query["page"])){
                    ?>
                    <hr>
                    <div id="np">
                        <p>
                            <a id="right" href="<?php echo $url."page=".(  ($query["page"] != 1)?$query["page"]-1:$query["page"] ) ?>">Previous</a>
                        </p>
                        <p>
                            <a id="left" href="<?php echo $url."page=".( $query["page"] + 1 )?>" >Next</a>
                        </p>
                    </div>
                    <?php
                }else {

                }
            ?>
        </div>
    </div>
</body>

</html>