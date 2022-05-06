<?php
    include "../../src/.admin_token.php";
    $log = token(realpath("../../src/"), 2);
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
    <link rel="stylesheet" type="text/css" href="../../css/myclasads.css">
    <link rel="shortcut icon" href="../../src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../../src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="../../src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="../../src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="../../src/lmc/favicon_144x144.png" sizes="144x144">
    <title>Users List | Admin Page</title>
    <style>
        .delete {
            bottom: 40% !important;
            top: 40%; 
            height: 20%; 
        }
        #first input {
            width: 79.5% !important
        }
        #email {
            width: 100%
        }
    </style>
</head>

<body>
    
    <div id="banner">
        <a href="../" id="logo">lemauvaiscoin</a>
        <a id="account" href="../account/">Admin</a>
    </div>
    

    <div id="searchbanner">
        <div id="boxsui">
            <?php
                $errads = false;
                if (isset($_POST["id"], $_POST["mode"])){
                    if ($_POST["mode"] != "delete"){
                        $errads = "Invalid Mode";
                        goto start;
                    }
                    if (intval($_POST["id"]) < 1){
                        $errads = "Invalid ID";
                        goto start;
                    }
                    include_once "../../src/.mysql.php";
                    try {
                        $connexion = mysqli_connect(MYSQL_HOST, MYSQL_LOG, MYSQL_PWD, MYSQL_DB);
                    } catch (Exception $e){
                        $errads = "Error during server connection";
                        goto end;
                    }
                    $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'account'");
                    if (mysqli_num_rows($res) == 0){
                        $errads = "Unknow Ad";
                        goto end;
                    }
                    $res = mysqli_query($connexion, "SELECT * FROM account WHERE id = '{$_POST["id"]}'");
                    if (mysqli_num_rows($res) == 0){
                        $errads = "Unkown Ad";
                        goto already;
                    }

                    $res = mysqli_query($connexion, "DELETE FROM account WHERE id = '{$_POST["id"]}'");
                    if (mysqli_errno($connexion)){
                        $errads = "Processing Error, Try later";
                        goto already;
                    }
                    try {
                        mysqli_query($connexion, "DELETE FROM classified WHERE user = '{$_POST["id"]}';");
                    } catch(Exception $e) {  
                    }
                }
                start:
                include_once "../../src/.mysql.php";
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
                $res = mysqli_query($connexion, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'account'");
                if (mysqli_num_rows($res) == 0){
                    mysqli_close($connexion);
                    goto end;
                }
                $query = array();
                if (isset($_GET["name"]) && $_GET["name"] != null ){
                    if (preg_match("/[A-Za-z^\s][A-Za-z\s]{0,14}[A-Za-z^\s]/", $_GET["name"]) != 1){
                        unset($_GET["name"]);
                        $err = "Bad Format User Name";
                        goto end;
                    }
                    $query["name"] = $_GET["name"];
                }

                if (isset($_GET["email"]) && $_GET["email"] != null ){
                    if (preg_match("/^.{0,128}$/", $_GET["email"]) != 1){
                        unset($_GET["city"]);
                        $err = "Bad Format User Email";
                        goto end;
                    }
                    $query["email"] = $_GET["email"];
                }

                $request = "SELECT * FROM account";
                if (count($query) > 0){
                    $request .= " WHERE ";
                    $first = true;
                    foreach($query as $key => $value){
                        if (!$first){
                            $request .= " AND ";
                        }
                        $request .= "{$key} LIKE '%{$value}%' ";
                        $first = false;
                    }
                }
                if (isset($_GET["page"]) && $_GET["page"] != null && preg_match("/[1-9]/", $_GET["page"]) == 1 && ($query["page"] = intval($_GET["page"])) > 0){
                }else {
                    $query["page"] = 1;
                }
                $page = ($query["page"] - 1) * 10;
                $request .= " ORDER BY id DESC LIMIT {$page},10";
                $res = mysqli_query($connexion, $request);
                $users = array();
                while(($tmp = mysqli_fetch_array($res)) != null){
                    array_push($users, $tmp);
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
                    <input name="name" type="text" placeholder="User Name" pattern="[A-Za-z0-9^\s][A-Za-z0-9\s]{0,63}[A-Za-z0-9^\s]" <?php
                        if (isset($_GET["name"])){
                            echo "value=\"{$_GET["name"]}\"";
                        }
                    ?>>              
                    <button type="submit">Search</button>
                </div>
                <div id="second">
                    <input name="email" id="email" type="text" placeholder="User Email" pattern="^.{0,128}$" <?php
                        if (isset($_GET["email"])){
                            echo "value=\"{$_GET["email"]}\"";
                        }
                    ?>>
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
                if (count($users) == 0){
                    ?>
                    <p id="noting">Sorry, we have no results!</p>
                    <?php
                }else {
                    foreach ($users as $key => $user){
                        ?>
                            <div class="ad">
                                <img class="adspic" alt="User picture" src="<?php echo ($user["img"] == null)?"../../src/img.jpg":$user["img"];?>">
                                <div class="adssb">
                                    <div class="adsssb">
                                        <p class="adsname" ><?php echo $user["name"]?></p>
                                        <ul>
                                            <li>Email: <?php echo $user["email"] ?></li>
                                            <li>ID: <?php echo $user["id"] ?></li>
                                        </ul>
                                    </div>
                                    <div class="form">
                                        <form class="delete" action="./" method="post">
                                            <input type="hidden" name="id" value="<?php echo $user["id"] ?>">
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
                if (count($users) == 0 && isset($query["page"]) && $query["page"] > 1){
                    ?>
                    <hr>
                    <div id="np">
                        <p>
                            <a id="right" href="<?php echo $url."page=".( $query["page"] - 1 ) ?>">Previous</a>
                        </p>
                    </div>
                    <?php
                } else if (count($users) != 0 && isset($query["page"])){
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