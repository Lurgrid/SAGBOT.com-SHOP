<?php
    include "../src/.admin_token.php";
    $log = token(realpath("../src/"), 1);
    if (!$log){
        $url = explode("/", $_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link rel="shortcut icon" href="../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>Admin Page</title>
</head>

<body>
    
    <div id="banner">
        <a href="./" id="logo">lemauvaiscoin</a>
        <a id="account" href="./account/">Admin</a>
    </div>
    
    <div id = "main">
        <div id = "searchbanner">
            <div class="boxsui">
                <form class="formsearch" action="./users" method="get">           
                    <div class="first">
                        <input id="username" name="name" type="text" placeholder="User Name" pattern="[A-Za-z0-9^\s][A-Za-z0-9\s]{0,63}[A-Za-z0-9^\s]">              
                        <button type="submit">Search</button>
                    </div>
                        <div class="second">
                            <input name="email" id="email" type="text" placeholder="User Email" pattern="^.{0,128}$">
                        </div>
                </form>
            </div>
        </div>
        <div id="searchbanner2">
            <div class="boxsui">
                <form class="formsearch" action="./ads" method="get">          
                    <div class="first">
                        <select name="AF">
                            <option value="">Sell/Buy</option>
                            <option>Sell</option>
                            <option>Buy</option>
                        </select>
                        <input name="name" type="text" placeholder="Your Ad's Name" pattern="[A-Za-z0-9^\s][A-Za-z0-9\s]{0,63}[A-Za-z0-9^\s]">              
                        <button type="submit">Search</button>
                    </div>
                    <div class="second">
                        <input name="city" id="city" type="text" placeholder="City" pattern="^.{0,128}$">
                        <select id="category" name="category">
                            <option value="">Category</option>
                                <?php
                                    include_once "../src/.config.php";
                                    foreach ($category as $cat){
                                        echo "<option>{$cat}</option>";
                                        }
                                ?>                 
                        </select>
                        <input name="amount" id="amont" type="number" min="0" max="999999999" placeholder="333€">
                        <select name="selector" id="selector">
                            <option value="">=</option>
                                <?php
                                    $selectors = [">", "<"];
                                    foreach ($selectors as $select){
                                        echo "<option>{$select}</option>";
                                    }
                                ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>