<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel\EventListener;


function render_template(Request $request): Response
{
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/pages/%s.php', $_route);
    return new Response(ob_get_clean());
}

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