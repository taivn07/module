<?php

// Create the router
$router = new \Phalcon\Mvc\Router();

//Remove trailing slashes automatically
$router->removeExtraSlashes(true);

//Define a route
$router->add(
    '/api/upload',
    array(
        'controller' => 'media',
        'action'     => 'upload',
    )
);

$router->add(
	"/api/medias",
	array(
		'controller' => 'media',
		'action' => 'index'
	)
);

$router->add(
	'/api/register',
	array(
		'controller' => 'user',
		'action' => 'register'
	)
);

$router->add(
	'/api/login',
	array(
		'controller' => 'user',
		'action' => 'login'
	)
);

return $router;