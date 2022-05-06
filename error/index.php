<!DOCTYPE html >



<html lang = "en">

<?php
        if(isset($_GET["code"])){
            $code = $_GET["code"];
        }else{
            $code = null;
        }
        http_response_code($code);
        function getmsg($code){
            switch($code){
                case "404": return "404 - Not Found";
                case "403": return "403 - Forbidden";
                case "500": return "500 - Internal Server Error";
                default: return "Unknown Code";
            }
        }
    ?>
<head>
    <title><?php echo getmsg($code)?></title>
    <meta charset="utf-8" />
</head>

<body>
    <h1><?php echo getmsg($code)?></h1>
    <hr>
</body>

</html>