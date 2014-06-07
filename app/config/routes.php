<?php

// Create the router
$router = new \Phalcon\Mvc\Router();

//Remove trailing slashes automatically
$router->removeExtraSlashes(true);

//Define a route
$router->add(
    "/api/upload",
    array(
        "controller" => "media",
        "action"     => "upload",
    )
);

return $router;