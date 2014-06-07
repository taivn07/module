<?php

error_reporting(E_ALL);
// setup time zone
date_default_timezone_set('Asia/Tokyo');
define('BASE_URL', 'http://paditech.com/');
(new Phalcon\Debug)->listen();

try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    /**
     * Load upload component
    */
    $di->set('uploader', function() {
        return new UploadHandler($di);
    });

    /**
     * Load common component
     */
    $di->set('common', function() {
        return new CommonComponent();
    });

    /**
    * add routing capabilities
    */
    $di->set('router', function(){
        require __DIR__.'/../app/config/routes.php';
        return $router;
    });

    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}