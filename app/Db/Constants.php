<?php

namespace App\Db;

    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'dev');
    define('DB_PASSWORD', '78737');
    define('DB_NAME', 'multi_vendor');

    define('USER_CREATED', 101);
    define('USER_EXISTS', 102);
    define('USER_FAILURE', 103);

    define('USER_AUTHENTICATED', 201);
    define('USER_NOT_FOUND', 202);
    define('USER_PASSWORD_DO_NOT_MATCH', 203);

    define('PASSWORD_CHANGED', 301);
    define('PASSWORD_DO_NOT_MATCHED', 302);
    define('PASSWORD_UPDATION_FAILED',303);