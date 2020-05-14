<?php

use Respect\Validation\Validator as validate;

session_start();

require __DIR__ . '/../vendor/autoload.php';

include_once dirname(__FILE__) . '/../app/Db/Constants.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
        'driver' => 'mysql',
        'host' => DB_HOST,
        'database' => DB_NAME,
        'username' => DB_USER,
        'password' => DB_PASSWORD,
        'port' => 8889,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'strict' => false
    ]
    ]
]);

$container = $app->getContainer();

// To use Eloquent outside of LARAVEL
$capsule = new \Illuminate\Database\Capsule\Manager;

// ADD Eloquent Connection
$capsule->addConnection($container['settings']['db']);

// Set Eloquent as Global
$capsule->setAsGlobal();

// Boot Eloquent
$capsule->bootEloquent();

// Adding Eloquent to the Container
$container['eloquent'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['db'] = function ($container) {
    // include_once dirname(__FILE__) . '/../app/Db/Constants.php';
    $con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 8889);
            if (mysqli_connect_errno()) {
                echo "Failed to connect " . mysqli_connect_error();
                return null; 
            }
            return $con; 
};

// Adding Authentication to the Container
$container['Auth'] = function ($container) {
    return new \App\Auth\Auth;
};

// Slim Flash
$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages;
};

// SLIM 3 Emailing Library
$container['mailer'] = function($container) {
    $twig = $container['view'];
    $mailer = new \Anddye\Mailer\Mailer($twig, [
        'host'      => 'smtp.mailtrap.io',  // SMTP Host
        'port'      => '2525',  // SMTP Port
        'username'  => '88abe6d6710c40',  // SMTP Username
        'password'  => '1bd4a459a9eeea',  // SMTP Password
        'protocol'  => 'TLS'   // SSL or TLS
    ]);
        
    // Set the details of the default sender
    $mailer->setDefaultFrom('f24f71709f-105632@inbox.mailtrap.io', 'Multi-Vendor');
    
    return $mailer;
};

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../public/views', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    // Twig Asset Manager
    $assetManager = new LoveCoding\TwigAsset\TwigAssetManagement([
        'verion' => '1'
    ]);
    $assetManager->addPath('css', 'public/views');
    $assetManager->addPath('img', 'public/views');
    $assetManager->addPath('js', 'public/views');
    $view->addExtension($assetManager->getAssetExtension());

    // Setting User session Global Variable
    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->Auth->check(),
        'user' => $container->Auth->user()
    ]);

    // Setting Flash messages in view
    $view->getEnvironment()->addGlobal('flash', $container->flash);

    $view->getEnvironment()->addGlobal('session', $_SESSION);

    return $view;
};



// Binding Validator to the Container
$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};

// Adding AuthController to the Container
$container['AuthController'] = function ($container) {
    return new \App\Controllers\Auth\AuthController($container);
};

// Adding PasswordController to the Container
$container['PasswordController'] = function ($container) {
    return new \App\Controllers\Auth\PasswordController($container);
};

// Adding Middleware
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));


validate::with('App\\Validation\\Rules\\');

require __DIR__ . '/../routes/main.php';
require __DIR__ . '/../routes/users.php';
// require __DIR__ . '/../routes/dashboard.php';
require __DIR__ . '/../routes/product.php';
require __DIR__ . '/../routes/brand.php';
require __DIR__ . '/../routes/category.php';
require __DIR__ . '/../routes/vendor.php';
require __DIR__ . '/../routes/cart.php';
require __DIR__ . '/../routes/sitecategory.php';
require __DIR__ . '/../routes/search.php';
require __DIR__ . '/../routes/account.php';
require __DIR__ . '/../routes/question.php';
require __DIR__ . '/../routes/seller.php';
require __DIR__ . '/../routes/reports.php';