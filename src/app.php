<?php

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', [
    '_controller' => 'Calendar\Controller\LeapYearController::index',
    'year' => null,
]));

$routes->add('hello', new Routing\Route('/hello/{name}', [
    '_controller' => 'render_template',
    'name' => 'World'
]));

$routes->add('bye', new Routing\Route('/bye', [
'_controller' => 'render_template'
]));

return $routes;