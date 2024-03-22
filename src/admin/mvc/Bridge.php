<?php
    // set timezone
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once './mvc/core/MiddleWare.php';
    require_once './mvc/core/App.php';
    require_once './mvc/core/Controller.php';
    require_once './mvc/core/DB.php';

    require_once './mvc/core/php-jwt/src/Key.php';
    require_once './mvc/core/php-jwt/src/JWTExceptionWithPayloadInterface.php';
    require_once './mvc/core/php-jwt/src/ExpiredException.php';
    require_once './mvc/core/php-jwt/src/SignatureInvalidException.php';
    require_once './mvc/core/php-jwt/src/JWT.php';
    $myApp = new App();
?>