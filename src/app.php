<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;

class LeapYearController
{
    public function index(Request $request): Response
    {
        if (is_leap_year($request->attributes->get('year'))) {
            return new Response('Yep, this is a leap year!');
        } return new Response('Nope, this is not a leap year.');
    }
}
function is_leap_year(?int $year = null): bool
{
    if (null === $year) {
        $year = (int)date('Y');
    }
    return 0 === $year % 400 || (0 === $year % 4 && 0 !== $year % 100);
}

function render_template(Request $request): Response
{
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/pages/%s.php', $_route);

    return new Response(ob_get_clean());
}

$routes = new Routing\RouteCollection();
$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', [
    'year' => null,
    '_controller' => 'LeapYearController::index',
]));

$routes->add('hello', new Routing\Route('/hello/{name}', [
    '_controller' => 'render_template',
    'name' => 'World'
]));

$routes->add('bye', new Routing\Route('/bye', [
'_controller' => 'render_template'
]));

return $routes;