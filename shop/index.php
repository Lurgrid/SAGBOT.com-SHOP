<?php
    include "./src/.token.php";
    $log = token(realpath("./src/"), 1);
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="./css/home.css">
    <link rel="shortcut icon" href="./src/lmc/favicon.ico" type="image/x-icon">
    <link rel="icon" href="./src/lmc/favicon_32x32.png" sizes="32x32">
    <link rel="icon" href="./src/lmc/favicon_48x48.png" sizes="48x48">
    <link rel="icon" href="./src/lmc/favicon_96x96.png" sizes="96x96">
    <link rel="icon" href="./src/lmc/favicon_144x144.png" sizes="144x144">
    <title>lemauvaiscoin</title>
</head>

<body>
    <!--- Top Barre Website --->
    <div id="banner">
        <a href="./" id="logo">lemauvaiscoin</a>
        <div id="findcreate">
            <form action="./create/">
                <button type="submit" class="btn">+</button>
            </form>
            <a id="search" href="./find/">
                <p>Search</p>
                <svg viewBox="0 0 1024 1024" role="img">
                    <path class="path1" d="M848.471 928l-263.059-263.059c-48.941 36.706-110.118 55.059-177.412 55.059-171.294 0-312-140.706-312-312s140.706-312 312-312c171.294 0 312 140.706 312 312 0 67.294-24.471 128.471-55.059 177.412l263.059 263.059-79.529 79.529zM189.623 408.078c0 121.364 97.091 218.455 218.455 218.455s218.455-97.091 218.455-218.455c0-121.364-103.159-218.455-218.455-218.455-121.364 0-218.455 97.091-218.455 218.455z"></path>
                </svg>
            </a>
        </div>
        <?php
            if ($log != false){
                echo "<a id=\"account\" href=\"./account/\">{$log['name']}";
                if ($log["img"] == NULL){
                    echo "<img id=\"pp\" alt=\"pp\" src=\"./src/img.jpg\">";
                }else {
                    echo "<img id=\"pp\" alt=\"pp\" src=\"{$log["img"]}\">";
                }
                echo "</a>";
            }else{
                echo "<a id=\"sui\" href=\"./login/\">Sign Up / Sign In</a>";
            }
        ?>
    </div>
    <!--- Top Barre Website --->


</body>

</html>