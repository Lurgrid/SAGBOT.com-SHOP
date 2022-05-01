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

    <div id="searchbanner">
            <div id="boxsui">
                <form id="formsearch" action="./find" method="get">          
                    <div id="first">
                        <select name="AF">
                            <option value="">Sell/Buy</option>
                            <option>Sell</option>
                            <option>Buy</option>
                        </select>
                        <input name="name" type="text" placeholder="Your Ad's Name" pattern="[A-Za-z0-9^\s][A-Za-z0-9\s]{0,63}[A-Za-z0-9^\s]">              
                        <button type="submit">Search</button>
                    </div>
                    <div id="second">
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
        <div id="info">
            <div class="sb">
                <h2>Create a Account</h2>
                <p>
                    To create an account on our platform just click on the left on <a class="sui" href="./login/">Sign Up / Sign In</a>. <br>
                    Once you click on it, you must complete the form with your various information. <br> Attention the security question and the only way to recover your account if you forgot your password and you could never change the answer then, thank you to complete the field
                </p>
            </div>
            <div class="sb">
                <h2>Login to your account</h2>
                <p>
                    To login to your account you must click on <a class="sui" href="./login/">Sign Up / Sign In</a>. <br>
                    Once on the page you must fill in your email and password. <br> 
                    If you have forgotten your password you must click on <a class="forgot" href="./login/forgot/">Forgot Password</a> <br>
                    Then you must enter your email, then you will be asked the answer to your security question, once answered you can enter a new password
                </p>
            </div>
            <div class="sb">
                <h2>Edit/Delete your account</h2>
                <p>
                    Once connected you must click on  <a class="account" href="./account/">Your Name <img class="pp" alt="pp" src="./src/img.jpg"> </a> <br>
                    Once on the page, the first button at the top will allow you to modify / delete your published ads <br>
                    The 3 forms above will allow you to change your profile picture / account names / password <br>
                    The second last button will allow you to delete your account and all your ads <br>
                    The last button will allow you to disconnect
                </p>
            </div>
            <div class="sb">
                <h2>Create an ad/View ads</h2>
                <p>
                    To create an ad you must be connected and you must click on <span ><a class="button" href="./create">+</a></span> <br>
                    Once on this page you must complete the form <br>
                    To see all the ads you have to click on the search bar, once on the search page you can do custom searches
                </p>
            </div>
        </div>


</body>

</html>